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
    <link rel="stylesheet" type="text/css" href="/css/form.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/page.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title><? echo($_SETTINGS['site_name_option']); ?> | Авторизация</title>
    <script type="text/javascript" src="/bower_components/jquery/js/jquery.min.js"></script> 
    <script src="/js/main.js"></script>
    <script src="/js/favorite.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-site-color: <?=$_SETTINGS['primay_color_option']?>;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <? include("./views/header.php"); ?>
        <div class="text">
            <section class="login p-fixed d-flex text-center bg-primary common-img-bg">
                <!-- Container-fluid starts -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <!-- Authentication card start -->
                            <div class="login-card card-block auth-body">
                                <form method="POST" class="md-float-material" action="login.php<? if(isset($_GET["redirect"])) echo("?redirect=".$_GET['redirect']); ?>">
                                    <input type="hidden" name="action" value="auth">
                                    <div class="text-center">
                                        <img src="/img/logo.png" alt="logo.png">
                                    </div>
                                    <div class="auth-box">
                                        <div class="row m-b-20">
                                            <div class="col-md-12">
                                                <h3 class="text-left txt-primary">Авторизация</h3>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="input-group">
                                            <input type="email" name="mail" class="form-control" placeholder="Почта">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="input-group">
                                            <input name="passwd" type="password" class="form-control" placeholder="Пароль">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="row m-t-25 text-left">
                                            <div class="col-sm-7 col-xs-12">
                                                <div class="checkbox-fade fade-in-primary">
                                                    <label>
                                                        <input type="checkbox" value="" name="remember">
                                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                        <span class="text-inverse">Запомнить</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-5 col-xs-12 forgot-phone text-right">
                                                <a href="?recovery" class="text-right f-w-600 text-inverse">Напомнить пароль</a>
                                            </div>
                                        </div>
                                        <div class="row m-t-30">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Войти</button>
                                            </div>
                                        </div>
                                        <?
                                            if($error){
                                                echo(
                                                    '<hr>
                                                    <div class="row m-t-20 login_error"><p>
                                                        <b>Ошибка:</b> '.$error.'
                                                    </p></div>'
                                                );
                                            }
                                            if($success){
                                                echo(
                                                    '<hr>
                                                    <div class="row m-t-20 login_error"><p>
                                                        <b>Успех:</b> '.$success.'
                                                    </p></div>'
                                                );
                                            }
                                        ?>
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
    </div>
</body>
</html>


