<?

class Vacancy{

    public $id;
    public $data;
    public $responses;

    public function __construct($link, $id, $user, $need_responses){
        $sql = "SELECT `vacancies`.`id`, `vacancies`.`name`, `vacancies`.`owner_id`, `vacancies`.`city_id`, `vacancies`.`type_id`,`vacancies`.`type_another`, `vacancies`.`sex`, `vacancies`.`age_min`, `vacancies`.`age_max`, `vacancies`.`experience`, `vacancies`.`time_type`, `vacancies`.`time_from`, `vacancies`.`time_to`, `vacancies`.`days`, `vacancies`.`payment_type`, `vacancies`.`salary_per_hour`, `vacancies`.`salary_per_day`, `vacancies`.`salary_per_month`, `vacancies`.`description`, `vacancies`.`desc_min`, `vacancies`.`workplace_count`, `vacancies`.`public_date`, `vacancies`.`contact_info`, `vacancies`.`views`, `vacancies`.`extra_params`, `company_owner_id`, `company_status` FROM `vacancies` INNER JOIN companies ON (companies.id = vacancies.owner_id) WHERE vacancies.id = ".((int) $id).";";
        $data = $responses = Null;
        if($need_responses){
            $sql = $sql."SELECT `responses`.`id`, `responses`.`user_id`, `vacancy_id`, `date`, `offer`, `responses`.`status`, `response`, CONCAT(`workers`.`first_name`, ' ', `workers`.`last_name`) AS `name` FROM `responses` INNER JOIN `workers` ON `workers`.`user_id` = `responses`.`user_id` WHERE `vacancy_id` = '".((int) $id)."' AND `responses`.`remove` != 1;"; // 
            list($data, $responses) = MultiQuery($link, $sql);
        }
        else{
            list($data) = MultiQuery($link, $sql);
        }
        $data = $data[0];

        if($data['company_owner_id'] == $user || $user = -1){
            $this->id = $data['id'];
            $this->data = $data;
            if($need_responses) $this->responses = $responses;
        } 
        else{
            $this->id = -1;
        }
    }

    // Для рендеринга страниц вакансий
    public static function GetVacancy($link, $id, $user_id = -1){
        $user_id = (int) $user_id;
        $purchased = $user_id > 0 ? ", (SELECT COUNT(`id`) FROM `purchases` WHERE `owner_id` = '$user_id' AND `object_type` = 1 AND `object_id` = (SELECT `owner_id` FROM `vacancies` WHERE `vacancies`.`id` = '".((int) $id)."')) as `purchased` " : "";
        $sql = "SELECT `companies`.`id`, `company_name`, `company_type_name` AS `company_type`, `company_desc`, `logo` FROM `companies` 
                    INNER JOIN `cities` ON `cities`.`id` = `companies`.`city` 
                    INNER JOIN `company_types` ON `company_types`.`id` = `companies`.`company_type` 
                WHERE `companies`.`id` = 
                (
                    SELECT `owner_id` FROM `vacancies` WHERE `vacancies`.`id` = '".((int) $id)."'
                ) AND `companies`.`company_status` = 1; 

                SELECT `vacancies`.`id`, `name`, `owner_id`, `cities`.`city_name` AS `city`, `vacancy_types`.`vacancy_type_name` 
                AS `type`, `vacancies`.`type_another` AS `type_another`, `vacancies`.`type_id` AS `type_id`,`sex`, `age_min`, `age_max`, `experience`, `time_type`, `time_from`, 
                `time_to`, `days`, `payment_type`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `description`, `desc_min`, 
                `workplace_count`, `public_date`, `contact_info`, `extra_params`, 
                (SELECT COUNT(*) FROM `responses` WHERE `vacancy_id` = `vacancies`.`id`) as `request_count` $purchased FROM `vacancies` 
                    INNER JOIN `cities` ON `cities`.`id` = `vacancies`.`city_id` 
                    INNER JOIN `vacancy_types` ON `vacancy_types`.`id` = `vacancies`.`type_id` 
                WHERE `vacancies`.`id` = '".((int) $id)."';
                
                SELECT * FROM `extra_filters` WHERE `object_type` = 0;";
                
        list($company, $vacancy, $filters) = MultiQuery($link, $sql);
        if($company){
            return Array(
                "vacancy" => $vacancy,
                "company" => $company,
                "filters" => $filters
            );
        }
        else{
            return Array(
                "vacancy" => false,
                "company" => false,
                "filters" => false
            );
        }      
    }

