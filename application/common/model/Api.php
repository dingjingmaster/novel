<?php
// +----------------------------------------------------------------------
// | KyxsCMS [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.kyxscms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kyxscms
// +----------------------------------------------------------------------

namespace app\common\model;
use think\Model;
use think\Db;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use think\facade\Validate;
use org\File;

class Api extends Model
{
	public function get_nav($category,$type,$limit,$cid,$field=true){
		$map = ['status' => 1,'pid' => $category];
		if($type!==false){
			$map['type']=$type;
		}
        $data=Db::name('category')->field($field)->where($map)->limit($limit)->order('sort')->select();
        if($data){
			foreach ($data as $k=>$v){
				$meun_list[$k]=$v;
				if($v["type"]==3){
					$meun_list[$k]["url"]=$this->nav_check_url($v["link"]);
					$meun_list[$k]["branch"]=0;
					$visit = strtolower(Request::module()."/".Request::controller()."/".Request::action());
					if($v["link"]==$visit){
						$meun_list[$k]["current"]=1;
					}else{
						$meun_list[$k]["current"]=0;
					}
				}else{
					$meun_list[$k]["url"]=url("home/lists/index",["id"=>$v["id"]]);
					$meun_list[$k]["branch"]=$this->get_branch($v["id"]);
					$meun_list[$k]["current"]=$this->has_current($v["id"],$cid);
				}
			}
			if($category===0 && $type===false){
				array_unshift($meun_list,['id'=>0,'title'=>'首页','url'=>url('home/index/index'),'branch'=>0,'current'=>$this->has_current(0,$cid)]);
			}
			return $meun_list;
		}
    }

    public function get_slider($limit){
		$map = ['status' => 1];
		$map['type'] = Request::isMobile()?'1':'0';
        $data=Db::name('slider')->where($map)->limit($limit)->order('sort')->select();
        if($data){
			foreach ($data as $k=>$v){
				$slider_list[$k]=['id'=>$v['id'],'title'=>$v['title'],'pic'=>$this->check_pic($v['picpath']),'url'=>$v['link']];
			}
			return $slider_list;
		}
    }

    public function get_news($category, $order, $limit, $pos, $time, $page, $id=null){
    	$category=$this->get_id($category);
		$map = $this->list_map($category,$pos);
		if($id){
			$map[] = ['id','in',$id];
		}
        $news=Db::name('news')->where($map)->whereTime('update_time',$time)->order($order);
        if($page){
        	$simple = Request::isMobile()?true:false;
        	$data=$news->paginate($limit,$simple);
    	}else{
    		$data=$news->limit($limit)->select();
    	}
        if($data){
         	foreach ($data as $k=>$v){
				$data[$k]=$this->data_change($v,'news');
			}
			return $data;
		}
    }

    public function get_novel($category, $order, $limit, $pos, $time, $newbook, $over, $author, $page, $id=null){
    	$category=$this->get_id($category);
    	if($page){
    		$map = $this->list_page_map($category);
	    	$update=Request::param('update',NULL);
	    	if(Request::param('order')){
	    		$order=Request::param('order');
	    		if(strstr($order,'+')){
	    			$order=str_replace('+',' ',$order);
	    		}
	    	}
	    	if(isset($update)){
				$filter_update=array_keys(Config::get('web.filter_update'));
				$time=$filter_update[$update];
			}
    	}else{
    		$map = $this->list_map($category,$pos);
    	}
		if($newbook){
			$map[] = ['serialize','=',0];
			$map[] = ['create_time','>=',strtotime("-1 month")];
		}
		if($over){
			$map[] = ['serialize','=',1];
		}
		if($author){
			$map[] = ['author','=',$author];
		}
		if($id){
			$map[] = ['id','in',$id];
		}
        $novel=Db::name('novel')->where($map)->whereTime('update_time',$time)->order($order);
        if($page){
        	$simple = Request::isMobile()?true:false;
        	$data=$novel->paginate($limit,$simple);
    	}else{
    		$data=$novel->limit($limit)->select();
    	}
        if($data){
         	foreach ($data as $k=>$v){
				$data[$k]=$this->data_change($v,'novel');
			}
			return $data;
		}
    }

