<?php
namespace app\common\model;

class Category extends Base
{
	//根据pid获取分类
	public function getCatesByPid($pid = 0)
	{
		$data = [
			'status' => 1,
			'pid' => $pid,
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];

		return $this->where($data)->order($order)->select();
	}

	//获取所有分类
	public function getAllCates()
	{
		$data = [
			'status' => ['neq',-1],
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		
		return $this->where($data)->order($order)->select();
	}

	//获取所有可用分类
	public function getNormalCates()
	{
		$data = [
			'status' => 1,
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		return $this->where($data)->order($order)->select();
	}
}