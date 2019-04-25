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

namespace app\home\controller;

use app\common\controller\Base;
use think\facade\Cookie;

class Chapter extends Base {
    public function index() {
        Cookie::set('__forward__',$this->request->url());
        $id=$this->request->param('id');                        // 书籍ID
        $key=$this->request->param('key');                      // key：章节 index
        $chapter=model('common/api')->get_chapter($id, $key);   // 查询章节表，获取章节内容
        $info=model('common/api')->novel_detail($id);           // 查询小说表
        if(!$info){
            $error = model('common/api')->getError();
            $this->error(empty($error) ? '未找到该小说！' : $error,url('Home/Index/index'));
        }
        $info['chapter']=$chapter;
        $reader_config=Cookie::get('reader_config_'.$this->mold,'');
        if(empty($reader_config)){
            if($this->mold=='web'){
                $reader_config=[0,18,1,1,1];
            }else{
                $reader_config=['d',3,0,'v'];
            }
        }else{
            $reader_config=explode('|',$reader_config);
        }
        //阅读记录
        model("user/recentread")->add($id, $key, $chapter['title']);
        $is_bookshelf=model('user/bookshelf')->check($info['id']);
        $this->assign($info);
        $this->assign('is_bookshelf',($is_bookshelf?$is_bookshelf:0));
        $this->assign('add_bookshelf','onclick=add_bookshelf()');
        $this->assign('reader_config',$reader_config);
        $this->assign('reader_tplpath','/template/reader/'.$this->mold.'/');
        if($this->mold=='web'){
            $recentread=model('user/recentread')->lists(5);
            $this->assign('recentread',$recentread['list']);
        }
        return $this->fetch('template/reader/'.$this->mold.'/index.html');
    }

    public function lists(){
       $this->assign('id',$this->request->param('id'));
       return $this->fetch('template/reader/'.$this->mold.'/list.html');
    }

    public function info(){
        $id=$this->request->param('id');
        $key=$this->request->param('key');
        $chapter=model('common/api')->get_chapter($id,$key);
        $chapter['vip']=0;
        $chapter['nextVip']=0;
        $info=model('common/api')->novel_detail($id);
        $info['chapter']=$chapter;
        //阅读记录
        model("user/recentread")->add($chapter['novel_id'],$id,$chapter['id']);
        return json(['code'=>1,'data'=>$info]);
    }
}
