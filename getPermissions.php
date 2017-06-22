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

if ($id > 0)
{
    try
    {
        $name = new faces($dbFile);
        $name->setId($id);
        $name->load();
        $name = $name->getName();

        $permissions = new faces($dbFile);
        $permissions->setId($id);
        $permissions = $permissions->getPermissions();
    }
    catch (Exception $e)
    {
        $msg = $e->getMessage();
        notify::showMsg($msg,'danger','faces.php');
        die(); 
    }

    if (empty($permissions))
    {
        echo $name;
    }
    else
    {
        echo $name . ':' . $permissions;
    }
}
?>
