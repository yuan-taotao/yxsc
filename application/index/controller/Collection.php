<?php
namespace app\index\controller;

class Collection extends Base
{
	public function _initialize()
	{
		parent::_initialize();
		if(!$this->user){
			$this->redirect('login/index');
		}
	}
	// 收藏列表
	public function list()
	{
		//获取用户收藏
		$uid = $this->user->id;
		$goodsIds = model('Collection')->where('user_id',$uid)->column('goods_id');
		if($goodsIds){
			$goods = model('Goods')->all($goodsIds);
		}else{
			$goods = [];
		}
		return $this->fetch('',[
			'goods' => $goods,
		]);
	}

	//收藏删除
	public function delete()
	{
		$gid = input('get.id');
		$data = [
			'user_id' => $this->user->id,
			'goods_id' => $gid,
		];

		$res = model('Collection')->where($data)->delete();
		if($res){
			return redirect($_SERVER['HTTP_REFERER']);
		}
	}
}