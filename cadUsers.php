<?php

session_start ();
if (! isset ( $_SESSION ['level'] ))
{
    header ( 'location:login.php' );
}

require_once('class.php');

if (is_numeric($_GET['id']))
{
    $title = 'Editar usu&aacute;rio';
    $label = 'Salvar';
    $func = 'edit(' . $_GET['id'] . ')';
    $adv = '<small id="passwdHelp" class="form-text text-muted">Se deixar esse campo vazio, a senha atual ser&aacute mantida.</small>';

    $user = new users($dbFile);
    $user->SetId($_GET['id']);
    $user->load();

    $name = $user->getName();
    $login = $user->getLogin();
}
else
{
    $title = 'Adicionar usu&aacute;rio';
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
          <input type="text" class="form-control" id="name" name="name" placeholder="Nome completo"
          <?php if(isset($name)) echo ' value="' . $name . '"'; ?>>
        </div>
        <div class="form-group">
          <label for="login">Login</label>
          <input type="text" class="form-control" id="login" name="login" placeholder="Login"
          <?php if(isset($login)) echo ' value="' . $login . '"'; ?>>
        </div>
        <div class="form-group">
          <label for="passwd">Senha</label>
          <input type="password" class="form-control" id="passwd" name="passwd" placeholder="Senha">
          <?php if(isset($adv)) echo $adv; ?>
        </div>
        <div class="form-group">
          <label for="passwd2">Confirma&ccedil;&atilde;o de senha</label>
          <input type="password" class="form-control" id="passwd2" name="passwd2" placeholder="Confirma&ccedil;&atilde;o de senha">
        </div>
        <div class="form-group">
          <button type="button" class="btn btn-default" id="btnAdd" name="btnAdd" onclick="<?php echo $func; ?>"><?php echo $label; ?></button>&nbsp;
          <button type="button" class="btn btn-default" id="btnCancel" name="btnCancel" onclick="populate('users.php')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>

function add()
{
    var form;
    form = new FormData();
    form.append('action', 'add');
    form.append('name', $('#name').val());
    form.append('login', $('#login').val());
    form.append('passwd', $('#passwd').val());
    form.append('passwd2', $('#passwd2').val());

    $.ajax({
        url: 'acUsers.php',
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
    var form;
    form = new FormData();
    form.append('action', 'edit');
    form.append('id', id);
    form.append('name', $('#name').val());
    form.append('login', $('#login').val());
    form.append('passwd', $('#passwd').val());
    form.append('passwd2', $('#passwd2').val());

    $.ajax({
        url: 'acUsers.php',
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
