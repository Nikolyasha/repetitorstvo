<? 


include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/offer.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/site.php");
if ($_SESSION['admin'] == 1){    
    header('Location: ../');
}
include("../views/lk_view/header.php"); 

  

switch($_SESSION['account_type']){
    case 1:
        $sql = User::GetAnketViewsSQL($_SESSION['id']).User::GetAnketPurchasesCountSQL($_SESSION['id']).User::GetAnketPurchasesSQL($_SESSION['id']);
        list($anket_views, $anket_purchases_count, $anket_purchases) = MultiQuery($link, $sql);
        $anket_views           = $anket_views[0]['anket_views'];
        $anket_purchases_count = $anket_purchases_count[0]['anket_purchases_count'];

        $user = User::GetWorker($link, $_SESSION['id']);
        $filters = $user['filters'];
        $user = $user['user'];
        
        include("../views/lk_view/employee_index.php"); 
        break;
    case 2:        
        $sql = Offer::GetListSQL($_SESSION['id'], $limit = 10).Offer::GetNewOffersCountSQL($_SESSION['id']).Offer::GetOffersCountSQL($_SESSION['id']).Vacancy::GetVacanciesCountSQL($_SESSION['id']).Vacancy::GetVacanciesViewsSQL($_SESSION['id']);
        list($responses, $new_offers_count, $offers_count, $vacancies_count, $vacancies_views) = MultiQuery($link, $sql);
        $offers_count     = $offers_count[0]['offers_count'];
        $vacancies_count  = $vacancies_count[0]['vacancies_count'];
        $vacancies_views  = $vacancies_views[0]['vacancies_views'];
        $new_offers_count = $new_offers_count[0]['new_offers_count'];
        $OFFER_STATUS = Array("<b>Новый</b>", "<b style='color: darkgreen;'>Принят</b>", "<b style='color: darkred;'>Отклонен</b>");

        include("../views/lk_view/employer_index.php"); 
        break;
    case 3:
        $sql = User::GetAnketViewsSQL($_SESSION['id']).User::GetAnketPurchasesCountSQL($_SESSION['id']).User::GetAnketPurchasesSQL($_SESSION['id']);
        list($anket_views, $anket_purchases_count, $anket_purchases) = MultiQuery($link, $sql);
        $anket_views           = $anket_views[0]['anket_views'];
        $anket_purchases_count = $anket_purchases_count[0]['anket_purchases_count'];

        $user = User::GetWorker($link, $_SESSION['id']);
        $filters = $user['filters'];
        $user = $user['user'];
        
        include("../views/lk_view/hr_index.php"); 
        break;    
    default:
        die("500 Internal Server Error");
}


include("../views/lk_view/footer.php"); 

?>