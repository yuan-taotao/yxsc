<?php
namespace app\index\controller;

class Order extends Base
{
	public function _initialize()
	{
		parent::_initialize();
		if(!$this->user){
			$this->redirect('login/index');
		}

		//根据积分修改会员等级
		if($this->user->user_points >= 50000 && $this->user->user_points < 200000){
			model('User')->doUpdate(['user_level'=>1],['id'=>$this->user->id]);
			$newUserInfo = model('User')->get($this->user->id);
			session('user',$newUserInfo,'yx');
		}elseif($this->user->user_points >= 200000 && $this->user->user_points < 500000){
			model('User')->doUpdate(['user_level'=>2],['id'=>$this->user->id]);
			$newUserInfo = model('User')->get($this->user->id);
			session('user',$newUserInfo,'yx');
		}elseif($this->user->user_points >= 500000){
			model('User')->doUpdate(['user_level'=>3],['id'=>$this->user->id]);
			$newUserInfo = model('User')->get($this->user->id);
			session('user',$newUserInfo,'yx');
		}
	}

	// 创建订单页面
	public function mkorder()
	{	
		if(!cookie('cartdata')){
			$this->redirect('goods/list');
		}
		$data = cookie('cartdata');
		$goods = [];
		$total_price = 0;
		if($data){
			foreach ($data as $k => $val) {
				$goods[$k]['goods'] = model('Goods')->get($val['goods_id'])->toArray();
				$goods[$k]['count'] = $val['count'];
				$goods[$k]['price'] = number_format($val['count'] * model('Goods')->get($val['goods_id'])->current_price,2);
				$total_price += number_format($val['count'] * model('Goods')->get($val['goods_id'])->current_price,2);
			}
		}
		//获取用户地址
		$uid = $this->user->id;
		$address = model('Address')->getAddressByUser($uid);
		//折后价
		switch($this->user->user_level){
			case 1:
				$discount_price = number_format($total_price * 0.95,2);
				break;
			case 2:
				$discount_price = number_format($total_price * 0.88,2);
				break;
			case 3:
				$discount_price = number_format($total_price * 0.8,2);
				break;
			default:
				$discount_price = $total_price;
		}
		//根据订单价格获取优惠券
		$coupons = model('Coupon')->getCouponsByPrice($uid,$discount_price);
		//获取默认地址
		$default_add = model('Address')->where(['is_default'=>1,'uid'=>$this->user->id])->find();
		//获取一级城市
		$provinces = model('City')->getCitysByPid();
		return $this->fetch('',[
			'address' =>$address,
			'goods' => $goods,
			'total_price' => $total_price,
			'coupons' => $coupons,
			'discount_price' => $discount_price,
			'default_add' => $default_add,
			'provinces' => $provinces,
		]);
	}

	//地址选择
	public function address_check()
	{
		$address_id = input('post.address_id');
		$address = model('Address')->get($address_id);
		$address = $address->toArray();
		$address['province'] = getCityName($address['province']);
		$address['city'] = getCityName($address['city']);
		$address['area'] = getCityName($address['area']);
		return $address;
	}

	//地址编辑
	public function address_edit()
	{
		$id = input('get.id');
		//获取地址
		$address = model('Address')->get($id);
		//获取城市
		$provinces = model('City')->getCitysByPid();
		$citys = model('City')->getCitysByPid($address->province);
		$areas = model('City')->getCitysByPid($address->city);
		return $this->fetch('',[
			'provinces' => $provinces,
			'citys' => $citys,
			'areas' => $areas,
			'address' => $address,
		]);
	}

	//地址修改
	public function address_update()
	{
		$data = input('post.');
		$id = $data['id'];
		$res = model('Address')->allowField(true)->save($data,['id'=>$id]);
		if($res){
			return output(1,'地址修改成功');
		}else{
			return output(0,'未作任何修改');
		}
	}

	//优惠券选择
	public function coupon_check()
	{
		$data = input('post.');
		$coupon = model('Coupon')->get($data['cid']);
		if($data['price'] < $coupon->limit){
			return output(0,'订单未达到减免金额');
		}else{
			return output(1,$coupon->price);
		}

	}

