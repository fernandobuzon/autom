<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    echo "Necess&aacute;rio autenticar-se!" . PHP_EOL;
    die();
}

require_once('class.php');

$back = 'cameras.php';

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
    if (empty($_POST['name']) || empty($_POST['netcam_url']))
    {
        $msg = '&Eacute; necess&aacute;rio informar os par&acirc;metros "name" e "netcam_url" via POST.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    if (isset($_POST['netcam_keepalive']) && $_POST['netcam_keepalive'] == 1)
    {
        $netcam_keepalive = 1;
    }
    else
    {
        $netcam_keepalive = 0;
    }

    if (isset($_POST['auto_brightness']) && $_POST['auto_brightness'] == 1)
    {
        $auto_brightness = 1;
    }
    else
    {
        $auto_brightness = 0;
    }

    try
    {
        $camera = new cameras($dbFile);
        $camera->setName($_POST['name']);
        $camera->setNetcam_url($_POST['netcam_url']);
        $camera->setNetcam_userpass($_POST['netcam_userpass']);
        $camera->setV4l2_palette($_POST['v4l2_palette']);
        $camera->setNorm($_POST['norm']);
        $camera->setWidth($_POST['width']);
        $camera->setHeight($_POST['height']);
        $camera->setFramerate($_POST['framerate']);
        $camera->setMinimum_frame_time($_POST['minimum_frame_time']);
        $camera->setNetcam_keepalive($netcam_keepalive);
        $camera->setAuto_brightness($auto_brightness);
        $camera->setBrightness($_POST['brightness']);
        $camera->setContrast($_POST['contrast']);
        $camera->setSaturation($_POST['saturation']);
        $camera->setHue($_POST['hue']);
        $camera->add();
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $msg = 'C&acirc;mera ' . $name . ' adicionada com sucesso.';
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

    $id = $_POST['id'];

    try
    {
        $camera = new cameras($dbFile);
        $camera->setId($id);
        $camera->del();
    }
    catch (Exception $e)
    {
        $msg .= $e->getMessage();
        notify::showMsg($msg,'danger',$back);
        die();
    }
    
    $msg = 'C&acirc;mera ' . $camera->getName($id) . ' removida com sucesso.';
    notify::showMsg($msg,'success',$back);
    die();
}
elseif ($action == 'edit')
{
    if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['netcam_url']))
    {
        $msg = '&Eacute; necess&aacute;rio informar os par&acirc;metros "id", "name" e "netcam_url" via POST.';
        notify::showMsg($msg,'danger',$back);
        die();
    }

    $id = $_POST['id'];

    if (isset($_POST['netcam_keepalive']) && $_POST['netcam_keepalive'] == 1)
    {
        $netcam_keepalive = 1;
    }
    else
    {
        $netcam_keepalive = 0;
    }

    if (isset($_POST['auto_brightness']) && $_POST['auto_brightness'] == 1)
    {   
        $auto_brightness = 1;
    }
    else
    {   
        $auto_brightness = 0;
    }

    try
    {
        $camera = new cameras($dbFile);
        $camera->setId($id);
        $camera->load();

        $camera->setName($_POST['name']);
        $camera->setNetcam_url($_POST['netcam_url']);
        $camera->setNetcam_userpass($_POST['netcam_userpass']);
        $camera->setV4l2_palette($_POST['v4l2_palette']);
        $camera->setNorm($_POST['norm']);
        $camera->setWidth($_POST['width']);
        $camera->setHeight($_POST['height']);
        $camera->setFramerate($_POST['framerate']);
        $camera->setMinimum_frame_time($_POST['minimum_frame_time']);
        $camera->setNetcam_keepalive($netcam_keepalive);
        $camera->setAuto_brightness($auto_brightness);
        $camera->setBrightness($_POST['brightness']);
        $camera->setContrast($_POST['contrast']);
        $camera->setSaturation($_POST['saturation']);
        $camera->setHue($_POST['hue']);
        $camera->save();

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
