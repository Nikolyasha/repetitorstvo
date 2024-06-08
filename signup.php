<?
    session_start();    
    include("./core/db.php"); 
    if(isset($_SESSION['retoken'])){
        $user_mail = "mail@gmail.com";
        if(isset($_GET['mail']))
        $user_mail = $_GET['mail'];
        $mail_site = explode("@", $user_mail);
        $mail_site = $mail_site[1];
        $reactivationToken = $_GET['token'];
        include("./views/reg_success.php");
    }
    else{
        $error = false;
        
        if(isset($_SESSION["admin"])){
            header("Location: /");
        }
        
        if(isset($_POST['mail']) && isset($_POST['passwd']) && $_POST['mail'] != "" && $_POST['passwd'] != "" && $_POST['name'] != ""){
            if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
                $error = "Некорректная почта";
            }
            else if(count(explode(" ", $_POST['name'])) != 2) {
                $error = "Введите ваше имя и фамилию";
            }
            else if((int) $_POST['account_type'] > 2 || (int) $_POST['account_type'] < 1){
                $error = "Форма заполнена некорректно";
            }
            else if(!isset($_POST['confirm_politics'])){
                $error = "Вы должны принять пользовательское соглашение";
            }
            else{
                require_once("./models/user.php");
                $reg = User::Create($link, $_POST['mail'], $_POST['passwd'], $_POST['name'], (int) $_POST['account_type'], $_SETTINGS['anket_create_price']);
                // die(var_dump($reg));
                if($reg[0]){
                    $_SESSION['retoken'] = $reg[2];
                    header("Location: /signup.php");
                }
                else{
                    switch($reg[1]){
                        case "mail":
                            $error = "Эта почта уже занята";
                            break;
                        default:
                            $error = "Попробуйте еще раз";
                            $error = $reg[1];
                            break;
                    } 
                    
                }
            }
        }

        include("./views/reg_view.php");
        // include("./views/reg_success.php");
    }

?>