<?php
namespace app\common\model;

class Address extends Base
{
	//根据用户获取地址
	public function getAddressByUser($uid)
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

}