    /**
     *  书籍章节列表
     *  $nid： 小说ID
     *  $order： 按那个字段 升序/降序 查找
     *  $limit： 空
     *  $page：0
     */
    public function get_chapter_list($nid, $order, $limit, $page){
    	$map[] = ['status','=',1];
    	$chapter_data=Db::name('novel_chapter')
            ->field('id, index, chapter_name, issued, update')
            ->where(['novel_id'=>$nid, 'status'=>1])
            ->order($order)
            ->select();
    	$data = array();
    	if($chapter_data){
    	    foreach ($chapter_data as $ik=>$iv) {
    	        $tmp = array();
    	        $tmp['id'] = $ik;
    	        $tmp['title'] = $iv['chapter_name'];
                $tmp['update_time'] = $iv['update'];
                $tmp['update'] = $iv['update'];
                $tmp['index'] = $iv['index'];
                $tmp['issued'] = $iv['issued'];
                $tmp['new']=(time()-$iv['update']<(1 * 24 * 3600)) ? 1 : 0;
                $tmp['time']=$iv['update'];
                $tmp['url']=url('home/chapter/index',['id'=>$nid,'key'=>$iv['index']]);
                array_push($data, $tmp);
            }

            if($page){
                $class='\\think\\paginator\\driver\\Bootstrap';
                $page_num  = call_user_func([$class,'getCurrentPage',]);
                $page_num = $page_num < 1 ? 1 : $page_num;
                $config['path'] = call_user_func([$class, 'getCurrentPath']);
                $totals=count($data);
                if($totals>$limit){
                    $start=$page_num*$limit;
                    $data=array_slice($data,$start,$limit,true);
                }
                $simple = Request::isMobile()?true:false;
                $data=$class::make($data, $limit, $page_num, $totals, $simple, $config);
            }else{
                if($limit){
                    $data=array_slice($data,0,$limit,true);
                }
            }
            if($data){
                foreach ($data as $k=>$v) {
                    if ($v['issued'] != 1) {
                        unset($data[$k]);
                    }
                }
            }
			return $data;
    	}
    }


    /**
     *  修改书籍章节内容
     *
     *  输入 novelID + chapterIndex
     *
     *  输出 id, novel_id 章节名、章节数量、prev
     */
    public function get_chapter($novelID, $chapterID){
        $chapter = array();
    	$chapter_data = Db::name('novel_chapter')
                    ->field('id,novel_id,index,chapter_name,chapter_content,update')
                    ->where(['status'=>1, 'novel_id'=>$novelID, 'index'=>$chapterID])
                    ->find();
    	if($chapter_data){
    	    // 获取本章信息
            $chapter['title'] = $chapter_data['chapter_name'];
            $chapter['id'] = $chapter_data['chapter_name'];
            $chapter['novel_id'] = $chapter_data['novel_id'];
            $chapter['content'] = $chapter_data['chapter_content'];
            $chapter['word'] = mb_strlen($chapter_data['chapter_content'], 'utf8');
            $chapter['source_id'] = $chapter_data['id'];
            $chapter['prev'] = null;
            $chapter['next'] = null;
            $chapter['time'] = time_format($chapter_data['update']);

            // 上一章 + 下一章
            $prex = (int)$chapterID - 1;
            if($prex >= 1) {
                $chapter['prev']['id'] = $prex;
                $chapter['prev']['url'] = url('home/chapter/index',['id'=>$novelID,'key'=>$prex]);
            }
            $next = (int)$chapterID + 1;
            if($next > 0) {
                $chapter_data = Db::name('novel_chapter')
                    ->field('id')
                    ->where(['status'=>1, 'novel_id'=>$novelID, 'index'=>$next])
                    ->find();
                if($chapter_data) {
                    $chapter['next']['id'] = $next;
                    $chapter['next']['url'] = url('home/chapter/index',['id'=>$novelID,'key'=>$next]);
                }
            }
            return $chapter;
	    }
    }

	public function get_link($limit){
		$map = ['status' => 1];
		$link=Db::name('link')->where($map)->field('id,title,url')->limit($limit)->select();
		return $link;
	}

