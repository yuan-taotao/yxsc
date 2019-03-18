<?php
namespace app\admin\controller;
use think\Controller;

class Base extends Controller
{
	public $controller = '';
	public $obj = '';
	public $adminInfo = '';

	public function _initialize()
	{
		//判断登录
		if(!session('admin','','yx_admin')){
			$this->redirect('login/index');
		}else{
			$this->adminInfo = session('admin','','yx_admin');
		}
		$this->assign('adminInfo',$this->adminInfo);
		$this->controller = request()->controller();
		$this->obj = model(request()->controller());

		//权限判断
		$c = request()->controller();
		$m = request()->action();
		$rule = $c.'/'.$m;
		$allList = [];
		$allauths = model('Auth')->getNormalAuths(0);
		foreach($allauths as $k => $v){
			$allList[] = $v->rule;
		}
		$controllers = [];
		$this->assign('controllers',$controllers);
		$roleIds = model('AdminRole')->where(['admin_id'=>$this->adminInfo->id])->column('role_id');
		if(is_array($roleIds) && count($roleIds) > 0){
			$authIds = model('RoleAuth')->where(['role_id'=>['in',$roleIds]])->column('auth_id');
			$cm_list = [];
			if(is_array($authIds) && count($authIds) > 0){
				$authIds = array_unique($authIds);
				$auths = model('Auth')->all($authIds);
				foreach($auths as $k=>$v){
					$controllers[] = $v->controller;
				}
			}
		}
		$controllers = array_unique($controllers);
		$this->assign('controllers',$controllers);
		if(in_array($rule,$allList)){
			$roleIds = model('AdminRole')->where(['admin_id'=>$this->adminInfo->id])->column('role_id');
			if(!is_array($roleIds) && !count($roleIds) > 0){
				$this->error('你无权限操作');
			}
			$authIds = model('RoleAuth')->where(['role_id'=>['in',$roleIds]])->column('auth_id');
			$cm_list = [];
			if(is_array($authIds) && count($authIds) > 0){
				$authIds = array_unique($authIds);
				$auths = model('Auth')->all($authIds);
				foreach($auths as $k=>$v){
					$cm_list[] = $v->rule;
				}
				if(!in_array($rule,$cm_list)){
					$this->error('你无权限操作');
				}
			}else{
				$this->error('你无权限操作');
			}
		}
	}

	//公共状态修改方法
	public function status()
	{
		$id = input('get.id',0,'intval');
		$status = input('get.status',0,'intval');
		if(!$id){
			$this->error('参数错误');
		}
		$res = $this->obj->doUpdate(['status' => $status],['id' => $id]);
		if($res){
			return redirect($_SERVER['HTTP_REFERER']);
		}
	}

	//公共添加验证ajax方法
	public function addCheck($data)
	{
		$controller = $this->controller;
		$validate = validate($controller);
		$res = $validate->check($data);
		if(true !== $res){
			$output = output(2,$validate->getError());

		}else{
			$res = $this->obj->add($data);
			if($res){
				$output = output(1,'操作成功');
			}else{
				$output = output(0,'操作失败');
			}
		}

		return $output;
	}

	//公共修改验证ajax方法
	public function updateCheck($data,$where)
	{

		$controller = $this->controller;
		$validate = validate($controller);
		$res = $validate->scene('update')->check($data);
		if(true !== $res){
			$output = output(2,$validate->getError());

		}else{
			$res = $this->obj->doUpdate($data,$where);
			if($res){
				$output = output(1,'操作成功');
			}else{
				$output = output(0,'操作失败');
			}
		}

		return $output;
	}

	//公共ajax提交删除方法
	public function delete()
	{
		$id = input('post.id',0,'intval');
		if(!$id){
			$this->error('参数错误');
		}
		$res = $this->obj->doUpdate(['status' => -1],['id' => $id]);
		if($res){
			$output = output(1,'操作成功');
		}else{
			$output = output(0,'操作失败');
		}
		return $output;
	}
}