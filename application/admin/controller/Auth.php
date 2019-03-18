<?php
namespace app\admin\controller;

class Auth extends Base
{
	public function index()
	{
		//获取正常权限
		$auths = $this->obj->getNormalAuths();
		return $this->fetch('',[
			'auths' => $auths,
		]);
	}

	//权限添加
	public function add()
	{
		$data = input('post.');
		$data['rule'] = ucfirst($data['controller']).'/'.$data['method'];
		$validate = validate('Auth');
		$res = $validate->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		$res = model('Auth')->add($data);
		if($res){
			return output(1,'添加新规则成功');
		}else{
			return output(0,'添加失败');
		}
	}

	//权限删除
	public function delete()
	{
		$id = input('post.id');
		$res = $this->obj->where('id',$id)->delete();
		if($res){
			model('RoleAuth')->where(['auth_id'=>$id])->delete();
			return output(1,'删除成功');
		}else{
			return output(0,'删除失败');
		}

	}
}