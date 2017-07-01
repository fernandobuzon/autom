<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    header ( 'location:login.php' );
}

?>

<div class="jumbotron"> 
    <h1>Bem vindo!</h1>
    <p>Sistema de reconhecimento facial para automa&ccedil;&atilde;o residencial.</p> 
</div>
