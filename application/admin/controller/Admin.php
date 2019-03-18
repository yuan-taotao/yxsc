<?php
namespace app\admin\controller;

class Admin extends Base
{
	public function index()
	{
		//获取所有管理员
		$admins = $this->obj->getAllAdmins();
		return $this->fetch('',[
			'admins' => $admins,
		]);
	}

	//管理员添加
	public function add()
	{
		//获取角色
		$roles = model('Role')->getNormalRoles(0);
		return $this->fetch('',[
			'roles' => $roles,
		]);
	}

	public function doadd()
	{
		$data = input('post.');
		$data['role'] = isset($data['role']) ? $data['role'] : [];
		$roleIds = $data['role'];
		unset($data['role']);
		$validate = validate('Admin');
		$res = $validate->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		if(!$roleIds){
			return output(2,'请至少选择一个角色');
		}
		$data['passcode'] = mt_rand(1000,9999);
		$data['password'] = md5($data['password'].$data['passcode']);
		$aid = $this->obj->add($data);
		$list = [];
		if($aid){
			foreach($roleIds as $k=>$v){
				$list[] = ['admin_id'=>$aid,'role_id'=>$v];
			}
			$res = model('AdminRole')->saveAll($list);
			if($res){
				return output(1,'添加管理员成功');
			}else{
				return output(0,'添加失败');
			}
		}else{
			return output(0,'添加失败');
		}
	}

	//管理员编辑
	public function edit()
	{
		$id = input('get.id');
		$admin = $this->obj->get($id);
		$roleIds = model('AdminRole')->where(['admin_id'=>$id])->column('role_id');
		//获取角色
		$roles = model('Role')->getNormalRoles(0);
		return $this->fetch('',[
			'roles' => $roles,
			'admin' => $admin,
			'roleIds' => $roleIds,
		]);
	}

	//管理员信息修改
	public function update()
	{
		$data = input('post.');
		$data['role'] = isset($data['role']) ? $data['role'] : [];
		$roleIds = $data['role'];
		$id = $data['id'];
		unset($data['role']);
		unset($data['id']);
		$validate = validate('Admin');
		$res = $validate->scene('update')->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		if(!$roleIds){
			return output(2,'请至少选择一个角色');
		}

		if($data['password']){
			$data['passcode'] = mt_rand(1000,9999);
			$data['password'] = md5($data['password'].$data['passcode']);
			$res = $this->obj->doUpdate($data,['id'=>$id]);
			
		}else{
			unset($data['password']);
			$res = $this->obj->doUpdate($data,['id'=>$id]);
		}
		if($res){
			model('AdminRole')->where(['admin_id'=>$id])->delete();
			$list = [];
			foreach($roleIds as $k=>$v){
				$list[] = ['admin_id'=>$id,'role_id'=>$v];
			}
			model('AdminRole')->saveAll($list);
			return output(1,'修改成功');
		}else{
			return output(0,'修改失败');
		}
	}

	//管理员删除
	public function delete()
	{
		$id = input('post.id');
		$res = $this->obj->where('id',$id)->delete();
		if($res){
			model('AdminRole')->where(['admin_id'=>$id])->delete();
			return output(1,'删除成功');
		}else{
			return output(0,'删除失败');
		}

	}
}