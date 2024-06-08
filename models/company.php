<?

class Company{

    public $id;
    public $info;
    public $vacancies;
    public $filters;

    public function __construct($link, $owner_id, $user_id = -1, $company_id = -1){
        $purchased = $user_id > 0 ? ", (SELECT COUNT(`id`) FROM `purchases` WHERE `owner_id` = '$user_id' AND `object_type` = 1 AND `object_id` = '$owner_id') as `purchased` " : "";
        $filter = $company_id > 0 ? "WHERE `companies`.`id` = $company_id" : "WHERE `companies`.`company_owner_id` = $owner_id";
        $sql = "SELECT `companies`.`id`, `company_owner_id`, `company_name`, `company_status`, 
                `company_type_name` AS `company_type`, `city_name` AS `city`, `office_adress`, 
                `company_desc`, `company_contacts`, `owner_name`, `owner_phone`, 
                `owner_status`, `logo`, `extra_params` $purchased FROM `companies` 
                INNER JOIN `cities` ON `cities`.`id` = `companies`.`city` 
                INNER JOIN `company_types` ON `company_types`.`id` = `companies`.`company_type` $filter; 
                
                SELECT `vacancies`.`id`, `name`, `owner_id`, `cities`.`city_name` AS `city`, `vacancy_types`.`vacancy_type_name` AS `type`, `sex`, `age_min`, `age_max`, `experience`, `time_type`, `time_from`, `time_to`, `days`, `payment_type`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `description`, `desc_min`, `workplace_count`, `public_date`, `contact_info` FROM `vacancies` 
                INNER JOIN `cities` ON `cities`.`id` = `vacancies`.`city_id`
                INNER JOIN `vacancy_types` ON `vacancy_types`.`id` = `vacancies`.`type_id`
                WHERE owner_id = (SELECT `id` FROM `companies` $filter) ORDER BY `id` DESC LIMIT 10;

                SELECT * FROM `extra_filters` WHERE `object_type` = 2;
                ";
        // print_r($sql);
        list($result, $vacancies, $filters) = MultiQuery($link, $sql);
        if($result){
            $this->id = $result[0]['id'];
            $this->info = $result[0];
            $this->vacancies = $vacancies;
            $this->filters = $filters;
        }
        else {
            $this->id = -1;
        }
    }
    
    public static function Edit($link, $id, $params, $extra){
        $required_params = [
            'company_name', 'company_office_adress', 'company_desc',
            'company_contacts', 'company_owner_name', 'company_owner_phone',
            'company_owner_status'
        ];
        foreach($required_params as $required_param){
            if(empty(trim($params[$required_param]))){
                return False;
            }
        }

        $id = (int) $id;
        $status = (int) $params['company_status'] == 1 ? 1 : 0;
        $sql = "UPDATE `companies` SET 
                    `company_name`      ='".htmlspecialchars(mysqli_real_escape_string($link, trim($params['company_name'])))."',
                    `company_type`      ='".((int) $params['company_type'])."',
                    `company_status`    ='".($status)."',
                    `city`              ='".((int) $params['company_city'])."',
                    `office_adress`     ='".htmlspecialchars(mysqli_real_escape_string($link, trim($params['company_office_adress'])))."',
                    `company_desc`      ='".htmlspecialchars(mysqli_real_escape_string($link, trim($params['company_desc'])))."',
                    `company_contacts`  ='".htmlspecialchars(mysqli_real_escape_string($link, trim($params['company_contacts'])))."',
                    `owner_name`        ='".htmlspecialchars(mysqli_real_escape_string($link, trim($params['company_owner_name'])))."',
                    `owner_phone`       ='".htmlspecialchars(mysqli_real_escape_string($link, trim($params['company_owner_phone'])))."',
                    `owner_status`      ='".htmlspecialchars(mysqli_real_escape_string($link, trim($params['company_owner_status'])))."',
                    `extra_params`      = '".mysqli_real_escape_string($link, $extra)."'
                    ".(!empty($params['company_logo']) ? ', `logo`=\''.htmlspecialchars(mysqli_real_escape_string($link, $params['company_logo'])).'\'' : '')."
                WHERE `id` = $id";
        
        if(mysqli_query($link, $sql)){
            $name = htmlspecialchars(mysqli_real_escape_string($link, trim($params['company_owner_name'])));
            $sql = "UPDATE `users` SET `name` = '$name' WHERE `users`.`id` = $id;";
            if(mysqli_query($link, $sql)){
                $_SESSION['name'] = $name;
                return True;
            }
            return False;
        }
        return False;
    }

    public static function AddFavorite($link, $user_id, $anket_id){
        $user_id = (int) $user_id;
        $anket_id = (int) $anket_id;
        $sql = "UPDATE `companies` SET `favorite` = JSON_ARRAY_APPEND(`favorite`, '$', '$anket_id') WHERE `company_owner_id` = $user_id;";
        $result = mysqli_query($link, "SELECT JSON_CONTAINS(`favorite`, '[\"{$anket_id}\"]') as `result`, `favorite` FROM `companies` WHERE `company_owner_id` = $user_id;");
        $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
        // die(var_dump($result));
        if($result['favorite'] == "null"){
            $sql = "UPDATE `companies` SET `favorite` = '[\"{$anket_id}\"]' WHERE `company_owner_id` = $user_id;";
            $result = 1;
        }
        $result = $result['result'] == 1 ? true : false;
        if($result){
            return "OK";
        } else {
            if(mysqli_query($link, $sql)){
                return "OK";
            } else {
                // print_r($sql);
                return "ERROR";
            }
        }
    }

    public static function RemoveFavorite($link, $user_id, $anket_id){
        $user_id = (int) $user_id;
        $anket_id = (int) $anket_id;
        $sql = "UPDATE `companies` SET `favorite` = JSON_REMOVE(`favorite`, JSON_UNQUOTE(JSON_SEARCH(`favorite`, 'one', '$anket_id'))) WHERE `company_owner_id` = $user_id;";
        if(mysqli_query($link, $sql)){
            return "OK";
        } else {
            return "ERROR";
        }
    }

    public static function UnlockWorkerContacts($link, $user_id, $owner_id){
        $user_id = (int) $user_id;
        $owner_id = (int) $owner_id;
        $sql = "INSERT INTO `purchases` (`owner_id`, `object_type`, `object_id`) VALUES ('$owner_id', 0, (SELECT `user_id` FROM `workers` WHERE `id`='$user_id'));";
        if(mysqli_query($link, $sql)){
            return "OK";
        } else {
            return "ERROR";
        }
    }

    
    // SQL functions
    public static function GetCompanyIDSQL($user_id){
        $user_id = (int) $user_id;
        return "SELECT `id` FROM `companies` WHERE `company_owner_id` = '$user_id';";
    }
    public static function GetFavoriteListSQL($user_id){
        $user_id = (int) $user_id;
        return "SELECT `favorite` FROM `companies` WHERE `company_owner_id` = '$user_id';";
    }
    public static function GetCityListSQL(){
        return "SELECT `id`, `city_name` as `name` FROM `cities`;"; 
    }
    public static function GetCompanyTypesSQL(){
        return "SELECT `id`, `company_type_name` as `name` FROM `company_types`;";
    }

}

?>