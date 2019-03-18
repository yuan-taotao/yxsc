<?php
namespace app\common\model;

class Admin extends Base
{
	//获取所有管理员
	public function getAllAdmins($isPage = 1)
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
}