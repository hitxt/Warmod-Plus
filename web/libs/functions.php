<?php
    function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. ' th';
        else
            return $number." ".$ends[$number % 10];
    }

    function ceil_dec($v, $precision)
    {
        $c = pow(10, $precision);
        return ceil($v*$c)/$c;
    }
    
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