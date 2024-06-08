<?

$ACCESS_LEVEL = 1;
$CURRENT_FILE = 'requests';

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/offer.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");

if(!isset($_GET['vacancy'])){
    header("Location: /lk/requests.php");
}

$vacancy = Vacancy::GetVacancy($link, $_GET['vacancy'], $_SESSION['id']);
if(!$vacancy){
    header("Location: /lk/requests.php");
}
$vacancy = $vacancy['vacancy'][0];

$user = User::GetWorker($link, $_SESSION['id']);
$filters = $user['filters'];
$user = $user['user'];

include("../views/lk_view/header.php"); 

include("../views/lk_view/offers/send_offer_form.php");

include("../views/lk_view/footer.php"); 

?>