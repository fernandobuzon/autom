<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    echo "Necess&aacute;rio autenticar-se!" . PHP_EOL;
    die();
}

require_once('class.php');

$back = 'home.php';

if (empty($_POST['step']))
{
    echo 'ERRO!<br>' . PHP_EOL;
    $msg = '&Eacute; necess&aacute;rio informar o "step" via POST.';
    notify::showMsg($msg,'danger',$back);
    die();
}

$step = $_POST['step'];
$environ = new environ($dbFile);

if ($step == 'conf')
{
    try
    {
        $environ->setConf();
    }
    catch (Exception $e)
    {
        echo 'ERRO!<br>' . PHP_EOL;
        $msg = $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }
    finally
    {
        echo 'OK.<br>';
    }
}
elseif ($step == 'threads')
{
    try
    {
        $environ->setThreads();
    }
    catch (Exception $e)
    {
        echo 'ERRO!<br>' . PHP_EOL;
        $msg = $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }
    finally
    {
        echo 'OK.<br>';
    }
}
elseif ($step == 'gallery')
{
    try
    {
        $environ->setGallery();
    }
    catch (Exception $e)
    {
        echo 'ERRO!<br>' . PHP_EOL;
        $msg = $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }
    finally
    {
        echo 'OK.<br>';
    }
}
elseif ($step == 'restart')
{
    try
    {
        $environ->restart();
    }
    catch (Exception $e)
    {
        echo 'ERRO!<br>' . PHP_EOL;
        $msg = $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }
    finally
    {
        echo 'OK.<br>';
    }
}

?>