	//订单创建
	public function order_create()
	{
		$data = input('post.');
		if($data['address_id'] == 0){
			return output(0,'请选择或者添加地址');
		}
		$data['uid'] = $this->user->id;
		$cookieData = cookie('cartdata');
		$data['goods_id'] = '';
		$data['goods_count'] = '';
		foreach($cookieData as $k=>$v){
			$data['goods_id'] .= ','.$v['goods_id'];
			$data['goods_count'] .= ','.$v['count'];
		}
		$data['goods_id'] = ltrim($data['goods_id'],',');
		$data['goods_count'] = ltrim($data['goods_count'],',');
		$data['order_number'] = date('YmdHis',time()).mt_rand(1000,9999);
		$staticgoods = model('StaticGoods');
		$staticaddress = model('StaticAddress');
		$order = model('Order');
		$address = model('Address')->get($data['address_id'])->toArray();
		$newAddress = [
			'linkman' => $address['linkman'],
			'phone' => $address['phone'],
			'addinfo' => $address['addinfo'],
			'province' => $address['province'],
			'city' => $address['city'],
			'area' => $address['area'],
		];
		$goodsIds = explode(',',$data['goods_id']);
		$goods = model('Goods')->all($goodsIds);
		$staticgoods->startTrans();
		$staticaddress->startTrans();
		$order->startTrans();
		$goodsList = [];
		foreach($goods as $k=>$v){
			$goodsList[] = ['goods_id'=>$v->id,'name'=>$v->name,'pic'=>showPrePic($v->id),'price'=>$v->current_price];
		}
		$res = $staticgoods->saveAll($goodsList);
		if($res){
			$newGoodsIds = '';
			foreach($res as $k=>$v){
				$newGoodsIds .= ','.$v->id;
			}
			$newGoodsIds = ltrim($newGoodsIds,',');
			$data['goods_id'] = $newGoodsIds;
			$addId = $staticaddress->add($newAddress);
			if($addId){	
				$data['address_id'] = $addId;
				$orderId = $order->add($data);
				if($orderId){
					$staticgoods->commit();
					$staticaddress->commit();
					$order->commit();
					model('Coupon')->save(['status'=>2],['id'=>$data['coupon_id']]);
					cookie('cartdata',null);
					cookie('shopcart',null);
					return output(1,$orderId);
				}else{
					$staticgoods->callBack();
					$staticaddress->callBack();
					$order->callBack();
					return output(0,'订单提交失败');
				}
			}else{
				$staticgoods->callBack();
				$staticaddress->callBack();
				return output(0,'订单提交失败');
			}	
		}else{
			$staticgoods->callBack();
			return output(0,'订单提交失败');
		}

	}

	//用户订单列表页
	public function list()
	{
		//获取所有订单
		$orders = model('Order')->getAllOrders($this->user->id);
		return $this->fetch('',[
			'orders' => $orders,
		]);
	}

	//订单详情页
	public function order_info()
	{
		$id = input('get.id');
		//获取订单信息
		$order = model('Order')->get($id);
		if($order){
			//获取地址信息
			$address = model('StaticAddress')->get($order->address_id);
			return $this->fetch('',[
				'order' => $order,
				'address' => $address,
			]);
		}else{
			$this->error('对不起,该订单不存在或已被删除');
		}
	}


	//立即购买创建订单
	public function goods_mkorder()
	{
		if(!cookie('cartdata')){
			$this->redirect('goods/list');
		}
		$data = cookie('cartdata');
		$goods = [];
		$total_price = 0;
		if($data){
			foreach ($data as $k => $val) {
				$goods[$k]['goods'] = model('Goods')->get($val['goods_id'])->toArray();
				$goods[$k]['count'] = $val['count'];
				$goods[$k]['price'] = number_format($val['count'] * model('Goods')->get($val['goods_id'])->current_price,2);
				$total_price += number_format($val['count'] * model('Goods')->get($val['goods_id'])->current_price,2);
			}
		}
		//获取用户地址
		$uid = $this->user->id;
		$address = model('Address')->getAddressByUser($uid);
		//折后价
		switch($this->user->user_level){
			case 1:
				$discount_price = number_format($total_price * 0.95,2);
				break;
			case 2:
				$discount_price = number_format($total_price * 0.88,2);
				break;
			case 3:
				$discount_price = number_format($total_price * 0.8,2);
				break;
			default:
				$discount_price = $total_price;
		}
		//根据订单价格获取优惠券
		$coupons = model('Coupon')->getCouponsByPrice($uid,$discount_price);
		//获取默认地址
		$default_add = model('Address')->where(['is_default'=>1,'uid'=>$this->user->id])->find();
		//获取一级城市
		$provinces = model('City')->getCitysByPid();
		return $this->fetch('mkorder',[
			'address' =>$address,
			'goods' => $goods,
			'total_price' => $total_price,
			'coupons' => $coupons,
			'discount_price' => $discount_price,
			'default_add' => $default_add,
			'provinces' => $provinces,
		]);
	}

	//订单支付页
	public function pay()
	{
		$id = input('get.id');
		$order = model('Order')->get($id);
		if($order && $order->order_status == 0 && $order->uid == $this->user->id){
			$url = 'http://apis.juhe.cn/qrcode/api?key=07fdeba75baa6f131aa42081892f9fdb&type=1&fgcolor=00b7ee&w=375&m=5&text=http://shop.oifox.com/index/order/dopay?id='.$id;
			$image = doUrl($url,0);
			$image = json_decode($image,true);
			$image = base64_decode($image['result']['base64_image']);
			$imageName = md5(mt_rand(1000,9999)).'.png';
			$path = './upload/pay/'.date('Ymd',time());
			if(!is_dir($path)){
				mkdir($path,0777,true);
			}
			$src = $path.'/'.$imageName;
			$res = file_put_contents($src,$image);
			if(!$res){
				$this->error('订单创建失败');
			}
			return $this->fetch('',[
				'order' => $order,
				'src' => ltrim($src,'.'),
			]);
		}else{
			$this->error('对不起,该订单不存在或已支付');
		}
	}

