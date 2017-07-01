<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    echo "Necess&aacute;rio autenticar-se!" . PHP_EOL;
    die();
}

require_once('class.php');

$back = 'motion.php';

if (empty($_POST['setting']) || empty($_POST['value']))
{
    $msg = '&Eacute; necess&aacute;rio informar os par&acirc;metros "setting" e "value" via POST.';
    notify::showMsg($msg,'danger',$back);
    die();
}

$setting = $_POST['setting'];
$value = $_POST['value'];

try
{
    $set = new settings($dbFile);
    $set->setValue($setting,$value);
}
catch (Exception $e)
{
    $msg .= $e->getMessage();
    notify::showMsg($msg,'danger',$back);
    die();
}

$msg = 'Dados atualizados com sucesso.';
notify::showMsg($msg,'success',$back);

?>
