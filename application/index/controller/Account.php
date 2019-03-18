<?php
namespace app\index\controller;
 
class Account extends Base
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

	//账户中心页
	public function index()
	{
		return $this->fetch('',[

		]);
	}

	//密码修改
	public function password_update()
	{
		return $this->fetch('',[

		]);
	}

	//执行密码修改
	public function password_doupdate()
	{
		$data = input('post.');
		unset($data['name']);
 		if(!$this->user->email && !$this->user->phone){
			return output(0,'对不起,QQ登录用户请先绑定邮箱或者手机后再设置密码');
		}
		$validate = validate('Regist');
		$res = $validate->scene('update_password')->check($data);
		if(true !== $res){
			return output(0,$validate->getError());
		}
		unset($data['repassword']);
		if(!$data['oldpassword']){
			if($this->user->password){
				return output(0,'原密码输入错误');
			}else{
				unset($data['oldpassword']);
				$data['passcode'] = mt_rand(1000,9999);
				$data['password'] = md5($data['password'].$data['passcode']);
				$res = model('User')->where(['id'=>$this->user->id])->update($data);
				if($res){
					return output(1,'密码修改成功');
				}else{
					return output(0,'密码修改失败');
				}
			}
		}else{
			if($data['oldpassword'] == $data['password']){
				return output(0,'新密码不能与原密码相同');
			}
			$password = md5($data['oldpassword'].$this->user->passcode);
			if($password != $this->user->password){
				return output(0,'原密码输入错误');
			}
			unset($data['oldpassword']);
			$data['passcode'] = mt_rand(1000,9999);
			$data['password'] = md5($data['password'].$data['passcode']);
			$res = model('User')->where(['id'=>$this->user->id])->update($data);
			if($res){
				return output(1,'密码修改成功');
			}else{
				return output(0,'密码修改失败');
			}


		}

	}

	//手机绑定换绑
	public function phone_bind()
	{
		if($this->user->phone){
			$str = substr_replace($this->user->phone,'####',3,4);
		}else{
			$str = '';
		}
		return $this->fetch('',[
			'phone_str' => $str,
		]);
	}

	//邮箱绑定换绑
	public function email_bind()
	{
		return $this->fetch('',[

		]);
	}

	//原手机获取验证码
	public function getOldMsg()
	{
		$code_time = empty(session('old_code_time','','yx')) ? '' : session('old_code_time','','yx');
		if($code_time && (time() <= ($code_time + 120))){
			return output(3,'请稍等一会儿后再重新获取');
		}

		$phone = $this->user->phone;
		$code = mt_rand(1000,9999);
		$res = \msglogin\Msg::send($code,120,$phone);
		$res = json_decode($res);
		if($res->code === '000000'){
			session('old_update_code',$code,'yx');
			session('old_code_time',time(),'yx');
			return output(1,'验证码已发送');
		}else{
			return output(0,'验证码发送失败');
		}
	}

	//新手机获取验证码
	public function getNewMsg()
	{
		$code_time = empty(session('new_code_time','','yx')) ? '' : session('new_code_time','','yx');
		if($code_time && (time() <= ($code_time + 120))){
			return output(3,'请稍等一会儿后再重新获取');
		}
		$phone = input('post.phone');
		$validate = validate('Phone');
		$res = $validate->check(['phone'=>$phone]);
		if($res !== true){
			return output(2,$validate->getError());
		}

		$code = mt_rand(1000,9999);
		$res = \msglogin\Msg::send($code,120,$phone);
		$res = json_decode($res);
		if($res->code === '000000'){
			session('new_update_phone',$phone,'yx');
			session('new_update_code',$code,'yx');
			session('new_code_time',time(),'yx');
			return output(1,'验证码已发送');
		}else{
			return output(0,'验证码发送失败');
		}
	}

	//手机换绑
	public function phone_update()
	{
		$data = input('post.');
		$validate = validate('Regist');
		if($this->user->phone){
			$res = $validate->scene('phone_update')->check($data);
			if($res !== true){
				return output(2,$validate->getError());
			}
			if($data['old_msgcode'] != session('old_update_code','','yx')){
				return output(3,'原手机短信验证码错误');
			}
			if($data['new_msgcode'] != session('new_update_code','','yx')){
				return output(3,'新手机短信验证码错误');
			}
			$res = model('User')->where(['id'=>$this->user->id])->update(['phone'=>session('new_update_phone','','yx')]);
			if($res){
				return output(1,'手机换绑成功');
			}else{
				return output(0,'手机换绑失败');
			}
		}else{
			$res = $validate->scene('phone_add')->check($data);
			if($res !== true){
				return output(2,$validate->getError());
			}
			if($data['new_msgcode'] != session('new_update_code','','yx')){
				return output(3,'短信验证码错误');
			}

			$res = model('User')->where(['id'=>$this->user->id])->update(['phone'=>session('new_update_phone','','yx')]);
			if($res){
				return output(1,'手机绑定成功,设置密码后可以使用手机登录');
			}else{
				return output(0,'手机绑定失败');
			}
		}
	}

	//获取邮件验证码
	public function getEmailCode()
	{
		$code_time = empty(session('emailcode_time','','yx')) ? '' : session('emailcode_time','','yx');
		if($code_time && (time() <= ($code_time + 120))){
			return output(3,'请稍等一会儿后再重新获取');
		}
		$data = input('post.');
		$validate = validate('Regist');
		$res = $validate->scene('email_code')->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$title = '【BetterBoy邮箱验证】';
		$code = mt_rand(1000,9999);
		$content = '您的邮箱验证码为['.$code.'],请在120内完成验证';
		$res = \phpmailer\Email::send($data['email'],$title,$content);
		if($res){
			session('email_update',$data['email'],'yx');
			session('email_code',$code,'yx');
			session('emailcode_time',time(),'yx');
			return output(1,'验证码已发送');
		}else{
			return output(0,'验证码发送失败');
		}
	}

	//邮箱修改
	public function email_update()
	{
		$data = input('post.');
		$validate = validate('Regist');
		$res = $validate->scene('email_update')->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}

		if($data['emailcode'] != session('email_code','','yx')){
			return output(2,'邮箱验证码错误');
		}
		$res = model('User')->where(['id'=>$this->user->id])->update(['email'=>session('email_update','','yx')]);
		if($res){
			return output(1,'邮箱绑定成功,设置密码后可以使用邮箱登录');
		}else{
			return output(0,'邮箱绑定失败');
		}
	}

	//用户地址
	public function address()
	{
		$editId = empty(input('get.edit')) ? '' : input('get.edit');
		if($editId){
			$addressInfo = model('Address')->get($editId);
			$citys = model('City')->getCitysByPid($addressInfo->province);
			$areas = model('City')->getCitysByPid($addressInfo->city);
		}else{
			$addressInfo = '';
			$citys = [];
			$areas = [];
		}
		//获取用户地址
		$address = model('Address')->getAddressByUser($this->user->id);
		$city = model('City');
		//根据父ID获取城市
		$provinces = $city->getCitysByPid();
		return $this->fetch('',[
			'provinces' => $provinces,
			'address' => $address,
			'addressInfo' => $addressInfo,
			'citys' => $citys,
			'areas' => $areas,
		]);
	}

	//获取子级市区
	public function getchilds()
	{
		$pid = input('post.pid');
		$childs = model('City')->getCitysByPid($pid);
		return $childs;
	}

	//用户地址添加
	public function address_add()
	{
		$data = input('post.');
		$validate = validate('Address');
		$res = $validate->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$data['uid'] = $this->user->id;
		$res = model('Address')->add($data);
		if($res){
			return output(1,'地址添加成功');
		}else{
			return output(0,'地址添加失败');
		}
	}

	//默认地址设置
	public function default_set()
	{
		$addId = input('post.id');
		model('Address')->where('id','neq',$addId)->update(['is_default'=>0]);
		model('Address')->save(['is_default'=>1],['id'=>$addId]);
		return output(1,'设置默认地址成功');
	}

	//地址信息修改
	public function address_update()
	{
		$id = input('get.id');
		$data = input('post.');
		$validate = validate('Address');
		$res = $validate->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$res = model('Address')->where('id',$id)->update($data);
		if($res){
			return output(1,'地址修改成功');
		}else{
			return output(0,'未作任何修改');
		}
	}

	//地址删除
	public function address_delete()
	{
		$id = input('post.id');
		$res = model('Address')->save(['status'=>-1],['id'=>$id]);
		if($res){
			return output(1,'地址删除成功');
		}else{
			return output(0,'地址删除失败');
		}
	}
}