    public static function CountView($link, $vacancy_id){
        $vacancy_id = (int) $vacancy_id;
        $sql = "UPDATE `vacancies` SET `views` = `views` + 1 WHERE `vacancies`.`id` = $vacancy_id;";
        if(mysqli_query($link, $sql)){
            return True;
        }
        return False;
    }

    public static function GetVacancyListSQL($order_by, $params, $offset){
        return "SELECT `vacancies`.`id`, `name`, `owner_id`, `cities`.`city_name` as `city`, 
                `vacancy_types`.`vacancy_type_name` as `type`, `vacancies`.`type_another` AS `type_another`, `vacancies`.`type_id` AS `type_id`,`sex`, `experience`, `time_type`, 
                `days`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `desc_min`, 
                `public_date`, `companies`.`company_name` as `company`, `companies`.`logo` as `logo`, 
                `company_types`.`company_type_name` as `company_type` FROM `vacancies`
                INNER JOIN cities ON (vacancies.city_id=cities.id) 
                INNER JOIN companies ON (vacancies.owner_id=companies.id) 
                INNER JOIN company_types ON (companies.company_type=company_types.id) 
                INNER JOIN vacancy_types ON (vacancies.type_id=vacancy_types.id)
                WHERE `companies`.`company_status` = 1 ".(count($params) > 0 ? " AND " . implode(" AND ", $params) : "")."  
                ORDER BY $order_by LIMIT 10 $offset;

                SELECT COUNT(*) as `vacancies_count` FROM `vacancies` INNER JOIN companies ON (vacancies.owner_id=companies.id) WHERE `companies`.`company_status` = 1 ".(count($params) > 0 ? " AND " . implode(" AND ", $params) : "").";";     
    }

    public static function GetVacancyFavoriteListSQL($order_by, $list, $offset){
        $list = implode(",", $list);
        return "SELECT `vacancies`.`id`, `name`, `owner_id`, `cities`.`city_name` as `city`, 
                `vacancy_types`.`vacancy_type_name` as `type`, `vacancies`.`type_another` AS `type_another`, `vacancies`.`type_id` AS `type_id`,`sex`, `experience`, `time_type`, 
                `days`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `desc_min`, 
                `public_date`, `companies`.`company_name` as `company`, `companies`.`logo` as `logo`, 
                `company_types`.`company_type_name` as `company_type` FROM `vacancies`
                INNER JOIN cities ON (vacancies.city_id=cities.id) 
                INNER JOIN companies ON (vacancies.owner_id=companies.id) 
                INNER JOIN company_types ON (companies.company_type=company_types.id) 
                INNER JOIN vacancy_types ON (vacancies.type_id=vacancy_types.id)
                WHERE `vacancies`.`id` in ($list)
                ORDER BY $order_by LIMIT 10 $offset;

                SELECT COUNT(*) as `vacancies_count` FROM `vacancies` INNER JOIN companies ON (vacancies.owner_id=companies.id) WHERE `vacancies`.`id` in ($list) AND `companies`.`company_status` = 1;";     
    }

    public static function GetAnketFavoriteListSQL($order_by, $list, $offset){
        $list = implode(",", $list);
        return "SELECT * FROM `workers` WHERE `id` in ($list) AND `activation` > 0 
                ORDER BY $order_by LIMIT 10 $offset;

                SELECT COUNT(*) as `ankets_count` FROM `workers` WHERE `id` in ($list) AND `activation` > 0;";     
    }

    public static function GetVacancyName($link, $id){
        $sql = "SELECT `name` FROM `vacancies` WHERE `id` = ".((int) $id).";";
        $result = MultiQuery($link, $sql);
        if(!$result[0]){
            return False;
        }
        return $result[0][0]['name'];
    }

