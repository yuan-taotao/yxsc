<?php
namespace app\index\controller;

class Index extends Base
{
	public function _initialize()
	{
		//自动修改签到状态
		parent::_initialize();
		if(session('user','','yx')){
			$nowUser = model('User')->get($this->user->id);
			$nowDate = date('Ymd',time());
			$sigDate = date('Ymd',$nowUser->last_sig_time);
			if($nowDate != $sigDate){
				model('User')->doUpdate(['is_sig'=>0],['id'=>$nowUser->id]);
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
	}

    public function index()
    {
    	//今日是否签到
    	if(session('user','','yx')){
			$nowUser = model('User')->get(session('user','','yx')->id);
			$isSig = $nowUser->is_sig;
		}else{
			$isSig = '';
		}
		//获取推荐位
		$vice_featured = model('Featured')->where(['status'=>1,'type'=>0])->select();
		$featured = model('Featured')->where(['status'=>1,'type'=>1])->select();
    	//获取所有分类
    	$cates = model('Category')->getNormalCates();
        return $this->fetch('',[
        	'cates' => $cates,
        	'isSig' => $isSig,
        	'vice_featured' => $vice_featured,
        	'featured' => $featured,
        ]);
    }


    //首页签到
	public function do_sig()
	{
		$id = input('post.id');
		if($id != 1){
			return output(0,'对不起,签到失败');
		}
		$nowUser = model('User')->get($this->user->id);
		$isSig = $nowUser->is_sig;
		if($isSig){
			return output(0,'您今日已签到哦');
		}
		$userModel = model('User');
		$pointsModel = model('Points');
		$userModel->startTrans();
		$pointsModel->startTrans();
		$res1 = model('User')->doUpdate(['is_sig'=>1,'last_sig_time'=>time()],['id'=>$this->user->id]);
		$res2 = model('User')->where('id',$this->user->id)->setInc('user_points',80);
		$res3 = model('Points')->add(['user_id'=>$this->user->id,'content'=>'每日签到','score'=>80]);
		if($res1 && $res2 && $res3){
			$userModel->commit();
			$pointsModel->commit();
			return output(1,'签到成功<br/>积分+80');
		}else{
			$userModel->callBack();
			$pointsModel->callBack();
			return output(0,'签到失败');
		}


	}

}
