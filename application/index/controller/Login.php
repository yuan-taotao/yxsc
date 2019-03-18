<?php
namespace app\index\controller;
use think\Controller;

class Login extends Controller
{
	public function index()
	{
		$lastUrl = empty($_SERVER['HTTP_REFERER'])? '' : $_SERVER['HTTP_REFERER'];
		return $this->fetch('',[
			'lastUrl' => $lastUrl,
		]);
	}

	//用户登录
	public function login()
	{
		$data = input('post.');
		$validate = validate('Login');
		$res = $validate->check($data);
		if(true !== $res){
			return output(2,$validate->getError());
		}
		$user = model('User')->where(['email'=>$data['username']])->whereOr(['phone'=>$data['username']])->find();
		if(!$user){
			return output(0,'对不起,该用户不存在');
		}
		if($user->status != 1){
			return output(0,'对不起,该用户被禁止登陆,请联系客服了解详情');
		}
		$code = $user->passcode;
		if(md5($data['password'].$code) != $user->password){
			return output(0,'对不起,密码错误');
		}
		session('user',$user,'yx');

		return output(1,'登录成功');
	}

	//退出登录
	public function logout()
	{
		session('user',null,'yx');
		return redirect('/');
	}

	//找回密码
	public function emailgetback()
	{
		return $this->fetch('',[

		]);
	}
	public function phonegetback()
	{
		return $this->fetch('',[

		]);
	}

	//找回密码邮件验证码获取
	//获取邮件验证码
	public function getEmailCode()
	{
		$code_time = empty(session('getback_emailcode_time','','yx')) ? '' : session('getback_emailcode_time','','yx');
		if($code_time && (time() <= ($code_time + 120))){
			return output(3,'请稍等一会儿后再重新获取');
		}
		$data = input('post.');
		$validate = validate('Regist');
		$res = $validate->scene('getback_email_code')->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$res = model('User')->where(['email'=>$data['email']])->find();
		if(!$res){
			return output(2,'对不起,该账号不存在');
		}
		$title = '【BetterBoy找回密码】';
		$code = mt_rand(1000,9999);
		$content = '您正在进行密码找回操作,邮箱验证码为['.$code.'],请在120内完成验证';
		$res = \phpmailer\Email::send($data['email'],$title,$content);
		if($res){
			session('getback_email',$data['email'],'yx');
			session('getback_email_code',$code,'yx');
			session('getback_emailcode_time',time(),'yx');
			return output(1,'验证码已发送');
		}else{
			return output(0,'验证码发送失败');
		}
	}

	//邮件找回密码
	public function emailSetPassword()
	{
		$data = input('post.');
		$validate = validate('Regist');
		$res = $validate->scene('getback_email')->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		if($data['emailcode'] != session('getback_email_code','','yx')){
			return output(2,'邮箱验证码错误');
		}


		$email = session('getback_email','','yx');
		$code = mt_rand(1000,9999);
		$password = md5($data['password'].$code);
		$res = model('User')->doUpdate(['passcode'=>$code,'password'=>$password],['email'=>$email]);
		if($res){
			return output(1,'新密码设置成功');
		}else{
			return output(0,'密码找回失败');
		}

	}

	//获取短信验证码
	public function getPhoneCode()
	{
		$phone = input('post.phone');
		$validate = validate('Phone');
		$res = $validate->scene('getback_phonecode')->check(['phone'=>$phone]);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$res = model('User')->where(['phone'=>$phone])->find();
		if(!$res){
			return output(2,'对不起,该账号不存在');
		}
		$code_time = empty(session('getback_phonecode_time','','yx')) ? '' : session('getback_phonecode_time','','yx');
		if($code_time && (time() <= ($code_time + 120))){
			return output(3,'请稍等一会儿后再重新获取');
		}

		//发送验证码
		$msgcode = mt_rand(1000,9999);
		$Msg = new \msglogin\Msg;
		$res = $Msg->send($msgcode,120,$phone);
		$res = json_decode($res);
		if($res->code === '000000'){
			session('getback_phone',$phone,'yx');
			session('getback_phonecode',$msgcode,'yx');
			session('getback_phonecode_time',time(),'yx');
			return output(1,'验证码已发送');
		}else{
			return output(0,'验证码发送失败');
		}
	}


	//手机找回密码
	public function phoneSetPassword()
	{
		$data = input('post.');
		$validate = validate('Regist');
		$res = $validate->scene('getback_phone')->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		if($data['msgcode'] != session('getback_phonecode','','yx')){
			return output(2,'手机验证码错误');
		}


		$phone = session('getback_phone','','yx');
		$code = mt_rand(1000,9999);
		$password = md5($data['password'].$code);
		$res = model('User')->doUpdate(['passcode'=>$code,'password'=>$password],['phone'=>$phone]);
		if($res){
			return output(1,'新密码设置成功');
		}else{
			return output(0,'密码找回失败');
		}

	}
	
}