	//支付订单
	public function dopay()
	{
		$id = input('get.id');
		$orderModel = model('Order');
		$goodsModel = model('Goods');
		$userModel = model('User');
		$order = $orderModel->get($id);
		if($order && $order->order_status == 0  && $order->uid == $this->user->id){
			$points = $order->price * 100;
			$spending = $order->price;
			$staticGoodsIds = explode(',',$order->goods_id);
			$counts = explode(',',$order->goods_count);
			$goodsIds = [];
			foreach ($staticGoodsIds as $k => $v) {
				$goodsIds[$k] = model('StaticGoods')->where('id',$v)->value('goods_id');
			}
			$orderModel->startTrans();
			$goodsModel->startTrans();
			$userModel->startTrans();
			$item = true;
			foreach ($goodsIds as $k => $v) {
				$res1 = $goodsModel->where('id',$goodsIds[$k])->setDec('store',$counts[$k]);
				$res2 = $goodsModel->where('id',$goodsIds[$k])->setInc('sales',$counts[$k]);
				if(!$res1 || !$res2){
					$item = false;
				}
			}
			if($item){
				$res3 = $userModel->where('id',$this->user->id)->setInc('user_points',$points);
				$res4 = $userModel->where('id',$this->user->id)->setInc('user_spending',$spending);
				if($res3 && $res4){
					$res5 = $orderModel->doUpdate(['order_status'=>1],['id'=>$id]);
					if($res5){
						$goodsModel->commit();
						$userModel->commit();
						$orderModel->commit();
						//获取订单
						$newOrder = $orderModel->get($id);
						//获取地址
						$address = model('StaticAddress')->get($newOrder->address_id);
						//积分明细添加
						$pointsData = [
							'user_id' => $this->user->id,
							'content' => '商品购买',
							'score' => $points,
						];
						model('Points')->add($pointsData);
						//支付成功页面
						return $this->fetch('success',[
							'order' => $newOrder,
							'address' => $address,
						]);
					}else{
						$goodsModel->callBack();
						$userModel->callBack();
						$orderModel->callBack();
						$this->error('订单支付失败');
					}
				}else{
					$goodsModel->callBack();
					$userModel->callBack();
					$this->error('订单支付失败');
				}
			}else{
				$goodsModel->callBack();
				$this->error('订单支付失败');
			}
		}else{
			$this->error('对不起,该订单不存在或已支付');
		}
	}
	//订单是否支付
	public function isPay()
	{
		$id = input('post.id');
		$order = model('Order')->get($id);
		if($order->order_status == 1){
			return output(1,'支付成功');
		}
	}
	//支付成功页面
	public function order_success()
	{
		$id = input('get.id');
		//获取订单
		$order = model('Order')->get($id);
		//获取地址
		$address = model('StaticAddress')->get($order->address_id);
		return $this->fetch('success',[
			'order' => $order,
			'address' => $address,
		]);
	}
	
	//确认收货
	public function getGoods()
	{
		$id = input('post.id');
		$res = model('Order')->doUpdate(['order_status'=>3],['id'=>$id]);
		if($res){
			return output(1,'确认收货成功');
		}else{
			return output(0,'确认收货失败');
		}
	}


	//评价商品
	public function docomment()
	{
		$id = input('get.id');
		$order = model('Order')->get($id);
		if($order && $order->order_status == 3 && $order->uid == $this->user->id){
			$goodsIds = explode(',',$order->goods_id);
			$goods = model('StaticGoods')->all($goodsIds); 
			return $this->fetch('',[
				'goods' => $goods,
				'orderid' => $id,
			]);
		}else{
			$this->error('该订单不存在或已评价');
		}
	}

	//提交评价
	public function comment_input()
	{
		$data = input('post.');
		$list = [];
		$uid = $this->user->id;
		foreach($data['ids'] as $k=>$v){
			$list[] = [
				'user_id' => $uid,
				'goods_id' => $v,
				'level' => $data['level'][$k],
				'content' => $data['content'][$k],
				'create_time' => time(),
			];
		}
		$res = model('Comment')->saveAll($list);
		if($res){
			model('Order')->doUpdate(['order_status'=>4],['id'=>$data['orderid']]);
			return output(1,'提交评论成功');
		}else{
			return output(0,'提交评论失败');
		}
	}

	//评价列表
	public function comment_list()
	{	
		//获取用户评论
		$comments = model('Comment')->where(['user_id'=>$this->user->id,'status'=>1])->select();
		return $this->fetch('',[
			'comments' => $comments,
		]);
	}

}