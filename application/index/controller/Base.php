<?php
namespace app\index\controller;
use think\Controller;

class Base extends Controller
{
	public $user = '';
	public $userinfo = '';
	public function _initialize()
	{
		$this->getUserInfo();

		$this->assign('user',$this->user);
		$this->assign('userinfo',$this->userinfo);
		$this->assign('cartCount',$this->getCartCount());
	}

	//获取用户信息
	public function getUserInfo()
	{
		if(session('user','','yx')){
			$this->user = session('user','','yx');
			//获取会员信息
			$this->userinfo = model('UserInfo')->where(['uid'=>session('user','','yx')->id])->find();

		}
	}

	//获取购物车商品数量
	public function getCartCount(){
		if(cookie('shopcart')){
			return count(cookie('shopcart'));
		}else{
			return 0;
		}
	}
}