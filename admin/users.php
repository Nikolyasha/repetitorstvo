<?

$CURRENT_FILE = 'users';
list($success, $error, $notify) = [null, null, ""];

if(isset($_GET['balance'])){
    $_GET['balance'] = (int) $_GET['balance'];
    if(!($_GET['balance'] > 0)){
        header("Location: /admin/users.php"); die();
    }
}

include("init.php");

$data = json_decode(file_get_contents("php://input"));
if(isset($data->action)){
    if($data->token != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    if(!((int) $data->user > 0)){
        header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
        exit();
    }
    switch($data->action){
        case "block":
            $sql = "UPDATE `users` SET `type` = 0 WHERE `id` = '" . ((int) $data->user) . "' AND `admin` != '1';";
            SendLog($link, "Блокировка пользователя id" . ((int) $data->user));
            break;
        case "unlock":
            $sql = "UPDATE `users` SET `type` = `base_type` WHERE `id` = '" . ((int) $data->user) . "' AND `admin` != '1';";
            SendLog($link, "Разблокировка пользователя id" . ((int) $data->user));
            break;
        case "activate":
            $sql = "UPDATE `users` SET `activation` = '' WHERE `id` = '" . ((int) $data->user) . "';";
            SendLog($link, "Активация почты пользователя id" . ((int) $data->user));
            break;
        default:
            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
            exit();
    }
    if(mysqli_query($link, $sql)){
        header($_SERVER['SERVER_PROTOCOL']." 200 OK");
    }
    else{
        header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
    }
    exit();
}
// print_r($_POST);
if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    switch($_POST['action']){
        case 'set_balance':
            $_POST['user_id'] = (int) $_POST['user_id'];
            $_POST['new_balance'] = (int) $_POST['new_balance'];
            if($_POST['user_id'] < 0){
                $error = "Неверный ID пользователя";
                break;
            }
            $sql = "SELECT `balance` FROM `users` WHERE `id` = {$_POST['user_id']};";
            $result = mysqli_fetch_row(mysqli_query($link, $sql))[0];
            if($result == NULL){
                $error = "Неверный ID пользователя";
                break;
            }

            $balance_diff = $_POST['new_balance'] - (int) $result;
            SendLog($link, "Изменение баланса пользователя id{$_POST['user_id']} " .($balance_diff > 0 ? "+":""). "$balance_diff на монет");
            $sql = "UPDATE `users` SET `balance` = '{$_POST['new_balance']}' WHERE `id` = {$_POST['user_id']};";

            if(mysqli_query($link, $sql)){
                $success = True;
            }
            else{
                $success = False;
                $error = mysqli_error($link);
            }
            for($i = 1; $i < $request_count; $i++)
                mysqli_next_result($link);
        default:
            break;
    }
}

$action = "view";
if(isset($_GET['balance'])){
    $sql = "SELECT * FROM `users` WHERE `id` = {$_GET['balance']};";
    $result = mysqli_query($link, $sql);
    if($result){
        $action = "balance";
        $element = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
        if($element == Null){
            header("Location: /admin/users.php"); die();
        }
    }
} else {
    $sql = "SELECT * FROM `users`;SELECT `id`, `user_id` FROM `workers`;SELECT `id`, `company_owner_id` as `user_id` FROM `companies`;";
    list($elements, $ankets, $companies) = MultiQuery($link, $sql);
}

if($success !== null){
    if($success){
        $notify = '
            <div class="alert background-success notify">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="icofont icofont-close-line-circled text-white"></i>
                </button>
                <strong>Успех</strong></b>
            </div>';
    }
    else{
        $notify = '
            <div class="alert background-danger notify">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="icofont icofont-close-line-circled text-white"></i>
                </button>
                
                <strong>Ошибка!</strong> '.mysqli_error($link).'
            </div>';
    }
}

include("../views/admin_view/header.php"); 

include("../views/admin_view/users.php");

include("../views/admin_view/footer.php"); 

?>