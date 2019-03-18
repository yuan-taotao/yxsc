<?php
//全局状态名获取
function status($statusId)
{
	$str = '';
	switch($statusId){
		case 1:
		$str = '<span class="layui-btn layui-btn-normal layui-btn-mini">正常</span>';
		break;
		case 0:
		$str = '<span class="layui-btn layui-btn-danger layui-btn-mini">禁用</span>';
		break;
		case -1:
		$str = '<span class="layui-btn layui-btn-danger layui-btn-mini">已删除</span>';
		break;
	}
	return $str;
}

//订单发货状态获取
function order_status($statusId)
{
	$str = '';
	switch($statusId){
		case 2:
		$str = '<span class="layui-btn layui-btn-normal layui-btn-mini">已发货</span>';
		break;
		case 1:
		$str = '<span class="layui-btn layui-btn-danger layui-btn-mini">未发货</span>';
		break;
	}
	return $str;
}

//公共返回结构
function output($code,$msg)
{
	return [
		'status' => $code,
		'msg' => $msg,
	];
}

//图片类型名获取
function pic_type($type)
{
	switch($type){
		case 1:
		$str = '<span class="layui-btn layui-btn-danger layui-btn-mini">预览图</span>';
		break;
		case 0:
		$str = '<span class="layui-btn layui-btn-normal layui-btn-mini">详情图</span>';
	}

	return $str;
}

//图片列表获取预览图
function showPrePic($goodsId)
{
	$res = model('GoodsPic')->where(['goods_id'=>$goodsId,'pic_type'=>1])->select();
	$pic_id = $res[0]['pic_id'];
	$pic = model('Picture')->get($pic_id);
	$src = $pic->pic_path;
	return $src;
}

//根据ID获取商品名称
function getGoodsName($id)
{
	$goods = model('Goods')->get($id);
	return $goods->name;
}

//获取分类名
function getCateName($cateId)
{
	$res = model('Category')->get($cateId);
	return $res->name;
}

//获取会员等级
function user_level($level)
{
	switch($level){
		case 0:
			$str = '普通会员';
			break;
		case 1:
			$str = '高级会员';
			break;
		case 2:
			$str = '铂金会员';
			break;
		case 3:
			$str = '钻石会员';
			break;
	}

	return $str;
}

//获取城市名称
function getCityName($areaId)
{
	$city = model('City')->get(['area_id'=>$areaId]);
	return $city->area_name;
}

//显示会员等级待遇
function getUserLevel($user_level){
	$str = '';
	switch($user_level){
		case 0:
			$str = '您是普通会员,暂无可享受折扣';
			break;
		case 1:
			$str = '尊敬的高级会员,您可享受9.5折优惠';
			break;
		case 2:
			$str = '尊敬的铂金会员,您可享受8.8折优惠';
			break;
		case 3:
			$str = '尊敬的钻石会员,您可享受8.0折优惠';
			break;
	}
	return $str;
}
//显示会员等级名
function getUserLevelName($user_level){
	$str = '';
	switch($user_level){
		case 0:
			$str = '<span class="layui-btn layui-btn-primary">普通会员</span>';
			break;
		case 1:
			$str = '<span class="layui-btn layui-btn-normal">高级会员</span>';
			break;
		case 2:
			$str = '<span class="layui-btn layui-btn-warm">铂金会员</span>';
			break;
		case 3:
			$str = '<span class="layui-btn layui-btn-danger">钻石会员</span>';
			break;
	}
	return $str;
}

//根据订单ID获取商品信息集
function getGoodsInfos($orderid)
{	
	$order = model('Order')->get($orderid);
	$goodsIds = explode(',',$order->goods_id);
	$counts = explode(',',$order->goods_count);
	$list = [];
	foreach ($goodsIds as $k => $v) {
		$goods = model('StaticGoods')->get($v);
		$list[$k]['goods'] = $goods->toArray();
		$list[$k]['count'] = $counts[$k]; 
	}
	return $list;
}

//获取订单状态
function getOrderStatus($statusid)
{
	$str = '';
	switch($statusid){
		case 0:
			$str = '等待支付';
			break;
		case 1:
			$str = '等待发货';
			break;
		case 2:
			$str = '已发货';
			break;
		case 3:
			$str = '交易完成';
			break;
		case 4:
			$str = '已评价';
			break;
	}	
	return $str;
}
//获取订单状态 带样式
function getOrderStatusWithCSS($statusid)
{
	$str = '';
	switch($statusid){
		case 0:
			$str = '<div class="layui-btn layui-btn-primary">等待支付</div>';
			break;
		case 1:
			$str = '<div class="layui-btn">等待发货</div>';
			break;
		case 2:
			$str = '<div class="layui-btn layui-btn-normal">等待收货</div>';
			break;
		case 3:
			$str = '<div class="layui-btn layui-btn-warm">等待评价</div>';
			break;
		case 4:
			$str = '<div class="layui-btn layui-btn-disabled">交易完成</div>';
			break;
	}	
	return $str;
}

