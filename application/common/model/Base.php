<?php
namespace app\common\model;

use think\Model;

class Base extends Model
{
	//公共添加方法
	public function add($data)
	{
		$data['create_time'] = time();
		$this->allowField(true)->save($data);
		return $this->id;
	}

	//公共修改方法
	public function doUpdate($data = [],$where = [])
	{
		$data['update_time'] = time();
		return $this->where($where)->update($data);
	}
}