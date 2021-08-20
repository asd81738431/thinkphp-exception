<?php
namespace app\common\exception\extend;

use app\common\lib\ApiCode;

class Index extends ExceptionAbstract{
    protected $codeArr = [
        1001 => [
            'HttpCode' => ApiCode::INVALID_PARAMETER,
            'msg'      => '用户已存在'
        ],
        1002 => [
            'HttpCode' => ApiCode::INVALID_PARAMETER,
            'msg'      => '错误2号'
        ],
        1003 => [
            'HttpCode' => ApiCode::INVALID_PARAMETER,
            'msg'      => '错误3号'
        ],
        1004 => [
            'HttpCode' => ApiCode::INVALID_PARAMETER,
            'msg'      => '错误4号'
        ],
    ];
}