<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_oos1 = "localhost";
$database_oos1 = "oos1";
$username_oos1 = "root";
$password_oos1 = "";
$oos1 = mysql_pconnect($hostname_oos1, $username_oos1, $password_oos1) or trigger_error(mysql_error(),E_USER_ERROR); 
?>