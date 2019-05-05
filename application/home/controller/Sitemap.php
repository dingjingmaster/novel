<?php

namespace app\home\controller;
use think\Controller;

/**
 *  首页
 *
 *
 */
class Sitemap extends Controller {
    public function index() {
        $buf = '';
        $info=model('common/api')->get_novel_sitemap();
        if(!$info){
            $error = model('common/api')->getError();
            $this->error(empty($error) ? '没有找到 sitemap' : $error,url('Home/Index/index'));
        }

        $buf = '<?xml version="1.0" encoding="UTF-8"?>' .
                    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
                        '<url>' .
                            '<loc>https://enjoyread.top</loc>' .
                            '<lastmod>2019-05-01</lastmod>' .
                            '<changefreq>weekly</changefreq>' .
                            '<priority>1</priority>' .
                        '</url>';
        foreach ($info as $ik=>$iv){
            $buf .= '<url><loc>' . 'https://enjoyread.top/home/novel/index/id/' . $iv['id'] . '.html</loc>';
            $buf .= '<lastmod>' . date('Y-m-d h:i:s',$iv['update_time']) . '</lastmod>';
            $buf .= '<changefreq>weekly</changefreq><priority>1</priority></url>';
        }
        $buf .= '</urlset>';
        return $result =  xml($buf,200,[],['root_node'=>'xml']);
    }
}