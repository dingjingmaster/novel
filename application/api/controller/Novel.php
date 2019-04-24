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

namespace app\api\controller;
use think\Controller;
use think\Db;
use org\Oauth;

class Novel extends Controller{

	protected $beforeActionList = [
        'checkDeduct'  =>  ['only'=>'content,get_chapter_list']
    ];

    protected function checkDeduct(){
    	$user_ip=$this->request->ip();
    	$allow_ip=['127.0.0.1','localhost'];
    	if(in_array($user_ip,$allow_ip)){
    		return false;
    	}
        $oauth = new Oauth();
		$check_deduct=$oauth->checkDeduct('novel');
		$check_deduct=json_decode($check_deduct,true);
		if($check_deduct['code']!=1){
			echo json_encode($check_deduct);
			exit;
		}
    }

	public function category($cid=false,$type=0,$filter=false){
		$category=model('api/api')->category($cid,$type,$filter);
		return json($category);
	}

	public function lists($cid=false,$order='update_time desc',$limit=20,$pos=false,$time=false,$newbook=false,$over=false,$author=false,$paginator=1,$id=null){
		$list=model('common/api')->get_novel($cid,$order,$limit,$pos,$time,$newbook,$over,$author,$paginator,$id);
		return json($list);
	}

	public function content($id,$order='id asc',$limit='',$page=false){
		$book=model('common/api')->novel_detail($id);
		$book['chapter']=model('common/api')->get_chapter_list($id,$order,$limit,$page);
		return json($book);
	}

	public function get_chapter_list($id, $order='id asc', $limit='', $page=false){
		$chapter_list=model('common/api')->get_chapter_list($id, $order, $limit, $page);
	}

	public function chapter($id,$key){
		$chapter=model('common/api')->get_chapter($id,$key);
		return json($chapter);
	}

}