	public function get_filter($name,$type,$cid){
		$name = ($name=="type")?"id":$name;
		$map=Request::param();
		unset($map[$name],$map['page']);
		$filter_name=Request::param($name);
		if($name=="id"){
			$id=$this->siblingsId($cid);
			if($id!==0){
				$map[$name]=$id;
			}
			$filter[]=["title"=>'全部',"url"=>url('home/lists/lists',$map),'current'=>($id==Request::param('id'))?1:0];
			$where = ['status' => 1,'pid' => $id];
			if($type!==false){
				$where['type'] = $type;
			}
			$data=Db::name('category')->where($where)->field('id,title,type,link')->select();
			foreach ($data as $key => $value){
				if($value["type"]==3){
					$filter[]=["title"=>$value["title"],"url"=>$this->nav_check_url($value["link"]),'current'=>$this->filter_has_current($value['id'],$filter_name)];
				}else{
					$map[$name]=$value['id'];
					$filter[]=["title"=>$value["title"],"url"=>url('home/lists/lists',$map),'current'=>$this->filter_has_current($value['id'],$filter_name)];
				}
			}
		}else{
			$filter[]=["title"=>'全部',"url"=>url('home/lists/lists',$map),'current'=>isset($filter_name)?0:1];
			$i=0;
			foreach (Config::get('web.filter_'.$name) as $key => $value) {
				$map[$name]=$i;
				$filter[]=["title"=>$value,"url"=>url('home/lists/lists',$map),'current'=>($filter_name==$i && isset($filter_name))?1:0];
				$i++;
			}
		}
		return $filter;
	}

	//面包屑导航
    public function get_crumbs($cid=0,$id=0){
    	$type=strtolower(Request::controller());
    	if(!in_array($type,['novel','news'])){
    		$type='novel';
    	}
		$crumbs[]=['title'=>'首页','url'=>url('home/index/index')];
		if($cid){
			$crumbs=array_merge($crumbs,$this->get_parent($cid));
		}
		if($keyword=Request::param('keyword')){
			$crumbs[]=['title'=>$keyword,'url'=>url('home/search/index',['keyword'=>$keyword])];
		}
		if($id){
			$data=Db::name($type)->where('id',$id)->field("id,title")->find();
			if($data){
				$crumbs[]=['title'=>$data['title'],'url'=>url('home/'.$type.'/index',['id'=>$data['id']])];
			}
		}
		return $crumbs;
	}

	public function get_branch($category){
		$map = ['status' => 1,'pid'=>$category];
		$Count=Db::name("category")->where($map)->Count();
		if($Count>0){
			return 1;
		}else{
			return 0;
		}
	}

	private function has_current($id,$cid){
		$visit = strtolower(Request::module()."/".Request::controller()."/".Request::action());
		if($id==0 && empty($cid) && $visit=='home/index/index'){
			return 1;
		}
		if($id==$cid && $id!=0 && $visit=='home/lists/index'){
			return 1;
		}
		if($id==$this->get_category($cid,'pid') && $cid  && $id!=0 && $visit=='home/lists/index'){
			return 1;
		}
		return 0;
	}

	private function filter_has_current($id,$cid){
		if($id==$cid && $id!=0){
			return 1;
		}
		if($id==$this->get_category($cid,'pid') && $cid  && $id!=0){
			return 1;
		}
		return 0;
	}

	private function get_id($id){
		if($id){
			$id = explode(',',$id);
			$map = ['status' => 1,'pid' => $id];
			$info = Db::name('category')->field("id")->where($map)->order('sort')->select();
			if($info){
				foreach ($info as $key=>$val){
					$ids[]=$val["id"];
				}
				return $ids;
			}else{
				return $id;
			}
		}
	}

	private function siblingsId($id){
		$pid=$this->get_category($id,'pid');
		if($pid==0){
			return $id;
		}
		return $pid;
	}

	private function list_map($category,$pos){
		$map[] = ['status','=',1];
		if(!empty($category)){
			$map[] = is_array($category)?['category','in',$category]:['category','=',$category];
		}
		if(is_numeric($pos)){
			$map[] = ['position','exp',Db::raw('& '.$pos.' = '.$pos)];
		}
		return $map;
	}

	private function list_page_map($category){
		$map[] = ['status','=',1];
		$serialize=Request::param('serialize',NULL);
		$size=Request::param('size',NULL);
		$keyword=Request::param('keyword',NULL);
		if(isset($keyword)){
			$map[]=['title|author','like','%'.$keyword.'%'];
			return $map;
		}
		if(!empty($category)){
			$map[] = is_array($category)?['category','in',$category]:['category','=',$category];
		}
		if(isset($serialize)){
			$filter_serialize=array_keys(Config::get('web.filter_serialize'));
			$map[]=['serialize','=',$filter_serialize[$serialize]];
		}
		if(isset($size)){
			$filter_size=array_keys(Config::get('web.filter_size'));
			$where_size=explode(' ',$filter_size[$size]);
			$map[]=['word',$where_size[0],$where_size[1]];
		}
		return $map;
	}

