<?php
namespace app\admin\validate;
use think\Validate;

class Login extends Validate
{
	protected $rule = [
		['account','require','请输入账号'],
		['password','require','请输入密码 '],
	];
}