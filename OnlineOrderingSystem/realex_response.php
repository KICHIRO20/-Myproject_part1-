<?php

// direct access to this page will be redirected to index.php
if(!isset($_POST['MD5HASH'])){
	header("HTTP/1.1 302 Moved Temporarily");
	header("Location: index.php");
	die;
}

// this text will be displayed on Realex server after successful payment.
echo "Your order is placed. Order ID: ". $_POST['ORDER_ID'];
echo "<br>Order details have been sent to your e-mail address.";

// Checks if https is available
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
    $protocol="https";
}
else{
	$protocol="http";
}

// redirect to checkout.php
$response_page= $protocol . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$checkout_page=str_replace('realex_response.php','checkout.php',$response_page);
require("avactis-extensions/payment_module_realex_cc/includes/realex_form.php");
?>