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

// 应用公共文件
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login($type='user'){
    $user = session($type.'_auth');
    if (empty($user)){
        return 0;
    } else {
        return session($type.'_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

function think_ucenter_md5($str, $key = 'HXyiyuanhuanlego'){
	return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 获取数据库中的配置列表
 * @return array 配置数组
 */
function config_lists(){
	$data   = db('config')->field('type,name,value')->select();
	$config = [];
	if($data && is_array($data)){
		foreach ($data as $value) {
			$config[$value['name']] = config_parse($value['type'], $value['value']);
		}
	}
	return $config;
}


/**
 * 根据配置类型解析配置
 * @param  integer $type  配置类型
 * @param  string  $value 配置值
 */
function config_parse($type, $value){
	
	switch ($type) {
		case 3: //解析数组
			$array = preg_split('/[\r\n]+/', trim($value, "\r\n"));
			if(strpos($value,':')===false){
				$value = $array;
			}else{
				$value  = [];
				foreach ($array as $val) {
					list($k, $v) = explode(':', $val);
					$value[$k]   = $v;
				}
			}
			break;
	}
	return $value;
}

function time_format($time = NULL,$format='Y-m-d H:i',$type=0){
    $time = $time === NULL ? time() : intval($time);
    if(empty($type)){
    	return date($format, $time);
    }else{
    	$current_time=time();
	    $span=$current_time-$time;
	    if($span<60){
	        return "刚刚";
	    }else if($span<3600){
	        return intval($span/60)."分钟前";
	    }else if($span<24*3600){
	        return intval($span/3600)."小时前";
	    }else if($span<(7*24*3600)){
	        return intval($span/(24*3600))."天前";
	    }else{
	        return date($format,$time);
	    }
    } 
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name){
    $class = "addons\\{$name}\\{$name}";
    return $class;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = [];
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = []){
    if(is_array($tree)) {
        $refer = [];
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}

/**
 * 生成随机字符
 * @param  array $lenght  位数
 */
function uniqidReal($lenght = 13) {
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        $bytes = uniqid(rand(10,99));
    }
    return substr(bin2hex($bytes), 0, $lenght);
}