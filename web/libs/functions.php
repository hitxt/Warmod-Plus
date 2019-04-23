<?php
    function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. ' th';
        else
            return $number." ".$ends[$number % 10];
    }
?>