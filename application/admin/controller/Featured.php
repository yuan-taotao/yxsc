<?php
namespace app\admin\controller;

class Featured extends Base
{
	public function index()
	{
		//获取所有推荐位
		$featureds = model('Featured')->getAllFeatureds();
		return $this->fetch('',[
			'featureds' => $featureds,
		]);
	}

	//推荐添加页面
	public function add()
	{
		return $this->fetch('',[

		]);
	}

	//提交添加
	public function doadd()
	{
		$data = input('post.');
		$validate = validate('Featured');
		$res = $validate->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$res = model('Featured')->add($data);
		if($res){
			return output(1,'添加成功');
		}else{
			return output(0,'添加失败');
		}
	}

	//修改推荐位状态
	public function status()
	{
		$id = input('get.id');
		$status = input('get.status');
		$featured = $this->obj->get($id);
		$type = $featured->type;
		if($status == 1){
			if($type == 1){
				$fs = $this->obj->where(['status'=>1,'type'=>1])->select();
				if(count($fs) >= 4){
					return output(0,'主轮播图启用不能超过4个,请更改其他已启用的状态后再作更改');
				} 
			}
			if($type == 0){
				$fs = $this->obj->where(['status'=>1,'type'=>0])->select();
				if(count($fs) >= 3){
					return output(0,'副推荐位启用不能超过3个,请更改其他已启用的状态后再作更改');
				} 
			}
		}
		$res = $this->obj->doUpdate(['status' => $status],['id' => $id]);
		if($res){
			return output(1,'操作成功');
		}else{
			return output(0,'操作失败');
		}
	}

	//编辑
	public function edit()
	{
		$id = input('get.id');
		$featured = $this->obj->get($id);
		return $this->fetch('',[
			'featured' => $featured,
		]);
	}

	//信息修改
	public function update()
	{
		$data = input('post.');
		$id = input('get.');
		$validate = validate('Featured');
		$res = $validate->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$res = model('Featured')->doUpdate($data,$id);
		if($res){
			return output(1,'修改成功');
		}else{
			return output(0,'修改失败');
		}
	}
}