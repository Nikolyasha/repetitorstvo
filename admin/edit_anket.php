<?

$CURRENT_FILE = 'edit_anket';

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
$notify = "";

// API обработчики
if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    switch ($_POST['action']) {
        case "anket_edit":
            // die(print_r($_POST));
            $user = new User($link, -1);
            $filters = $user->filters;
            $user_id = (int) $_GET['id'];
            $extra_fields = [];

            if(((int) $_POST['worker_status'] > 1 || (int) $_POST['worker_status'] < 0) || 
               ((int) $_POST['worker_age'] < 14 || (int) $_POST['worker_age'] > 100) || 
               ((int) $_POST['worker_sex'] > 1 || (int) $_POST['worker_sex'] < 0) || (int) $_POST['worker_city'] < 0)
                    $notify = '
                    <div class="alert background-danger notify">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="icofont icofont-close-line-circled text-white"></i>
                        </button>
                        
                        <strong>Провал.</strong> Данные введены некорректно
                    </div>';
            else{            
                foreach($filters as $filter){
                    if((int) $filter['type'] == 0){
                        if(isset($_POST['extra_'.$filter['name']])){
                            array_push($extra_fields, ["name" => $filter['name'], "value" => 1]);
                        }
                        else{
                            array_push($extra_fields, ["name" => $filter['name'], "value" => 0]);
                        }
                    }
                    else{
                        array_push($extra_fields, ["name" => $filter['name'], "value" => (int) $_POST['extra_'.$filter['name']]]);
                    }
                }

                $week_days_list = ["monday" => 1, "tuesday" => 2, "wednesday" => 3, "thursday" => 4, "friday" => 5, "saturday" => 6, "sunday" => 7];
                $week_days = [];
                foreach(array_keys($_POST) as $param){
                    // print_r(explode("work_", $param)); echo("<hr>");
                    if(count(explode("work_", $param)) > 1 && in_array(explode("work_", $param)[1], array_keys($week_days_list))){
                        array_push($week_days, $week_days_list[explode("work_", $param)[1]]);
                    }
                }
                $_POST["week_days"] = implode(",", $week_days);

                $vacancy_types = [];
                foreach(array_keys($_POST) as $param){
                    if(count(explode("vacancy_type_", $param)) > 1 && (int) explode("vacancy_type_", $param)[1] > 0){
                        array_push($vacancy_types, (int) explode("vacancy_type_", $param)[1]);
                    }
                }
                $_POST["vacancy_types"] = implode(",", $vacancy_types);
                // die(print_r($_POST));

                $extra_fields = json_encode($extra_fields);
                if(User::Edit($link, -1, $_POST, $extra_fields, $user_id)){
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
                        
                        <strong>Провал.</strong> Произошла неизвестная ошибка, попробуйте позднее
                    </div>';
                }
            }
            break;
        case "remove_photo":
            if(isset($_POST['photo_id']) && $_POST['id'] > 0){
                $photo_id = (int) $_POST['photo_id'];
                if($photo_id > 0){
                    if(User::RemovePhoto($link, $photo_id, $_POST['id'])){
                        die("OK");
                    }
                    else{
                        http_response_code(400);
                        die("Bad Request");
                    }
                }
                else{
                    http_response_code(400);
                    die("Bad Request");
                }
            }
            else{
                http_response_code(400);
                die("Bad Request");
            }
        case "upload_photo":
            header("Content-Type: application/json");
            $photos = mysqli_fetch_array(mysqli_query($link, "SELECT `photos` FROM `workers` WHERE `user_id` = '{$_POST['id']}';"))[0];
            if($photos != "")
                $photos = explode(",", $photos);
            else
                $photos = [];
            $result = Array();
            if(!empty($_FILES['bm_photos'])){
                $files = array();
                $diff = count($_FILES['bm_photos']) - count($_FILES['bm_photos'], COUNT_RECURSIVE);
                if ($diff == 0) {
                    $files = array($_FILES['bm_photos']);
                } else {
                    foreach($_FILES['bm_photos'] as $k => $l) {
                        foreach($l as $i => $v) {
                            $files[$i][$k] = $v;
                        }
                    }		
                }	
                $offset = 0;
                for($i = 1; $i <= $_SETTINGS['max_profile_photos_option']; $i++){
                    $name = "{$_POST['id']}_".($i).".jpg";
                    if(!in_array($name, $photos)){
                        $offset = $i;
                        break;
                    }
                }

                $quality = 75;
                $photos = [];
                for($i = 0; $i < count($files); $i++){
                    $error = "";
                    $success = "";
                    while($offset < $_SETTINGS['max_profile_photos_option'] + 1){
                        $name = "{$_POST['id']}_".($i+$offset).".jpg";
                        if(in_array($name, $photos)){
                            $offset++;
                            continue;
                        }
                        $result[$i]['file'] = $name;
                        break;
                    }

                    if($offset == 0 or $offset > $_SETTINGS['max_profile_photos_option']){
                        $error = 'Вы достигли ограничения';
                    }
                    else if($files[$i]['size'] > 524288){
                        $error = 'Файл слишком большой';
                    }
                    else{
                        set_error_handler(function() { /* ignore errors */ });
                        try{
                            switch($files[$i]['type']){
                                case 'image/jpeg': $source = imagecreatefromjpeg($files[$i]['tmp_name']); break;
                                case 'image/png': $source = imagecreatefrompng($files[$i]['tmp_name']); break;  
                                case 'image/gif': $source = imagecreatefromgif($files[$i]['tmp_name']); break;
                                default: $quality = 0; $error = 'Не удалось загрузить файл'; break;
                            }
                        } catch(Exception $ex){
                            die(var_dump($ex));
                            $quality = -1;
                        }
                        restore_error_handler();
                        if($quality > 0 && $source){
                            imagejpeg($source, $_SERVER["DOCUMENT_ROOT"] . "/img/avatars/$name", $quality);
                            imagedestroy($source);
                            array_push($photos, $name);
                            $result[$i]['photo_id'] = ($i+$offset);
                            $success = 'OK';
                        } else {
                            $error = 'Неверный формат фотографии';
                        }
                    }
                    if(!empty($error))
                        $result[$i]['result'] =  $error;
                    else
                        $result[$i]['result'] =  $success;
                    }
                }

                User::InsertPhoto($link, $photos, $_POST['id']);
                die(json_encode($result));
        default:
            break;
    }
} 

$sql = Company::GetCityListSQL().Vacancy::GetTypesSQL();
list($city_list, $vacancy_types) = MultiQuery($link, $sql);

$user = User::GetWorker($link, -1, (int) $_GET['id']);
$filters = $user['filters'];
$user = $user['user'];

include("../views/admin_view/header.php"); 

include("../views/admin_view/edit_anket.php");

include("../views/admin_view/footer.php"); 

?>