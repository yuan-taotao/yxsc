<?php 
namespace app\index\validate;
use think\Validate;

class Login extends Validate
{
	protected $rule = [
		['username','require','请输入您的账号'],
		['password','require','请输入您的密码'],
	];
}