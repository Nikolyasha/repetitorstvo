<?

$CURRENT_FILE = 'edit_page';

include("init.php");
// require_once($_SERVER['DOCUMENT_ROOT']."/models/filters.php");

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
            $page_content = mysqli_real_escape_string($link, $_POST['page_content']);
            $page_name = htmlspecialchars(mysqli_real_escape_string($link, $_POST['page_title']));
            $page_id = (int) $_POST['page_id'];
            $sql = "UPDATE `site_pages` SET `name`='$page_name', `html` = '$page_content' WHERE `site_pages`.`id` = '$page_id';" .
                   "UPDATE `site_pages` SET `name`='Preview', `html` = '<script>swal(\"Ошибка предпросмотра\", \"Открывайте эту страницу только из админ панели\", \"error\").then(() => {window.location=\"/admin/edit_page.php\"});</script> ' WHERE `site_pages`.`id` = '0';";
            if(mysqli_multi_query($link, $sql)){
                header("Location: edit_page.php?success");
            }
            else{
                header("Location: edit_page.php?error=".$code);
            }
            die();
        case 'preview':
            $page_content = mysqli_real_escape_string($link, $_POST['page_content']);
            $page_name = htmlspecialchars(mysqli_real_escape_string($link, $_POST['page_name']));
            $sql = "UPDATE `site_pages` SET `name`='$page_name', `html` = '$page_content' WHERE `site_pages`.`id` = '0';";
            if(mysqli_query($link, $sql)){
                die("OK");
            }
            die("ERROR");
        default:
            header("Location: ".$redirect);
            die();
    }
}

$action = "";

//Обработчик страниц
if(isset($_GET['edit'])){
    $action = "edit";
    $_GET['edit'] = (int) $_GET['edit'];
    if($_GET['edit'] > 0){
        $sql = "SELECT * FROM `site_pages` WHERE `id` = '{$_GET['edit']}';";
        $page = MultiQuery($link, $sql)[0][0];
    } else {
        $action = "view";
        $sql = "SELECT * FROM `site_pages` WHERE `id` >= 1;";
        list($pages) = MultiQuery($link, $sql);
    }
}
else{
    $action = "view";
    $sql = "SELECT * FROM `site_pages` WHERE `id` >= 1;";
    list($pages) = MultiQuery($link, $sql);
}

if($action == "view" && isset($_GET['success'])){
    $notify = '
        <div class="alert background-success notify">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            <strong>Успех!</strong> Страница успешно отредактирована</b>
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
        include("../views/admin_view/edit_page.php");
        break;
    default:
        include("../views/admin_view/edit_page_list.php");
        break;
}

include("../views/admin_view/footer.php"); 

?>