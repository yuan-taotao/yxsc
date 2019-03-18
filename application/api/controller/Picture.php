<?php
namespace app\api\controller;
use think\Controller;

class Picture extends Controller
{
	//文件上传处理
	public function doUpload()
	{
		$file = request()->file('file');
		$res = $file->move('upload');
		if($res && $res->getPathname()){
			return output(1,'/'.$res->getPathname());
		}else{
			return output(0,'上传失败');
		}
	}

	//头像上传
	public function userUpload()
	{
		$file = request()->file('file');
		$res = $file->move('./upload/user');
		if($res && $res->getPathname()){
			return output(1,'/'.$res->getPathname());
		}else{
			return output(0,'上传失败');
		}
	}

	//推荐位上传
	public function featuredUpload()
	{
		$file = request()->file('file');
		$res = $file->move('./upload/featured');
		if($res && $res->getPathname()){
			return output(1,'/'.$res->getPathname());
		}else{
			return output(0,'上传失败');
		}
	}

}
