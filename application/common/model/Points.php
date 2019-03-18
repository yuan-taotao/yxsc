<?php
namespace app\common\model;

class Points extends Base
{
	//获取用户积分
	public function getPointsByUser($uid)
	{
		$data = [
			'status' => 1,
			'user_id' => $uid,
		];

		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];

		return $this->where($data)->order($order)->paginate(5);
	}
}