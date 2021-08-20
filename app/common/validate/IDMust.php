<?php
namespace app\common\validate;

class IDMust extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
    ];

    protected $message = [
        'id.require' => '必须传入id号',
    ];
}