<?php
namespace app\common\model;

class Picture extends Base
{
	//获取所有图片
	public function getAllPics()
	{
		$data = [
			'status' => 1,
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		return $this->where($data)->order($order)->paginate();
	}

	//根据条件获取图片
	public function getPicsByData($data,$isPage=1)
	{
		$data['status'] = 1;
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