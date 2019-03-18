<?php
namespace app\admin\validate;
use think\Validate;

class Auth extends Validate
{
	protected $rule = [
		['name','require','请输入权限名称'],
		['controller','require','请输入控制器'],
		['method','require','请输入方法'],
		['rule','unique:auth','该规则已经存在'],
	];
}