<?php
namespace app\admin\validate;
use think\Validate;

class Featured extends Validate
{
	protected $rule = [
		['pic','require','请选择图片'],
		['title','require','请输入标题'],
		['url','require','请输入URL路径'],
	];
}