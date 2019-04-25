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

namespace app\user\model;

use think\Model;
use think\facade\Request;
use think\facade\Cookie;

class Recentread extends Model
{
    protected $autoWriteTimestamp = true;

	public function info($id){
		$data=unserialize(Cookie::get('read_log'));
		return $data[$id];
	}

    public function lists($limit=10){
        $readlog=[];
        $page=Request::get('page',1);
        $data=Cookie::get('read_log');
        if($data){
            $data=array_reverse(unserialize($data),true);
            $count=count($data);
            $data=array_slice($data,($page-1)*$limit,$limit,true);
            foreach ($data as $key=>$val){
                $novel=model('common/api')->novel_detail($key);
                if($novel !== false){
                    $read=explode('|',$val);
                    $readlog[]=['novel_id'=>$key,'chapter_id'=>$read[0],'read_time'=>$read[2],'book'=>$novel,'reader_url'=>url('home/chapter/index',['id'=>$read[0],'key'=>$read[1]])];
                }
            }
            return ['count'=>$count,'list'=>$readlog];
        }
    }

    /**
     *  添加/更新阅读进度
     *
     *  输入： 小说ID、章节ID、 章节名
     */
    public function add($novel_id,$chapter_id,$chapter_key){
        $data=unserialize(Cookie::get('read_log'));
        $data[$novel_id]=$chapter_id.'|'.$chapter_key.'|'.time();
        if(count($data)>40){
            array_shift($data); // 最近一次阅读的小说章节id 和 章节名
        }
        /* 持久化存储小说阅读进度 */
        model('user/bookshelf')->chapter_update($novel_id, $chapter_id, $chapter_key);
        Cookie::forever('read_log',serialize($data));
    }

    public function del($id){
        $data=unserialize(Cookie::get('read_log'));
        unset($data[$id]);
        Cookie::forever('read_log',serialize($data));
    }
}