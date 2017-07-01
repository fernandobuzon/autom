<?php
session_start ();

if (empty($_POST['login']) || empty($_POST['passwd']))
{
    echo 'Login ou senha nulos';
    die();
}

require_once('class.php');

$login = $_POST['login'];
$passwd = $_POST['passwd'];

try
{
    $check = new users($dbFile);
    $id = $check->findId($login);
    $check->setId($id);
    $check->load();

    $result = $check->checkLogin($passwd);

    if (empty($result))
    {
        $_SESSION['level'] = 1;
        $_SESSION['login'] = $check->getLogin();

        echo 'OK';
    }
    else
    {
        echo 'Login e senha inconsistentes';
    }
}
catch (Exception $e)
{
    $msg = $e->getMessage();
    echo $msg;
}

?>
