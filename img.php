<?php

if (empty($_GET['id']) || empty($_GET['class']))
{
    echo 'Especifique "id" e "class" via POST.';
    die();
}
else
{
    $id = $_GET['id'];
    $class = $_GET['class'];
}

require_once('class.php');

if ($class == 'faces')
{
    $img = new faces($dbFile);
}
elseif ($class == 'logs')
{
    $img = new logs($dbFile);
}
else
{
    echo 'Classe ' . $classe . ' inv&aacute;lida.';
    die();
}

$img->setId($id);
$img->load();

header('Content-Type: image/jpeg');
echo $img->getImg();

?>
