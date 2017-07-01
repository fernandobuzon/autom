<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    echo "Necess&aacute;rio autenticar-se!" . PHP_EOL;
    die();
}

require_once('class.php');

try
{
    $logout = new users($dbFile);
    $logout->logout();
}
catch (Exception $e)
{
    $msg = $e->getMessage();
    echo $msg;
}

?>
