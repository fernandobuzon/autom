<div class="jumbotron">

    <center>
        <div id="title"></div>
        <div id="status"></div>
    </center>

<script>
$("#title").html("<h1>Aplicando altera&ccedil;&otilde;es...</h1>");

$("#status").append("Arquivo de configura&ccedil;&atilde;o do motor...");

setConf();

function setConf()
{
    var form;
    form = new FormData();
    form.append('step', 'conf');

    $.ajax({
        url: 'acMake.php',
        data: form,
        processData: false,
        contentType: false,
        type: 'POST',
        cache: false,
        success: function (data) {
            $('#status').append(data);
        }
    });
};

</script>
