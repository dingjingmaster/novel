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

namespace app\admin\controller;
use think\Db;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use org\File;

class Tool extends Base
{
    public function datadel(){
        $tool=model('tool');
        if($this->request->param('type')){
            switch ($this->request->param('type')) {
                case 'novel':
                    $tool->datadel_novel();
                    break;
                case 'news':
                    $tool->datadel_news();
                    break;
                case 'user':
                    $tool->datadel_user();
                    break;
            }
            return $this->success('清空成功！');
        }else{
            $this->assign('meta_title','数据清除');
            return $this->fetch();
        }
    }

    public function datato(){
        $tool=model('tool');
        $this->assign('meta_title','数据转换');
        if($this->request->isPost()){
            return $this->fetch('datato_progress');
        }else{
            return $this->fetch();
        }
    }

    public function data_to_progress($page=1,$page_num=0){
        $chapter_count=Db::name('novel_chapter')->count('id');
        $limit=20;
        $id=$this->request->param('id');
        if($id){
            $chapter=Db::name('novel_chapter')->where(['id'=>$id])->value('chapter');
        }else{
            $chapter=Db::name('novel_chapter')->page($page,1)->value('chapter'); 
        }
        if(Config::get('web.data_save_compress')){
            $chapter=@gzuncompress(base64_decode($chapter));
        }
        $chapter=json_decode($chapter,true);
        $totals=count($chapter);
        $page_count=ceil($totals/$limit);
        if($totals>$limit){
            $start=$page_num*$limit;
            $data=array_slice($chapter,$start,$limit,true);
        }else{
            $data=$chapter;
        }
        $addons_name = Cache::remember('addons_storage',function(){
            $map = ['status'=>1,'group'=>'storage'];
            return Db::name('Addons')->where($map)->value('name');
        });
        if($addons_name){
            $addons_class = get_addon_class($addons_name);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
            }
        }
        foreach ($data as $key => $value){
            if($value['auto']==0){
                $content=File::read(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$value['path']);
                if($addons_name){
                    $addon->put($value['path'],$content);
                }
                if($this->request->param('del')){
                    File::unlink(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$value['path']);
                }
            }
        }
        if($chapter_count<=$page){
            return $this->success('转换完成','',['complete'=>true,'chapter_count'=>$chapter_count,'page'=>$page]);
        }else{
            if($page_count<=$page_num+1){
                $page_num=0;
                return $this->success('转换进度',url('data_to_progress',['del'=>$this->request->param('del'),'id'=>$id,'page'=>$page+1,'page_num'=>$page_num]),['complete'=>false,'chapter_count'=>$chapter_count,'page'=>$page+1]);
            }else{
                return $this->success('转换进度',url('data_to_progress',['del'=>$this->request->param('del'),'id'=>$id,'page'=>$page,'page_num'=>$page_num+1]),['complete'=>false,'chapter_count'=>$chapter_count,'page'=>$page]);
            }
        }
    }
}