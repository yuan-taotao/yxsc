<?php
//配置文件
return [
	// 错误页面模板 
    'dispatch_error_tmpl'    => APP_PATH . 'tpl' . DS . 'my_default_jump.tpl',
    //自定义404页面
    'http_exception_template'    =>  [
        404 =>  APP_PATH.'tpl'.DS.'404.html',
    ],
];