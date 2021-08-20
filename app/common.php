<?php

/**
 * @info 报出自定义错误
 * @param string $modelName
 * @param int $serverCode
 * @param string $message
 * @param null|mixed $data
 */
function exception(string $modelName = '', int $serverCode = 0, string  $message = '', $data = null){
    app('CommonException')::name($modelName)
        ->serverCode($serverCode)
        ->message($message)
        ->data($data)
        ->exception();
}