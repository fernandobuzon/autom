<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    echo "Necess&aacute;rio autenticar-se!" . PHP_EOL;
    die();
}

require_once('class.php');

if (empty($_POST['command']))
{
    $msg = '&Eacute; necess&aacute;rio informar o comando requerido via POST.' . PHP_EOL;
    echo $msg;
    die();
}

$command = $_POST['command'];

try
{
    $cmd = new environ($dbFile);

    if ($command == 'start')
    {
        $cmd = $cmd->start();
        echo 'started';
    }
    elseif ($command == 'stop')
    {
        $cmd = $cmd->stop();
        echo 'stoped';
    }
    elseif ($command == 'restart')
    {
        $cmd = $cmd->restart();
        echo 'restarted';
    }
}
catch (Exception $e)
{
    $msg .= $e->getMessage() . PHP_EOL;
    echo $msg;
}

?>
