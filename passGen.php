<?php
namespace Password\generated\passGen;

function passGenerated()
{
    $arrRequired = ['!','@','#','$','%','^','&','*'];
    $arrCapital = ['Q','W','E','R','T','Y','U','I','O','P','A','S','D','F','G','H','J','K','L','Z','X','C','V','B','N','M'];
    $arrStrokovi = ['q','w','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m'];
    $arrNumber = [1,2,3,4,5,6,7,8,9,0];

    $newPass = [...gen($arrRequired, $arrCapital, $arrStrokovi, $arrNumber), ...gen($arrRequired, $arrCapital, $arrStrokovi, $arrNumber)];
    shuffle($newPass);
    return (implode('',$newPass));
}

function gen(...$arr)
{
    return [ $arr[0][rand(0,7)], $arr[1][rand(0,24)], $arr[2][rand(0,24)], $arr[3][rand(0,9)] ];
}