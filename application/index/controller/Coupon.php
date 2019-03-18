<?php
namespace app\index\controller;

class Coupon extends Base
{
	public function _initialize()
	{
		parent::_initialize();
		if(!$this->user){
			$this->redirect('login/index');
		}
		$coupons = model('Coupon')->where(['status'=>1])->select();
		foreach ($coupons as $k => $v) {
			if(time() >= $v['end_time']){
				model('Coupon')->save(['status'=>3],['id'=>$v['id']]);
			}
		}
	}

	//优惠券列表
	public function index()
	{
		//获取所有优惠券
		$data = ['status'=>['neq',-1],'user_id'=>$this->user->id];
		$coupons = model('Coupon')->where($data)->select();
		return $this->fetch('',[
			'coupons' => $coupons,
		]);
	}

	//删除优惠券
	public function delete()
	{
		$id = input('get.id');
		$res = model('Coupon')->doUpdate(['status'=>-1],['id'=>$id]);
		if($res){
			return redirect($_SERVER['HTTP_REFERER']);
		}
	}
}