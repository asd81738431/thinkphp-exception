<?php
namespace app\common\validate;


#use app\common\exception\ValidateException;
use think\exception\ValidateException;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     * 参数检查公共调用方法
     */
    public function goCheck(){
        if(!$this->check(request()->param())){
            throw new ValidateException($this->getError());
        }else{
            return true;
        }
    }

    /**
     * @param array $arrays 通常传入request.param()变量数组
     * @return array 按照规则key过滤后的变量数组
     */
    public function getDataByRule($array){
        //这里还可以过滤一些一定不能传的参数
        $newArray = array();
        foreach ($this->rule as $key=>$value){
            $newArray[$key] = $array[$key];
        }

        return $newArray;
    }

    /**
     * 适用于场景验证获取参数
     * @param array $arrays 通常传入request.param()变量数组
     * @param string $senceName 传入验证场景的名称
     * @return array 按照规则key过滤后的变量数组
     */
    public function getDataBySceneRule($array,$senceName){
        //这里还可以过滤一些一定不能传的参数
        $newArray = array();
        foreach ($this->scene[$senceName] as $key=>$value){
            $newArray[$value] = $array[$value];
        }

        return $newArray;
    }
}