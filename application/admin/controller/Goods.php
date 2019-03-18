<?php
namespace app\admin\controller;

class Goods extends Base
{
	//商品列表页
	public function list()
	{
		//获取所有商品
		
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
			if(!empty($data['keywords'])){
				$info['keywords'] = ['like','%'.$data['keywords'].'%'];
			}
		}
		$goods = $this->obj->getGoodsByData($info);
		$count = count($goods);
		//URL参数
		$params = request()->param();
		return $this->fetch('',[
			'goods' => $goods,
			'count' => $count,
			'start_time' => empty($data['start_time']) ? '' : $data['start_time'],
			'end_time' => empty($data['end_time']) ? '' : $data['end_time'],
			'name' => empty($data['name']) ? '' : $data['name'],
			'keywords' => empty($data['keywords']) ? '' : $data['keywords'],
			'params' => $params,
		]);
	}

	//商品添加页
	public function add()
	{
		//获取一级分类
		$parentCates = model('Category')->getCatesByPid();
		return $this->fetch('',[
			'parentCates' => $parentCates,
		]);
	}
	//异步获取子分类
	public function getChildCates()
	{
		$pid = input('post.id');
		$cates = model('Category')->getCatesByPid($pid);

		return $cates;
	}

	//异步添加商品
	public function doadd()
	{
		$data = input('post.');
		if($data['cate_pid'] == 0 || $data['cate_id'] == 0){
			return output(0,'请选择一级分类或者子分类');
		}
		$validate = validate('Goods');
		$res = $validate->check($data);
		if(true !== $res){
			return output(2,$validate->getError());

		}
		$pre_pics = $data['pre_pic'];
		unset($data['pre_pic']);
		$info_pics = $data['info_pic'];
		unset($data['info_pic']);
		$Goods = model('Goods');
		$GoodsPic = model('GoodsPic');
		$GoodsPic->startTrans();
		$Goods->startTrans();
		$goodsId = $Goods->add($data);
		$arr = [];
		if($goodsId){
			foreach ($pre_pics as $k => $v) {
				$arr[] = ['goods_id'=>$goodsId,'pic_id'=>$v,'create_time'=>time(),'pic_type'=>1];
			}
			foreach($info_pics as $key => $val){
				$arr[] = ['goods_id'=>$goodsId,'pic_id'=>$val,'create_time'=>time(),'pic_type'=>0];
			}
			$res = $GoodsPic->saveAll($arr);
			if($res){
				$GoodsPic->commit();
				$Goods->commit();
				return output(1,'添加商品成功');
			}else{
				$GoodsPic->rollBack();
				$Goods->rollBack();
				return output(0,'添加商品失败');
			}
		}else{
			$Goods->rollBack();
			return output(0,'添加商品失败');
		}
	}

	//商品信息修改
	public function edit()
	{
		$id = input('get.id');
		if(!$id){
			$this->error('参数错误');
		}
		//获取商品图片信息
		$pre_pic_ids = model('GoodsPic')->where(['goods_id'=>$id,'pic_type'=>1])->column('pic_id');
		$info_pic_ids = model('GoodsPic')->where(['goods_id'=>$id,'pic_type'=>0])->column('pic_id');
		$pre_pics = model('Picture')->all($pre_pic_ids);
		$info_pics = model('Picture')->all($info_pic_ids);
		//获取商品信息
		$goods = $this->obj->get($id);
		$cate_pid = $goods->cate_pid;
		//获取一级分类
		$parentCates = model('Category')->getCatesByPid();
		//获取子级分类
		$childCates = model('Category')->getCatesByPid($cate_pid);
		return $this->fetch('',[
			'parentCates' => $parentCates,
			'goods' => $goods,
			'childCates' => $childCates,
			'pre_pic_ids' => $pre_pic_ids,
			'info_pic_ids' => $info_pic_ids,
			'pre_pics' => $pre_pics,
			'info_pics' => $info_pics,
		]);
	}


	//异步修改商品
	public function update()
	{
		$data = input('post.');
		if($data['cate_pid'] == 0 || $data['cate_id'] == 0){
			return output(0,'请选择一级分类或者子分类');
		}
		$validate = validate('Goods');
		$res = $validate->check($data);
		if(true !== $res){
			return output(2,$validate->getError());

		}
		$pre_pics = $data['pre_pic'];
		unset($data['pre_pic']);
		$info_pics = $data['info_pic'];
		unset($data['info_pic']);
		$id = $data['id'];
		unset($data['id']);
		$Goods = model('Goods');
		$GoodsPic = model('GoodsPic');
		$arr = [];
		$GoodsPic->startTrans();
		$Goods->startTrans();
		$res = $Goods->doUpdate($data,['id'=>$id]);
		if($res){
			$result = $GoodsPic->where(['goods_id'=>$id])->delete();
			if($result){
				foreach ($pre_pics as $k => $v) {
				$arr[] = ['goods_id'=>$id,'pic_id'=>$v,'create_time'=>time(),'pic_type'=>1];
				}
				foreach($info_pics as $key => $val){
					$arr[] = ['goods_id'=>$id,'pic_id'=>$val,'create_time'=>time(),'pic_type'=>0];
				}
				$res2 = $GoodsPic->saveAll($arr);
				if($res2){
					$Goods->commit();
					$GoodsPic->commit();
					return output(1,'修改商品成功');
				}else{
					$Goods->rollBack();
					$GoodsPic->rollBack();
					return output(0,'修改商品失败');
				}
			}else{
				$Goods->rollBack();
				$GoodsPic->rollBack();
				return output(0,'修改商品失败');
			}
		}else{
			$Goods->rollBack();
			return output(0,'修改商品失败');
		}

	}

}