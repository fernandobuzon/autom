<style>
a.list-group-item {
    height:auto;
    min-height:220px;
}
a.list-group-item.active small {
    color:#fff;
}
.stars {
    margin:20px auto 1px;    
}
</style>

<?php

require_once('class.php');

$logs = new logs($dbFile);
$logs = $logs->getAll();

if(empty($logs))
{
    echo '<div class="row">';
    echo '  <div class="page-header">';
    echo '    <h1>Nenhuma atividade encontrada.</h1>';
    echo '  </div>';
    echo '</div>';
    die();
}

?>

<div class="container">
    <div class="row">
        <div class="well">
            <h1 class="text-center">&Uacute;ltimos registros&nbsp;<a href="#" onclick="populate('logs.php')"><span class="glyphicon glyphicon-refresh"></span></a></h1>
            <div class="list-group">

<?php

foreach($logs as $l)
{

?>

                <a href="#" class="list-group-item">
                <div class="media col-md-3">
                    <figure class="pull-left">
                        <img class="media-object img-rounded img-responsive"  src="img.php?id=<?php echo $l['id']; ?>&class=logs" alt="<?php echo $l['name']; ?>" >
                    </figure>
                </div>
                <div class="col-md-9 text-center">
                    <h2><b><?php echo $l['name']; ?></b></h2>
                    <h3><?php echo $l['date']; ?></h3>
                    <h4><span class="badge badge-info"><?php echo $l['door']; ?></span>&nbsp;<span class="badge badge-info"><?php echo $l['camera']; ?></span>&nbsp;<span class="badge badge-info"><?php echo $l['match']; ?></span></h4>
                </div>
                </a>

<?php

}
?>

            </div>
        </div>
    </div>
</div>
