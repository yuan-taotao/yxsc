<?php
namespace app\index\controller;

class Goods extends Base
{
	//商品列表
	public function list()
	{
		$cateId = input('get.cate');
		$name = input('get.name');
		$order = empty(input('get.order'))? 'default' : input('get.order');
		if($cateId){
			//获取当前分类
			$cate = model('Category')->get($cateId);
			if(!$cate){
				$this->error('对不起,没有你想要的分类');
			}
			$cate_pid = $cate->pid;
			if($cate_pid){
				$childCates = model('Category')->getCatesByPid($cate_pid);
			}else{
				$childCates = model('Category')->getCatesByPid($cateId);
			}
			$cateName = $cate->name;
		}else{
			$cate_pid = 'all';
			$childCates = [];
			$cateName = '全部商品';
		}
		//获取一级分类
		$parentCates = model('Category')->getCatesByPid();
		//根据分类获取商品
		$goods = model('Goods')->getGoodsByDataAndOrder($cateId,$cate_pid,$order,$name);
		//获取热卖商品
		$hotSaleGoods = model('Goods')->getHotSaleGoods();
		//获取页面参数
		$params = request()->param();
		return $this->fetch('',[
			'cateId' => $cateId,
			'cate_pid' => $cate_pid,
			'parentCates' => $parentCates,
			'childCates' => $childCates,
			'goods' => $goods,
			'order' => $order,
			'hotSaleGoods' => $hotSaleGoods,
			'cateName' => $cateName,
			'params' => $params,
		]);
	}

	//商品详细信息
	public function show()
	{
		if(is_array(cookie('trace'))){
			$cookieTrace = cookie('trace');
		}else{
			$cookieTrace = [];
		}
		$id = input('get.id');
		//获取商品信息
		$goods = model('Goods')->get($id);
		if($goods && $goods->status == 1){
			//如果没有登录足迹存入COOKIE
			if(!session('user','','yx')){
				if(!in_array($id,$cookieTrace))
				$cookieTrace[] = $id;
				cookie('trace',$cookieTrace);
			}else{
			//登录则存表
				cookie('trace',null);
				$res = model('trace')->where(['user_id'=>session('user','','yx')->id,'goods_id'=>$id])->find();
				if(!$res){
					model('trace')->save(['user_id'=>session('user','','yx')->id,'goods_id'=>$id]);
				}

			}
			$cate_id = $goods->cate_id;
			$cate_pid = $goods->cate_pid;
			$keywords = $goods->keywords;
			//获取评论信息
			$allComments = model('Comment')->getComments($goods->id,0);
			$commentsCount = count($allComments);
			$comments = model('Comment')->getComments($goods->id);
			//获取好中差评论
			$best = count(model('Comment')->getCommentsByLevel($goods->id,0));
			$middle = count(model('Comment')->getCommentsByLevel($goods->id,1));
			$worse = count(model('Comment')->getCommentsByLevel($goods->id,2));
			if($commentsCount != 0){
				$percent = $best / $commentsCount * 100;
			}else{
				$percent = 0;
			}
			//获取预览图
			$preData = [
				'goods_id' => $id,
				'pic_type' => 1,
			];
			$pre_pic_ids = model("GoodsPic")->where($preData)->column('pic_id');
			$pre_pics = model('Picture')->all($pre_pic_ids);
			//获取详情图
			$infoData = [
				'goods_id' => $id,
				'pic_type' => 0,
			];
			$info_pic_ids = model("GoodsPic")->where($infoData)->column('pic_id');
			$info_pics = model('Picture')->all($info_pic_ids);

			//获取相关商品
			$relatedGoods = model('Goods')->getGoodsByKeywords($keywords);

			//获取足迹
			if(!session('user','','yx')){
				$ints = [];
				foreach($cookieTrace as $k=>$v){
					$ints[] = intval($v);
				}
				$traces = model('Goods')->all($ints);
			}else{
				$goodsIds = model('Trace')->where(['user_id'=>session('user','','yx')->id])->column('goods_id');
				$traces = model('Goods')->all($goodsIds);
			}
			return $this->fetch('',[
				'goods' => $goods,
				'comments' => $comments,
				'commentsCount' => $commentsCount,
				'pre_pics' => $pre_pics,
				'info_pics' => $info_pics,
				'relatedGoods' => $relatedGoods,
				'cate_id' => $cate_id,
				'cate_pid' => $cate_pid,
				'traces' => $traces,
				'best' => $best,
				'middle' => $middle,
				'worse' => $worse,
				'percent' => $percent,
			]);
		}else{
			$this->error('对不起,你访问的商品不存在或已下架');
		}
	}

	//获取优惠券
	public function get_coupon()
	{
		if(!session('user','','yx')){
			return output(0,'请登录后再领取优惠券');
		}
		$coupon = [];
		$info = [];
		$coupon['user_id'] = $this->user->id;
		$info['user_id'] = $this->user->id;
		$coupon['start_time'] = time();
		$coupon['end_time'] = time() + (7*24*3600);
		$coupon['coupon_number'] = date('mdHi',time()).mt_rand(10,99);
		$data = input('post.data-id');
		/**
		 * 1 125-5  2 198-10  3 298-20
		 */
		switch($data){
			case 1:
				$coupon['price'] = 5;
				$info['price'] = 5;
				$coupon['limit'] = 125;
				$info['limit'] = 125;
				break;
			case 2:
				$coupon['price'] = 10;
				$info['price'] = 10;
				$coupon['limit'] = 198;
				$info['limit'] = 198;
				break;
			case 3:
				$coupon['price'] = 20;
				$info['price'] = 20;
				$coupon['limit'] = 298;
				$info['limit'] = 298;
				break;
		}
		$info['status'] = 1;
		$getCoupon = model('Coupon')->where($info)->find();
		if($getCoupon){
			return output(0,'对不起,你已经领取过该优惠券了,请使用后再来领取');
		}
		$res = model('Coupon')->add($coupon);
		if($res){
			return output(1,'优惠券领取成功,7天内有效');
		}else{
			return output(0,'优惠券领取失败');
		}
	}

	//商品立即购买
	public function buy_now()
	{
		if(!session('user','','yx')){
			return output(3,'请登录');
		}

		$data = input('post.');
		$cookieData = [];
		$cookieData[0]['goods_id'] = $data['id']; 
		$cookieData[0]['count'] = $data['count']; 

		cookie('cartdata',$cookieData);
		return output(1,'1');
	}
}