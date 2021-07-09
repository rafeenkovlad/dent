<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Сохранение данных dentaline</title>

</head>
<body>
<div class="container">
  <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">                        
        <h2>Отлично, почти закончили.</h2>
      </div>
  </div>
  <div class="row">
      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-3">
          <form id="contact-form" class="form" action="" method="POST" role="form">
              <div class="form-group">
                  <label class="form-label" for="user">Роль</label>
                  <input type="radio" id="user" name="user" value="company" checked> Компания
                  <div></div>
                  <input type="radio" id="user" name="user" value="worker"> Сотрудник компании
              </div>
              <div class="form-group">
                  <label class="form-label" for="name">Имя</label>
                  <input type="text" class="form-control" id="name" name="name_dental" placeholder="Имя, отображаемое для отсальных пользователей" tabindex="1" required>
              </div>
              <div class="form-group">
                  <label class="form-label" for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Ваш Email" tabindex="2" required>
              </div>
              <div class="form-group">
                  <label class="form-label" for="Contact">Контакты</label>
                  <input type="text" class="form-control" id="contact" name="contact" placeholder="Адрес, телефон, сайт" tabindex="3" required>
              </div>
              <div class="form-group">
                  <label class="form-label" for="message">О Вас и вашей компании</label>
                  <textarea rows="5" cols="50" name="message" class="form-control" id="message" placeholder="Напишите немного о вашей компании. Так если Вы предлагаете какие либо услуги, пользователи смогут задавать Вам компетентные вопросы" tabindex="4" required></textarea>

                  <input type="hidden" name="login" value="<?= $_REQUEST['login'] ?>">
                  <input type="hidden" name="password" value="<?= $_REQUEST['password'] ?>">
                  <input type="hidden" name="repass" value="<?= $_REQUEST['repass'] ?>">
                  <input type="hidden" name="reg_dental_sub" value="<?= $_REQUEST['reg_dental_sub'] ?>">
              </div>
              <div class="text-center">
                  <button type="submit" id="set_user_dent" class="btn btn-start-order">Сохранить</button>
              </div>
              <div class="text-center" id="info"></div>
          </form>
      </div>
  </div>
</div>
<!-- partial -->

<?php //$request = json_encode(['login' => $_REQUEST['login'], 'password' => $_REQUEST['password'], 'repass' => $_REQUEST['repass'], 'reg_dental_sub' => $_REQUEST['reg_dental_sub']]); ?>
<!-- <script type = "text/javascript">
    const arr = ;
    $("#info").html($("#hidden_login").val());
    $(document).ready(function(){
        $("#set_user_dent").on("click", function(){
            $("#set_user_dent").prop("disabled", true);

            $.ajax({
                url: "",
                method: 'POST',
                data: {
                    reg_dental_sub: arr.reg_dental_sub,
                    login: arr.login,
                    password: arr.password,
                    repass: arr.repass,
                    user: $("#user").val(),
                    name_dental: $("#name").val(),
                    email: $("#email").val(),
                    contact: $("#contact").val(),
                    message: $("#message").val()
                    }
            }).done(function(data){
                $("#set_user_dent").prop("disabled", false);
            });
        });
    });

</script> -->


</body>
</html>

