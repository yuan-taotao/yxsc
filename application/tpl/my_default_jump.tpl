<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>付款成功页面</title>
<link rel="stylesheet"  type="text/css" href="__STATIC__index/AmazeUI-2.4.2/assets/css/amazeui.css"/>
<link href="__STATIC__index/AmazeUI-2.4.2/assets/css/admin.css" rel="stylesheet" type="text/css">
<link href="__STATIC__index/basic/css/demo.css" rel="stylesheet" type="text/css" />

<link href="__STATIC__index/css/sustyle2.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="__STATIC__index/basic/js/jquery-1.7.min.js"></script>

</head>

<body>


{include file="public/header"}


<div class="take-delivery">
 <div class="status">
 	<?php switch ($code) {?>
        <?php case 1:?>
        <h2><?php echo(strip_tags($msg));?></h2>
        <?php break;?>
        <?php case 0:?>
        <h2><?php echo(strip_tags($msg));?></h2>
        <?php break;?>
    <?php } ?>
  </div>
</div>


<div class="footer" >
 <div class="footer-hd">
 <p>
 <a href="#">商城首页</a>
 <b>|</b>
 <a href="#">支付宝</a>
 <b>|</b>
 <a href="#">物流</a>
 </p>
 </div>
 <div class="footer-bd">
 <p>
 <a href="#">合作伙伴</a>
 <a href="#">联系我们</a>
 <a href="#">网站地图</a>
 </p>
 </div>
</div>


</body>
</html>
