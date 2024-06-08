<? 

if(isset($_POST['action'])){
    include("init.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    switch($_POST['action']){
        case "addFavorite":
            if($_SESSION['account_type'] == 1 && (int) $_POST['vacancy_id'] > 0){
                echo User::AddFavorite($link, $_SESSION['id'], (int) $_POST['vacancy_id']);
            }
            else if($_SESSION['account_type'] == 2 && (int) $_POST['anket_id'] > 0){
                echo Company::AddFavorite($link, $_SESSION['id'], (int) $_POST['anket_id']);
            }
            break;
        case "removeFavorite":
            if($_SESSION['account_type'] == 1 && (int) $_POST['vacancy_id'] > 0){
                echo User::RemoveFavorite($link, $_SESSION['id'], (int) $_POST['vacancy_id']);
            }
            else if($_SESSION['account_type'] == 2 && (int) $_POST['anket_id'] > 0){
                echo Company::RemoveFavorite($link, $_SESSION['id'], (int) $_POST['anket_id']);
            }
            break;
        default: break;
    }
}

// echo User::AddFavorite($link, 1, 1);
// echo User::RemoveFavorite($link, 1, 1);

// UPDATE `workers` SET `favorite` = JSON_ARRAY_APPEND(`favorite`, '$', "1") WHERE id = 1;
// UPDATE `workers` SET `favorite` = JSON_REMOVE(`favorite`, JSON_UNQUOTE(JSON_SEARCH(`favorite`, 'one', '1'))) WHERE id = 1;
?>