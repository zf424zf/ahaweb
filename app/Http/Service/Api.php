<?php

namespace App\Http\Services;

use GuzzleHttp\Client as Http;
use GuzzleHttp\Exception\ClientException;
use Log;

class Api
{

    const SOURCE = 2;//h5端请求源
    const TIMEOUT = 50000000;//超时时间

    /**
     * @param $path  请求url path
     * @param array  请求参数
     * @param bool $cache 请求结果是否缓存 默认
     * @param int $expire 缓存过期时间, 单位秒
     * @return \Psr\Http\Message\ResponseInterface
     * POST请求方法
     */
    public function post($path, array $parameter = [], $cache = 0)
    {
        return self::request($path, $parameter, 'POST', $cache);
    }

    /**
     * @param $path  请求url path
     * @param array  请求参数
     * @param bool $cache 请求结果是否缓存 默认
     * @param int $expire 缓存过期时间, 单位秒
     * @return \Psr\Http\Message\ResponseInterface
     * GET请求接口
     */
    public function get($path, array $parameter = [], $cache = 0)
    {
        return self::request($path, $parameter, 'GET', $cache);
    }

    /**
     * @param $path  请求url path
     * @param array  请求参数
     * @param bool $cache 请求结果是否缓存 默认
     * @param int $expire 缓存过期时间, 单位秒
     * @return \Psr\Http\Message\ResponseInterface
     * PUT请求接口 , 实现方案：使用post请求方式，在参数中添加_method=PUT
     */
    public function put($path, array $parameter = [], $cache = 0)
    {
        return self::request($path, $parameter, 'PUT', $cache);
    }

    /**
     * @param $path  请求url path
     * @param array  请求参数
     * @param bool $cache 请求结果是否缓存 默认
     * @param int $expire 缓存过期时间, 单位秒
     * @return \Psr\Http\Message\ResponseInterface
     * delete 请求接口，实现方案：使用post请求方式，在参数中添加_method=DELETE
     */
    public function delete($path, array $parameter = [], $cache = 0)
    {
        return self::request($path, $parameter, 'DELETE', $cache);
    }

    /**
     * @param $path
     * @param array $parameter
     * @param string $method
     * @param bool $cache 请求结果是否缓存 默认
     * @param int $expire 缓存过期时间, 单位秒
     * @return mixed
     * 通用请求方法
     */
    protected static function request($path, array $parameter, $method, $cache = 0)
    {
        if (!$path) {
            return false;
        }
        $path = trim(config('api.version'), '/') . '/' . config('api.' . $path);
        //处理缓存
        $hash = md5(json_encode(func_get_args()));
        $key = cacheKey('api', $hash);
        if ($cache) {
            if ($response = app('redis')->get($key)) {
                return unserialize($response);
            }
        }
        $parse = parse_url(config('api.root'));
        $resolve = array(sprintf("%s:%d:%s", $parse['host'], config('api.port'), config('api.ip')));
        $http = new Http([
            'base_uri' => config('api.root'),
            'timeout'  => self::TIMEOUT,
            'curl'     => [CURLOPT_IPRESOLVE => $resolve]
        ]);
        $parameters = ['headers' => self::headers()];
        $method = strtoupper($method);
        //put,delete 请求处理
        if (in_array(strtoupper($method), ['PUT', 'DELETE'])) {
            $parameter['_method'] = strtoupper($method);
            $method = 'POST';
        }
        $method == 'GET' ? $parameters['query'] = $parameter : $parameters['form_params'] = $parameter;
        try {
            $response = new ApiResponse($http->request($method, $path, $parameters)->getBody());
            if ($cache) {
                app('redis')->setex($key, $cache, serialize($response));
            }
            return $response;
        } catch (ClientException $e) {
            $response = $e->getResponse()->getBody()->__toString();
            view()->share('error', $response);
            if (env('APP_DEBUG')) {
                Log::error(json_decode($response, true));
            }
            abort(503);
        }
    }

    /**
     * @return array
     * 获取头信息
     */
    protected static function headers()
    {
        return [
            'token'  => app('request')->cookie('token'),
            'source' => self::SOURCE,
        ];
    }

}
