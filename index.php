<?php
require_once('class.php');

//session_start ();
//if (! isset ( $_SESSION ['level'] ))
//    header ( 'location:login.php' );
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="bootstrap-3.3.7/docs/favicon.ico">

    <title>CredDefense - Central Infrastructure</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap-3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="bootstrap-3.3.7/dist/css/bootstrap-theme.min.css" rel="stylesheet">
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="bootstrap-3.3.7/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="bootstrap-3.3.7/docs/examples/theme/theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="bootstrap-3.3.7/docs/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="?page=home.php">Bootstrap</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cadastro <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="?page=faces.php">Faces</a></li>
                <li><a href="?page=doors.php">Portas</a></li>
                <li><a href="?page=cameras.php">C&acirc;meras</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="?page=users.php">Usu&aacute;rios</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Configura&ccedil;&otilde;es <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="?page=motion.php">Motor</a></li>
                <li><a href="?page=settings.php">Par&acirc;metros</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Comandos <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="?page=environ.php">Aplicar altera&ccedil;&otilde;es</a></li>
                <?php
                    $checkStatus = new environ($dbFile);
                    $checkStatus = $checkStatus->checkStatus();
                ?>
                <li id="stop" <?php if ($checkStatus == false) echo ' class="disabled "' ?>><a href="#" onclick="cmd('stop');">Parar o servi&ccedil;o</a></li>
                <li id="start" <?php if ($checkStatus == true) echo ' class="disabled "' ?>><a href="#" onclick="cmd('start');">Iniciar o servi&ccedil;o</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Relat&oacute;rio<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="?page=logs.php">Movimenta&ccedil;&atilde;o</a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <form name="formT" id="formT">
        <input type="hidden" id="transfer" name="transfer" value="<?php echo $_POST['image']; ?>">
    </form>

    <div id="pageBox" class="container theme-showcase" role="main"></div>
    <div id="details" class="container theme-showcase" role="main"></div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="bootstrap-3.3.7/docs/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap-3.3.7/dist/js/bootstrap.min.js"></script>
    <script src="bootstrap-3.3.7/docs/assets/js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap-3.3.7/docs/assets/js/ie10-viewport-bug-workaround.js"></script>

    <script>
      function populate(page,onend) {
        $.ajax({
          type: 'GET',
          url: page,
        }).always(function() {
          var r = arguments[1];
          var body = arguments[0];
          if(r == "success") {
            $('#pageBox').html(body);
            if(typeof(onend) != 'undefined') onend();
          } else {
            // mensagem de erro
            var error = "<div class='alert alert-danger' role='alert'>Erro ao abrir arquivo: <strong>" + page + "</strong></div>";
            $('#pageBox').html(error)
          }
        });
      }

      <?php
        if (empty($_GET['page']))
        {
            echo 'populate("home.php");';
        }
        else
        {
            echo 'populate("' . $_GET['page'] .'");';
        }
      ?>

    function cmd(cmd)
    {
        if (cmd == 'stop') {
            var r = confirm('Tem certeza que deseja interromper?');
            if (r == false) {
                throw 'cancelado';
            }
        }

        var form;
        form = new FormData();
        form.append('command', cmd);

        $.ajax({
            url: 'acCommands.php',
            data: form,
            processData: false,
            contentType: false,
            type: 'POST',
            cache: false,
            success: function (data) {
                if (data == 'started') {
                    alert('Inicializado.');
                    $('#start').attr('class', 'disabled');
                    $('#stop').attr('class', '');
                } else if (data == 'stoped') {
                    alert('Interrompido.');
                    $('#start').attr('class', '');
                    $('#stop').attr('class', 'disabled');
                } else if (data == 'restarted') {
                    alert('Reinicializado.');
                };
            }
        });
    };

</script>

  </body>
</html>