    public static function Execute($link, $sql){
        // $sql = "INSERT INTO `vacancies`(`id`, `name`, `owner_id`, `city_id`, `type_id`, `sex`, `age_min`, `age_max`, `experience`, `time_type`, `time_from`, `time_to`, `days`, `payment_type`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `description`, `desc_min`, `workplace_count`, `public_date`, `contact_info`) VALUES (NULL,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')";
        // $sql = vsprintf($sql, array_values($data));
        if(mysqli_multi_query($link, $sql)){
            return Array(true, mysqli_insert_id($link));
        }
        else{
            return Array(false, mysqli_error($link));
        }

    }

    public static function Remove($link, $id, $user){
        $sql = "DELETE FROM `vacancies` WHERE `owner_id` = (SELECT id FROM companies WHERE company_owner_id = '".((int) $user)."') AND id = '".((int) $id)."';";
        if($user == -1)
            $sql = "DELETE FROM `vacancies` WHERE id = '".((int) $id)."';";
        if(mysqli_query($link, $sql)){
            return Array(true);
        }
        else{
            return Array(false, mysqli_error($link));
        }
    }

    public static function GetTypesSQL(){
        return "SELECT * FROM `vacancy_types` LIMIT 0, 100;";
    }

    public static function GetFilterListSQL(){
        return "SELECT * FROM `extra_filters` WHERE `object_type` = 0;";     
    }
    
    public static function FilterList($link){
        return $link->query("SELECT * FROM `extra_filters` WHERE `object_type` = 0;");     
    }

    public static function GetLastVacancySQL(){
        return "SELECT `vacancies`.`id`, `name`, `owner_id`, `cities`.`city_name` as `city`, `vacancy_types`.`vacancy_type_name` as `type`, `vacancies`.`type_another` AS `type_another`, `vacancies`.`type_id` AS `type_id`, `sex`, `experience`, `time_type`, `days`, `salary_per_hour`, `salary_per_day`, `salary_per_month`, `desc_min`, `public_date`, `companies`.`company_name` as `company`, `companies`.`logo` as `logo`, `company_types`.`company_type_name` as `company_type` FROM `vacancies`
                INNER JOIN cities ON (vacancies.city_id=cities.id) 
                INNER JOIN companies ON (vacancies.owner_id=companies.id) 
                INNER JOIN company_types ON (companies.company_type=company_types.id) 
                INNER JOIN vacancy_types ON (vacancies.type_id=vacancy_types.id) 
                WHERE `companies`.`company_status` = 1 
                ORDER BY vacancies.id DESC LIMIT 10;";
    }

    public static function GetCompanyVacanciesSQL($user){
        return "SELECT * FROM `vacancies` WHERE owner_id = (SELECT id FROM `companies` WHERE company_owner_id = '".((int) $user)."') LIMIT 100;
                SELECT vacancies.id, (
                    SELECT COUNT(*) FROM responses WHERE responses.vacancy_id = vacancies.id AND remove != 1
                ) as resp_count, (
                    SELECT COUNT(*) FROM responses WHERE responses.vacancy_id = vacancies.id AND status = 0 AND remove != 1
                ) as new_resp_count FROM `vacancies` WHERE owner_id = (
                    SELECT id FROM `companies` WHERE company_owner_id = 1 AND `companies`.`company_status` = 1
                ) LIMIT 100;";
    }

    
    public static function GetVacanciesCountSQL(int $user_id)
    {
        return "SELECT COUNT(`id`) as `vacancies_count` FROM `vacancies` WHERE `owner_id` = $user_id;";
    }

    public static function GetVacanciesViewsSQL(int $user_id)
    {
        return "SELECT SUM(`views`) as `vacancies_views` FROM `vacancies` WHERE `owner_id` = $user_id;";
    }

    // public static function GetCompanyVacancyInfoSQL($user){
    //     return "SELECT `id`, `vacancy_avaiable` as `count` FROM `companies` WHERE `company_owner_id` = '".((int) $user)."';";
    // }
}

?>