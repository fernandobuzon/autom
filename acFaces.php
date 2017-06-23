<?php

require_once('class.php');

if (empty($_POST['action']))
{
    $msg = '&Eacute; necess&aacute;rio informar o par&acirc;metro "action" via POST.';
    notify::showMsg($msg,'danger',$back);
    die();
}
else
{
    $action = $_POST['action'];
}

if ($action == 'add')
{
    if (empty($_POST['name']) || empty($_POST['image']))
    {
        $msg = '&Eacute; necess&aacute;rio informar os par&acirc;metros "name" e "image" via POST.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $back = 'faces.php';
    $name = $_POST['name'];
    $image = $_POST['image'];
    $permissions = $_POST['permissions'];

    try
    {
        $face = new faces($dbFile);
        $face->setName($name);
        $face->setImg($image);
        $face->add();

        $id = $face->findId();
        $face->setId($id);

        $face->setPermissions($permissions);
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $msg = 'Face ' . $name . ' adicionada com sucesso.';
    notify::showMsg($msg,'success',$back);
    die();
}
elseif ($action == 'del')
{
    if (empty($_POST['id']))
    {
        $msg = '&Eacute; necess&aacute;rio informar o par&acirc;metro "id" via POST.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $back = 'faces.php';
    $id = $_POST['id'];

    try
    {
        $face = new faces($dbFile);
        $face->setId($id);
        $face->del();
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }
    
    $msg = 'Face ' . $face->getName($id) . ' removida com sucesso.';
    notify::showMsg($msg,'success',$back);
    die();
}
elseif ($action == 'edit')
{
    if (empty($_POST['id']) || empty($_POST['name']))
    {
        $msg = '&Eacute; necess&aacute;rio informar os par&acirc;metros "id" e "name" via POST.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $id = $_POST['id'];
    $back = 'cadFaces.php?id=' . $id;
    $name = $_POST['name'];
    $permissions = $_POST['permissions'];

    try
    {
        $face = new faces($dbFile);
        $face->setId($id);
        $face->load();

        $face->setName($name);
        $face->setPermissions($permissions);
        $face->save();

        $msg = 'Permiss&otilde;es atualizadas com sucesso.';
        notify::showMsg($msg,'success',$back);
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die(); 
    }
}
?>
