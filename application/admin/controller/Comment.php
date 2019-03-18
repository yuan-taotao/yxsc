<?php
namespace app\admin\controller;

class Comment extends Base
{
	public function index()
	{
		$data = input('get.');
		$list = [];
		if(!empty($data['name'])){
			$arr = model('Goods')->where(['name'=>['like','%'.$data['name'].'%']])->column('id');
			$list['goods_id'] = ['in',$arr];
		}
		if(isset($data['level']) && $data['level'] != -1){
			$list['level'] = $data['level'];
		}
		//获取所有评论
		$comments = model('Comment')->getCommentsByData($list);
		//URL参数
		$params = request()->param();
		return $this->fetch('',[
			'comments' => $comments,
			'params' => $params,
			'name' => isset($data['name']) ? $data['name'] : '',
			'level' => isset($data['level']) ? $data['level'] : '',
		]);
	}
}