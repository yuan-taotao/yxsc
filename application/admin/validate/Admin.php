<?php
namespace app\admin\validate;
use think\Validate;

class Admin extends Validate
{
	protected $rule = [
		['account','require|alphaNum|max:16|unique:admin','请输入管理员账号|账号只能由英文或数字组成|账号不能超过16个字符|该账号已使用'],
		['password','require|alphaNum|max:16','请输入密码|密码只能由英文或数字组成|密码不能超过16个字符'],
		['repassword','require|confirm:password','请再次输入密码|两次密码输入不一致'],
	];

	protected $scene = [
		'update' => ['account'=>'require|alphaNum|max:16','password'=>'alphaNum|max:16'],
	];
}