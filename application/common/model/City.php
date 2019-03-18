<?php
namespace app\common\model;
use think\Model;

class City extends Model
{
	//根据父ID获取城市
	public function getCitysByPid($pid = 0)
	{
		$data = [
			'status' => 1,
			'area_pid' => $pid,
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'asc',
		];

		return $this->where($data)->order($order)->select();
	}
}