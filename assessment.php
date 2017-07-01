<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    echo "Necess&aacute;rio autenticar-se!" . PHP_EOL;
    die();
}

require_once('class.php');

$assessment = new environ($dbFile);
$assessment = $assessment->assessment($_POST['image']);

if (floatval($assessment) == floatval('-3.40282e+38'))
{
    echo $assessment;
}

?>
