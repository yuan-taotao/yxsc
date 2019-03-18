<?php
namespace app\admin\controller;

class Index extends Base
{
	//后台首页
    public function index()
    {
        return $this->fetch('',[

        ]);
    }

    //后台欢迎页
    public function welcome()
    {
    	return $this->fetch('',[

    	]);
    }
}
