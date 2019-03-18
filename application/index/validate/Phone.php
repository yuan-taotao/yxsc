<?php 
namespace app\index\validate;
use think\Validate;

class Phone extends Validate
{
	protected $rule = [
		['phone','require|length:11|regex:/^1[3-8]{1}[0-9]{9}$/|unique:user','请输入手机号|请输入正确的手机号码|请输入正确的手机号码|该手机号已经注册'],
	];

	protected $scene = [
		'getback_phonecode' => ['phone'=>'require|length:11|regex:/^1[3-8]{1}[0-9]{9}$/'],
	];
}