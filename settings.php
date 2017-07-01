<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    header ( 'location:login.php' );
}

require_once('class.php');

$list = new settings($dbFile);
$list = $list->getAll();

if (empty($list))
{
    echo '<div class="row">';
    echo '  <div class="page-header">';
    echo '    <h1>Nenhum par&acirc;metro localizado.</h1>';
    echo '  </div>';
    echo '</div>';
    die();
}

?>

<div class="row"> 
  <div class="panel panel-default"> 
    <div class="panel-heading"> 
      <h3 class="panel-title">Par&acirc;metros</h3> 
    </div> 
    <div class="panel-body">
      <form id="form1" name="form1">

<?php

foreach($list as $setting)
{
    echo '<div class="form-group">';
    echo '  <label for="' . $setting['setting'] . '">' . $setting['setting'] . '</label>';
    echo '  <input class="form-control" name="' . $setting['setting'] . '" type="text" value="' . $setting['value'] . '" id="' . $setting['setting'] . '">';
    echo '</div>';
}
?>
        <div class="form-group">
          <button type="button" class="btn btn-default" id="btnAdd" name="btnAdd" onclick="save();">Salvar</button>&nbsp;
          <button type="button" class="btn btn-default" id="btnCancel" name="btnCancel" onclick="populate('home.php')">Cancelar</button>
        </div>
      </form> 
    </div>
  </div> 
</div>

<script>

function save()
{
    var form;
    form = $('#form1').serialize();

    $.ajax({
        url: 'acSettings.php',
        data: form,
        type: 'POST',
        cache: false,
        success: function (data) {
            $('#details').html("");
            $('#pageBox').html("");
            $('#pageBox').append(data);
        }
    });
};

</script>
