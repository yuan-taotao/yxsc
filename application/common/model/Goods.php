<?php
namespace app\common\model;

class Goods extends Base
{
	//获取所有商品
	public function getAllGoods()
	{
		$data = [
			'status' => ['neq',-1],
		];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];
		
		return $this->where($data)->order($order)->paginate();
	}

	//根据条件获取图片
	public function getGoodsByData($data)
	{
		$data['status'] = ['neq',-1];
		$order = [
			'listorder' => 'desc',
			'id' => 'desc',
		];

		return $this->where($data)->order($order)->paginate(5);
	}

	//根据分类获取商品并排序
	public function getGoodsByDataAndOrder($cateId,$cate_pid,$order,$name)
	{	
		if($cate_pid === 'all'){
			$data = [
				'status' => 1,
				'name' => ['like','%'.$name.'%'],
			];
		}else{
			if($cate_pid == 0){
				$data = [
					'status' => 1,
					'cate_pid' => $cateId,
				];
			}else{
				$data = [
					'status' => 1,
					'cate_id' => $cateId,
				];
			}
		}
		if($order == 'default'){
			$listorder = [
				'listorder' => 'desc',
				'id' => 'desc',
			];
		}elseif($order == 'sales'){
			$listorder = ['sales'=>'desc'];
		}elseif($order == 'price'){
			$listorder = ['current_price'=>'asc'];
		}else{
			$listorder = [
				'listorder' => 'desc',
				'id'=>'desc',
			];
		}

		return $this->where($data)->order($listorder)->paginate(12);
	}

	//获取热销商品
	public function getHotSaleGoods()
	{
		$data = [
			'status' => 1,
		];
		$order = [
			'sales' => 'desc',
		];
		return $this->where($data)->order($order)->limit(3)->select();
	}

	//根据关键字获取商品
	public function getGoodsByKeywords($keywords)
	{
		return $this->query("select * from yx_goods where keywords regexp replace('".$keywords."',',','|') and status = 1 order by id desc limit 12");
	}
}