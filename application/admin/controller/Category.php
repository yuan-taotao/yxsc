<?php
namespace app\admin\controller;

class Category extends Base
{
	//分类列表页
	public function list()
	{
		//获取所有分类
		$cates = $this->obj->getAllCates();
		$count = count($cates);
		return $this->fetch('',[
			'cates' => $cates,
			'count' => $count,
		]);
	}

	//分类添加页
	public function add()
	{
		//获取一级分类
		$cates = $this->obj->getCatesByPid();
		return $this->fetch('',[
			'cates' => $cates,
		]);
	}

	//ajax提交添加
	public function doadd()
	{
		$data = input('post.');
		$output = $this->addCheck($data);
		return $output;
	}

	//分类编辑页
	public function edit()
	{
		$id = input('get.id',0,'intval');
		if(!$id){
			$this->error('参数错误');
		}
		//获取当前分类
		$cate = $this->obj->get($id);
		//获取一级分类
		$cates = $this->obj->getCatesByPid();
		return $this->fetch('',[
			'cate' => $cate,
			'cates' => $cates,
		]);
	}

	//ajax提交分类修改
	public function update()
	{
		$data = input('post.');
		$id = $data['id'];
		unset($data['id']);
		$output = $this->updateCheck($data,['id' => $id]);
		return $output;
	}

	//分类状态修改
	public function status()
	{
		$id = input('get.id',0,'intval');
		$status = input('get.status',0,'intval');
		if(!$id){
			$this->error('参数错误');
		}
		$cate = $this->obj->get($id);
		if($cate->pid == 0){
			$this->obj->doUpdate(['status' => $status],['pid' => $id]);
		}
		$this->obj->doUpdate(['status' => $status],['id' => $id]);
		return redirect($_SERVER['HTTP_REFERER']);

	}

	//ajax提交ajax删除方法
	public function delete()
	{
		$id = input('post.id',0,'intval');
		if(!$id){
			$this->error('参数错误');
		}
		$cate = $this->obj->get($id);
		if($cate->pid == 0){
			$this->obj->doUpdate(['status' => -1],['pid' => $id]);
		}
		$res = $this->obj->doUpdate(['status' => -1],['id' => $id]);
		if($res){
			$output = output(1,'操作成功');
		}else{
			$output = output(0,'操作失败');
		}
		return $output;
	}

	//ajax分类排序
	public function listorder()
	{
		$id = input('post.id',0);
		if(!$id){
			$this-error('参数错误');
		}
		$listorder = input('post.listorder',0);
		$listorder = intval($listorder);
		$res = $this->obj->doUpdate(['listorder' => $listorder],['id' => $id]);
		if($res){
			$output = output(1,'操作成功');
		}else{
			$output = output(0,'操作失败');
		}
		return $output;
	}
}