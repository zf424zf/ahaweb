<?php

if (!function_exists('staticFile')) {
    /**
     * @param string $file
     * @param bool $return
     * @return string
     * 返回静态资源地址
     */
    function staticFile($file)
    {
        $source = trim(config('app.static_path'), '/') . '/' . trim($file, '/');
        return asset($source) . '?v=' . config('app.version');
    }

}

if (!function_exists('avatar')) {
    /**
     * @param $uid
     * @param string $size
     * @return mixed|string
     * 生成头像链接地址
     */
    function avatar($uid, $size = 'middle')
    {
        $url = config('api.root') . config('api.version') . '/user/avatar/' . $uid . '/' . $size;
        return urlSecure($url);
    }
}

if (!function_exists('qrcode')) {
    /**
     * @param $code 二维码内容
     * @param int $size 二维码尺寸
     * @return mixed|string
     * 生成二维码链接地址
     */
    function qrcode($code, $size = 300)
    {
        $param = [
            'code' => $code,
            'size' => $size
        ];
        $url = config('api.root') . config('api.version') . "/helper/qrcode?" . http_build_query($param);
        return urlSecure($url);
    }
}

if (!function_exists('urls')) {
    /**
     * @param null $path
     * @param array $parameters
     * @param null $secure
     * @return mixed
     * 自动过滤laravel 的https或者http协议
     */
    function urls($path = null, $parameters = [], $secure = null)
    {
        return urlSecure(url($path, $parameters, $secure));
    }
}

if (!function_exists('urlSecure')) {
    /**
     *用于过滤所有页面地址为与当前访问协议一致的地址
     */
    function urlSecure($url)
    {
        if (starts_with($url, 'http://')) {
            $url = preg_replace('/http:\/\//', '', $url, 1);
        } elseif (starts_with($url, 'https://')) {
            $url = preg_replace('/https:\/\//', '', $url, 1);
        } elseif (starts_with($url, '//')) {
            $url = preg_replace('/\/\//', '', $url, 1);
        } else {
            return $url;
        }
        return (isSecure() ? 'https://' : 'http://') . $url;

    }
}


if (!function_exists('isSecure')) {
    /**
     * @return bool
     * 判断是否为https请求
     */
    function isSecure()
    {
        if (request()->isSecure()) {
            return true;
        }
        if (isset($_SERVER['HTTP_X_SCHEME']) && strtolower($_SERVER['HTTP_X_SCHEME']) == 'https') {
            return true;
        }

        return false;
    }
}

if (!function_exists('getWeek')) {
    /**
     * @param $timestamp
     * @return mixed
     * 根据时间戳获取语意化的星期
     */
    function getWeek($timestamp)
    {
        $week = date('w', $timestamp);
        $map = [
            1 => '星期一',
            2 => '星期二',
            3 => '星期三',
            4 => '星期四',
            5 => '星期五',
            6 => '周六',
            0 => '周日'
        ];
        return $map[$week];
    }
}

if (!function_exists('cache')) {
    /**
     * Get / set the specified cache value.
     *
     * If an array is passed, we'll assume you want to put to the cache.
     *
     * @param  dynamic  key|key,default|data,expiration|null
     * @return mixed
     *
     * @throws \Exception
     */
    function cache()
    {
        $arguments = func_get_args();

        if (empty($arguments)) {
            return app('cache');
        }

        if (is_string($arguments[0])) {
            return app('cache')->get($arguments[0], isset($arguments[1]) ? $arguments[1] : null);
        }

        if (is_array($arguments[0])) {
            if (!isset($arguments[1])) {
                throw new Exception(
                    'You must set an expiration time when putting to the cache.'
                );
            }

            return app('cache')->put(key($arguments[0]), reset($arguments[0]), $arguments[1]);
        }
    }
}


if (!function_exists('parseDomain')) {
    /*
     *	获得url的domain
     * 	2013年5月9日20:27:56
     */
    function parseDomain($url)
    {

        if (empty($url)) {
            return "";
        }
        $urlinfo = parse_url($url);
        $host = isset($urlinfo['host']) ? $urlinfo['host'] : '';

        if ($host == "") {
            return "";
        } else {
            preg_match("/(.*?)([^\.]+\.[^\.]+)$/", $host, $matches);
            if ($matches) {
                return $matches[2];
            } else {
                return "";
            }
        }

    }
}

