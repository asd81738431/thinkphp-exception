<?php
namespace app\common\lib;

abstract class ApiCode
{
    const SUCCESS = 200;//请求成功
    const CREATED = 201;//创建|修改|更新成功
    const ACCEPTED = 202;//异步返回的成功
    const INVALID_PARAMETER = 400;//参数验证错误
    const UNAUTHORIZED = 401;//无权限
    const FORBIDDEN = 403;//被禁止访问
    const NOT_FOUND = 404;//找不到路由
    const SERVICE_ERROR = 500;//程序错误
    const ERROR = 999;//失败(未知错误)
}