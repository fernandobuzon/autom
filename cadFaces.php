<style>
.hide-bullets {
  list-style:none;
  margin-left: -40px;
  margin-top:20px;
}

.thumbnail {
  padding: 0;
}

.carousel-inner>.item>img, .carousel-inner>.item>a>img {
  width: 100%;
}

</style>

<?php

require_once('class.php');

if (is_numeric($_GET['id']))
{
    $id = $_GET['id'];

    $title = 'Editar permiss&otilde;es';
    $label = 'Salvar';
    $func = 'edit(' . $_GET['id'] . ')';

    $faces = new faces($dbFile);
    $faces = $faces->getAll();
}
else
{
    $title = 'Adicionar face';
    $label = 'Adicionar';
    $func = 'add()';
}

?>

<div class="container">
    <div id="main_area">
        <!-- Slider -->
        <div class="row">
            <div class="col-sm-6">
                <div class="col-xs-12" id="slider">
                    <!-- Top part of the slider -->
                    <div class="row">
                        <div class="col-sm-12" id="carousel-bounding-box">
                            <div class="carousel slide" id="myCarousel">
                                <!-- Carousel items -->
                                <div class="carousel-inner">

<?php

if (is_numeric($_GET['id']))
{
    foreach($faces as $f)
    {
        if ($f['id'] == $id)
        {
            echo '<div class="active item" data-slide-number="' . $f['id'] . '">';
            echo '  <img src="img.php?id=' . $f['id'] . '&class=faces">';
            echo '</div>';
        }
        else
        {
            echo '<div class="item" data-slide-number="' . $f['id'] . '">';
            echo '  <img src="img.php?id=' . $f['id'] . '&class=faces">';
            echo '</div>';
        }

        echo '<div id="carousel-text" name="carousel-text" class="carousel-caption" style="top: 0; bottom: auto;">';
        echo '</div>';
    }
}
else
{
    echo '<div class="active item" data-slide-number="0">';
    echo '  <img id="newimage" class="faces" src="">';
    echo '</div>';
}

?>

                                </div>
                                <!-- Carousel nav -->
                                <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                </a>
                                <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right panel -->
            <div class="col-sm-6" id="slider-thumbs">
                <!-- Bottom switcher of slider -->
                <div class="row"></div>
                <ul class="hide-bullets">
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><?php echo $title; ?></h3>
                            </div>
                            <div class="panel-body">
                                <form name="form1" id="form1">
                                    <div class="form-group">
                                        <label for="name">Nome</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Nome completo">
                                    </div>

<?php

$doors = new doors($dbFile);
$doors = $doors->getAll();

if (empty($doors))
{
    echo '<h3>Nenhuma porta cadastrada.</h3>';
}
else
{
    echo '<label>Portas permitidas:</label>';

    foreach ($doors as $door)
    {
        echo '<div class="form-check has-success">';
        echo '    <label class="form-check-label">';
        echo '        <input type="checkbox" class="form-check-input" name="door' . $door['id'] . '"  id="door' . $door['id'] . '" value="' . $door['id'] . '">';
        echo '        &nbsp;' . $door['name'];
        echo '    </label>';
        echo '</div>';
    }
}

?>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-default" id="btnSave" name="btnSave" onclick="<?php echo $func; ?>"><?php echo $label; ?></button>
                                        <button type="button" class="btn btn-default" id="btnCancel" name="btnCancel" onclick="populate('faces.php')">Voltar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>

var id = $('.item.active').data('slide-number');
var form;
form = new FormData();
form.append('id', id);

$.ajax({
  url: 'getPermissions.php',
  data: form,
  processData: false,
  contentType: false,
  type: 'POST',
  cache: false,
  success: function (data) {

    var arr = data.split(':');
    var name = arr[0];
    delete arr[0];

    $('#carousel-text').html("");
    $('#carousel-text').append('<h1>' + name + '</h1>');
    $('#name').val(name);

    arr.forEach(function(entry) {
        var comp = '#door' + entry;
        $(comp).prop('checked', true);
    });
  }
});

jQuery(document).ready(function($) {
  $('#myCarousel').carousel({
    interval: false
  });
 
  //Handles the carousel thumbnails
  $('[id^=carousel-selector-]').click(function () {
    var id_selector = $(this).attr("id");
    try {
      var id = /-(\d+)$/.exec(id_selector)[1];
      console.log(id_selector, id);
      jQuery('#myCarousel').carousel(parseInt(id));
    } catch (e) {
      console.log('Regex failed!', e);
    }
  });
  // When the carousel slides, auto update the text
  $('#myCarousel').on('slid.bs.carousel', function (e) {
    var id = $('.item.active').data('slide-number');

    var form;
    form = new FormData();
    form.append('id', id);

    $.ajax({
      url: 'getPermissions.php',
      data: form,
      processData: false,
      contentType: false,
      type: 'POST',
      cache: false,
      success: function (data) {

        var arr = data.split(':');
        var name = arr[0];
        delete arr[0];

        $('#carousel-text').html("");
        $('#carousel-text').append('<h1>' + name + '</h1>');
        $('#name').val(name);

        var comp;
        var formc = $('#form1 *').filter(':checkbox');
        for (var i = 0, l = formc.length; i < l; i++) {
            comp = '#' + formc[i].name;
            $(comp).prop('checked', false);
        }

        arr.forEach(function(entry) {
            var comp = '#door' + entry;
            $(comp).prop('checked', true);
        });
      }
    });
  });
});

function edit(id)
{
    var comp;
    var permissions;
    var formc = $('#form1 *').filter(':checkbox');
    for (var i = 0, l = formc.length; i < l; i++) {
        comp = '#' + formc[i].name;
        if ($(comp).is(":checked")) {
            if ( i == 0 ) {
                permissions = formc[i].name;
            } else {
                permissions += ':' + formc[i].name;
            };
        };
    }

    var form;
    form = new FormData();
    form.append('action', 'edit');
    form.append('id', id);
    form.append('name', $('#name').val());
    form.append('permissions', permissions);

    $.ajax({
        url: 'acFaces.php',
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

function add()
{
    var comp;
    var permissions;
    var formc = $('#form1 *').filter(':checkbox');
    for (var i = 0, l = formc.length; i < l; i++) {
        comp = '#' + formc[i].name;
        if ($(comp).is(":checked")) {
            if ( i == 0 ) {
                permissions = formc[i].name;
            } else {
                permissions += ':' + formc[i].name;
            };
        };
    }

    var form;
    form = new FormData();
    form.append('action', 'add');
    form.append('name', $('#name').val());
    form.append('permissions', permissions);
    form.append('image',$('#transfer').val());

    $.ajax({
        url: 'acFaces.php',
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
            url: 'acFaces.php',
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

document.getElementById('newimage').setAttribute('src', $('#transfer').val());

</script>

