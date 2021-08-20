<?php
namespace app;

use app\common\exception\extend\ExceptionAbstract;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\db\exception\PDOException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\exception\RouteNotFoundException;
use think\Response;
use Throwable;
use think\facade\Log;
use app\common\lib\ApiCode;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    //HTTP Status Code状态码
    protected $code;
    //应用返回信息
    protected $msg;

    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        if(!env('app_debug')){
            //SQL错误记录
            if ($exception instanceof PDOException) {
                $sql = $exception->getData()['Database Status']['Error SQL'];
                if (!empty($sql)) {
                    Log::write('PDOException: ' . $sql,'error');
                }
            }

            //应用错误记录
            if ($exception instanceof ExceptionAbstract) {
                $log = 'ServiceException: [' . $exception->getCode() . ']' . $exception->getMessage();
                $data = $exception->getData();
                if (!empty($data)) {
                    $log .= ' ' . json_encode($data);
                }
                Log::write($log,'error');
            }
        }

        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        if(!env('app_debug')){
            //路由错误或者http错误
            if ($e instanceof RouteNotFoundException || $e instanceof HttpException && $e->getCode() === 404) {
                return json(['code'=>ApiCode::NOT_FOUND,'msg'=>'404 Not found','info'=>$e->getMessage()]);
            //参数验证错误
            } else if ($e instanceof ValidateException) {
                return json(['code'=>ApiCode::INVALID_PARAMETER,'msg'=>$e->getMessage()]);
            //应用错误
            } else if ($e instanceof ExceptionAbstract) {
                return json(['code'=>$e->getServerCode(),'msg'=>$e->getMessage(),'data'=>$e->getData()],$e->getCode());
            }
            return json(['code'=>ApiCode::ERROR,'msg'=>'系统异常']);
        }else{
            return parent::render($request, $e);
        }
    }
}
