<?

$CURRENT_FILE = 'filters';

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/filters.php");

// API обработчики
$data = json_decode(file_get_contents("php://input"));
if(isset($data->action)){
    if($data->token != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    $status = 0;
    switch($data->action){
        case 'remove':
            if(isset($data->filter_id)){
                $filter = new Filter($link, (int) $data->filter_id);
                list($result, $code) = $filter->DeleteFilter($link, (int) $data->filter_id);
                if($result){
                    header($_SERVER['SERVER_PROTOCOL']." 200 OK");
                }
                else{
                    header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
                }
            }
            else{
                header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
            }
            break;
        default:
            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
            die();
    }
    die();
}

if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    $redirect = "filters.php";
    $sing = "?";
    if(isset($_POST['redirect']) && strlen($_POST['redirect']) != 0){
        $redirect = $_POST['redirect'];
        if(strpos($_POST['redirect'], "?")){
            $sing = "&";
        }
    }
    switch($_POST['action']){
        case 'edit':
            $filter = new Filter($link, (int) $_POST['filter_id']);
            $text = htmlspecialchars(mysqli_real_escape_string($link, $_POST['offer_reply']));
            list($result, $code) = $filter->EditFilter($link, 
                htmlspecialchars(mysqli_real_escape_string($link, $_POST['filter_display_name'])),
                htmlspecialchars(mysqli_real_escape_string($link, $_POST['filter_type'])),
                htmlspecialchars(mysqli_real_escape_string($link, $_POST['filter_options']))
            );
            if($result){
                header("Location: filters.php?e_success");
            }
            else{
                header("Location: filters.php?error=".$code);
            }
            die();
        default:
            header("Location: ".$redirect);
            die();
    }
}

$action = "";

//Обработчик страниц
if(isset($_GET['edit'])){
    $action = "edit";
    $filter = new Filter($link, (int) $_GET['edit']);
    $filter = $filter->data;
    // print_r($filter);
    // die();
}
else{
    $action = "view";
    $sql = Filter::GetFilterListSQL();
    list($filters) = MultiQuery($link, $sql);
}

if($action == "view" && isset($_GET['success'])){
    $notify = '
        <div class="alert background-success notify">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            <strong>Успех!</strong> Ответ успешно отправлен</b>
        </div>';
}
else if($action == "view" && isset($_GET['e_success'])){
    $notify = '
        <div class="alert background-success notify">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            <strong>Успех!</strong> Фильтр успешно отредактирован</b>
        </div>';
}
else if($action == "view" && isset($_GET['error'])){
    $notify = '
        <div class="alert background-danger notify">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            
            <strong>Ошибка!</strong> Код ошибки: '.$_GET['error'].'
        </div>';
}

include("../views/admin_view/header.php"); 

switch($action){
    case "edit":
        include("../views/admin_view/filters/edit_filter_form.php");
        break;
    default:
        include("../views/admin_view/filters/show_filter_list.php");
        break;
}

include("../views/admin_view/footer.php"); 

?>