if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        $ip = "unknown";
        /*
         * 访问时用localhost访问的，读出来的是“::1”是正常情况。
         * ：：1说明开启了ipv6支持,这是ipv6下的本地回环地址的表示。
         * 使用ip地址访问或者关闭ipv6支持都可以不显示这个。
         * */
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif (isset($_SERVER["HTTP_CLIENT_ip"])) {
                $ip = $_SERVER["HTTP_CLIENT_ip"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_ip')) {
                $ip = getenv('HTTP_CLIENT_ip');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        }
        if (trim($ip) == "::1") {
            $ip = "127.0.0.1";
        }
        return $ip;
    }
}

/**
 *  返回api response 的data数据
 */
if (!function_exists('apiData')) {
    function apiData($response, $dataKey = 'data')
    {
        $data = [];
        //$response = json_decode($response,true);
        if (2000 == $response['status'] && isset($response[$dataKey])) {
            $data = $response[$dataKey];
        }
        return $data;

    }
}

/**
 * 格式化4个直辖市和港澳地区的数组格式
 * @param array $array 地区数组
 * 格式例如{
 * "city": {
 * "no": "110105",
 * "parent_no": "110000",
 * "area_name": "\u671d\u9633\u533a",
 * "area_postcode": "010",
 * "area_level": "2"
 * },
 * "province": {
 * "no": "110000",
 * "parent_no": "0",
 * "area_name": "\u5317\u4eac\u5e02",
 * "area_postcode": "010",
 * "area_level": "1"
 * }
 * }
 * @return array
 */
if (!function_exists('MunicipalityFormat')) {

    function MunicipalityFormat(array $array)
    {
        $municipality = [110000, 120000, 310000, 500000, 820000, 810000];
        if (array_key_exists('city', $array) && array_key_exists('province', $array)) {
            if (in_array($array['province']['no'], $municipality)) {
                $array['city'] = $array['province'];
                $array['city']['parent_no'] = $array['province']['no'];
                $array['city']['area_level'] = 2;
            }
        }
        return $array;
    }
}

/**
 * 获取分页信息函数
 * @param int $curpage 当前页面
 * @param int $pagesize 每页数据数量
 * @param int $total 总数据数量
 * @param array $urlParams 分页链接URL的参数
 * @return array
 */
if (!function_exists('paging')) {

    function paging($curpage, $pagesize, $total, $urlParams = [], $returnView = false)
    {
        $pageurl = urls(\Request::fullUrl());
        $pageurlExt = '';
        //url的链接符号   & ?
        $urlSign = strpos($pageurl, '?') !== false ? '&' : '?';
        $sign = '';
        if ($urlParams) {
            foreach ($urlParams as $key => $val) {
                $pageurlExt .= $sign . "$key=$val";
                $sign = '&';
            }
        }

        //页面展示的分页链接数目
        $pageLinks = 5;
        $totalPages = (int)(($total + $pagesize - 1) / $pagesize);
        $curpage = max(intval($curpage), 1);

        $pstart = $pend = 0;
        //需要展示全部页数链接
        if ($totalPages <= $pageLinks) {

            $pstart = 1;
            $pend = $totalPages;

        } else {

            $halfLinks = (int)($pageLinks / 2);
            //当前页显示在左边
            if (($curpage - $halfLinks) <= 0) {
                $pstart = 1;
                $pend = $pageLinks;

                //当前页显示在右边
            } else if (($curpage + $halfLinks) > $totalPages) {

                $pstart = $totalPages - $pageLinks + 1;
                $pend = $totalPages;

                //当前页面显示在页面中间
            } else {
                $pstart = $curpage - $halfLinks;
                $pend = $curpage + $halfLinks;
            }
        }

        //左右省略号
        $dotLeft = $dotRight = '';
        $dotLeft = $pstart > 1 ? '...' : '';
        $dotRight = $pend < $totalPages ? '...' : '';

        $links = [];  //要显示的页数
        if ($pstart <= $pend) {
            for ($i = $pstart; $i <= $pend; $i++) {
                $links[] = $i;
            }
        }

        //处理掉
        $pageurl = preg_replace('/page=\d*/i', '', $pageurl);
        //$pageurl = preg_replace('/\?page=\d*/i','',$pageurl);
        $urlSign = strpos($pageurl, '?') !== false ? '&' : '?';

        $page['url_sign'] = $urlSign;
        //上一页，下一页
        $page['last_page'] = max($curpage - 1, 1);
        $page['next_page'] = min($totalPages, $curpage + 1);
        $page['last_url'] = $pageurl . $urlSign . 'page=' . $page['last_page'];
        $page['next_url'] = $pageurl . $urlSign . 'page=' . $page['next_page'];
        //左右省略号
        $page['dot_left'] = $dotLeft;
        $page['dot_right'] = $dotRight;

        //总条目
        $page['total'] = $total;
        //总页数
        $page['total_pages'] = $totalPages;
        $page['current_page'] = $curpage;
        //要展示的链接页， 2  3  4  5  6 
        $page['page_link_num'] = $links;
        $page['page_url'] = $pageurl ? $pageurl : url();
        return $returnView ? view('layout.paging', ['paging' => $page]) : $page;

    }
}

