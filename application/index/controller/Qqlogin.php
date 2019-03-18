<?php
namespace app\index\controller;
use think\Controller;

class Qqlogin extends Base
{
	//回调地址
	public function callback()
	{
		$data = input();
		$Oauth = new \qqlogin\Oauth;
		$access_token = $Oauth->qq_callback($data);
		$openid = $Oauth->get_openid();
		$QC = new \qqlogin\QC($access_token,$openid);
		$user_info = $QC->get_user_info();
		$res = model('User')->where(['openid'=>$openid])->find();
		if(!$res){
			$uid = model('User')->add(['openid'=>$openid]);
			if($uid){
				$user = model('User')->get($uid);
				model('UserInfo')->add(['uid'=>$uid,'nickname'=>$user_info['nickname'],'pic'=>$user_info['figureurl_qq_2']]);
				session('user',$user,'yx');
				return redirect('/');
			}
		}else{
			if($res->status != 1){
				$this->error('对不起,该QQ被禁止登陆,请联系客服了解详情');
			}
			session('user',$res,'yx');
			return redirect('/');
		}
		

  	}

	//登录地址
	public function login()
	{
		$Oauth = new \qqlogin\Oauth;
		return redirect($Oauth->qq_login());
	}
 
}