	private function get_parent($id,&$list=[]){
		$data=$this->get_category($id);
		if($data){
			array_unshift($list,['title'=>$data['title'],'url'=>url('home/lists/index',['id'=>$data['id']])]);
			$this->get_parent($data['pid'],$list);
		}
		return $list;
	}

	public function get_category($cid,$field=''){
		$data=Cache::remember('category',function(){
			return Db::name("category")->where('status',1)->column('*','id');
		});
		if($field){
			return isset($data[$cid][$field])?$data[$cid][$field]:false;
		}else{
			return isset($data[$cid])?$data[$cid]:false;
		}
	}

	/**
     *  $data： novel 表中的所有字段
     *  $type： novel
     */
	public function data_change($data,$type){
		$data["cid"]=$data["category"];                                                 // 分类ID
		$data["ctitle"]=$this->get_category($data["category"],'title');           // 分类标题
		$data["curl"]=url('home/lists/index',["id"=>$data["category"]]);           // 分类链接
		$data["time"]=$data["update_time"];                                             // 更新时间
		$data['pic']=$this->check_pic($data['pic']);                                    // 图片
		switch ($type) {
			case 'news':
				preg_match_all('/<img.*?src="(.*?)".*?>/is',$data["content"],$matches);
				$data["content_pic"]=$matches[1];
				break;
			case 'novel':                                                                         // 小说
				$data['tag_array']=explode(',', $data['tag']);                           // tag, 根据, 分割
				$data["word_million"]=number_format($data["word"]/10000,2);     // 字数
				$data["serialize_text"]=($data["serialize"]==1)?"已完结":"连载中";                // 连载完结
				$data["author_url"]=url('home/search/index',['keyword'=>$data["author"]]);   // 作者 url
				$chapter_data = Db::name('novel_chapter')
                            ->field('id,index,chapter_name,update')
                            ->where(['novel_id'=>$data['id'],'status'=>1])
                            ->order('index', 'desc')
                            ->all(); // 章节
                if($chapter_data){
                    // 最新章节 + 更新时间
                    $data['chapter_content'] = "";                                               // 最新章节内容
                    $data['chapter_title'] = $chapter_data[0]['chapter_name'];
                    $data['chapter_url'] = url('home/chapter/index',['id'=>$data['id'],'key'=>$chapter_data[0]['index']]);
                    $data['chapter_id'] = $chapter_data[0]['index'];
                    $data['chapter_time'] = $chapter_data[0]['update'];
                    $data['chapter_word'] = 0;
                    $data['chapter_count'] = count($chapter_data);

                    // 最新章节内容
                    $chapter_data = Db::name('novel_chapter')
                                ->field('chapter_content')
                                ->where(['novel_id'=>$data['id'],'index'=>$data['chapter_id'],'status'=>1])
                                ->find(); // 章节
                    if($chapter_data) {
                        $data['chapter_content'] = $chapter_data['chapter_content'];
                        $data['chapter_word'] = mb_strlen($chapter_data['chapter_content'], 'utf8');
                    }

                    // $visit = strtolower(Request::module()."/".Request::controller()."/".Request::action());
					// $allowUrl = ['home/novel/index'];
                    // if(in_array($visit,$allowUrl)){
                    //}

				}else{
					$data['chapter_id'] = "";
					$data['chapter_title'] = "";
					$data['chapter_content'] = "";

					$data['chapter_time'] = "";
					$data['chapter_word'] = "";
					$data['chapter_count'] = "";
					$data['chapter_url'] = "";
				}
				break;
			default:
				break;
		}
		$data["url"]=url("home/".$type."/index",["id"=>$data["id"]]);
		$data["digg"]=[
			"up"=>$data["up"],
			"up_js"=>"onclick=digg('".$data["id"]."','up','".$type."')",
			"down"=>$data["down"],
			"down_js"=>"onclick=digg('".$data["id"]."','down','".$type."')"
		];
		unset($data["category"],$data["status"],$data["up"],$data["down"]);
		return $data;
	}

	public function get_tpl($id,$tpl_type){
		$tpl=$this->get_category($id);
		if($tpl[$tpl_type]){
			return $tpl[$tpl_type];
		}
		if($tpl['pid']==0){
			$this->error='模板未设置！';
		}else{
			return $this->get_tpl($tpl['pid'],$tpl_type);
		}
	}

