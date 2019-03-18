<?php
namespace app\common\model;

class User extends Base
{
	//根据条件获取用户
	public function getUsersByData($data = [],$isPage = 1)
	{	
		$data['status'] = ['neq',-1];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		if($isPage){
			return $this->where($data)->order($order)->paginate(5);
		}else{
			return $this->where($data)->order($order)->select();
		}
	}
}