<?php
// http://xyz.cinc.biz/2013/02/php.html
//無條件進位
function ceil_dec($v, $precision)
{
    $c = pow(10, $precision);
    return ceil($v*$c)/$c;
}

//無條件捨去
function floor_dec($v, $precision)
{
    $c = pow(10, $precision);
    return floor($v*$c)/$c;
}

function random_password($length) 
{     
	$str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";    
	$password = substr(str_shuffle($str), 0, $length);    
	return $password;
}
?>