<?php
namespace app\admin\controller;

class Order extends Base
{
	//订单列表
	public function index()
	{
		$data = input('get.');
		$list = [];
		if(empty($data['start_time']) && !empty($data['end_time'])){
			$list['create_time'] = ['< time',$data['end_time']];
		}
		if(empty($data['end_time']) && !empty($data['start_time'])){
			$list['create_time'] = ['> time',$data['start_time']];
		}
		if(!empty($data['end_time']) && !empty($data['start_time']) && ($data['start_time'] < $data['end_time'])){
			$list['create_time'] = ['between time',[$data['start_time'],$data['end_time']]];
		}
		if(isset($data['order_status']) && $data['order_status'] != -1){
			$list['order_status'] = $data['order_status'];
		}
		if(!empty($data['order_number'])){
			$list['order_number'] = ['like','%'.$data['order_number'].'%'];
		}
		if(!empty($data['name'])){
			$arr = model('StaticGoods')->where(['name'=>['like','%'.$data['name'].'%']])->column('id');
			$allOrders = model('Order')->select();
			$idArr = [];
			foreach($allOrders as $k=>$v){
				$goodsArr = explode(',',$v->goods_id);
				foreach($arr as $key => $val){
					if(in_array($val,$goodsArr)){
						$idArr[] = $v->id;
						break;
					}
				}
			}
			$list['id'] = ['in',$idArr];
		}
		if(!empty($data['linkman']) || !empty($data['phone'])){
			$arr1 = [];
			$arr2 = [];
			if(!empty($data['linkman'])){
				$arr1 = model('StaticAddress')->where(['linkman'=>['like','%'.$data['linkman'].'%']])->column('id');
			}
			if(!empty($data['phone'])){
				$arr2 = model('StaticAddress')->where(['phone'=>['like','%'.$data['phone'].'%']])->column('id');
			}
			$arr = array_merge($arr1,$arr2);
			$list['address_id'] = ['in',$arr];
		}
		//URL参数
		$params = request()->param();
		//获取所有订单
		$orders = model('Order')->getOrdersByData($list);
		return $this->fetch('',[
			'orders' => $orders,
			'params' => $params,
			'data' => $data,
		]);
	}

	//订单收发货状态
	public function order_status()
	{
		$id = input('get.id',0,'intval');
		$status = input('get.order_status',0,'intval');
		$res = $this->obj->doUpdate(['order_status' => $status],['id' => $id]);
		if($res){
			return redirect($_SERVER['HTTP_REFERER']);
		}
	}

	//订单批量操作
	public function all_send()
	{
		$data = input('post.');
		$data = $data['data'];
		foreach($data as $k=>$v){
			$data[$k] = intval($v);
		}
		$orders = model('Order')->all($data);
		foreach($orders as $k=>$v){
			if($v->order_status != 1){
				return output(2,'所选订单中包含其他状态订单,请重新选择后操作');
			}
		}
		$res = model('Order')->doUpdate(['order_status'=>2],['id'=>['in',$data]]);
		if($res){
			return output(1,'操作成功');
		}else{
			return output(0,'操作失败');
		}
	}

	//显示订单xinxi 
	public function order_info()
	{
		$id = input('get.id');
		$order = model('Order')->get($id);
		$address = model('StaticAddress')->get($order->address_id);
		return $this->fetch('',[
			'order' => $order,
			'address' => $address,
		]);
	}
}