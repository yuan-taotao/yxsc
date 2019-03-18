<?php
namespace app\index\controller;
 
class User extends Base
{
	public function _initialize()
	{
		parent::_initialize();
		if(!$this->user){
			$this->redirect('login/index');
		}
		//自动修改签到状态
		$nowUser = model('User')->get($this->user->id);
		$nowDate = date('Ymd',time());
		$sigDate = date('Ymd',$nowUser->last_sig_time);
		if($nowDate != $sigDate){
			model('User')->doUpdate(['is_sig'=>0],['id'=>$nowUser->id]);
		}

		//根据积分修改会员等级
		if($this->user->user_points >= 50000 && $this->user->user_points < 200000){
			model('User')->doUpdate(['user_level'=>1],['id'=>$this->user->id]);
			$newUserInfo = model('User')->get($this->user->id);
			session('user',$newUserInfo,'yx');
		}elseif($this->user->user_points >= 200000 && $this->user->user_points < 500000){
			model('User')->doUpdate(['user_level'=>2],['id'=>$this->user->id]);
			$newUserInfo = model('User')->get($this->user->id);
			session('user',$newUserInfo,'yx');
		}elseif($this->user->user_points >= 500000){
			model('User')->doUpdate(['user_level'=>3],['id'=>$this->user->id]);
			$newUserInfo = model('User')->get($this->user->id);
			session('user',$newUserInfo,'yx');
		}
	}

	//会员中心首页
	public function index()
	{
		//优惠券数量
		$coupons = model('Coupon')->where(['user_id'=>$this->user->id,'status'=>1])->select();
		$orders = model('Order')->where(['uid'=>$this->user->id])->order(['create_time'=>'desc'])->limit(2)->select();
		return $this->fetch('',[
			'coupons' => $coupons,
			'orders' => $orders,
		]);
	}

	//会员信息
	public function info()
	{
		
		return $this->fetch('',[

		]);
	}

	//会员信息修改
	public function info_update()
	{
		
		$data = input('post.');
		unset($data['name']);
		$uid = $this->user->id;
		if($data['pic']){
			$res = model('UserInfo')->where(['uid'=>$uid])->update($data);
		}else{
			unset($data['pic']);
			$res = model('UserInfo')->where(['uid'=>$uid])->update($data);
		}
		if($res){
			return output(1,'会员信息修改成功');
		}else{
			return output(0,'未修改任何信息');
		}
	}

	//订单再次购买
	public function try_buyagain()
	{
		$id = input('post.id');
		$order = model('Order')->get($id);
		$staticIds = explode(',',$order->goods_id);
		$staticGoods = model('StaticGoods')->all($staticIds);
		$goodsIds = [];
		foreach ($staticGoods as $key => $val) {
			$goodsIds[] = $val->goods_id;
		}
		$goods = model('Goods')->all($goodsIds);
		foreach ($goods as $key => $val) {
			if($val->status != 1){
				return output(3,'部分商品已下架或删除');
			}
		}
		$cookieCart = [];
		foreach ($goodsIds as $key => $val) {
			$cookieCart[] = ['count'=>1,'goods_id'=>$val];
		}

		cookie('shopcart',$cookieCart);

		return output(1,'');


	}

	//订单再次购买
	public function do_buyagain()
	{
		$id = input('post.id');
		$order = model('Order')->get($id);
		$staticIds = explode(',',$order->goods_id);
		$staticGoods = model('StaticGoods')->all($staticIds);
		$goodsIds = [];
		foreach ($staticGoods as $key => $val) {
			$goodsIds[] = $val->goods_id;
		}
		$goods = model('Goods')->all($goodsIds);
		foreach ($goods as $key => $val) {
			if($val->status != 1){
				unset($goodsIds[$key]);
			}
		}
		$cookieCart = [];
		foreach ($goodsIds as $key => $val) {
			$cookieCart[] = ['count'=>1,'goods_id'=>$val];
		}

		cookie('shopcart',$cookieCart);

		return output(1,'');


	}
}