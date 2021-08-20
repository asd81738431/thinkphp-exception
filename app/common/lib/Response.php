<?php
namespace app\common\lib;


abstract class Response{
    /**
     * @info 返回json
     * @name send
     * @param string $msg 消息
     * @param int $code 状态码 200
     * @param null $data 返回的数据
     * @return \think\response\Json
     * @author xing.guo
     */
    public final static function send($msg = '', $code = ApiCode::SUCCESS, $data = null){
        $resulf = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        return json($resulf,$code)->send();
    }
}