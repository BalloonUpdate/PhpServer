<?php

function httpHeaders()
{
    foreach ($_SERVER as $name => $value)
    {
        if (substr($name, 0, 5) == 'HTTP_')
        {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

$headers = httpHeaders();
$origin = isset($headers['Origin'])? $headers['Origin']:NULL;

if($origin != null)
    header('Access-Control-Allow-Origin: '.$origin);

header("Access-Control-Allow-Methods: ".$_SERVER['REQUEST_METHOD']);

echo file_get_contents('anno.txt');

?>