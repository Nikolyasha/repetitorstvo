<?

$CURRENT_FILE = 'lists';
list($success, $error, $notify) = [null, null, ""];

include("init.php");

$data = json_decode(file_get_contents("php://input"));
if(isset($data->remove)){
    if($data->token != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    $sql = "DELETE FROM `" . htmlspecialchars(mysqli_real_escape_string($link, $data->table)) . "` WHERE `id` = '" . ((int) $data->remove) . "';";
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
        case "create_element":
            $sql = "INSERT INTO `" . htmlspecialchars(mysqli_escape_string($link, $_POST['table'])) . "` VALUES (NULL, 'Новый элемент');";
            // die($sql);
            $success = mysqli_query($link, $sql);
            break;
        case "edit_element":
            $sql = "UPDATE `" . htmlspecialchars(mysqli_escape_string($link, $_POST['table'])) . "` SET `id` = " . ((int) $_POST['new_element_id']) . ", `" . htmlspecialchars(mysqli_escape_string($link, $_POST['value_field'])) . "` = '" . htmlspecialchars(mysqli_escape_string($link, $_POST[$_POST['value_field']])) . "' WHERE `id` = " . ((int) $_POST['element_id']) . ";";
            // die($sql);
            $_GET['el'] = (int) $_POST['new_element_id'];
            $success = mysqli_query($link, $sql);
            if($success && ((int) $_POST['new_element_id'] != (int) $_POST['element_id'])){
                header("Location: lists.php?edit=".htmlspecialchars(mysqli_escape_string($link, $_POST['table']))."&el=".((int) $_POST['new_element_id']));
                die();
            }
            break;
        default:
            break;
    }
}

$action = "view";
if(isset($_GET['edit'])){
    $sql = "SELECT * FROM `" . htmlspecialchars(mysqli_escape_string($link, $_GET['edit'])) . "`;";
    $result = mysqli_query($link, $sql);
    if($result){
        $action = "edit";
        $elements = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
} else {
    $sql = "SELECT * FROM `site_settings` WHERE `name` LIKE '%_list';";
    $elements = mysqli_fetch_all(mysqli_query($link, $sql), MYSQLI_ASSOC);
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

switch($action){
    case "edit":
        include("../views/admin_view/list_edit.php");
        break;
    default:
        include("../views/admin_view/lists.php");
        break;
}

include("../views/admin_view/footer.php"); 

?>