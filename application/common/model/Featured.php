<?php
namespace app\common\model;

class Featured extends Base
{
	//获取所有推荐位
	public function getAllFeatureds()
	{
		$data = ['status'=>['neq',-1]];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		return $this->where($data)->order($order)->paginate(4);
	}

}