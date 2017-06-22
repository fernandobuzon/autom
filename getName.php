<?php

require_once('class.php');

if (is_numeric($_POST['id']))
{
    $id = $_POST['id'];
}
else
{
    die();
}

try
{
    $name = new faces($dbFile);
    $name->setId($id);
    $name->load();
    $name = $name->getName();
}
catch (Exception $e)
{
    $msg = $e->getMessage();
    notify::showMsg($msg,'danger','faces.php');
    die(); 
}

echo $name;
?>
