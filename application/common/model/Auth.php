<?php
namespace app\common\model;

class Auth extends Base
{
	public function getNormalAuths($isPage = 1)
	{
		$data = [
			'status' => 1,
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];

		if($isPage){
			return $this->where($data)->order($order)->paginate(10);
		}else{
			return $this->where($data)->order($order)->select();
		}
	}
}