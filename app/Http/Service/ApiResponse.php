<?php

namespace App\Http\Services;

use GuzzleHttp\Psr7\Stream;

class ApiResponse
{

    protected $response;

    protected $data = [];

    protected $code;

    protected $message;

    public function __construct(Stream $respone)
    {
        $this->response = json_decode($respone->__toString(), true);
    }

    /**
     * @return null
     * 获取api请求状态
     */
    public function status()
    {
        return isset($this->response['status']) ? $this->response['status'] : null;
    }

    /**
     * @return null|string|\Symfony\Component\Translation\TranslatorInterface
     * 获取api请求信息
     */
    public function message()
    {
        $message = trans('api.' . $this->status());
        if (strpos($message, 'api.') === false) {
            return $message;
        }
        return isset($this->response['message']) ? $this->response['message'] : null;
    }

    /**
     * @return null
     * 获取api请求数据
     */
    public function data()
    {
        return isset($this->response['data']) ? $this->response['data'] : [];
    }

    /**
     * @return mixed
     * 获取完整的api返回数据
     */
    public function response()
    {
        return $this->response;
    }


}