if (!function_exists('avatar')) {
    /**
     * @param $uid
     * @param string $size
     * @return mixed|string
     * 生成头像链接地址
     */
    function avatar($uid, $size = 'middle')
    {
        $url = config('apiurl.root') . "user/avatar/" . $size . '/' . $uid;
        return urlSecure($url);
    }
}

if (!function_exists('qrcode')) {
    /**
     * @param $code 二维码内容
     * @param int $size 二维码尺寸
     * @return mixed|string
     * 生成二维码链接地址
     */
    function qrcode($code, $size = 300)
    {
        $param = [
            'code' => $code,
            'size' => $size
        ];
        $url = config('apiurl.root') . "helper/qrcode?" . http_build_query($param);
        return urlSecure($url);
    }
}

if (!function_exists('raceStatus')) {
    function raceStatus($status = false)
    {
        $statuses = [
            -3 => '已取消',
            -2 => '已撤回',
            -1 => '审核未通过',
            0  => '审核中',
            1  => '预告中',
            2  => '报名中',
            3  => '待开赛',
            4  => '进行中',
            5  => '已结束',
        ];
        if ($status === false) {
            return $statuses;
        }
        if (isset($statuses[$status])) {
            return $statuses[$status];
        }
        return '未知';
    }
}

if (!function_exists('orderStatus')) {
    function orderStatus($status = false)
    {
        $statuses = [
            '-3' => '审核失败',    //订单审核失败，未通过
            '-2' => '已取消',    //在订单未支付前，用户自行取消
            '-1' => '已过期',    //订单超时未支付，由系统取消
            '0'  => '待审核',    //订单等待管理员审核
            '1'  => '待支付',    //订单审核通过，等待支付，不需要审核时自动进入此状态
            '2'  => '待支付',    //(可以退款)订单支付了一部分
            '3'  => '已支付',    //(可以退款)订单支付完成，金额为0的订单，在审核通过之后或者等待支付时应该直接进入此状态。
            '4'  => '已发货',    //(可以退款)订单已经发货，用于需要邮寄的实体货物
            '5'  => '已完成',//(可以退款)货物已签收或者订单完成
            '6'  => '已支付(有退款)',    //(可以退款)订单部分退款中
            '7'  => '已支付(有退款)',    //(可以退款)订单部分退款中,此状态已经提交给第三方支付平台处理,但未收到退款结果通知
            '8'  => '已支付(有退款)',    //(可以退款)订单部分退款成功
            '9'  => '已支付(有退款)',    //(可以退款)部分退款失败
            '10' => '退款中',    //全额退款中
            '11' => '退款中',    //全额退款中,此状态已经提交给第三方支付平台处理,但未收到退款结果通知
            '12' => '退款成功',    //订单全额退款成功
            '13' => '退款失败',    //(可以退款)退款失败
            '14' => '已评价',    //(可以退款)已评价
        ];
        if ($status === false) {
            return $statuses;
        }
        if (isset($statuses[$status])) {
            return $statuses[$status];
        }
        return '未知';
    }
}

/**
 * 通过给定图片地址自动拼接图片url
 */