    /**
     *  查找小说表
     *  $id： 小说 ID
     */
	public function novel_detail($id){
		$info = Db::name("novel")
                    ->where(['id'=>$id,'status'=>1])
                    ->find();
		if(!$info){
			$this->error = '小说被禁用或已删除！';
			return false;
		}
		return $this->data_change($info,'novel');
	}

	/**
     *  $id： 小说ID
	 */
	public function novel_reader_url($id){
		$chapter=Db::name('novel_chapter')->field('novel_id,index')
                    ->where(['novel_id'=>$id,'status'=>1])
                    ->order('index')
                    ->find();
		if($chapter){
			return url('home/chapter/index',['id'=>$chapter['novel_id'],'key'=>$chapter['index']]);
		}
	}

	public function news_detail($id){
		$info = Db::name("news")->where(['id'=>$id,'status'=>1])->find();
		if(!$info){
			$this->error = '文章被禁用或已删除！';
			return false;
		}
		return $this->data_change($info,'news');
	}

	/**
     *  $id： 小说ID
     *  $type： novel
	 */
	public function hits($id,$type){
		$hits_time=Db::name($type)->where(['id'=>$id])->value('hits_time');
		if(date('d',$hits_time)==date('d',time())){
			$data['hits_day']=Db::raw('hits_day+1');
		}else{
			$data['hits_day']=1;
		}
		if(date('W',$hits_time)==date('W',time())){
			$data['hits_week']=Db::raw('hits_week+1');
		}else{
			$data['hits_week']=1;
		}
		if(date('m',$hits_time)==date('m',time())){
			$data['hits_month']=Db::raw('hits_month+1');
		}else{
			$data['hits_month']=1;
		}
		$data['hits']=Db::raw('hits+1');
		$data['hits_time']=time();
		Db::name($type)->where('id',$id)->update($data);
	}

	public function digg($id,$type,$digg){
		if(!cookie('digg_'.$type.$digg.$id)){
			cookie('digg_'.$type.$digg.$id,true);
			Db::name($type)->where('id',$id)->setInc($digg);
			return true;
		}else{
			return false;
		}
	}

	public function get_chapter_content($path){
		$addons_name = Cache::remember('addons_storage',function(){
        	$map = ['status'=>1,'group'=>'storage'];
			return Db::name('Addons')->where($map)->value('name');
		});
		if($addons_name){
        	$addons_class = get_addon_class($addons_name);
	        if(class_exists($addons_class)){
	        	$addon = new $addons_class();
	        	$content = $addon->read($path);
	        }
        }else{
        	$content=File::read(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$path);
        }
		if(Config::get('web.data_save_compress')){
            $content=@gzuncompress(base64_decode($content));
        }
        return $content;
	}

	public function set_chapter_content($path,$content){
		if(Config::get('web.data_save_compress')){
            $content=base64_encode(gzcompress($content,Config::get('web.data_save_compress_level')));
        }
        $addons_name = Cache::remember('addons_storage',function(){
        	$map = ['status'=>1,'group'=>'storage'];
			return Db::name('Addons')->where($map)->value('name');
		});
        if($addons_name){
        	$addons_class = get_addon_class($addons_name);
	        if(class_exists($addons_class)){
	        	$addon = new $addons_class();
	        	$addon->put($path,$content);
	        }
        }else{
        	File::put(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$path,$content);
        }
        return $path;
	}

	private function change_chapter_content($content){
		$content = str_replace(['<br>','<br\/>','<br />','　','&nbsp;'], ["\r\n","\r\n","\r\n","",""], strtolower($content));
		$content_array = preg_split('/[\r\n]+/', trim($content,"\r\n"));
		if(is_array($content_array)){
			$content = '';
			foreach ($content_array as $value) {
	    		if($value){
					$content.='<p>　　'.$value.'<p/>';
				}
	    	}
		}
		return $content;
	}

	private function nav_check_url($url){
		$validate = Validate::checkRule($url,'url');
		if(!$validate){
			return url($url);
		}else{
			return $url;
		}
	}

	private function check_pic($pic){
		if($pic){
			$validate = Validate::checkRule($pic,'url');
			if(!$validate){
				return Request::domain().$pic;
			}else{
				return $pic;
			}
		}
	}
}