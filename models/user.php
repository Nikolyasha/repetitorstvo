<?php

class User{

    public $id = 1;
    public $mail = "";
    public $name = "";
    public $admin = 0;
    public $type = 0;
    public $filters = [];

    public function __construct($link, $id){
        $sql = "SELECT * FROM `users` WHERE `id` = ".((int) $id)."; SELECT * FROM `extra_filters` WHERE `object_type` = 1;";
        list($result, $filters) = MultiQuery($link, $sql);
        $this->id = (int) $result[0]['id'];
        $this->name = $result[0]['name'];
        $this->admin = $result[0]['admin'];
        $this->type = $result[0]['type'];
        $this->mail = $result[0]['email'];
        $this->filters = $filters;
    }

    public static function Users($link) {
        return $link->query("SELECT * FROM `users`");
    }
    public static function GetUser($link, $user_id, $company_id = -1){
        $user_id = (int) $user_id;
        $purchased = $company_id > 0 ? ", (SELECT COUNT(`id`) FROM `purchases` WHERE `owner_id` = '$company_id' AND `object_type` = 0 AND `object_id` = '$user_id') as `purchased` " : "";
        $sql = "SELECT * $purchased FROM `workers` WHERE `user_id`=$user_id;
                SELECT * FROM `extra_filters` WHERE `object_type` = 1;";
                
        list($result, $filters) = MultiQuery($link, $sql);
        if(!$result){
            return [False, False];
        }
        return [$result[0], $filters];
    }

    public static function GetBalanceSQL($user){
        return "SELECT `balance` as `count` FROM `users` WHERE `id` = '".((int) $user)."';";
    }

    public static function GetWorker($link, $user_id, $anket_id = -1){
        $user_id = (int) $user_id;
        $sql = "SELECT * FROM `workers` WHERE `user_id`=$user_id;
                SELECT * FROM `extra_filters` WHERE `object_type` = 1;";
        if($anket_id > 0)
            $sql = "SELECT * FROM `workers` WHERE `id`=$anket_id;
                    SELECT * FROM `extra_filters` WHERE `object_type` = 1;";
        list($result, $filters) = MultiQuery($link, $sql);
        if(!$result){
            return ["user" => False, "filters" => False];
        }
        return ["user" => $result[0], "filters" => $filters];

    }

    static function GenCode($size) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $size; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public static function Auth($link, $mail, $pass, $remember, $by_token = null){
        if($by_token != null){
            $sql = "SELECT * FROM `users` WHERE `auth_token` = '".htmlspecialchars(mysqli_real_escape_string($link, $by_token))."';";
            $result = mysqli_query($link, $sql);
            if(!$result){
                return Array(False);
            }
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            if($result['activation'] != ""){
                // Аккаунт не активирован
                return Array(False, "activation", $mail, explode("_", $result['activation'])[0]);
            }
            return Array(True, $result); 
        } else {
            $sql = "SELECT * FROM `users` WHERE `email` = '".htmlspecialchars(mysqli_real_escape_string($link, $mail))."';";
            $result = mysqli_query($link, $sql);
            if(!$result){
                return Array(False);
            }
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            if(strtolower($result['passwd']) == strtolower(md5($pass))){
                if($result['activation'] != ""){
                    // Аккаунт не активирован
                    return Array(False, "activation", $mail, explode("_", $result['activation'])[0]);
                }

                if($remember){
                    $token = md5(implode("_", $result)."_".time());
                    $sql = "UPDATE `users` SET `auth_token` = '{$token}' WHERE `users`.`id` = {$result['id']};";
                    if(mysqli_query($link, $sql)){
                        $result['token'] = $token;
                    } else {
                        $result['token'] = null;
                    }
                }

                return Array(True, $result); 
            }
            else{
                return Array(False, "wrong");
            }
        }
    }

    public static function SendMail($mail, $activation_code, $title = 'Активация аккаунта', $text = "Ссылка для активации: ", $url = ""){
        if($url == "")
            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/activation.php?token=$activation_code";
        mail($mail, $title, $text . $url);
    }

