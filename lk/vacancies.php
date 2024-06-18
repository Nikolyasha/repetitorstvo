<? 

$ACCESS_LEVEL = 2;
include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/offer.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/filters.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
if ($_SESSION['admin'] == 1){    
    header('Location: ../');
}
$action = "show";
$CURRENT_FILE = 'vacancies';


// API обработчики
$data = json_decode(file_get_contents("php://input"));

if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    $days = "1,2,3,4,5";
    switch($_POST['vacancy_duration']){
        case "0":
            $days = ($_POST['work_monday'] == "on" ? "1," : "").
                    ($_POST['work_tuesday'] == "on" ? "2," : "").
                    ($_POST['work_wednesday'] == "on" ? "3," : "").
                    ($_POST['work_thursday'] == "on" ? "4," : "").
                    ($_POST['work_friday'] == "on" ? "5," : "").
                    ($_POST['work_saturday'] == "on" ? "6," : "").
                    ($_POST['work_sunday'] == "on" ? "7," : "");
            $days = htmlspecialchars(mysqli_real_escape_string($link, substr($days, 0, -1)));
            break;
        case "1":
            $days = htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_date_start'])).":".htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_date_end']));
            break;
        case "2":
            $days = htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_date']));
            break;
    }

    $filters = Vacancy::FilterList($link);
    $extra_fields = [];

    foreach($filters as $filter){                                                                        

        if((int) $filter['type'] == 0) {                        
            if (count(explode(';', $filter['options'])) > 2) {
                $i = 0;
                $val = ["name" => $filter['name']];
                foreach(explode(";", $filter['options']) as $option) {
                    
                    if ($_POST['extra_'.$filter['name'].'_'.$i] == "on") {
                        echo 'OPT = '.$option.'; ';                                                               
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
        else if ((int) $filter['type'] == 3){
            
                        
            $ph = [];
            // print_r(array_walk($_POST['extra_photos_'.$filter['name']], 'trim_value'));                        

            if (!empty($_POST['extra_photos_add_'.$filter['name']])) {
                $i = 0;
                foreach($_POST['extra_photos_add_'.$filter['name']] as $value){
                    
                    $value = explode(";", $value)[1];
                    $value = explode(",", $value)[1];
                    $value = str_replace(" ", "+", $value);
                    $value = base64_decode($value);

                    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                    $shuflChars = strtoupper(substr(str_shuffle($chars), 0, 4));
                    $randNum = rand(1000, 9999);

                    $photoName = "$randNum$shuflChars.jpg";

                    $ph[$i] = $photoName;

                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/img/filter_photos/$photoName", $value);                                
                    $i++;
                }
            }

            if (!empty($_POST['extra_photos_name_'.$filter['name']])) {
                foreach($_POST['extra_photos_name_'.$filter['name']] as $name) {
                    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/img/filter_photos/".$name)) {
                        array_push($ph, $name);
                    }
                }
            }
            
            if (!empty($_POST['extra_remove_photos_'.$filter['name']])) {
                foreach($_POST['extra_remove_photos_'.$filter['name']] as $name) {
                    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/img/filter_photos/".$name)) {
                        unlink($_SERVER["DOCUMENT_ROOT"] . "/img/filter_photos/".$name);
                    }
                }
            }
            

            if (!empty($_POST['inpUrl_'.$filter['name']])) 
                array_push($ph, $_POST['inpUrl_'.$filter['name']]);
            

            array_push($extra_fields, ["name" => $filter['name'], "value" => implode(",", $ph)]);
        }
        else{
            array_push($extra_fields, ["name" => $filter['name'], "value" => (int) $_POST['extra_'.$filter['name']]]);
        }                    
        
    }

    // foreach($_POST['extra_fields'] as $param){
    //     if(!isset($_POST['extra_'.$param])) $_POST['extra_'.$param] = 0;
    //     if(htmlspecialchars(mysqli_real_escape_string($link, $_POST['extra_'.$param])) == "on") $_POST[$param] = 1;
    //     array_push($extra_fields, ["name" => $param, "value" => ctype_digit($_POST['extra_'.$param]) ? (int) $_POST['extra_'.$param] : ($_POST['extra_'.$param] == "on" ? 1 : 0)]);
    // }    
    
    $extra_fields = json_encode($extra_fields, JSON_UNESCAPED_UNICODE);
    
    $company_id = MultiQuery($link, Company::GetCompanyIDSQL($_SESSION['id']))[0][0]['id'];
    $data = Array(
        "name" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_name'])),
        "owner_id" => $company_id,
        "city_id" => (int) $_POST['vacancy_city'],
        "type_id" => (int) $_POST['vacancy_type'],
        "type_another" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_type_another'])),
        "sex" => (int) $_POST['vacancy_sex'],
        "age_min" => (int) $_POST['vacancy_age_min'],
        "age_max" => (int) $_POST['vacancy_age_max'],
        "experience" => mysqli_real_escape_string($link, $_POST['vacancy_experience']),
        // "need_medical_book" => mysqli_real_escape_string($link, ($_POST['need_medbook'] == "on" ? 1 : 0)),
        // "need_car" => htmlspecialchars(mysqli_real_escape_string($link, ($_POST['need_auto'] == "on" ? 1 : 0))),
        "time_type" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_duration'])),
        "time_from" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_time_start'])),
        "time_to" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_time_end'])),
        "days" => $days,
        "payment_type" => (int) $_POST['vacancy_payment_type'],
        "salary_per_hour" => (int) $_POST['vacancy_salary_per_hour'],
        "salary_per_day" => (int) $_POST['vacancy_salary_per_day'],
        "salary_per_month" => (int) $_POST['vacancy_salary_per_month'],
        "description" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_desc'])),
        "desc_min" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_desc_min'])),
        "workplace_count" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_workplace_count'])),
        "public_date" => date("Y-m-d h:i:s"),
        "contact_info" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_contacts'])),
        "extra_params" => $extra_fields
    );

    if($_POST['action'] == 'create'){
        if ($_SETTINGS['payment_active_option'] == 'false') {

            $sql = "INSERT INTO `vacancies`(`id`, `name`, `owner_id`, `city_id`, `type_id`, `type_another`, `sex`, `age_min`, `age_max`, `experience`, `time_type`, `time_from`, `time_to`, `days`, `payment_type`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `description`, `desc_min`, `workplace_count`, `public_date`, `contact_info`, `extra_params`) VALUES (NULL,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');UPDATE `companies` SET vacancy_avaiable=(vacancy_avaiable-1) WHERE company_owner_id = ".$_SESSION['id'].";";
            $sql = vsprintf($sql, array_values($data));

            list($success, $result) = Vacancy::Execute($link, $sql);
            if($success){
                header("Location: vacancies.php?success");
            }
            else{
                //User::MoneyBack($link, $_SESSION['id'], $_SETTINGS['vacancy_price']);
                header("Location: vacancies.php?error=Не удалось создать вакансию - ".$result);
            }
            die();
        }
        else {
            if(User::Payment($link, $_SESSION['id'], $_SETTINGS['vacancy_price'])){
                $sql = "INSERT INTO `vacancies`(`id`, `name`, `owner_id`, `city_id`, `type_id`, `type_another`, `sex`, `age_min`, `age_max`, `experience`, `time_type`, `time_from`, `time_to`, `days`, `payment_type`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `description`, `desc_min`, `workplace_count`, `public_date`, `contact_info`, `extra_params`) VALUES (NULL,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');UPDATE `companies` SET vacancy_avaiable=(vacancy_avaiable-1) WHERE company_owner_id = ".$_SESSION['id'].";";
                $sql = vsprintf($sql, array_values($data));
                list($success, $result) = Vacancy::Execute($link, $sql);
                if($success){
                    header("Location: vacancies.php?success");
                }
                else{
                    User::MoneyBack($link, $_SESSION['id'], $_SETTINGS['vacancy_price']);
                    header("Location: vacancies.php?error=".$result);
                }
                die();
            }
            else{
                header("Location: vacancies.php?error=Недостаточно средств на балансе");
                die();
            }
            header("Location: vacancies.php?error=Внутренняя ошибка, попробуйте позднее");
            die();
        }
        
    }
    else if($_POST['action'] == 'edit'){
        if(!isset($_POST['post_id'])){
            header("Location: vacancies.php?error=Неверный запрос");
            die();
        }

        if ($_SETTINGS['payment_active_option'] == 'false') {
            unset($data['public_date']);
            unset($data['owner_id']);
    
            $sql = "UPDATE `vacancies` SET `name`='%s',`city_id`='%s',`type_id`='%s',`type_another`='%s',`sex`='%s',`age_min`='%s',`age_max`='%s',`experience`='%s',`time_type`='%s',`time_from`='%s',`time_to`='%s',`days`='%s',`payment_type`='%s',`salary_per_hour`='%s',`salary_per_day`='%s',`salary_per_month`='%s',`description`='%s',`desc_min`='%s',`workplace_count`='%s',`contact_info`='%s',`extra_params`='%s' WHERE id=".((int) $_POST['post_id'])." AND owner_id=".$company_id;
            $sql = vsprintf($sql, array_values($data));
    
            list($success, $result) = Vacancy::Execute($link, $sql);
            if($success){
                header("Location: vacancies.php?success=".((int) $_POST['post_id']));            
            }
            else{
                header("Location: vacancies.php?error=Не удалось отредактировать эту вакансию - ".$result);
            }
            die();
        }
        else {
            if ($_SETTINGS['active_vacancy_edit_option'] == 'true') {
                if(User::Payment($link, $_SESSION['id'], $_SETTINGS['vacancy_price'])){
                    unset($data['public_date']);
                    unset($data['owner_id']);
            
                    $sql = "UPDATE `vacancies` SET `name`='%s',`city_id`='%s',`type_id`='%s',`type_another`='%s',`sex`='%s',`age_min`='%s',`age_max`='%s',`experience`='%s',`time_type`='%s',`time_from`='%s',`time_to`='%s',`days`='%s',`payment_type`='%s',`salary_per_hour`='%s',`salary_per_day`='%s',`salary_per_month`='%s',`description`='%s',`desc_min`='%s',`workplace_count`='%s',`contact_info`='%s',`extra_params`='%s' WHERE id=".((int) $_POST['post_id'])." AND owner_id=".$company_id;
                    $sql = vsprintf($sql, array_values($data));
            
                    list($success, $result) = Vacancy::Execute($link, $sql);
                    if($success){
                        header("Location: vacancies.php?success=".((int) $_POST['post_id']));            
                    }
                    else{
                        User::MoneyBack($link, $_SESSION['id'], $_SETTINGS['vacancy_price']);                
                        header("Location: vacancies.php?error=Не удалось отредактировать эту вакансию - ".$result);
                    }
                    die();
                }
                else{
                    header("Location: vacancies.php?error=Недостаточно средств на балансе");
                    die();
                }
                header("Location: vacancies.php?error=Внутренняя ошибка, попробуйте позднее");
                die();
            }
            else {
                unset($data['public_date']);
                unset($data['owner_id']);
        
                $sql = "UPDATE `vacancies` SET `name`='%s',`city_id`='%s',`type_id`='%s',`type_another`='%s',`sex`='%s',`age_min`='%s',`age_max`='%s',`experience`='%s',`time_type`='%s',`time_from`='%s',`time_to`='%s',`days`='%s',`payment_type`='%s',`salary_per_hour`='%s',`salary_per_day`='%s',`salary_per_month`='%s',`description`='%s',`desc_min`='%s',`workplace_count`='%s',`contact_info`='%s',`extra_params`='%s' WHERE id=".((int) $_POST['post_id'])." AND owner_id=".$company_id;
                $sql = vsprintf($sql, array_values($data));
        
                list($success, $result) = Vacancy::Execute($link, $sql);
                if($success){
                    header("Location: vacancies.php?success=".((int) $_POST['post_id']));            
                }
                else{
                    header("Location: vacancies.php?error=Не удалось отредактировать эту вакансию - ".$result);
                }
                die();
            }
            

            
        }

        
    }
    else{
        header("Location: vacancies.php?error=Неверный запрос");
        die();  
    }

}

if(isset($data->remove)){
    if($data->token != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    if($data->remove > 0){
        if(Vacancy::Remove($link, (int) $data->remove, $_SESSION['id'])){
            header($_SERVER['SERVER_PROTOCOL']." 200 OK");
        }
        else{
            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
        }
    }
    else{
        header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
    }
    exit();
}

// Action SHOW
$vacancy_list = NULL;
$response_count = NULL;
// Action VIEW
$vacancy = NULL;
$responses = NULL;
$OFFER_STATUS = Array("<b>Новый</b>", "<b style='color: darkgreen;'>Принят</b>", "<b style='color: darkred;'>Отклонен</b>");

// Обычные обработчики
if(isset($_GET['view'])){
    $action = "view";
    $vacancy = new Vacancy($link, (int) $_GET['view'], $_SESSION['id'], true);
    if($vacancy->id == -1){
        header("Location: vacancies.php");
    }
    $responses = $vacancy->responses;
    $vacancy = $vacancy->data;
}
else if(isset($_GET['edit'])){
    $action = "edit";
    $vacancy = new Vacancy($link, (int) $_GET['edit'], $_SESSION['id'], false);
    if($vacancy->id == -1){
        header("Location: vacancies.php");
    }
    $sql = User::GetBalanceSQL($_SESSION['id']).Vacancy::GetTypesSQL().Company::GetCityListSQL().Filter::GetFilterListByObjectTypeSQL(0);
    list($current_balance, $vacancy_types, $city_list, $extra_field_list) = MultiQuery($link, $sql);
    
    $vacancy = $vacancy->data;
    $company_id = $current_balance[0]['id'];
}
else if(isset($_GET['create'])){
    $action = "create";
    $CURRENT_FILE = "create_vacanсy";
    $sql = User::GetBalanceSQL($_SESSION['id']).Vacancy::GetTypesSQL().Company::GetCityListSQL().Filter::GetFilterListByObjectTypeSQL(0);
    list($current_balance, $vacancy_types, $city_list, $extra_field_list) = MultiQuery($link, $sql);

    $company_id = $current_balance[0]['id'];
    $current_balance = $current_balance[0]['count'];

    // die(var_dump($current_balance));

    if($current_balance < 1){
        header("Location: buy.php?more");
    }
}
else{
    $sql = Vacancy::GetCompanyVacanciesSQL($_SESSION['id']);
    list($vacancy_list, $response_count) = MultiQuery($link, $sql);
}

$notify = "";
if($action == "show" && isset($_GET['success'])){
    foreach($vacancy_list as $item){
        if($item['id'] == $_GET['success']){
            $notify = '
                <div class="alert background-success notify">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="icofont icofont-close-line-circled text-white"></i>
                    </button>
                    
                    <strong>Успех!</strong> Вакансия «'.$item['name'].'» успешно размещена под <b><a target="blank" href="/vacancy/'.$item['id'].'">#ID'.$item['id'].'</a></b>
                </div>';
            break;
        }
    }
    if($notify == ""){
        $notify = '
            <div class="alert background-success notify">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="icofont icofont-close-line-circled text-white"></i>
                </button>
                
                <strong>Успех!</strong> Вакансия успешно размещена под <b><a target="blank" href="/vacancy/'.$_GET['success'].'">#ID'.$_GET['success'].'</a></b>
            </div>';
    }
}
else if($action == "show" && isset($_GET['error'])){
    $notify = '
        <div class="alert background-danger notify">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            
            <strong>Ошибка!</strong> '.$_GET['error'].'
        </div>';
}
else if($action == "view" && isset($_GET['success'])){
    $notify = '
        <div class="alert background-success notify">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            <strong>Успех!</strong> Ответ успешно отправлен</b>
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

$sql = Offer::GetNewOffersCountSQL($_SESSION['id']);
list($new_offers_count) = MultiQuery($link, $sql);
$new_offers_count = $new_offers_count[0]['new_offers_count'];

include("../views/lk_view/header.php"); 

switch($action){
    case "view":
        include("../views/lk_view/vacancy/show_vacancy_offers.php");
        break;
    case "create":
        include("../views/lk_view/vacancy/new_vacancy_form.php");
        break;
    case "edit":
        include("../views/lk_view/vacancy/edit_vacancy_form.php");
        break;
    default:
        include("../views/lk_view/vacancy/show_vacancy_list.php");
        break;
}

include("../views/lk_view/footer.php"); 

?>