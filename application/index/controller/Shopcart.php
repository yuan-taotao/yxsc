<?php
namespace app\index\controller;

class Shopcart extends Base
{
	//添加到购物车
	public function add()
	{
		$count = input('post.count');
		$id = input('post.id');
		$goods = model('Goods')->get($id);
		if($count > $goods->store){
			return output(4,'对不起,加入数量超过商品实时库存');
		}

		if(is_array(cookie('shopcart'))){
			$cookieCart = cookie('shopcart');
		}else{
			$cookieCart = [];
		}
		foreach($cookieCart as $k=>$v){
			if($v['goods_id'] == $id){
				$count = $count + $v['count'];
				$cookieCart[$k] = ['count'=>$count,'goods_id'=>$id];
				cookie('shopcart',$cookieCart);
				return output(1,'添加购物车成功');
			}
		}
		$cookieCart[] = ['count'=>$count,'goods_id'=>$id];
		cookie('shopcart',$cookieCart);
		return output(1,'添加购物车成功');
	}

	//购物车列表页
	public function list()
	{	
		$goods = [];
		$total_price = 0;
		if(cookie('shopcart')){
			foreach (cookie('shopcart') as $k => $val) {
				$goods[$k]['goods'] = model('Goods')->get($val['goods_id'])->toArray();
				$goods[$k]['count'] = $val['count'];
				$goods[$k]['price'] = number_format($val['count'] * model('Goods')->get($val['goods_id'])->current_price,2);
				$total_price += number_format($val['count'] * model('Goods')->get($val['goods_id'])->current_price,2);
			}
		}
		$goodsCount = count($goods);
		return $this->fetch('',[
			'goods' => $goods,
			'total_price' => $total_price,
			'goodsCount' => $goodsCount,
		]);
	}

	//添加到收藏
	public function collect()
	{
		$key = input('post.key');
		if(!session('user','','yx')){
			return output(3,'请登录后添加收藏');
		}
		$data = [];
		$cookieCart = cookie('shopcart');
		$data['goods_id'] = $cookieCart[$key]['goods_id'];
		$data['user_id'] = $this->user->id;
		$collect = model('Collection')->where($data)->find();
		if(count($collect) > 0){
			return output(0,'该商品已经加入收藏夹');
		}
		$res = model('Collection')->add($data);
		if($res){
			unset($cookieCart[$key]);
			cookie('shopcart',$cookieCart);
			return output(1,'收藏成功');
		}else{
			return output(0,'收藏失败');
		}

	}

	//单个删除
	public function delete()
	{
		$key = input('post.key');
		$cookieCart = cookie('shopcart');
		unset($cookieCart[$key]);
		cookie('shopcart',$cookieCart);
		return output(1,'');
	}

	//多个删除
	public function delete_all()
	{
		$data = input('post.');
		$keys = $data['items'];
		$cookieCart = cookie('shopcart');
		foreach($keys as $k=>$v){
			unset($cookieCart[$v]);
		}
		cookie('shopcart',$cookieCart);
		return output(1,'删除成功');
	}

	//多个收藏
	public function collect_all()
	{
		if(!session('user','','yx')){
			return output(3,'请登录后添加收藏');
		}
		$data = input('post.');
		$keys = $data['items'];
		$list = [];
		$cookieCart = cookie('shopcart');
		$uid = $this->user->id;
		foreach($keys as $k=>$v){
			$list[] = ['goods_id'=>$cookieCart[$v]['goods_id'],'user_id'=>$uid,'create_time'=>time()];
		}

		$res = model('Collection')->saveAll($list);
		if($res){
			foreach($keys as $k=>$v){
				unset($cookieCart[$v]);
			}
			cookie('shopcart',$cookieCart);
			return output(1,'移入收藏夹成功');
		}else{
			return output(0,'收藏失败');
		}

	}

	//提交购物车到订单
	public function cartToOrder()
	{
		if(!session('user','','yx')){
			return output(3,'请登录');
		}
		$data = input('post.');
		$list = [];
		foreach($data['goodsIds'] as $k=>$v){
			$list[] = ['goods_id'=>$v,'count'=>$data['count'][$k]];
		}
		cookie('cartdata',$list);
		return output(1,'');
	} 
}