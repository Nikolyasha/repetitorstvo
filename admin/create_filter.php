<?


include("init.php");
$CURRENT_FILE = 'create_filter';
$ACTION = "view";
require_once($_SERVER['DOCUMENT_ROOT']."/models/filters.php");

// API обработчики
if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    list($result, $code) = Filter::CreateFilter($link, 
        htmlspecialchars(mysqli_real_escape_string($link, $_POST['filter_object_type'])),
        htmlspecialchars(mysqli_real_escape_string($link, $_POST['filter_name'])),
        htmlspecialchars(mysqli_real_escape_string($link, $_POST['filter_display_name'])),
        htmlspecialchars(mysqli_real_escape_string($link, $_POST['filter_type'])),
        htmlspecialchars(mysqli_real_escape_string($link, $_POST['filter_options']))
    );
    if($result){
        header("Location: filters.php?success");
    }
    else{
        header("Location: filters.php?error=".$code);
    }
    die();
}

include("../views/admin_view/header.php"); 

include("../views/admin_view/filters/create_filter_form.php");

include("../views/admin_view/footer.php"); 

?>