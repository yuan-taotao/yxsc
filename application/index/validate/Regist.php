<?php 
namespace app\index\validate;
use think\Validate;

class Regist extends Validate
{
	protected $rule = [
		['email','require|email|unique:user','请输入邮箱账号|请输入正确的邮箱格式|该邮箱已注册,请直接登录'],
		['phone','require|length:11|regex:/^1[3-8]{1}[0-9]{9}$/|unique:user','请输入手机号|请输入正确的手机号码|请输入正确的手机号码|该手机号已经注册'],
		['msgcode','require|number|length:4','请输入短信验证码|验证码格式错误|验证码格式错误'],
		['old_msgcode','require|number|length:4','请输入短信验证码|验证码格式错误|验证码格式错误'],
		['new_msgcode','require|number|length:4','请输入短信验证码|验证码格式错误|验证码格式错误'],
		['emailcode','require|number|length:4','请输入短信验证码|验证码格式错误|验证码格式错误'],
		['password','require|alphaNum|max:16','请输入密码|密码只能由英文或数字组成|密码不能超过16个字符'],
		['repassword','require|confirm:password','请再次输入密码|两次密码输入不一致'],
		['agreement','require|isAgree'],
		['captcha','require|captcha','请输入验证码|验证码错误'],
	];

	protected $scene = [
		'email_regist' => ['email','password','repassword','captcha','agreement'],
		'phone_regist' => ['phone','msgcode','password','repassword','agreement'],
		'update_password' => ['password','repassword'],
		'phone_update' => ['phone','old_msgcode','new_msgcode'],
		'phone_add' => ['phone','new_msgcode'],
		'email_code' => ['email'],
		'email_update' => ['email','emailcode'],
		'getback_email_code' => ['email'=>'require|email'],
		'getback_email' => ['email'=>'require|email','emailcode','password','repassword'],
		'getback_phone' => ['phone'=>'require|length:11|regex:/^1[3-8]{1}[0-9]{9}$/','msgcode','password','repassword'],
	];

	//自定义验证是否同意协议
	protected function isAgree($value)
	{
		if('true' === $value){
			return true;
		}else{
			return '请阅读并同意协议';
		}
	}
}

?>