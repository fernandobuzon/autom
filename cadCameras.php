<?php

require('class.php');

if (is_numeric($_GET['id']))
{
    $title = 'Editar c&acirc;mera';
    $label = 'Salvar';
    $func = 'edit(' . $_GET['id'] . ')';

    $camera = new cameras($dbFile);
    $camera->SetId($_GET['id']);
    $camera->load();

    $name = $camera->getName();
    $netcam_url = $camera->getNetcam_url();
    $netcam_userpass = $camera->getNetcam_userpass();
    $v4l2_palette = $camera->getV4l2_palette();
    $norm = $camera->getNorm();
    $width = $camera->getWidth();
    $height = $camera->getHeight();
    $framerate = $camera->getFramerate();
    $minimum_frame_time = $camera->getMinimum_frame_time();
    $netcam_keepalive = $camera->getNetcam_keepalive();
    $auto_brightness = $camera->getAuto_brightness();
    $brightness = $camera->getBrightness();
    $contrast = $camera->getContrast();
    $saturation = $camera->getSaturation();
    $hue = $camera->getHue();
}
else
{
    $title = 'Adicionar c&acirc;mera';
    $label = 'Adicionar';
    $func = 'add()';
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
        <div class="form-group">
          <label for="netcam_utl">URL</label>
          <input type="text" class="form-control" id="netcam_url" name="netcam_url" placeholder="URL"
          <?php if(isset($netcam_url)) echo ' value="' . $netcam_url . '"'; ?>>
        </div>
        <div class="form-group">
          <label for="netcam_userpass">Autentica&ccedil;&atilde;o</label>
          <input type="text" class="form-control" id="netcam_userpass" name="netcam_userpass" placeholder="usu&aacute;rio:senha"
          <?php if(isset($netcam_url)) echo ' value="' . $netcam_userpass . '"'; ?>>
        </div>
        <div class="form-group">
          <label for="v4l2_palette">v4l2_palette</label>
          <input type="text" class="form-control" id="v4l2_palette" name="v4l2_palette" placeholder="17"
          value="<?php if(isset($v4l2_palette)) echo $v4l2_palette; else echo '17'; ?>">
        </div>
        <div class="form-group">
          <label for="norm">Norma</label>
          <input type="text" class="form-control" id="norm" name="norm" placeholder="0 (PAL)"
          value="<?php if(isset($norm)) echo $norm; else echo '0'; ?>">
        </div>
        <div class="form-group">
          <label for="width">Largura</label>
          <input type="text" class="form-control" id="width" name="width" placeholder="320"
          value="<?php if(isset($width)) echo $width; else echo '320'; ?>">
        </div>
        <div class="form-group">
          <label for="height">Altura</label>
          <input type="text" class="form-control" id="height" name="height" placeholder="240"
          value="<?php if(isset($height)) echo $height; else echo '240'; ?>">
        </div>
        <div class="form-group">
          <label for="framerate">Frames por segundo</label>
          <input type="text" class="form-control" id="framerate" name="framerate" placeholder="2"
          value="<?php if(isset($framerate)) echo $framerate; else echo '2'; ?>">
        </div>
        <div class="form-group">
          <label for="minimum_frame_time">Tempo m&iacute;nimo de intervalo entre frames</label>
          <input type="text" class="form-control" id="minimum_frame_time" name="minimum_frame_time" placeholder="1"
          value="<?php if(isset($minimum_frame_time)) echo $minimum_frame_time; else echo '1'; ?>">
        </div>
        <div class="form-group">
          <label for="brightness">Brilho</label>
          <input type="text" class="form-control" id="brightness" name="brightness" placeholder="0"
          value="<?php if(isset($brightness)) echo $brightness; else echo '0'; ?>">
        </div>
        <div class="form-group">
          <label for="contrast">Contraste</label>
          <input type="text" class="form-control" id="contrast" name="contrast" placeholder="0"
          value="<?php if(isset($contrast)) echo $contrast; else echo '0'; ?>">
        </div>
        <div class="form-group">
          <label for="contrast">Satura&ccedil;&atilde;o</label>
          <input type="text" class="form-control" id="saturation" name="saturation" placeholder="0"
          value="<?php if(isset($saturation)) echo $saturation; else echo '0'; ?>">
        </div>
        <div class="form-group">
          <label for="hue">Hue</label>
          <input type="text" class="form-control" id="hue" name="hue" placeholder="0"
          value="<?php if(isset($hue)) echo $hue; else echo '0'; ?>">
        </div>
        <div class="form-check has-success">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="netcam_keepalive"  id="netcam_keepalive" value="1"
                    <?php if ($netcam_keepalive == 1 || !isset($netcam_keepalive)) echo ' checked'; ?>>
                    &nbsp;Sempre ativo
            </label>
        </div>
        <div class="form-check has-success">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="auto_brightness"  id="auto_brightness" value="1"
                    <?php if ($auto_brightness == 1) echo ' checked'; ?>>
                    &nbsp;Brilho autom&aacute;tico
            </label>
        </div>
        <div class="form-group">
          <button type="button" class="btn btn-default" id="btnAdd" name="btnAdd" onclick="<?php echo $func; ?>"><?php echo $label; ?></button>&nbsp;
          <button type="button" class="btn btn-default" id="btnCancel" name="btnCancel" onclick="populate('cameras.php')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>

function add()
{
    if ($('#netcam_keepalive').is(':checked'))
    {
        var netcam_keepalive = 1;
    }
    else
    {
        var netcam_keepalive = 0;
    }

    if ($('#auto_brightness').is(':checked'))
    {
        var auto_brightness = 1;
    }
    else
    {
        var auto_brightness = 0;
    }

    var form;
    form = new FormData();
    form.append('action', 'add');
    form.append('name', $('#name').val());
    form.append('netcam_url', $('#netcam_url').val());
    form.append('netcam_userpass', $('#netcam_userpass').val());
    form.append('v4l2_palette', $('#v4l2_palette').val());
    form.append('norm', $('#norm').val());
    form.append('width', $('#width').val());
    form.append('height', $('#height').val());
    form.append('framerate', $('#framerate').val());
    form.append('minimum_frame_time', $('#minimum_frame_time').val());
    form.append('netcam_keepalive', netcam_keepalive);
    form.append('auto_brightness', auto_brightness);
    form.append('brightness', $('#brightness').val());
    form.append('contrast', $('#contrast').val());
    form.append('saturation', $('#saturation').val());
    form.append('hue', $('#hue').val());

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
};

function edit(id)
{
    if ($('#netcam_keepalive').is(':checked'))
    {
        var netcam_keepalive = 1;
    }
    else
    {
        var netcam_keepalive = 0;
    }

    if ($('#auto_brightness').is(':checked'))
    {
        var auto_brightness = 1;
    }
    else
    {
        var auto_brightness = 0;
    }

    var auto_brightness

    var form;
    form = new FormData();
    form.append('action', 'edit');
    form.append('id', id);
    form.append('name', $('#name').val());
    form.append('netcam_url', $('#netcam_url').val());
    form.append('netcam_userpass', $('#netcam_userpass').val());
    form.append('v4l2_palette', $('#v4l2_palette').val());
    form.append('norm', $('#norm').val());
    form.append('width', $('#width').val());
    form.append('height', $('#height').val());
    form.append('framerate', $('#framerate').val());
    form.append('minimum_frame_time', $('#minimum_frame_time').val());
    form.append('netcam_keepalive', netcam_keepalive);
    form.append('auto_brightness', auto_brightness);
    form.append('brightness', $('#brightness').val());
    form.append('contrast', $('#contrast').val());
    form.append('saturation', $('#saturation').val());
    form.append('hue', $('#hue').val());

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
};

</script>
