<?php
//String Sanitization
function sanitizeString($var)
{
    //if(get_magic_quotes_gpc())
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = str_replace("'", '`', $var);
    return $var;
}
function sanitizeMySQL($connection, $var)
{
    $var = $connection->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}
/* function mysql_fix_string($dbconn, $string) {
        if(get_magic_quotes_gpc()) $string = stripslashes($string);
        return $dbconn->real_escape_string($string);
    } */
function generateAlphaNumericRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function generateUpperCapsAlphaNumericRandomString($length)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function generateNumericRandomString($length)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function generatePasswordRandomString($length)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// function to get string between two characters/markers
// source: http://stackoverflow.com/questions/5696412/ddg#9826656
function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
