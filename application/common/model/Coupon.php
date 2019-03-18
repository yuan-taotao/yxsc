<?php
namespace app\common\model;


class Coupon extends Base
{

	//根据用户获取优惠券
	public function getCouponsByUser($uid)
	{
		$data = [
			'status' => 1,
			'user_id' => $uid,
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		return $this->where($data)->order($order)->select();
	}

	//根据价格获取优惠券
	public function getCouponsByPrice($uid,$price)
	{
		$data = [
			'status' => 1,
			'user_id' => $uid,
			'limit' => ['<=',$price],
		];

		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];

		return $this->where($data)->order($order)->select();
	}
}