    public static function ActivateMail($link, int $id){
        $sql = "UPDATE `users` SET `activation` = '' WHERE `id` = $id;";
        return mysqli_query($link, $sql);
    }

    public static function Create($link, $mail, $pass, $name, $account_type, $anket_price){
        if($account_type > 2 || $account_type < 1){
            return Array(False, "bad type");
        }
        $sql = "SELECT COUNT(*) FROM `users` WHERE `email` = '".$mail."';";
        $result = mysqli_query($link, $sql);
        if(!$result){
            return Array(False);
        }
        $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if($result['COUNT(*)'] > 0){
            return Array(False, "mail");
        }
        else{
            // THERE IS A BOY NEXT DOOR
            $activation_code = User::GenCode(10);
            $retoken = md5(htmlspecialchars(mysqli_real_escape_string($link, $mail)));
            $sql = "INSERT INTO `users`(`id`, `email`, `passwd`, `name`, `admin`, `activation`, `type`, `base_type`) VALUES (NULL,'".htmlspecialchars(mysqli_real_escape_string($link, $mail))."','".md5($pass)."','".htmlspecialchars(mysqli_real_escape_string($link, $name))."',0,'".htmlspecialchars(mysqli_real_escape_string($link, $retoken.'_'.$activation_code))."',$account_type,$account_type)";
            // die($sql);
            if(mysqli_query($link, $sql)){
                $user_id = mysqli_insert_id($link);
                $name = htmlspecialchars(mysqli_real_escape_string($link, $name));
                if($account_type == 1){
                    $name = explode(" ", $name);
                    $activation = $anket_price > 1 ? 0 : 1;
                    $sql = "INSERT INTO `workers`(`user_id`, `status`, `activation`, `age`, `first_name`, `last_name`, `sex`, `phone`, `job_types`, `min_salary`, `week_days`, `time_range`, `experience`, `special`, `about`, `view`, `city`, `birthday`, `photos`, `last_online`, `views`) VALUES ($user_id,0,$activation,18,'{$name[0]}','{$name[1]}',0,'','',0,'','08:00-18:00','','','','',1,'2000-01-01 00:00:00','',0,0)";
                }
                else{
                    $sql = "INSERT INTO `companies`(`company_owner_id`, `company_name`, `company_type`, `city`, `office_adress`, `company_desc`, `company_contacts`, `owner_name`, `owner_phone`, `owner_status`, `logo`) VALUES ($user_id,'Компания',0,1,'','','','$name', '','','no_photo.jpg')";
                }
                if(mysqli_query($link, $sql)){
                    
                    User::SendMail($mail, $activation_code);
                    return Array(True, $user_id, $retoken);
                }
                return Array(False, "Внутренняя ошибка, попробуйте позднее (Код: 2)");
            }
            else{
                return Array(False, "Внутренняя ошибка, попробуйте позднее (Код: 1)");
            }
        }
    }
    public static function ChangePasswd($link, $id, $old_pass, $new_pass, $new_pass_repeat, $token = ""){
        if(md5($new_pass) != md5($new_pass_repeat)){
            return False;
        }
        if($token != ""){
            $sql = "SELECT `id` FROM `users` WHERE `recovery_token` = '".htmlspecialchars(mysqli_real_escape_string($link, $token))."';";
            $result = mysqli_query($link, $sql);
            if(!$result){
                return False;
            }
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            if((int) $result['id'] > 0){
                $sql = "UPDATE `users` SET `passwd` = '".(strtolower(md5($new_pass)))."' WHERE `id` = '".((int) $result['id'])."';";
                $result = mysqli_query($link, $sql); 
                if($result){
                    return True;
                }
                else{
                    return False; 
                }
            }
            else{
                return False;
            }
        } else {
            $sql = "SELECT `passwd` FROM `users` WHERE `id` = '".((int) $id)."';";
            $result = mysqli_query($link, $sql);
            if(!$result){
                return Array(False);
            }
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            if(strtolower($result['passwd']) == strtolower(md5($old_pass))){
                $sql = "UPDATE `users` SET `passwd` = '".(strtolower(md5($new_pass)))."' WHERE `id` = '".((int) $id)."';";
                $result = mysqli_query($link, $sql); 
                if($result){
                    return Array(True, $result);
                }
                else{
                    return Array(False, $result); 
                }
            }
            else{
                return Array(False, "wrong");
            }
        }
    }

