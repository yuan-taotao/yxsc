<?php
namespace app\common\model;

class Comment extends Base
{
	//获取常规评价
	public function getComments($goodsId,$isPage=1)
	{
		$data = [
			'status' => 1,
			'goods_id' => $goodsId,
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		if($isPage){
			return $this->where($data)->order($order)->paginate();
		}else{
			return $this->where($data)->order($order)->select();
		}
	}

	//根据等级获取评论
	public function getCommentsByLevel($goodsId,$level)
	{
		$data = [
			'status' => 1,
			'goods_id' => $goodsId,
			'level' => $level,
		];

		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];

		return $this->where($data)->order($order)->select();
	}

	//获取所有评论
	public function getCommentsByData($data = [])
	{
		$data['status'] = ['neq',-1];

		$order = [	
			'listorder' => 'desc',
			'id' => 'desc',
		];
		return $this->where($data)->order($order)->paginate(5);
	}
}