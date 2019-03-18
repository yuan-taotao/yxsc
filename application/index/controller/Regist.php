<?php
namespace app\index\controller;
use think\Controller;

class Regist extends Controller
{
	//注册页面
	public function index()
	{
		$lastUrl = empty($_SERVER['HTTP_REFERER'])? '' : $_SERVER['HTTP_REFERER'];
		return $this->fetch('',[
			'lastUrl' => $lastUrl,
		]);
	}

	//异步提交注册
	public function regist()
	{
		$data = input('post.');
		$validate = validate('Regist');
		$res = $validate->scene('email_regist')->check($data);
		if(true !== $res){
			return output(2,$validate->getError());
		}
		$data['passcode'] = mt_rand(1000,9999);
		$data['password'] = md5($data['password'].$data['passcode']);
		unset($data['repassword']);
		unset($data['captcha']);
		unset($data['agreement']);
		$uid = model('User')->add($data);
		if($uid){
			//直接登录
			$userinfo = model('User')->get($uid);
			session('user',$userinfo,'yx');
			$str = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
			$newStr = '';
			$str = str_shuffle($str);
			for ($i=0; $i < 8; $i++) { 
				$newStr .= $str{$i};
			}
			model('UserInfo')->add(['uid'=>$uid,'nickname'=>'用户'.$newStr]);
			\phpmailer\Email::send($data['email'],'【BetterBoy】恭喜您,注册成功','您已成功注册BetterBoy会员,请赶快使用您的邮箱账号登录吧！');
			return output(1,'恭喜您,注册成功！');
		}else{
			return output(0,'对不起,注册失败');
		}
	}

	//异步获取短信验证码
	public function getMsg()
	{
		$phone = input('post.phone');
		$validate = validate('Phone');
		$res = $validate->check(['phone'=>$phone]);
		if($res !== true){
			return output(2,$validate->getError());
		}

		$code_time = empty(session('code_time','','yx')) ? '' : session('code_time','','yx');
		if($code_time && (time() <= ($code_time + 120))){
			return output(3,'请稍等一会儿后再重新获取');
		}

		//发送验证码
		$msgcode = mt_rand(1000,9999);
		$Msg = new \msglogin\Msg;
		$res = $Msg->send($msgcode,120,$phone);
		$res = json_decode($res);
		if($res->code === '000000'){
			session('regist_phone',$phone,'yx');
			session('regist_code',$msgcode,'yx');
			session('code_time',time(),'yx');
			return output(1,'验证码已发送');
		}else{
			return output(0,'验证码发送失败');
		}
	}

	//异步手机注册
	public function phone_regist()
	{	
		$data = input('post.');
		$validate = validate('Regist');
		$res = $validate->scene('phone_regist')->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		if($data['msgcode'] != session('regist_code','','yx')){
			return output(3,'短信验证码错误');
		}
		$data['phone'] = session('regist_phone','','yx');
		$data['passcode'] = mt_rand(1000,9999);
		$data['password'] = md5($data['password'].$data['passcode']);
		unset($data['msgcode']);
		unset($data['repassword']);
		unset($data['agreement']);
		$uid = model('User')->add($data);
		if($uid){
			//直接登录
			$userinfo = model('User')->get($uid);
			session('user',$userinfo,'yx');
			$str = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
			$newStr = '';
			$str = str_shuffle($str);
			for ($i=0; $i < 8; $i++) { 
				$newStr .= $str{$i};
			}
			model('UserInfo')->add(['uid'=>$uid,'nickname'=>'用户'.$newStr]);
			return output(1,'恭喜您,注册成功');
		}else{
			return output(0,'对不起,注册失败');
		}
	}
}