if (!function_exists('thumb')) {
    function thumb($urls, $width = 0, $height = 0,$limit=false)
    {
        is_array($urls) || $urls = [$urls];
        $append = [];
        if ($width || $height) {
            $append[] = 'x-oss-process=image/resize';
            $append[] = 'm_fill';
            $append[] = 'Q_100';
        }
        if ($width) $append[] = 'w_' . $width;
        if ($height) $append[] = 'h_' . $height;
        if ($limit) $append[] = 'limit_0';//取到放大的图片
        $thumb = [];
        foreach ($urls as $url) {
            $thumb[] = $url . '?' . implode(',', $append);
        }
        return count($thumb) == 1 ? current($thumb) : $thumb;
    }
}

if (!function_exists('tranTime')) {
    function tranTime($time)
    {
        $rtime = date("m-d H:i", $time);
        $htime = date("H:i", $time);

        $time = time() - $time;

        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . '小时前 ' . $htime;
        } elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1)
                $str = '昨天 ' . $rtime;
            else
                $str = '前天 ' . $rtime;
        } else {
            $str = $rtime;
        }
        return $str;
    }
}

if (!function_exists('isWeixin')) {
    function isWeixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
}


//缓存key 统一管理
if (!function_exists('cacheKey')) {
    function cacheKey($key, $arr1 = '', $arr2 = '', $arr3 = '', $arr4 = '')
    {
        $str = config('cachekey.' . $key);
        return sprintf($str, $arr1, $arr2, $arr3, $arr4);
    }
}
//格式化数字，每三位一个逗号
if (!function_exists('num_format')) {
    function num_format($num)
    {
        if (!is_numeric($num)) {
            return false;
        }
        $rvalue = '';
        $num = explode('.', $num);//把整数和小数分开
        $rl = !isset($num['1']) ? '' : $num['1'];//小数部分的值
        $j = strlen($num[0]) % 3;//整数有多少位
        $sl = substr($num[0], 0, $j);//前面不满三位的数取出来
        $sr = substr($num[0], $j);//后面的满三位的数取出来
        $i = 0;
        while ($i <= strlen($sr)) {
            $rvalue = $rvalue . ',' . substr($sr, $i, 3);//三位三位取出再合并，按逗号隔开
            $i = $i + 3;
        }
        $rvalue = $sl . $rvalue;
        $rvalue = substr($rvalue, 0, strlen($rvalue) - 1);//去掉最后一个逗号
        $rvalue = explode(',', $rvalue);//分解成数组
        if ($rvalue[0] == 0) {
            array_shift($rvalue);//如果第一个元素为0，删除第一个元素
        }
        $rv = $rvalue[0];//前面不满三位的数
        for ($i = 1; $i < count($rvalue); $i++) {
            $rv = $rv . ',' . $rvalue[$i];
        }
        if (!empty($rl)) {
            $rvalue = $rv . '.' . $rl;//小数不为空，整数和小数合并
        } else {
            $rvalue = $rv;//小数为空，只有整数
        }
        return $rvalue;
    }

}


if (!function_exists('getlasttime')) {
    /**获取剩余时间
     *
     */
    function getlasttime($time_s)
    {
        //开始时间
        $strtime = '';
        $time = $time_s - time();
        //月
        if ($time >= 2592000) {
            return $strtime = intval($time / 2592000) . '个月';
        }
        //日
        if ($time >= 86400) {
            $strtime .= intval($time / 86400) . '天';
            $time = $time % 86400;
        }
        //时
        if ($time >= 3600) {
            $strtime .= intval($time / 3600) . '小时';
            $time = $time % 3600;
        } else {
            $strtime .= '';
        }
        //分
        if ($time >= 60) {
            $strtime .= intval($time / 60) . '分钟';
            $time = $time % 60;
        } else {
            $strtime .= '';
        }

        return $strtime;
    }
}
if(!function_exists('hump_to_snake')){
    //驼峰转变成蛇形
    function hump_to_snake($str){
        $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
            return '_'.strtolower($matches[0]);
        },$str);
        return $str;
    }
}
if(!function_exists('snake_to_hump')){
    //蛇形转驼峰
    function snake_to_hump($str){
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i',function($matches){
            return strtoupper($matches[2]);
        },$str);
        return $str;
    }
}