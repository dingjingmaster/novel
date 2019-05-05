<?php

namespace app\home\controller;

use app\common\controller\Base;

/**
 *  首页
 *
 *
 */
class Sitemap extends Base {
    public function index() {
        $info=model('common/api')->get_novel_sitemap();
        if(!$info){
            $error = model('common/api')->getError();
            $this->error(empty($error) ? '没有找到 sitemap' : $error,url('Home/Index/index'));
        }

        $this->assign('sitemap',$info);
        return $this->fetch($this->home_tplpath.'sitemap.xml');
    }
}