//订单操作状态
function getOrderAction($statusid)
{
	$str = '';
	switch($statusid){
		case 0:
			$str = '<div class="am-btn am-btn-danger anniu pay-btn">立即支付</div>';
			break;
		case 1:
			$str = '<div class="am-btn am-btn-danger anniu sendgoods-btn">提醒发货</div>';
			break;
		case 2:
			$str = '<div class="am-btn am-btn-danger anniu getgoods-btn">确认收货</div>';
			break;
		case 3:
			$str = '<div class="am-btn am-btn-danger anniu comment-btn">立即评价</div>';
			break;
		case 4:
			$str = '<div class="am-btn am-btn-danger anniu" disabled>交易完成</div>';
			break;
	}	
	return $str;
}

//判断优惠券是否领取
function isHaveCoupon($id)
{
	$data = [
		'user_id' => session('user','','yx')->id,
		'status' => 1,
	];
	switch ($id) {
		case 1:
			$data['price'] = 5.00;
			$data['limit'] = 125.00;
			break;
		
		case 2:
			$data['price'] = 10.00;
			$data['limit'] = 198.00;
			break;
		case 3:
			$data['price'] = 20.00;
			$data['limit'] = 298.00;
			break;
	}
	$res = model('Coupon')->where($data)->select();
	if($res){
		return 1;
	}else{
		return 0;
	}
}

//根据level显示评价等级
function getCommentLevel($level)
{
	$str = '';
	switch ($level) {
		case 0:
			$str = '好评';
			break;
		case 1:
			$str = '中评';
			break;
		case 2:
			$str = '差评';
			break;
	}
	return $str;
}
//根据level显示评价等级
function getCommentLevelWithCSS($level)
{
	$str = '';
	switch ($level) {
		case 0:
			$str = '<span class="layui-btn layui-btn-warm">好评</span>';
			break;
		case 1:
			$str = '<span class="layui-btn layui-btn-normal">中评</span>';
			break;
		case 2:
			$str = '<span class="layui-btn layui-btn-danger">差评</span>';
			break;
	}
	return $str;
}

//根据goods_ids字符串获得一条商品信息
function getOneGoodsPic($string)
{
	$arr = explode(',',$string);
	$goods = model('StaticGoods')->get($arr[0]);
	return $goods->pic;
}

//根据goods_ids字符串获得一条商品信息
function getOneGoodsId($string)
{
	$arr = explode(',',$string);
	$goods = model('StaticGoods')->get($arr[0]);
	return $goods->goods_id;
}

//根据userID获取匿名
function getSecName($id)
{
	$userinfo = model('UserInfo')->where('uid',$id)->find();
	$nickname = $userinfo->nickname;
	$first = mb_substr($nickname,0,1,'utf-8');
	$last = mb_substr($nickname,-1,1,'utf-8');
	return $first.'****'.$last;
}

//根据userID获取头像
function getUserPic($id)
{
	$userinfo = model('UserInfo')->where('uid',$id)->find();
	return $userinfo->pic;
}

//获取用户昵称
function getUserName($id)
{
	$userinfo = model('UserInfo')->where('uid',$id)->find();
	return $userinfo->nickname;
}

//根据订单ID获取收货地址信息
function getAddress($id)
{
	$order = model('Order')->get($id);
	$address = model('StaticAddress')->get($order->address_id);
	$str = $address->linkman.':&nbsp;&nbsp;'.$address->phone;
	return $str;
}

//获取推荐位类型
function getFeaturedType($id)
{
	$str = '';
	switch($id){
		case 0:
			$str = '副推荐位';
			break;
		case 1:
			$str = '主轮播图';
			break;
	}
	return $str;
}

//推荐位状态
function featured_status($statusId)
{
	$str = '';
	switch($statusId){
		case 1:
			$str = '<span class="layui-btn layui-btn-normal layui-btn-mini">已启用</span>';
			break;
		case 0:
			$str = '<span class="layui-btn layui-btn-danger layui-btn-mini">未启用</span>';
			break;
	}
	return $str;
}

//获取角色权限名称列表
function getRoleAuths($id)
{
	$str = '';
	$authIds = model('RoleAuth')->where(['role_id'=>$id])->column('auth_id');
	$auths = model('Auth')->all($authIds);
	foreach($auths as $k=>$v){
		$str .= ','.$v->name;
	}
	$str = ltrim($str,',');
	return $str;
}

//获取管路员角色
function getAdminRoles($id)
{
	$str = '';
	$roleIds = model('AdminRole')->where(['admin_id'=>$id])->column('role_id');
	$roles = model('Role')->all($roleIds);
	foreach($roles as $k=>$v){
		$str .= ','.$v->name;
	}
	$str = ltrim($str,',');
	return $str;
}

/**
 * 
 * @param $url 
 * @param $type  0  get  1 post
 * @param $data  
 * 
 */
function doUrl($url,$type=0,$data=[])
{
	//初始化
	$ch = curl_init();

	//设置选项
	curl_setopt($ch,CURLOPT_URL,$url);//请求路径
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置成功后只返回结果不输出
	curl_setopt($ch, CURLOPT_HEADER,0);//不输出头信息

	//设置请求方式
	if($type == 1){
		//post方式
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	}

	//执行并获取内容
	$output = curl_exec($ch);

	//释放句柄
	curl_close($ch);
	return $output;

}