<?php
namespace app\admin\controller;

class Picture extends Base
{
	//图片列表页
	public function list()
	{
		$info = [];
		$data = [];
		//获取所有图片
		if(request()->isPost()){
			$data = input('post.');
			// var_dump($data);exit;
			//是否输入时间
			if(empty($data['start_time']) && !empty($data['end_time'])){
				$info['create_time'] = ['< time',$data['end_time']];
			}
			if(empty($data['end_time']) && !empty($data['start_time'])){
				$info['create_time'] = ['> time',$data['start_time']];
			}
			if(!empty($data['end_time']) && !empty($data['start_time']) && ($data['start_time'] < $data['end_time'])){
				$info['create_time'] = ['between time',[$data['start_time'],$data['end_time']]];
			}
			if(!empty($data['name'])){
				$info['name'] = ['like','%'.$data['name'].'%'];
			}
		}
		$pics = $this->obj->getPicsByData($info);
		$count = count($pics);
		return $this->fetch('',[
			'pics' => $pics,
			'count' => $count,
			'start_time' => empty($data['start_time']) ? '' : $data['start_time'],
			'end_time' => empty($data['end_time']) ? '' : $data['end_time'],
			'name' => empty($data['name']) ? '' : $data['name'],
		]);
	}

	//图片添加页
	public function add()
	{
		return $this->fetch('',[

		]);
	}

	//ajax提交上传图片
	public function doadd()
	{
		$data = input('post.');
		$insert = [];
		$arr = $data['pic'];
		$name = $data['name'];
		$type = $data['type'];
		foreach ($arr as $key => $value) {
			$insert[] = ['pic_path'=>$value,'name'=>$name,'create_time'=>time(),'type'=>$type];
		}
		$res = $this->obj->saveAll($insert);
		if($res){
			return output(1,'图片添加成功');
		}else{
			return output(0,'图片添加失败');
		}
	}

	//点击显示大图
	public function show()
	{
		$data = input('get.pic_path');
		return '<img src="'.$data.'" />';
		return $data;
		return $this->fetch('',[

		]);
	}

	//点击选择图片
	public function choosePics()
	{
		$info = [];
		$data = [];
		$info ['type'] = input('get.type');
		//获取图片
		//获取所有图片
		if(request()->isPost()){
			$data = input('post.');
			$info['type'] = $data['type'];
			// var_dump($data);exit;
			//是否输入时间
			if(empty($data['start_time']) && !empty($data['end_time'])){
				$info['create_time'] = ['< time',$data['end_time']];
			}
			if(empty($data['end_time']) && !empty($data['start_time'])){
				$info['create_time'] = ['> time',$data['start_time']];
			}
			if(!empty($data['end_time']) && !empty($data['start_time']) && ($data['start_time'] < $data['end_time'])){
				$info['create_time'] = ['between time',[$data['start_time'],$data['end_time']]];
			}
			if(!empty($data['name'])){
				$info['name'] = ['like','%'.$data['name'].'%'];
			}
		}
		$pics = $this->obj->getPicsByData($info);
		$count = count($pics);
		//URL参数
		$params = request()->param();
		return $this->fetch('',[
			'type' => $info['type'],
			'pics' => $pics,
			'count' => $count,
			'start_time' => empty($data['start_time']) ? '' : $data['start_time'],
			'end_time' => empty($data['end_time']) ? '' : $data['end_time'],
			'name' => empty($data['name']) ? '' : $data['name'],
			'params' => $params,
		]);
	}

	//图片选中异步接收
	public function picChoose()
	{
		$arr = [];
		$data = input('post.');
		$datas = $this->obj->where('id','in',$data['data'])->select();
		foreach($datas as $k=>$v){
			$arr[$v['id']] = $v['pic_path'];
		}

		return $arr;
	}
	//根据商品显示所有图片
	public function getPicsByGoods()
	{
		$data = input('post.');
		$picIds = [];
		$res = model('GoodsPic')->where($data)->select();
		foreach($res as $k=>$v){
			$picIds[] = $v['pic_id'];
		}
		$pics = $this->obj->all($picIds);
		return $pics;
	}

	//图片删除(包括商品-图片表信息)
	public function delete()
	{
		$id = input('post.id');
		if(!$id){
			$this->error('参数错误');
		}
		$Picture = model('Picture');
		$GoodsPic = model('GoodsPic');
		$res = $Picture->doUpdate(['status' => -1],['id' => $id]);
		$res2 = $GoodsPic->where(['pic_id'=>$id])->select();		
		if($res){
			if(!$res2){
				return output(1,'图片删除成功');
			}else{
				$result = $GoodsPic->where(['pic_id'=>$id])->delete();
				if($result){
					return output(1,'图片删除成功');
				}else{
					return output(0,'图片删除失败');
				}
			}
		}else{
			return output(0,'图片删除失败');
		}
	}
}