<?

$CURRENT_FILE = 'settings';
$ACCESS_LEVEL = 2;

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/offer.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
if ($_SESSION['admin'] == 1){    
    header('Location: ../');
}
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
        case "company_edit":
            $company = new Company($link, $_SESSION['id']);
            $filters = $company->filters;
            $company_id = $company->id;
            $extra_fields = [];

            if(!empty($_FILES['company_logo'])){
                $quality = 100;
                set_error_handler(function() { /* ignore errors */ });
                try{
                switch($_FILES['company_logo']['type']){
                        case 'image/jpeg': $source = imagecreatefromjpeg($_FILES['company_logo']['tmp_name']); break;
                        case 'image/png': $source = imagecreatefrompng($_FILES['company_logo']['tmp_name']); break;  
                        case 'image/gif': $source = imagecreatefromgif($_FILES['company_logo']['tmp_name']); break;
                        default: $quality = -1; break;
                    }
                } catch(Exception $ex){
                    $quality = -1;
                }
                if($quality > 0){
                    imagejpeg($source, $_SERVER["DOCUMENT_ROOT"] . "/img/companies/$company_id.jpg", $quality);
                    $_POST['company_logo'] = "$company_id.jpg";
                    imagedestroy($source);
                }
                restore_error_handler();
            }

            foreach($filters as $filter){
                // if((int) $filter['type'] == 0){
                //     if(isset($_POST['extra_'.$filter['name']])){
                //         array_push($extra_fields, ["name" => $filter['name'], "value" => 1]);
                //     }
                //     else{
                //         array_push($extra_fields, ["name" => $filter['name'], "value" => 0]);
                //     }
                // }
                // else{
                //     array_push($extra_fields, ["name" => $filter['name'], "value" => (int) $_POST['extra_'.$filter['name']]]);
                // }
                if((int) $filter['type'] == 0) {                        
                    if (count(explode(';', $filter['options'])) > 2) {
                        $i = 0;
                        $val = ["name" => $filter['name']];
                        foreach(explode(";", $filter['options']) as $option) {
                            if ($_POST['extra_'.$filter['name'].'_'.$i] == "on") {                                                               
                                $val += [''.$option.'' => 1];
                            }
                            else
                                $val += [''.$option.'' => 0];
                            
                            $i++;
                        }

                        array_push($extra_fields, $val);
                       
                    }                                        
                    else if ($_POST['extra_'.$filter['name']] == "on") {
                        array_push($extra_fields, ["name" => $filter['name'], "value" => 1]);
                    }
                    else
                        array_push($extra_fields, ["name" => $filter['name'], "value" => 0]);
                }
                else{
                    array_push($extra_fields, ["name" => $filter['name'], "value" => (int) $_POST['extra_'.$filter['name']]]);
                }
            }
            
            $extra_fields = json_encode($extra_fields);
            if(Company::Edit($link, $company_id, $_POST, $extra_fields)){
                $notify = '
                <div class="alert background-success notify">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="icofont icofont-close-line-circled text-white"></i>
                    </button>   
                    <strong>Успех!</strong> Данные успешно сохранены</b>
                </div>';
            }
            else{
                $notify = '
                <div class="alert background-danger notify">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="icofont icofont-close-line-circled text-white"></i>
                    </button>
                    
                    <strong>Провал.</strong> Произошла неизвестная ошибка, попробуйте позднее -> '.mysqli_error($link).'
                </div>';
            }
            break;
        default:
            break;
    }
} 

$sql = Offer::GetNewOffersCountSQL($_SESSION['id']).Company::GetCityListSQL().Company::GetCompanyTypesSQL();
list($new_offers_count, $city_list, $company_types) = MultiQuery($link, $sql);
$new_offers_count = $new_offers_count[0]['new_offers_count'];

$company = new Company($link, $_SESSION['id']);
$filters = $company->filters;
$company = $company->info;

if($_SESSION['account_type'] == 2){
    $sql = Offer::GetNewOffersCountSQL($_SESSION['id']);
    list($new_offers_count) = MultiQuery($link, $sql);
    $new_offers_count = $new_offers_count[0]['new_offers_count'];
} else {
    $user = User::GetWorker($link, $_SESSION['id']);
    $filters = $user['filters'];
    $user = $user['user'];
}

// print_r($company); echo("<br>");
// print_r($city_list); echo("<br>");
// print_r($company_types);

include("../views/lk_view/header.php");  

include("../views/lk_view/settings_form.php");

include("../views/lk_view/footer.php"); 

?>