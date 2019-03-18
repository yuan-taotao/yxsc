<?php
namespace app\admin\validate;
use think\Validate;

class Role extends Validate
{
	protected $rule = [
		['name','require|unique:role','请输入角色名|该角色名已经存在'],
	];

	protected $scene = [
		'update' => ['name'=>'require'],
	];
}