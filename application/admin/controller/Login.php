<?php
namespace app\admin\controller;
use think\Controller;
class Login extends Controller
{
	//登录页
	public function index()
	{
		return $this->fetch('',[

		]);
	}

	//登录
	public function login()
	{
		$data = input('post.');
		$validate = validate('Login');
		$res = $validate->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$admin = model('Admin')->where(['account'=>$data['account']])->find();
		if(!$admin){
			return output(0,'该账号不存在或已删除');
		}
		if($admin->status != 1){
			return output(0,'该账号已被禁用或已删除');
		}
		$code = $admin->passcode;
		if(md5($data['password'].$code) != $admin->password){
			return output(0,'密码错误');
		}
		$ip = request()->ip();
		$loginData = [
			'last_login_time' => time(),
			'last_login_ip' => ip2long($ip),
		];
		$res = model('Admin')->save($loginData,['id'=>$admin->id]);
		if($res){
			session('admin',$admin,'yx_admin');
			return output(1,'登录成功');
		}else{
			return output(0,'登录失败');
		}
	}

	//退出登录
	public function logout()
	{
		session('admin',null,'yx_admin');
		$this->redirect('login/index');
	}
}