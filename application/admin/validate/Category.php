<?php
namespace app\admin\validate;
use think\Validate;

class Category extends Validate
{
	protected $rule = [
		['name','require|max:30|unique:category','请输入分类名称|分类名不能超过30个字符|该分类名已存在'],
		['pid','require|number','请输入分类级别|分类级别参数错误'],
	];

	protected $scene = [
		'update' => ['name'=>'require|max:30','pid'],
	];
}

?>