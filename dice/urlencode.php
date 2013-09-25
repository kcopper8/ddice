<?
$now_uri_arr = explode("/", $_SERVER['REQUEST_URI']);
array_pop($now_uri_arr);

echo implode("/", $now_uri_arr);

?>