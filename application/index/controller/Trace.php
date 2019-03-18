<?php
namespace app\index\controller;

class Trace extends Base
{
	public function _initialize()
	{
		parent::_initialize();
		if(!$this->user){
			$this->redirect('login/index');
		}
	}
	
	//足迹列表
	public function index()
	{
		//获取商品
		$goodsIds = model('Trace')->where('user_id',$this->user->id)->column('goods_id');
		$goods = model('Goods')->all($goodsIds);
		return $this->fetch('',[
			'goods' => $goods,
		]);	
	}

	//足迹删除
	public function delete()
	{
		$gid = input('get.id');
		$data = [
			'user_id' => $this->user->id,
			'goods_id' => $gid,
		];

		$res = model('Trace')->where($data)->delete();
		if($res){
			return redirect($_SERVER['HTTP_REFERER']);
		}
	}
}