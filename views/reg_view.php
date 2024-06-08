<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/css/bootstrap.min.css">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- color .css -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/page.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title><? echo($_SETTINGS['site_name_option']); ?> | Регистрация</title>
    <script type="text/javascript" src="/bower_components/jquery/js/jquery.min.js"></script> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-site-color: <?=$_SETTINGS['primay_color_option']?>;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="/css/form.css">
    <script type="text/javascript" src="/js/registration.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>
</head>
<body>
    <div class="wrapper">
        <? include("./views/header.php"); ?>
        <div class="text">
            <section class="login">
                <!-- Container-fluid starts -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <!-- Authentication card start -->
                            <div class="login-card card-block auth-body">
                                <form class="md-float-material" method="POST">
                                    <div class="auth-box">
                                        <div class="row m-b-20">
                                            <div class="col-md-12">
                                                <h3 class="text-center txt-primary">Регистрация на сайте</h3>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="input-group">
                                            <input maxlength="100" name="name" type="text" class="form-control" placeholder="Введите ваше имя и фамилию">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="input-group">
                                            <input maxlength="100" name="mail" type="email" class="form-control" placeholder="Введите почту">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="input-group">
                                            <input name="passwd" id="passwd" type="password" class="form-control" placeholder="Введите пароль">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="input-group">
                                            <input name="passwd_confirm" type="password" class="form-control" placeholder="Повторите пароль">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <select name="account_type" class="form-control" required>
                                                    <option value="1">Я работник</option>
                                                    <option value="2">Я работодатель</option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="row m-t-25 text-left">
                                            <div class="col-md-12">
                                                <div class="checkbox-fade fade-in-primary">
                                                    <label>
                                                        <input name="confirm_politics" type="checkbox" value="">
                                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                        <span class="text-inverse">Принимаю <a href="/terms.html" target="_blank">пользовательское соглашение</a></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row m-t-30">
                                            <div class="col-md-12">
                                                <button onclick="verifryRegForm();" type="button" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Зарегистрироваться</button>
                                            </div>
                                        </div>
                                        <div id="error_place"></div>
                                        <? if($error) echo("<script> renderError('".$error."', true); </script>"); ?>
                                    </div>
                                </form>
                                <!-- end of form -->
                            </div>
                            <!-- Authentication card end -->
                        </div>
                        <!-- end of col-sm-12 -->
                    </div>
                    <!-- end of row -->
                </div>
                <!-- end of container-fluid -->
            </section>
        </div>
        <? include("./views/footer.php"); ?>
        <? if(!empty($error)) { ?><script>renderError('<?=$error?>');</script><? } ?>
    </div>
</body>
</html>


