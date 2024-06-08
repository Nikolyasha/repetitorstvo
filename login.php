<?
    session_start();
    $error = "";
    $redirect = "index.php";
    if(isset($_GET["redirect"])){
        $redirect = $_GET['redirect'];
    }
    
    if(isset($_SESSION["admin"])){
        header("Location: ".$redirect);
    }
    
    include("./core/db.php"); 

    require_once("./models/user.php");
    $auth = User::ResetPassword($link, "admin@adm1in.ru");
    
    switch($_POST['action']){
        case "auth":
            if(isset($_POST['mail']) && isset($_POST['passwd']) && $_POST['mail'] != "" && $_POST['passwd'] != ""){
                require_once("./models/user.php");
                $auth = User::Auth($link, mysqli_real_escape_string($link, $_POST['mail']), mysqli_real_escape_string($link, $_POST['passwd']), isset($_POST['remember']));
                if($auth[0]){
                    $data = $auth[1];
                    //['id'], $result['admin'], $result['name'], $result['mail']
                    unset($_SESSION['retoken']);
                    $_SESSION['id'] = $data['id'];
                    $_SESSION['admin'] = $data['admin'];
                    $_SESSION['name'] = $data['name'];
                    $_SESSION['mail'] = $data['mail'];
                    $_SESSION['account_type'] = $data['type'];
                    $_SESSION['token'] = bin2hex(random_bytes(32));
                    if(isset($_POST['remember']) && !empty($data['token'])){
                        setcookie("bb33f285255ebb9089d20aaa82b56eb4", $data['token']);
                    }
                    // if($redirect == "index.php")
                        if($_SESSION['admin'] == 1)
                            $redirect = "/admin";
                    header("Location: ".$redirect);
                }
                else{
                    switch($auth[1]){
                        case 'wrong':
                            $error = "Логин или пароль неверный";
                            break;
                        case "activation":
                            $_SESSION['retoken'] = $auth[3];
                            header("Location: /signup.php");
                            break;
                        default:
                            $error = "Ошибка авторизации (500)";
                            break;
                    }
                }
            }
            break;
        case "recovery":
            if(isset($_POST['mail']) && isset($_POST['g-recaptcha-response']) && $_POST['mail'] != "" && $_POST['g-recaptcha-response'] != ""){
                
                $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$_SETTINGS['captcha_private_option']."&response=".$_POST['g-recaptcha-response']);
                $g_response = json_decode($response);
                if ($g_response->success !== true){
                    $error = "Проверка на робота не пройдена";
                    break;   
                }	

                require_once("./models/user.php");
                $auth = User::ResetPassword($link, mysqli_real_escape_string($link, $_POST['mail']));

                if($auth) {
                    header("Location: /login.php?recovery&done"); 
                    die();
                }
                else {
                    $error = "Произошла ошибка, попробуйте еще раз";
                    break;
                }
            } else {
                $error = "Произошла ошибка, попробуйте еще раз";
                break;
            }
            break;
        case "change_password":
            if(isset($_POST['passwd']) && isset($_POST['token']) && isset($_POST['passwd_re']) && $_POST['passwd'] != ""  && $_POST['token'] != "" && $_POST['passwd_re'] != ""){
                if($_POST['passwd'] != $_POST['passwd_re']){
                    $error = "Пароли не совпадают";
                    $_GET['recovery'] = true;
                    $_GET['token'] = $_POST['token'];
                    break;
                }
                $result = User::ChangePasswd($link, null, null, $_POST['passwd'], $_POST['passwd_re'], $token = $_POST['token']);
                if($result){
                    $success = "Пароль успешно сброшен";
                    break;
                }
            } else {
                $error = "Не все поля заполнены";
                $_GET['recovery'] = true;
                $_GET['token'] = $_POST['token'];
                break;
            }
    }

    if(isset($_GET['recovery'])){
        include("./views/password_recovery.php");
    } else {
        include("./views/login.php");
    }

?>