    public static function ResetPassword($link, $mail){
        $sql = "SELECT * FROM `users` WHERE `email` = '".htmlspecialchars(mysqli_real_escape_string($link, $mail))."';";
        $result = mysqli_query($link, $sql);
        if(!$result){
            return Array(False);
        }
        $result = mysqli_fetch_array($result, MYSQLI_ASSOC);

        if((int) $result['id'] > 0){
            $token = md5(implode("_", $result)."_".time());
            $sql = "UPDATE `users` SET `recovery_token` = '$token' WHERE `id` = {$result['id']};";
            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/login.php?recovery&token=$token";
            User::SendMail($mail, null, $title = "Сброс пароля", $text = "Ссылка для восстановления пароля: ", $url = $url);
            return mysqli_query($link, $sql);
        } else {
            return False;
        }
    }

    public static function RemovePhoto($link, int $photo_id, $anket_id = -1)
    {
        $sql = "SELECT `photos` FROM `workers` WHERE `user_id` = '{$_SESSION['id']}';";
        if($anket_id > 0)
            $sql = "SELECT `photos` FROM `workers` WHERE `id` = '{$anket_id}';";
        $photos = mysqli_fetch_array(mysqli_query($link, $sql))[0];
        $photos = explode(",", $photos);
        if(count($photos) > 0){
            unset($photos[$photo_id-1]);
            $photos = implode(",", $photos);
            $sql = "UPDATE `workers` SET `photos`='$photos' WHERE `user_id` = '{$_SESSION['id']}';";
            if($anket_id > 0)
                $sql = "UPDATE `workers` SET `photos`='$photos' WHERE `id` = '{$anket_id}';";
            mysqli_query($link, $sql);
            return True;
        }
        else{
            return False;
        }
    }

