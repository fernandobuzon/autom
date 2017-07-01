<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    header ( 'location:login.php' );
}

require_once('class.php');

$settings = new settings($dbFile);
$value = $settings->getValue('conf_value');
?>

<div class="row"> 
  <div class="panel panel-default"> 
    <div class="panel-heading"> 
      <h3 class="panel-title">Motor</h3> 
    </div> 
    <div class="panel-body">
      <form id="form1"> 
        <div class="form-group">
          <label for="value">Configura&ccedil;&atilde;o padr&atilde;o</label>
          <textarea class="form-control" id="value" rows="30"><?php echo $value; ?></textarea>
        </div>
        <div class="form-group">
          <p><button type="button" class="btn btn-default" id="btnAdd" name="btnAdd" onclick="save()">Salvar&nbsp;</button>
          <button type="button" class="btn btn-default" onclick="populate('home.php');">Voltar</button>
        </div>
      </form> 
    </div>
  </div> 
</div>

<script>

function save()
{
    var form;
    form = new FormData();
    form.append('setting', 'conf_value');
    form.append('value', $('#value').val());
    form.append('page', 'motion.php');

    $.ajax({
        url: 'acMotion.php',
        data: form,
        processData: false,
        contentType: false,
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
