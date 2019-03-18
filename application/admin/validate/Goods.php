<?php
namespace app\admin\validate;
use think\Validate;

class Goods extends Validate
{
	protected $rule = [
		['name','require|max:100','请输入商品名|商品名称不能超过100个字符'],
		['keywords','require','请输入商品关键字'],
		['origin_price','require|number','请输入商品原价|原价请输入数字'],
		['current_price','require|number','请输入商品促销价|促销价请输入数字'],
		['store','require|number','请输入库存|库存请输入数字'],
		['pre_pic','require','请选择一张预览图片'],
		['info_pic','require','请选择一张详情图'],
	];
}