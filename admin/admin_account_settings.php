<?

$CURRENT_FILE = 'account_settings';

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/models/offer.php");
$notify = "";

// API обработчики
if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    switch ($_POST['action']) {
        case 'change_passwd':
            if(strlen($_POST['new_pass']) < 6){
                $notify = '
                <div class="alert background-danger notify">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="icofont icofont-close-line-circled text-white"></i>
                    </button>
                    
                    <strong>Ошибка!</strong> Слишком кототкий пароль
                </div>';
            }
            else if($_POST['new_pass'] != $_POST['new_pass_repeat']){
                $notify = '
                <div class="alert background-danger notify">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="icofont icofont-close-line-circled text-white"></i>
                    </button>
                    
                    <strong>Ошибка!</strong> Пароли не совпадают
                </div>';
            }
            else{
                list($status, $result) = User::ChangePasswd($link, $_SESSION['id'], $_POST['old_pass'], $_POST['new_pass'], $_POST['new_pass_repeat']);
                if($status){
                    $notify = '
                    <div class="alert background-success notify">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="icofont icofont-close-line-circled text-white"></i>
                        </button>   
                        <strong>Успех!</strong> Пароль успешно изменен</b>
                    </div>';
                }
                else{
                    $notify = '
                    <div class="alert background-danger notify">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="icofont icofont-close-line-circled text-white"></i>
                        </button>
                        
                        <strong>Ошибка!</strong> '.$result.'
                    </div>';
                }
            }
            break;
        default:
            break;
    }
}

// if($_SESSION['account_type'] == 2){
//     $sql = Offer::GetNewOffersCountSQL($_SESSION['id']);
//     list($new_offers_count) = MultiQuery($link, $sql);
//     $new_offers_count = $new_offers_count[0]['new_offers_count'];
// } else {
//     $user = User::GetWorker($link, $_SESSION['id']);
//     $filters = $user['filters'];
//     $user = $user['user'];
// }

include("../views/admin_view/header.php"); 

include("../views/admin_view/admin_account_settings_form.php");

include("../views/admin_view/footer.php"); 

?>