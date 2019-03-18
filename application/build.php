<?php 
return [
	// //后台模块
	// 'admin' => [
	// 	'__dir__' => ['controller','view'],
	// 	'controller' => ['Base','Index','Admin','Category','User','Role','Auth','Order','Coupon','Featured'],
	// 	'view' => ['index/index','admin/list','category/list','user/list','role/list','auth/list','order/list','coupon/list','featured/list'],
	// ],
	// //接口模块
	// 'api' => [
	// 	'__dir__' => ['controller'],
	// 	'controller' => ['Index'],
	// ],
	// //公共模块
	// 'common' => [
	// 	'__dir__' => ['controller','model'],
	// 	'controller' => ['Index'],
	// 	'model' => ['Base','Admin','Category','User','Role','Auth','adminRole','roleAuth','Coupon','Featured','Order'], 
	// ],
	//前台模块
	'index' => [
		'__dir__' => ['controller','view'],
		'controller' => ['Base','Index','User','Login','Regist','Goods'],
		'view' => ['index/index','goods/list','login/login','regist/regist'],

	],

];


?>