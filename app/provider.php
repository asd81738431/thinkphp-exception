<?php
use app\ExceptionHandle;
use app\Request;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,

    /**
     * 自定义门面
     */
    #自定义异常
    'CommonException'        => app\common\facade\ExceptionFacade::class,
    #自定义助手类
    'Helper'                 => app\common\facade\HelperFacade::class,

];
