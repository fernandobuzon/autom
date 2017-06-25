<div class="jumbotron">

    <center>
        <div id="title"></div>

        <div class="progress">
            <div id="progress" class="progress-bar" style="width: 0%;"></div>
        </div>

        <div id="status"></div>
    </center>

<script>

$("#title").html("<h1>Aplicando altera&ccedil;&otilde;es...</h1>");

$("#status").append("<img id='loading-conf' src='loading.gif'>&nbsp; Arquivo de configura&ccedil;&atilde;o do motor... ");
setConf(10);

function setConf(prog)
{
    var step = 'conf';

    var form;
    form = new FormData();
    form.append('step', step);

    $.ajax({
        url: 'acEnviron.php',
        data: form,
        processData: false,
        contentType: false,
        type: 'POST',
        cache: false,
        complete: function(){
            $('#loading-' + step).hide();
            $('#progress').attr("style", 'width: ' + prog + '%')
        },
        success: function (data) {
            $('#status').append(data);
            $("#status").append("<img id='loading-threads' src='loading.gif'>&nbsp; Arquivo(s) de configura&ccedil;&atilde;o da(s) c&acirc;mera(s)... ");
            setThreads(40);
        }
    });
};

function setThreads(prog)
{
    var step = 'threads';

    var form;
    form = new FormData();
    form.append('step', step);

    $.ajax({
        url: 'acEnviron.php',
        data: form,
        processData: false,
        contentType: false,
        type: 'POST',
        cache: false,
        complete: function(){
            $('#loading-' + step).hide();
            $('#progress').attr("style", 'width: ' + prog + '%')
        },
        success: function (data) {
            $('#status').append(data);
            $("#status").append("<img id='loading-gallery' src='loading.gif'>&nbsp; Galerias de fotos... ");
            setGallery(100);
        }
    });
};

function setGallery(prog)
{
    var step = 'gallery';

    var form;
    form = new FormData();
    form.append('step', step);

    $.ajax({
        url: 'acEnviron.php',
        data: form,
        processData: false,
        contentType: false,
        type: 'POST',
        cache: false,
        complete: function(){
            $('#loading-' + step).hide();
            $('#progress').attr("style", 'width: ' + prog + '%')
        },
        success: function (data) {
            $('#status').append(data);
            //$("#status").append("<img id='loading-gallery' src='loading.gif'>&nbsp; Galerias de fotos... ");
            //setGallery(100);
        }
    });
};

//galerias

</script>
