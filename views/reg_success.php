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
    <link rel="stylesheet" type="text/css" href="/css/form.css">
</head>
<body>
    <div class="wrapper">
        <? include("./views/header.php"); ?>
        <div class="text">
            <div class="container">
                <div class="reg_success_block">
                    <div class="reg_success_img"><img src="img/mail.png" alt="Email" width="152" height="152" style=""/></div>
                    <div class="reg_success_text">
                        <b>Завершение регистрации</b><br/>
                        Ваш аккаунт успешно зарегистрирован, осталось активировать его по ссылке в письме, которое мы отправили на вашу почту
                    </div>
                    <div class="reg_success_resend">
                        Не пришло письмо? <a style="color: blue; text-decoration: underline; cursor: pointer;" id="send_email" onclick="sendActivation('<? echo($_SESSION['retoken']); ?>');">Отправить еще раз</a>
                    </div>
                </div>
            </div>
        </div>
        <? include("./views/footer.php"); ?>
        <script type="text/javascript" src="/js/main.js"></script>
        <script type="text/javascript" src="/js/registration.js"></script>
    </div>
</body>
</html>