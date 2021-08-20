<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\facade\ExceptionFacade;
use app\common\lib\ApiCode;
use app\common\lib\Response;
use app\common\lib\upload\extend\Image;
use think\facade\Filesystem;
use think\Request;

class Index extends BaseController
{
    public function index(Request $request){
        $file = request()->file();
        $a = Filesystem::disk('public')->putFile(   'test', $file['image']);

        var_dump($a);

        exit;

        #自定义异常错误
        #exception('Index',1001);
        #exception('Index',1001,'msg',['test'=>'test']);
        #异常捕捉
        #try {
        #    throw new \Exception(1001);
        #}catch (\Exception $e){
        #    exception('Index',$e->getMessage());
        #}
        #返回成功
        #Response::send('请求成功');
        #Response::send('创建|修改|更新成功',ApiCode::CREATED);
    }
}