<?php
namespace app\common\model;

class Collection extends Base
{
	//获取用户收藏
	public function getCollectsByUser($uid)
	{
		$data = [
			'status' => 1,
			'user_id' => $uid,
		];

		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];

		return $this->where($data)->order($order)->paginate();
	}
}