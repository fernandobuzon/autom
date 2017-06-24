<?php

require('class.php');

if (is_numeric($_GET['id']))
{
    $title = 'Editar porta';
    $label = 'Salvar';
    $func = 'edit(' . $_GET['id'] . ')';

    $door = new doors($dbFile);
    $door->SetId($_GET['id']);
    $door->load();

    $name = $door->getName();
    $id = $_GET['id'];
}
else
{
    $title = 'Adicionar porta';
    $label = 'Adicionar';
    $func = 'add()';

    $id = 0;
}

?>

<div class="row">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><?php echo $title; ?></h3>
    </div>
    <div class="panel-body">
      <form id="form1">
        <div class="form-group">
          <label for="name">Nome</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="Nome"
          <?php if(isset($name)) echo ' value="' . $name . '"'; ?>>
        </div>


<?php

$cameras = new cameras($dbFile);
$cameras = $cameras->getAll();

if (empty($cameras))
{
    echo '<h3>Nenhuma c&acirc;mera cadastrada.</h3>';
}
else
{
    foreach ($cameras as $camera)
    {
        echo '<div class="form-check has-success">';
        echo '    <label class="form-check-label">';
        echo '        <input type="checkbox" class="form-check-input" name="camera' . $camera['id'] . '"  id="camera' . $camera['id'] . '" value="' . $camera['id'] . '">';
        echo '        &nbsp;' . $camera['name'];
        echo '    </label>';
        echo '</div>';
    }
}

?>

        <div class="form-group">
          <button type="button" class="btn btn-default" id="btnAdd" name="btnAdd" onclick="<?php echo $func; ?>"><?php echo $label; ?></button>&nbsp;
          <button type="button" class="btn btn-default" id="btnCancel" name="btnCancel" onclick="populate('doors.php')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>

function add()
{
    var comp;
    var cameras;
    var formc = $('#form1 *').filter(':checkbox');
    for (var i = 0, l = formc.length; i < l; i++) {
        comp = '#' + formc[i].name;
        if ($(comp).is(":checked")) {
            if ( i == 0 ) {
                cameras = formc[i].name;
            } else {
                cameras += ':' + formc[i].name;
            };
        };
    }

    var form;
    form = new FormData();
    form.append('action', 'add');
    form.append('name', $('#name').val());
    form.append('cameras', cameras);

    $.ajax({
        url: 'acDoors.php',
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

function edit(id)
{
    var comp;
    var cameras;
    var formc = $('#form1 *').filter(':checkbox');
    for (var i = 0, l = formc.length; i < l; i++) {
        comp = '#' + formc[i].name;
        if ($(comp).is(":checked")) {
            if ( i == 0 ) {
                cameras = formc[i].name;
            } else {
                cameras += ':' + formc[i].name;
            };
        };
    }

    var form;
    form = new FormData();
    form.append('action', 'edit');
    form.append('id', id);
    form.append('name', $('#name').val());
    form.append('cameras', cameras);

    $.ajax({
        url: 'acDoors.php',
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

var form;
form = new FormData();

form.append('id', '<?php echo $id; ?>');

$.ajax({
  url: 'getCameras.php',
  data: form,
  processData: false,
  contentType: false,
  type: 'POST',
  cache: false,
  success: function (data) {

    var arr = data.split(':');

    arr.forEach(function(entry) {
        var comp = '#camera' + entry;
        $(comp).prop('checked', true);
    });
  }
});

</script>
