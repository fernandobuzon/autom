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
    <div class="row">
      <div class="col-sm-4"></div>
      <div class="col-sm-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Autentica&ccedil;&atilde;o</h3>
          </div>
          <div class="panel-body">
            <form name="form1">
              <div class="form-group">
                <label for="inputLogin">Login</label>
                <input type="text" name="login" class="form-control" id="login" placeholder="Login">
              </div>
              <div class="form-group">
                <label for="inputPassword">Password</label>
                <input type="password" name="passwd" class="form-control" id="passwd" placeholder="Senha">
              </div>
              <div class="form-group">
                <div id="msg" style="display: none;">
                  <div class="alert alert-danger" role="alert"><a href="#" onclick="$('#msg').hide();"><span class="glyphicon glyphicon-remove-circle" style="color:red"></span></a>&nbsp;Dados inconsistentes!</div>
                </div>
              </div>
             <div class="form-group">
               <button type="button" class="btn btn-default" id="btnCheck" name="btnCheck" onclick="check();">Confirmar</button>
             </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-sm-4"></div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="bootstrap-3.3.7/docs/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap-3.3.7/dist/js/bootstrap.min.js"></script>
    <script src="bootstrap-3.3.7/docs/assets/js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap-3.3.7/docs/assets/js/ie10-viewport-bug-workaround.js"></script>

  </body>
</html>

<script>

$('#passwd').keypress(function (e) {
  if (e.which == 13) {
    check();
  }
});

function check()
{
    if ($('#login').val() === "")
    {
        alert('Preencha o campo Login');
        throw 'null login';
    }

    if ($('#passwd').val() === "")
    {
        alert('Preencha o campo Senha');
        throw 'null passwd';
    }

    var form;
    form = new FormData();
    form.append('login', $('#login').val());
    form.append('passwd', $('#passwd').val());

    $.ajax({
        url: 'checklogin.php',
        data: form,
        processData: false,
        contentType: false,
        type: 'POST',
        cache: false,
        success: function (data) {
            if (data != 'OK')
            {
                alert(data);
                $('#msg').show();
            }
            else
            {
                window.location.href = 'index.php';
            }
        }
    });
};

</script>
