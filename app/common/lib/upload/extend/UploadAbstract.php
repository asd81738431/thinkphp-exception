<?php
namespace app\common\lib\upload\extend;

use think\facade\Filesystem;
use think\exception\ValidateException;

abstract class UploadAbstract{
    protected $validate; //上传验证规则
    protected $files; //上传文件
    protected $configName; //上传配置名称

    protected function file($file = null){
        if($file === null){
            throw new ValidateException('上传失败');
        }

        if(is_object($file)){
            $this->files = ['files' => $file];
        }
    }

    protected function configName($configName = null){
        $this->configName = $configName ?: 'public';
    }

    protected function upload(): array
    {
        try {
            $dir = config('filesystem.disks.' . $this->configName . '.root');
            if(!is_dir($dir)){
                mkdir($dir,0777,true);
                chmod($dir,0777);
            }
            validate(['image' => $this->validate])->check($this->files);

            $saveName = Filesystem::disk($this->configName)->putFile(   '', $this->files['files']);
            return ['code' => 1, 'url' => config('filesystem.disks.' . $this->configName . '.url') . DIRECTORY_SEPARATOR . $saveName];
        } catch (ValidateException $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
}