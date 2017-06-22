<?php

require_once('class.php');

$back = 'users.php';

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
    if (empty($_POST['name']) || empty($_POST['login']))
    {
        $msg = 'Os par&acirc;metros "name" e "login" n&atilde;o podem ser nulos.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $name = $_POST['name'];
    $login = $_POST['login'];
    $passwd = $_POST['passwd'];
    $passwd2 = $_POST['passwd2'];

    if ($passwd != $passwd2)
    {
        $msg = 'A senha e a confirma&ccedil;&atilde;o de senha não conferem.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    try
    {
        $user = new users($dbFile);
        $user->setName($name);
        $user->setLogin($login);
        $user->setPasswd($passwd);
        $user->add();
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $msg = 'Usu&aacute;rio ' . $name . ' adicionado com sucesso.';
    notify::showMsg($msg,'success',$back);
    die();
}
elseif ($action == 'del')
{
    if (empty($_POST['id']))
    {
        $msg = 'O par&acirc;metro "id" n&atilde;o pode ser nulo.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $id = $_POST['id'];

    try
    {
        $user = new users($dbFile);
        $user->setId($id);
        $user->load();
        $user->del();
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }
    
    $msg = 'Usu&aacute;rio ' . $user->getName() . ' removido com sucesso.';
    notify::showMsg($msg,'success',$back);
    die();
}
elseif ($action == 'edit')
{
    if (empty($_POST['name']) || empty($_POST['login']))
    {
        $msg = 'Os par&acirc;metros "name" e "login" n&atilde;o podem ser nulos.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $login = $_POST['login'];
    $passwd = $_POST['passwd'];
    $passwd2 = $_POST['passwd2'];

    if (!empty($passwd) && $passwd != $passwd2)
    {
        $msg = 'A senha e a confirma&ccedil;&atilde;o de senha não conferem.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    try
    {
        $user = new users($dbFile);
        $user->setId($id);
        $user->load();

        if (!empty($passwd))
        {
            $user->setPasswd($passwd);
            $user->setName($name);
            $user->setLogin($login);
            $user->save();

            $msg = 'Usu&aacute;rio modificado com sucesso. Senha atualizada.';
            notify::showMsg($msg,'success',$back);
        }
        else
        {
            if ($user->getName() != $name || $user->getLogin() != $login)
            {
                $user->setName($name);
                $user->setLogin($login);
                $user->save();

                $msg = 'Usu&aacute;rio modificado com sucesso (Senha atual mantida).';
                notify::showMsg($msg,'success',$back);
            }
            else
            {
                $msg = 'Nenhuma modifica&ccedil;&atilde;o detectada.';
                notify::showMsg($msg,'success',$back);
            }
        }
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die(); 
    }
}
?>
