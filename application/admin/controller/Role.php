<?php
namespace app\admin\controller;

class Role extends Base
{
	public function index()
	{
		//获取角色
		$roles = $this->obj->getAllRoles();
		return $this->fetch('',[
			'roles' => $roles,
		]);
	}

	//角色添加
	public function add()
	{
		//获取后台模块所有控制器
		$paths = glob('../application/admin/controller/*.php');
		$contrllers = [];
		foreach($paths as $k=>$v){
				$controllers[] = strtolower(basename($v));
		}
		//获取所有权限
		$auths = model('Auth')->getNormalAuths(0);
		return $this->fetch('',[
			'auths' => $auths,
			'controllers' => $controllers,
		]);
	}

	//提交添加
	public function doadd()
	{
		$data = input('post.');
		$data['id'] = isset($data['id']) ? $data['id'] : [];
		$authArr = $data['id'];
		unset($data['id']);
		$validate = validate('Role');
		$res = $validate->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		if(!$authArr){
			return output(2,'请选择权限');
		}
		
		$rid = model('Role')->add($data);
		$list = [];
		if($rid){
			foreach($authArr as $k=>$v){
				$list[] = ['role_id'=>$rid,'auth_id'=>$v];
			}
		}else{
			return output(0,'角色创建失败');
		}
		$res = model('RoleAuth')->saveAll($list);
		if($res){
			return output(1,'角色创建成功');
		}else{
			return output(0,'角色创建失败');
		}
	}

	//角色编辑
	public function edit()
	{
		$id = input('get.id');
		if($id == 1){
			$this->error('该角色不允许被编辑');
		}
		$role = $this->obj->get($id);
		$authIds = model('RoleAuth')->where(['role_id'=>$id])->column('auth_id');
		//获取后台模块所有控制器
		$paths = glob('../application/admin/controller/*.php');
		$contrllers = [];
		foreach($paths as $k=>$v){
				$controllers[] = strtolower(basename($v));
		}
		//获取所有权限
		$auths = model('Auth')->getNormalAuths(0);
		return $this->fetch('',[
			'auths' => $auths,
			'controllers' => $controllers,
			'authIds' => $authIds,
			'role' => $role,
		]);
	}

	//角色信息修改
	public function update()
	{
		$data = input('post.');
		$data['id'] = isset($data['id']) ? $data['id'] : [];
		$authArr = $data['id'];
		$id = $data['role_id'];
		unset($data['role_id']);
		unset($data['id']);
		$validate = validate('Role');
		$res = $validate->scene('update')->check($data);
		if($res !== true){
			return output(2,$validate->getError());
		}
		if(!$authArr){
			return output(2,'请选择权限');
		}
		$result = $this->obj->doUpdate($data,['id'=>$id]);
		$list = [];
		if($result){
			model('RoleAuth')->where(['role_id'=>$id])->delete();
			foreach($authArr as $k=>$v){
				$list[] = ['role_id'=>$id,'auth_id'=>$v];
			}
			$res = model('RoleAuth')->saveAll($list);
			if($res){
				return output(1,'修改成功');
			}else{
				return output(0,'修改失败');
			}
		}else{
			return output(0,'修改失败');
		}
	}

	//角色删除
	public function delete()
	{
		$id = input('post.id');
		$res = $this->obj->where('id',$id)->delete();
		if($res){
			model('AdminRole')->where(['role_id'=>$id])->delete();
			model('RoleAuth')->where(['role_id'=>$id])->delete();
			return output(1,'删除成功');
		}else{
			return output(0,'删除失败');
		}

	}
}