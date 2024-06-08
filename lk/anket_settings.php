<?

$CURRENT_FILE = 'anket';
$ACCESS_LEVEL = 1;

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
$notify = "";

function resize_photo($path,$filename,$filesize,$type,$tmp_name){
	$quality = 50;
	$size = 524288;
	if($filesize>$size){
		switch($type){
			case 'image/jpeg': $source = imagecreatefromjpeg($tmp_name); break;
			case 'image/png': $source = imagecreatefrompng($tmp_name); break;  
			case 'image/gif': $source = imagecreatefromgif($tmp_name); break;
			default: echo '$type'; return false;
		}
		imagejpeg($source, $path.$filename, $quality);
		imagedestroy($source);
		return true;
	}
	move_uploaded_file($tmp_name, $path.$filename);
	return true;     
}

function uploadPhotos($input_files, $photos, $max_photos) {
    // header("Content-Type: application/json");
    // $photos = mysqli_fetch_array(mysqli_query($link, "SELECT `photos` FROM `workers` WHERE `user_id` = '{$_SESSION['id']}';"))[0];
    if($photos != "")
        $photos = explode(",", $photos);
    else
        $photos = [];
    $result = Array();
    // if(!empty($_FILES['bm_photos'])){
    if(!empty($input_files)){

        $files = array();
        // $diff = count($_FILES['bm_photos']) - count($_FILES['bm_photos'], COUNT_RECURSIVE);
        $diff = count($input_files) - count($input_files, COUNT_RECURSIVE);
        if ($diff == 0) {
            // $files = array($_FILES['bm_photos']);
            $files = array($input_files);
        } else {
            // foreach($_FILES['bm_photos'] as $k => $l) {
            foreach($input_files as $k => $l) {
                foreach($l as $i => $v) {
                    $files[$i][$k] = $v;
                }
            }		
        }	
        $offset = 0;
        // for($i = 1; $i <= $_SETTINGS['max_profile_photos_option']; $i++){
        for($i = 1; $i <= $max_photos; $i++){
            $name = "{$_SESSION['id']}_".($i).".jpg";
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
            // while($offset < $_SETTINGS['max_profile_photos_option'] + 1){
            while($offset < $max_photos + 1){
                $name = "{$_SESSION['id']}_".($i+$offset).".jpg";
                if(in_array($name, $photos)){
                    $offset++;
                    continue;
                }
                $result[$i]['file'] = $name;
                break;
            }

            // if($offset == 0 or $offset > $_SETTINGS['max_profile_photos_option']){
            if($offset == 0 or $offset > $max_photos){
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

            return [$photos, $result];
        }
        
}

// API обработчики
if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    switch ($_POST['action']) {
        case "anket_edit":
            // die(print_r($_POST));
            $user = new User($link, $_SESSION['id']);
            $filters = $user->filters;
            $user_id = $user->id;
            $extra_fields = [];
            $test = ["name" => $filter['name'], "value" => 1];

            if(((int) $_POST['worker_status'] > 1 || (int) $_POST['worker_status'] < 0) || 
               ((int) $_POST['worker_age'] < 14 || (int) $_POST['worker_age'] > 100) || 
               ((int) $_POST['worker_sex'] > 1 || (int) $_POST['worker_sex'] < 0) || (int) $_POST['worker_city'] < 0 )
                    $notify = '
                    <div class="alert background-danger notify">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="icofont icofont-close-line-circled text-white"></i>
                        </button>
                        
                        <strong>Провал.</strong> Данные введены некорректно
                    </div>';
            else{
                                    
                foreach($filters as $filter){                                                                        

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
                    // else if ((int) $filter['type'] == 3 && $filter['name'] == 'portfolio'){
                    else if ((int) $filter['type'] == 3){
                        
                        $ph = [];                        

                        if (!empty($_POST['extra_photos_'.$filter['name']])) {
                            $i = 0;
                            foreach($_POST['extra_photos_'.$filter['name']] as $value){
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
                if(User::Edit($link, $user_id, $_POST, $extra_fields)){
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
        // case "extra_photo":
        //     http_response_code(400);
        //     echo "AAAA, yes";
        //     break;
        case "remove_photo":
            if(isset($_POST['photo_id']) && $_SESSION['id'] > 0){
                $photo_id = (int) $_POST['photo_id'];
                if($photo_id > 0){
                    if(User::RemovePhoto($link, $photo_id)){
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
            // header("Content-Type: application/json");
            // $photos = mysqli_fetch_array(mysqli_query($link, "SELECT `photos` FROM `workers` WHERE `user_id` = '{$_SESSION['id']}';"))[0];
            // if($photos != "")
            //     $photos = explode(",", $photos);
            // else
            //     $photos = [];
            // $result = Array();
            // if(!empty($_FILES['bm_photos'])){
            //     $files = array();
            //     $diff = count($_FILES['bm_photos']) - count($_FILES['bm_photos'], COUNT_RECURSIVE);
            //     if ($diff == 0) {
            //         $files = array($_FILES['bm_photos']);
            //     } else {
            //         foreach($_FILES['bm_photos'] as $k => $l) {
            //             foreach($l as $i => $v) {
            //                 $files[$i][$k] = $v;
            //             }
            //         }		
            //     }	
            //     $offset = 0;
            //     for($i = 1; $i <= $_SETTINGS['max_profile_photos_option']; $i++){
            //         $name = "{$_SESSION['id']}_".($i).".jpg";
            //         if(!in_array($name, $photos)){
            //             $offset = $i;
            //             break;
            //         }
            //     }

            //     $quality = 75;
            //     $photos = [];
            //     for($i = 0; $i < count($files); $i++){
            //         $error = "";
            //         $success = "";
            //         while($offset < $_SETTINGS['max_profile_photos_option'] + 1){
            //             $name = "{$_SESSION['id']}_".($i+$offset).".jpg";
            //             if(in_array($name, $photos)){
            //                 $offset++;
            //                 continue;
            //             }
            //             $result[$i]['file'] = $name;
            //             break;
            //         }

            //         if($offset == 0 or $offset > $_SETTINGS['max_profile_photos_option']){
            //             $error = 'Вы достигли ограничения';
            //         }
            //         else if($files[$i]['size'] > 524288){
            //             $error = 'Файл слишком большой';
            //         }
            //         else{
            //             set_error_handler(function() { /* ignore errors */ });
            //             try{
            //                 switch($files[$i]['type']){
            //                     case 'image/jpeg': $source = imagecreatefromjpeg($files[$i]['tmp_name']); break;
            //                     case 'image/png': $source = imagecreatefrompng($files[$i]['tmp_name']); break;  
            //                     case 'image/gif': $source = imagecreatefromgif($files[$i]['tmp_name']); break;
            //                     default: $quality = 0; $error = 'Не удалось загрузить файл'; break;
            //                 }
            //             } catch(Exception $ex){
            //                 die(var_dump($ex));
            //                 $quality = -1;
            //             }
            //             restore_error_handler();
            //             if($quality > 0 && $source){
            //                 imagejpeg($source, $_SERVER["DOCUMENT_ROOT"] . "/img/avatars/$name", $quality);
            //                 imagedestroy($source);
            //                 array_push($photos, $name);
            //                 $result[$i]['photo_id'] = ($i+$offset);
            //                 $success = 'OK';
            //             } else {
            //                 $error = 'Неверный формат фотографии';
            //             }
            //         }
            //         if(!empty($error))
            //             $result[$i]['result'] =  $error;
            //         else
            //             $result[$i]['result'] =  $success;
            //         }
            //     }

                header("Content-Type: application/json");

                $photos_sql = mysqli_fetch_array(mysqli_query($link, "SELECT `photos` FROM `workers` WHERE `user_id` = '{$_SESSION['id']}';"))[0];
                $result = uploadPhotos($_FILES['bm_photos'], $photos_sql, $_SETTINGS['max_profile_photos_option']);

                User::InsertPhoto($link, $result[0]);
                die(json_encode($result[1]));

                // User::InsertPhoto($link, $photos);
                // die(json_encode($result));
        case "activateAnket":
            if(User::Payment($link, $_SESSION['id'], $_SETTINGS['vacancy_price'])){
                if(User::ActivateAnket($link, $_SESSION['id'])){
                    die("OK");
                }
                else{
                    User::MoneyBack($link, $_SESSION['id'], $_SETTINGS['vacancy_price']);
                    die("ERROR");
                }
            }
            else{
                die("LOW_BALANCE");
            }
        default:
            break;
    }
} 

$sql = Company::GetCityListSQL().Vacancy::GetTypesSQL();
list($city_list, $vacancy_types) = MultiQuery($link, $sql);

$user = User::GetWorker($link, $_SESSION['id']);
$filters = $user['filters'];
$user = $user['user'];


include("../views/lk_view/header.php");  

include("../views/lk_view/anket_settings_form.php");

include("../views/lk_view/footer.php"); 

?>