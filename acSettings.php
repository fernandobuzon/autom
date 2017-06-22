<?php

require_once('class.php');

$back = 'settings.php';

//if (empty($_POST['setting']) || empty($_POST['value']))
if (empty($_POST))
{
    $msg = '&Eacute; necess&aacute;rio informar os par&acirc;metros "setting" e "value" via POST.';
    notify::showMsg($msg,'danger',$back);
    die();
}

try
{
    $set = new settings($dbFile);

    foreach($_POST as $key => $value)
    {
        if ($key == 'page')
        {
            continue;
        }
        else
        {
            $set->setValue($key,$value);
        }
    }
}
catch (Exception $e)
{
    $msg .= $e->getMessage();
    notify::showMsg($msg,'danger',$back);
    die();
}

$msg = 'Dados atualizados com sucesso.';
notify::showMsg($msg,'success',$back);
die();

?>
