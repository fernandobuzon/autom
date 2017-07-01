<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    echo "Necess&aacute;rio autenticar-se!" . PHP_EOL;
    die();
}

require_once('class.php');

if (is_numeric($_POST['id']))
{
    $id = $_POST['id'];
}
else
{
    die();
}

if ($id > 0)
{
    try
    {
        $cameras = new doors($dbFile);
        $cameras->setId($id);
        $cameras = $cameras->getCameras();
    }
    catch (Exception $e)
    {
        $msg = $e->getMessage();
        notify::showMsg($msg,'danger','faces.php');
        die(); 
    }

    echo $cameras;
}
?>
