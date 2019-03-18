<?php
namespace app\common\model;


class Role extends Base
{
	//获取所有角色
	public function getAllRoles($isPage = 1)
	{
		$data = [
			'status' => ['neq',-1],
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		if($isPage){
			return $this->where($data)->order($order)->paginate(8);
		}else{
			return $this->where($data)->order($order)->select();
		}
	}

	//获取正常角色
	public function getNormalRoles($isPage = 1)
	{
		$data = [
			'status' => 1,
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		if($isPage){
			return $this->where($data)->order($order)->paginate(8);
		}else{
			return $this->where($data)->order($order)->select();
		}
	}
}