    public static function Edit($link, $id, $params, $extra, $anket_id = -1){
        
        // Array ( [action] => anket_edit [worker_name] => Кондратьев [worker_phone] => +79000000000 [worker_city] => 2 [worker_sex] => 1 [worker_age] => 25 [worker_birthday] => 2021-04-13 [worker_exp_desc] => e [worker_time_start] => 08:00 [worker_time_end] => 18:00 [work_monday] => on [work_tuesday] => on [work_wednesday] => on [work_thursday] => on [work_friday] => on [vacancy_type_3] => on [vacancy_type_4] => on [vacancy_type_5] => on [worker_salary_per_hour] => 100 [worker_about] => a [worker_view] => v [worker_special] => s [extra_have_auto] => on [photos] => 4_2.jpg,4_5.jpg,4_4.jpg,4_1.jpg,4_3.jpg ) 1

        $id = (int) $id;
        $anket_id = $anket_id > 0 ? "`id` = ". ((int) $anket_id) : "`user_id` = $id";
        $sql = "UPDATE `workers` SET 
                    `status`            = '".((int) $params['worker_status'])."',
                    `age`               = '".((int) $params['worker_age'])."',
                    `first_name`        = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_first_name'])))."',
                    `last_name`         = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_last_name'])))."',
                    `sex`               = '".((int) $params['worker_sex'])."',
                    `phone`             = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_phone'])))."',
                    `viber`             = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_viber'])))."',
                    `telegram`          = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_telegram'])))."',
                    `whatsapp`          = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_whatsapp'])))."',
                    `job_types`         = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['vacancy_types'])))."',
                    `min_salary`        = '".((int) $params['worker_salary_per_hour'])."',
                    `week_days`         = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['week_days'])))."',
                    `time_range`        = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_time_start'])))."-".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_time_end'])))."',
                    `experience`        = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_exp_desc'])))."',
                    `special`           = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_special'])))."',
                    `about`             = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_about'])))."',
                    `view`              = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_view'])))."',
                    `city`              = '".((int) $params['worker_city'])."',
                    `birthday`          = '".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_birthday'])))."',
                    `extra_fields`      = '".mysqli_real_escape_string($link, $extra)."'
                    
                WHERE $anket_id";
        // die($sql);
        if(mysqli_query($link, $sql)){
            $name = htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_first_name'])))." ".htmlspecialchars(mysqli_real_escape_string($link, trim($params['worker_last_name'])));
            $sql = "UPDATE `users` SET `name` = '$name' WHERE `users`.`id` = $id;";
            if(mysqli_query($link, $sql)){
                $_SESSION['name'] = $name;
                return True;
            }
            return False;
        }
        return False;
    }

    public static function InsertPhoto($link, $photo_list, $user_id = -1)
    {
        $user_id = $user_id > 0 ? $user_id: $_SESSION['id'];
        $user_id = "`user_id` = '{$user_id}'";
        $photos = mysqli_fetch_array(mysqli_query($link, "SELECT `photos` FROM `workers` WHERE $user_id;"))[0];
        
        if($photos != "")
            $photos = explode(",", $photos);
        else
            $photos = [];

        foreach($photo_list as $photo){
            if(count($photos) >= 5){
                break;
            }
            array_push($photos, $photo);
        }
        $photos = implode(",", $photos);
        mysqli_query($link, "UPDATE `workers` SET `photos`='$photos' WHERE $user_id;");
        
        return True;
    }

    public static function SetOnline($link, $user_id){
        $user_id = (int) $user_id;
        $time = time();
        $sql = "UPDATE `workers` SET `last_online` = '$time' WHERE `user_id`=$user_id;";
        if(mysqli_query($link, $sql)){
            return "OK";
        } else {
            return "ERROR";
        }
    }

    public static function CountView($link, $anket_id){
        $anket_id = (int) $anket_id;
        $sql = "UPDATE `workers` SET `views` = `views` + 1 WHERE `workers`.`user_id` = $anket_id;";
        if(mysqli_query($link, $sql)){
            return True;
        }
        return False;
    }

    public static function Payment($link, $user_id, $amount){
        $user_id = (int) $user_id;
        $amount = (int) $amount;
        $sql = "UPDATE `users` SET `balance` = `balance` - $amount WHERE `id`=$user_id AND `balance` >= $amount;";
        if(mysqli_query($link, $sql)){
            if(mysqli_affected_rows($link) > 0){
                return True;
            }
            return False;
        } else {
            return False;
        }
    }

    public static function MoneyBack($link, $user_id, $amount){
        $user_id = (int) $user_id;
        $amount = (int) $amount;
        $sql = "UPDATE `users` SET `balance` = `balance` + $amount WHERE `id`=$user_id;";
        if(mysqli_query($link, $sql)){
            if(mysqli_affected_rows($link) > 0){
                return True;
            }
            return False;
        } else {
            return False;
        }
    }

    public static function AddFavorite($link, $user_id, $vacancy_id){
        $user_id = (int) $user_id;
        $vacancy_id = (int) $vacancy_id;
        $sql = "UPDATE `workers` SET `favorite` = JSON_ARRAY_APPEND(`favorite`, '$', '$vacancy_id') WHERE `user_id` = $user_id;";
        $result = mysqli_query($link, "SELECT JSON_CONTAINS(`favorite`, '[\"{$vacancy_id}\"]') as `result` FROM `workers` WHERE `user_id` = $user_id;");
        $result = mysqli_fetch_array($result, MYSQLI_ASSOC)['result'];
        if($result == null){
            $sql = "UPDATE `workers` SET `favorite` = '[\"{$vacancy_id}\"]' WHERE `user_id` = $user_id;";
            $result = 0;
        }
        $result = $result == 1 ? true : false;
        if($result){
            return "OK";
        } else {
            if(mysqli_query($link, $sql)){
                return "OK";
            } else {
                return "ERROR";
            }
        }
    }

    public static function RemoveFavorite($link, $user_id, $vacancy_id){
        $user_id = (int) $user_id;
        $vacancy_id = (int) $vacancy_id;
        $sql = "UPDATE `workers` SET `favorite` = JSON_REMOVE(`favorite`, JSON_UNQUOTE(JSON_SEARCH(`favorite`, 'one', '$vacancy_id'))) WHERE `user_id` = $user_id;";
        if(mysqli_query($link, $sql)){
            return "OK";
        } else {
            return "ERROR";
        }
    }

    public static function UnlockCompanyContacts($link, $company_id, $owner_id){
        $company_id = (int) $company_id;
        $owner_id = (int) $owner_id;
        $sql = "INSERT INTO `purchases` (`owner_id`, `object_type`, `object_id`) VALUES ('$owner_id', 1, '$company_id');";
        if(mysqli_query($link, $sql)){
            return "OK";
        } else {
            return "ERROR";
        }
    }

    public static function ActivateAnket($link, int $user_id, $anket_id = -1){
        $user = $anket_id > 0 ? "`id` = $user_id" : "`user_id` = $user_id";
        $sql = "UPDATE `workers` SET `activation` = 1 WHERE $user";
        // die($sql);
        if(mysqli_query($link, $sql)){
            return "OK";
        } else {
            return "ERROR";
        }
    }

    
    // SQL functions
    public static function GetLastWorkerPhotos($link){
        return "SELECT `user_id`, `photos` FROM `workers` WHERE `photos` <> '' AND `activation` > 0 ORDER BY `id` DESC LIMIT 22;";
    }

    public static function GetFavoriteListSQL($user_id){
        $user_id = (int) $user_id;
        return "SELECT `favorite` FROM `workers` WHERE `user_id` = '$user_id';";
    }
    public static function GetWorkersCountSQL(){
        return "SELECT COUNT(*) FROM `workers` WHERE `activation` > 0;";
    }

    public static function GetFilterListSQL(){
        return "SELECT * FROM `extra_filters` WHERE `object_type` = 1;";     
    }

    public static function GetAnketsListSQL($order_by, $params, $offset){
        return "SELECT * FROM `workers`
                WHERE `activation` > 0 " . (count($params) > 0 ? " AND " . implode(" AND ", $params) : "") . "  
                ORDER BY $order_by LIMIT 10 $offset;

                SELECT COUNT(*) as `ankets_count` FROM `workers` WHERE `activation` > 0 " . (count($params) > 0 ? " AND " . implode(" AND ", $params) : "") . ";";     
    }

    public static function GetAnketViewsSQL(int $user_id){
        return "SELECT `views` as `anket_views` FROM `workers` WHERE `user_id` = $user_id;";
    }

    public static function GetAnketPurchasesCountSQL(int $user_id){
        return "SELECT COUNT(`id`) as `anket_purchases_count` FROM `purchases` WHERE `object_type` = 0 AND `object_id` = $user_id;";
    }

    public static function GetAnketPurchasesSQL(int $user_id){
        return "SELECT `companies`.`id` as `id`, `companies`.`company_name` as `name`, `purchase_date` FROM `purchases` INNER JOIN `companies` ON (`companies`.`id` = `purchases`.`owner_id`) WHERE `object_type` = 0 AND `object_id` = $user_id;";
    }

    // public static function GetAnketsFavoriteListSQL($order_by, $list, $offset){
    //     $list = implode(",", $list);
    //     return "SELECT `vacancies`.`id`, `name`, `owner_id`, `cities`.`city_name` as `city`, 
    //             `vacancy_types`.`vacancy_type_name` as `type`, `sex`, `experience`, `time_type`, 
    //             `days`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `desc_min`, 
    //             `public_date`, `companies`.`company_name` as `company`, `companies`.`logo` as `logo`, 
    //             `company_types`.`company_type_name` as `company_type` FROM `vacancies`
    //             INNER JOIN cities ON (vacancies.city_id=cities.id) 
    //             INNER JOIN companies ON (vacancies.owner_id=companies.id) 
    //             INNER JOIN company_types ON (companies.company_type=company_types.id) 
    //             INNER JOIN vacancy_types ON (vacancies.type_id=vacancy_types.id)
    //             WHERE `vacancies`.`id` in ($list) 
    //             ORDER BY $order_by LIMIT 10 $offset;

    //             SELECT COUNT(*) as `vacancies_count` FROM `vacancies` WHERE `vacancies`.`id` in ($list);";     
    // }

}
?>