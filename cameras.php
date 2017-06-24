<div class="row">
  <p><button type="button" class="btn btn-default" id="btnAdd" name="btnAdd" onclick="populate('cadCameras.php');">Nova c&acirc;mera</button>
</div>

<?php
require_once('class.php');

$cameras = new cameras($dbFile);
$result = $cameras->getAll();

if (empty($result))
{
    echo '<div class="row">';
    echo '  <div class="page-header">';
    echo '    <h1>Nenhuma c&acirc;mera cadastrada.</h1>';
    echo '  </div>';
    echo '</div>';
}
else
{
    echo '<div class="row">';
    echo '  <div class="panel panel-default">';
    echo '    <div class="panel-heading">';
    echo '      <h3 class="panel-title">C&acirc;meras cadastradas</h3>';
    echo '    </div>';
    echo '    <div class="panel-body">';
    echo '      <table class="table table-striped">';
    echo '        <thead>';
    echo '          <tr>';
    echo '            <th>#</th>';
    echo '            <th>Nome</th>';
    echo '            <th>URL</th>';
    echo '            <th>Op&ccedil;&otilde;es</th>';
    echo '          </tr>';
    echo '        </thead>';
    echo '        <tbody>';

    foreach ($result as $row)
    {
        echo '      <tr>';
        echo '        <td>' . $row['id'] . '</td>';
        echo '        <td>' . $row['name'] . '</td>';
        echo '        <td>' . $row['netcam_url'] . '</td>';
        echo '        <td><button type="button" class="btn btn-xs btn-default" onclick="populate(\'cadCameras.php?id=' . $row['id'] . '\')">Editar</button>&nbsp;';
        echo '            <button type="button" class="btn btn-xs btn-danger" onclick="del(' . $row['id'] . ')">Excluir</button></td>';
        echo '      </tr>';
    }

    echo '        </tbody>';
    echo '      </table>';
    echo '    </div>';
    echo '  </div>';
    echo '</div>';
}

?>

<script>

function del(id)
{
    var r = confirm("Tem certeza que deseja remover o id nr: " + id);
    if (r == true)
    {
        var form;
        form = new FormData();
        form.append('action', 'del');
        form.append('id', id);

        $.ajax({
            url: 'acCameras.php',
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
    }
};

</script>
