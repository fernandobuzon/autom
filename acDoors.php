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
    if (empty($_POST['name']))
    {
        $msg = '&Eacute; necess&aacute;rio informar o par&acirc;metro "name" via POST.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $back = 'doors.php';
    $name = $_POST['name'];
    $cameras = $_POST['cameras'];

    try
    {
        $door = new doors($dbFile);
        $door->setName($name);
        $door->add();

        $id = $door->findId();
        $door->setId($id);

        $door->setCameras($cameras);
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $msg = 'Porta ' . $name . ' adicionada com sucesso.';
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

    $back = 'doors.php';
    $id = $_POST['id'];

    try
    {
        $door = new doors($dbFile);
        $door->setId($id);
        $door->del();
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }
    
    $msg = 'Porta ' . $door->getName($id) . ' removida com sucesso.';
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
    $back = 'doors.php';
    $name = $_POST['name'];
    $cameras = $_POST['cameras'];

    try
    {
        $door = new doors($dbFile);
        $door->setId($id);
        $door->load();

        $door->setName($name);
        $door->setCameras($cameras);
        $door->save();

        $msg = 'Dados atualizados com sucesso.';
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
