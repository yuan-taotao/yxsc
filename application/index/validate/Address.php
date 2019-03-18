<?php 
namespace app\index\validate;
use think\Validate;

class Address extends Validate
{
	protected $rule = [
		['linkman','require|max:50','请输入收货人|收货人名称不能超过50个字符'],
		['phone','require|length:11|regex:/^1[3-8]{1}[0-9]{9}$/','请输入手机号|请输入正确的手机号码|请输入正确的手机号码'],
		['province','require|isZero','请选择所在地|请选择所在地'],
		['addinfo','require|max:255','请输入详细地址|详细地址不能超过255个字符'],
	];

	protected function isZero($value)
	{
		if($value == 0){
			return '请选择所在地';
		}else{
			return true;
		}
	}
}