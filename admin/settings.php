<?

$CURRENT_FILE = 'settings';
list($success, $error, $notify) = [null, null, ""];

include("init.php");
// require_once($_SERVER['DOCUMENT_ROOT']."/models/filters.php");

// print_r($_POST);
if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    switch($_POST['action']){
        case 'edit':
            $sql = "";
            $request_count = 0;
            foreach(array_keys($_POST) as $post_key){
                if(explode("_", $post_key)[count(explode("_", $post_key)) - 1] = "price"){
                    $_POST[$post_key] = htmlspecialchars(mysqli_real_escape_string($link, $_POST[$post_key]));
                    $key = htmlspecialchars(mysqli_real_escape_string($link, $post_key)); 
                    $sql .= "UPDATE `site_settings` SET `value`='{$_POST[$post_key]}' WHERE `name` = '$key';";
                    $request_count++;
                }
            }

            if(mysqli_multi_query($link, $sql)){
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
$sql = "SELECT * FROM `site_settings` WHERE `name` LIKE '%_option';";
$elements = mysqli_fetch_all(mysqli_query($link, $sql), MYSQLI_ASSOC);

if($success !== null){
    if($success){
        $notify = '
            <div class="alert background-success notify">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="icofont icofont-close-line-circled text-white"></i>
                </button>
                <strong>Успех!</strong> Параметры сохранены</b>
            </div>';
    }
    else{
        $notify = '
            <div class="alert background-danger notify">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="icofont icofont-close-line-circled text-white"></i>
                </button>
                
                <strong>Ошибка!</strong> '.$error.'
            </div>';
    }
}

include("../views/admin_view/header.php"); 

include("../views/admin_view/settings.php");

include("../views/admin_view/footer.php"); 

?>