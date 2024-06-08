<? 

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/filters.php");

$action = "edit";
$CURRENT_FILE = 'edit_vacancy';

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
    
    $extra_fields = [];
    foreach($_POST['extra_fields'] as $param){
        if(!isset($_POST['extra_'.$param])) $_POST['extra_'.$param] = 0;
        if(htmlspecialchars(mysqli_real_escape_string($link, $_POST['extra_'.$param])) == "on") $_POST[$param] = 1;
        array_push($extra_fields, ["name" => $param, "value" => ctype_digit($_POST['extra_'.$param]) ? (int) $_POST['extra_'.$param] : ($_POST['extra_'.$param] == "on" ? 1 : 0)]);
    }
    $extra_fields = json_encode($extra_fields);
    $company_id = MultiQuery($link, Company::GetCompanyIDSQL($_SESSION['id']))[0][0]['id'];
    $data = Array(
        "name" => htmlspecialchars(mysqli_real_escape_string($link, $_POST['vacancy_name'])),
        "owner_id" => $company_id,
        "city_id" => (int) $_POST['vacancy_city'],
        "type_id" => (int) $_POST['vacancy_type'],
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

    if($_POST['action'] == 'edit'){
        if(!isset($_POST['post_id'])){
            header("Location: edit_vacancy.php?error=Неверный запрос");
            die();
        }

        unset($data['public_date']);
        unset($data['owner_id']);

        $sql = "UPDATE `vacancies` SET `name`='%s',`city_id`='%s',`type_id`='%s',`sex`='%s',`age_min`='%s',`age_max`='%s',`experience`='%s',`time_type`='%s',`time_from`='%s',`time_to`='%s',`days`='%s',`payment_type`='%s',`salary_per_hour`='%s',`salary_per_day`='%s',`salary_per_month`='%s',`description`='%s',`desc_min`='%s',`workplace_count`='%s',`contact_info`='%s',`extra_params`='%s' WHERE id=".((int) $_POST['post_id'])." AND owner_id=".$company_id;
        $sql = vsprintf($sql, array_values($data));

        list($success, $result) = Vacancy::Execute($link, $sql);
        if($success){
            header("Location: /vacancy/".((int) $_POST['post_id']));
        }
        else{
            header("Location: edit_vacancy.php?error=Не удалось отредактировать эту вакансию - ".$result);
        }
        die();
    }
    else{
        header("Location: edit_vacancy.php?error=Неверный запрос");
        die();  
    }

}
if(isset($data->remove)){
    if($data->token != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    if($data->remove > 0){
        if(Vacancy::Remove($link, (int) $data->remove, -1)){
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

$vacancy = new Vacancy($link, (int) $_GET['id'], -1, false);
if($vacancy->id == -1){
    header("Location: /admin/");
}
$sql = Vacancy::GetTypesSQL().Company::GetCityListSQL().Filter::GetFilterListByObjectTypeSQL(0);
list($vacancy_types, $city_list, $extra_field_list) = MultiQuery($link, $sql);

$vacancy = $vacancy->data;
$company_id = $current_balance[0]['id'];

include("../views/admin_view/header.php"); 

include("../views/admin_view/edit_vacancy.php");

include("../views/admin_view/footer.php"); 

?>