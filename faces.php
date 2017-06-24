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

$faces = new faces($dbFile);
$faces = $faces->getAll();

if(empty($faces))
{
    echo '<div class="row">';
    echo '<p><button type="button" class="btn btn-default" id="btnAdd" name="btnAdd" onclick="populate(\'cam.php\');">Nova face</button>';
    echo '</div>';

    echo '<div class="row">';
    echo '  <div class="page-header">';
    echo '    <h1>Nenhuma face cadastrada.</h1>';
    echo '  </div>';
    echo '</div>';
    die();
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

$i = 0;
foreach($faces as $f)
{
    if ($i == 0)
    {
        echo '<div class="active item" data-slide-number="' . $f['id'] . '">';
        echo '    <img src="img.php?id=' . $f['id'] . '&class=faces"></div>';
    }
    else
    {
        echo '<div class="item" data-slide-number="' . $f['id'] . '">';
        echo '    <img src="img.php?id=' . $f['id'] . '&class=faces"></div>';
    }

    echo '<div id="carousel-text" name="carousel-text" class="carousel-caption" style="top: 0; bottom: auto;">';
    echo '</div>';
    echo '<div id="carousel-btn" name="carousel-btn" class="carousel-caption">';
    echo '</div>';

    $i++;
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
            <!--/Slider-->
            <div class="col-sm-6" id="slider-thumbs">
                <!-- Bottom switcher of slider -->
                <div class="row">
                    <center><button type="button" class="btn btn-default" id="btnAdd" name="btnAdd" onclick="populate('cam.php');">Nova Face</button></center>
                </div>
                <ul class="hide-bullets">

<?php

$i = 0;
foreach($faces as $f)
{
    echo '<li class="col-sm-3">';
    echo '    <a class="thumbnail" id="carousel-selector-' . $i . '"><img src="img.php?id=' . $f['id'] . '&class=faces"></a>';
    echo '</li>';
    $i++;
}

?>

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
  url: 'getName.php',
  data: form,
  processData: false,
  contentType: false,
  type: 'POST',
  cache: false,
  success: function (data) {
    $('#carousel-text').html("");
    $('#carousel-text').append('<h1>' + data + '</h1>');
    $('#carousel-btn').html("");
    $('#carousel-btn').append('<div class="btn-group row-fluid btn-group-justified">');
    $('#carousel-btn').append('  <button type="button" class="btn btn-lg btn-default" onclick="populate(\'cadFaces.php?id=' + id +  '\')">Editar</button>');
    $('#carousel-btn').append('  <button type="button" class="btn btn-lg btn-danger" onclick="del(' + id + ')">Excluir</button>');
    $('#carousel-btn').append('</div>');
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
      url: 'getName.php',
      data: form,
      processData: false,
      contentType: false,
      type: 'POST',
      cache: false,
      success: function (data) {
        $('#carousel-text').html("");
        $('#carousel-text').append('<h1>' + data + '</h1>');
        $('#carousel-btn').html("");
        $('#carousel-btn').append('<div class="btn-group row-fluid btn-group-justified">');
        $('#carousel-btn').append('  <button type="button" class="btn btn-lg btn-default" onclick="populate(\'cadFaces.php?id=' + id +  '\')">Editar</button>');
        $('#carousel-btn').append('  <button type="button" class="btn btn-lg btn-danger" onclick="del(' + id + ')">Excluir</button>');
        $('#carousel-btn').append('</div>');
      }
    });
  });
});

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

</script>

