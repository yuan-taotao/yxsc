<?php
namespace app\common\model;

class Order extends Base
{
	//获取用户所有订单
	public function getAllOrders($uid)
	{
		$data = [
			'status' => 1,
			'uid' => $uid,
		];

		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		return $this->where($data)->order($order)->select();
	}

	//获取所有订单
	public function getOrdersByData($data = [])
	{
		$data['status'] = ['neq',-1];

		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];

		return $this->where($data)->order($order)->paginate(5);
	}
}