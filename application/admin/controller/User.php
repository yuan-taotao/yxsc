<?php
namespace app\admin\controller;

class User extends Base
{
	//会员首页
	public function index()
	{
		$data = input('get.');
		$list = [];
		if(!empty($data['email'])){
			$list['email'] = ['like','%'.$data['email'].'%'];
		}
		if(!empty($data['phone'])){
			$list['phone'] = ['like','%'.$data['phone'].'%'];
		}
		if(isset($data['user_level']) && $data['user_level'] != -1){
			$list['user_level'] = $data['user_level'];
		}
		if(!empty($data['nickname'])){
			$arr = model('UserInfo')->where(['nickname'=>['like','%'.$data['nickname'].'%']])->column('uid');
			$list['id'] = ['in',$arr];
		}
		//URL参数
		$params = request()->param();
		//根据条件获取用户
		$users = model('User')->getUsersByData($list);
		return $this->fetch('',[
			'users' => $users,
			'data' => $data,
			'params' => $params,
		]);
	}

	//用户批量禁止
	public function all_ban()
	{
		$data = input('post.');
		$data = $data['data'];
		$res = model('User')->doUpdate(['status'=>0],['id'=>['in',$data]]);
		if($res){
			return output(1,'批量操作成功');
		}else{
			return output(0,'批量操作失败');
		}
	}

	//会员详细信息
	public function user_info()
	{
		$data = input('get.');
		// var_dump($data);
		$userinfo = model('UserInfo')->where($data)->find();
		return $this->fetch('',[
			'userinfo' => $userinfo,
 		]);
	}
}	