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
    <title><? echo($_SETTINGS['site_name_option']); ?> | Восстановление пароля</title>
    <script type="text/javascript" src="/bower_components/jquery/js/jquery.min.js"></script> 
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="/js/main.js"></script>
    <script src="/js/favorite.js"></script>
    <style>
        :root {
            --main-site-color: <?=$_SETTINGS['primay_color_option']?>;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="/css/form.css">
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
                                <? if(isset($_GET['token'])){ ?>
                                <form method="POST" class="md-float-material" action="login.php<? if(isset($_GET["redirect"])) echo("?redirect=".$_GET['redirect']); ?>">
                                    <input type="hidden" name="action" value="change_password">
                                    <input type="hidden" name="token" value="<?=$_GET['token']?>">
                                    <div class="text-center">
                                        <img src="/img/logo.png" alt="logo.png">
                                    </div>
                                    <div class="auth-box">
                                        <div class="row m-b-20">
                                            <div class="col-md-12">
                                                <h3 class="text-left txt-primary">Восстановлене пароля</h3>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="input-group">
                                            <input name="passwd" type="password" class="form-control" placeholder="Новый пароль">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="input-group">
                                            <input name="passwd_re" type="password" class="form-control" placeholder="Пароль еще раз">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="row m-t-30">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Сменить</button>
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
                                        ?>
                                    </div>
                                </form>
                                <? } else if(isset($_GET['done'])) { ?>
                                <form class="md-float-material">
                                    <div class="text-center">
                                        <img src="/img/logo.png" alt="logo.png">
                                    </div>
                                    <div class="auth-box">
                                        <div class="row m-b-20">
                                            <div class="col-md-12">
                                                <h3 class="text-left txt-primary">На вашу почту отправлено письмо с инструкциями</h3>
                                            </div>
                                        </div>
                                        <div class="row m-t-30">
                                            <div class="col-md-12">
                                                <a href="/login.php">
                                                    <button type="button" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Назад</button>
                                                </a>
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
                                        ?>
                                    </div>
                                </form>
                                <? } else { ?>
                                <form method="POST" class="md-float-material" action="login.php<? if(isset($_GET["redirect"])) echo("?redirect=".$_GET['redirect']); ?>">
                                    <input type="hidden" name="action" value="recovery">
                                    <div class="text-center">
                                        <img src="/img/logo.png" alt="logo.png">
                                    </div>
                                    <div class="auth-box">
                                        <div class="row m-b-20">
                                            <div class="col-md-12">
                                                <h3 class="text-left txt-primary">Восстановлене пароля</h3>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="input-group">
                                            <input type="email" name="mail" class="form-control" placeholder="Почта">
                                            <span class="md-line"></span>
                                        </div>
                                        <div class="input-group">
                                            <div class="g-recaptcha" data-sitekey="<?=$_SETTINGS['captcha_public_option']?>"></div>
                                        </div>
                                        <div class="row m-t-30">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Восстановить</button>
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
                                        ?>
                                    </div>
                                </form>
                                <? } ?>
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


