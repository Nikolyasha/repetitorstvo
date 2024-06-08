<?

class Offer{

    public $id;
    public $data;

    public function __construct($link, $id, $user_id){
        $id = (int) $id;
        $user_id = (int) $user_id;
        $sql = "SELECT `responses`.`id`, `responses`.`user_id`, `vacancy_id`, `date`, `offer`, `responses`.`status`, `response`, 
                `remove`, `vacancies`.`name` AS `vacancy_name`, CONCAT(`workers`.`first_name`, ' ', `workers`.`last_name`) AS `worker_name` FROM `responses` 
                INNER JOIN vacancies ON vacancies.id = responses.vacancy_id 
                INNER JOIN workers ON workers.user_id = responses.user_id 
                WHERE ((`vacancy_id` IN ( 
                    SELECT id FROM `vacancies` WHERE owner_id = ( 
                        SELECT id FROM companies WHERE company_owner_id = '$user_id'
                    )   AND responses.remove != 1)
                ) OR (`responses`.`user_id` = '$user_id' AND responses.remove != 2)) AND responses.id = '$id';";

        $result = mysqli_query($link, $sql);
        if($result){
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $this->id = $result['id'];
            $this->data = $result;
        }
        else{
            $this->id = -1;
        }
    }

    public static function GetListSQL($user_id, $limit = -1){
        return "SELECT `responses`.`id`, `responses`.`user_id`, `vacancy_id`, `date`, `offer`, `responses`.`status`, `response`, 
                `remove`, `vacancies`.`name` AS `vacancy_name`, CONCAT(`workers`.`first_name`, ' ', `workers`.`last_name`) AS `worker_name` FROM `responses` 
                INNER JOIN `vacancies` ON `vacancies`.`id` = `responses`.`vacancy_id` 
                INNER JOIN `workers` ON `workers`.`user_id` = `responses`.`user_id` 
                WHERE `vacancy_id` IN ( 
                    SELECT `id` FROM `vacancies` WHERE `owner_id` = ( 
                        SELECT `id` FROM `companies` WHERE `company_owner_id` = '".$user_id."'
                    ) 
                ) AND `responses`.`remove` != 1 " . ($limit > 0 ? "ORDER BY `id` DESC LIMIT $limit" : "") . ";";
    }

    public static function GetUserListSQL($user_id){
        $user_id = (int) $user_id;
        return "SELECT `responses`.`id`, `responses`.`user_id`, `vacancy_id`, `date`, `offer`, `responses`.`status`, `response`, `remove`, `vacancies`. `name` AS `vacancy_name`, CONCAT(`workers`.`first_name`, ' ', `workers`.`last_name`) AS `worker_name` FROM `responses` INNER JOIN `vacancies` ON `vacancies`.`id` = `responses`.`vacancy_id` INNER JOIN `workers` ON `workers`.`user_id` = `responses`.`user_id` WHERE `responses`.`user_id` = $user_id AND `responses`.`remove` != 2; ";
    }

    public static function GetNewOffersCountSQL($user_id){
        return "SELECT COUNT(`responses`.`id`) AS `new_offers_count` FROM `responses` 
                WHERE vacancy_id IN ( 
                    SELECT id FROM `vacancies` WHERE owner_id = ( 
                        SELECT id FROM companies WHERE company_owner_id = '".$user_id."'
                    ) 
                ) AND responses.status = 0;";
    }

    public function SetStatus($link, $status, $vacancy_id, $reply, $user_id){
        /* Номера ошибок:
        * 1 - уже установлен другой статус
        * 2 - несоответсвие данных
        */
        if($this->data['status'] == 0){
            $sql = "UPDATE `responses` SET `status`='%s', `response`='%s' WHERE vacancy_id = 
                    (
                        SELECT id FROM vacancies WHERE id=%s AND owner_id = 
                        (
                            SELECT id FROM companies WHERE company_owner_id = %s
                        )
                    ) AND id = %s";
            $sql = vsprintf($sql, Array((int) $status, $reply, (int) $vacancy_id, (int) $user_id, $this->id));

            if(mysqli_query($link, $sql)){
                return Array(true, 0);
            }
            else{
                return Array(false, 2);
            }
        }
        else{
            return Array(false, 1);
        }
    }

    public static function SendOffer($link, $vacancy_id, $message){
        $vacancy_id = (int) $vacancy_id;
        $message = htmlspecialchars(mysqli_real_escape_string($link, trim($message)));
        $sql = "SELECT `id` FROM `responses` WHERE `user_id` = {$_SESSION['id']} AND `vacancy_id` = $vacancy_id;";
        $result = mysqli_query($link, $sql);

        if($result){
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if(!empty($result['id']))
                return $result['id'];
            else{
                $sql = "INSERT INTO `responses`(`user_id`, `vacancy_id`, `offer`, `status`, `response`, `remove`) VALUES 
                ({$_SESSION['id']},$vacancy_id,'$message',0,'',0);";
                return mysqli_query($link, $sql);
            }
        }
        else{
            return "err";
        }
    }

    public function RemoveCompany($link, $vacancy_id, $user_id){
        /* Номера ошибок:
        * 1 - нет ответа на новый оффер
        * 2 - несоответсвие данных
        */
        $sql = "";
        if($this->data["status"] == 0 || $this->data["remove"] == 1){
            return Array(false, 1);
        }
        else if($this->data["remove"] == 2){
            $sql = "DELETE FROM `responses` WHERE vacancy_id = 
                    (
                        SELECT id FROM vacancies WHERE id=%s AND owner_id = 
                        (
                            SELECT id FROM companies WHERE company_owner_id = %s
                        )
                    ) AND id = %s";
        }
        else{
            $sql = "UPDATE `responses` SET `remove`=1 WHERE vacancy_id = 
                    (
                        SELECT id FROM vacancies WHERE id=%s AND owner_id = 
                        (
                            SELECT id FROM companies WHERE company_owner_id = %s
                        )
                    ) AND id = %s";
        }
        $sql = vsprintf($sql, Array((int) $vacancy_id, (int) $user_id, $this->id));
        if(mysqli_query($link, $sql)){
            return Array(true, 0);
        }
        else{
            return Array(false, 2);
        }
    }

    public function RemoveWorker($link, $user_id){
        /* Номера ошибок:
        * 1 - ***
        * 2 - несоответсвие данных
        */
        $sql = "";
        if(($this->data["remove"] == 0 && $this->data["status"] == 0) || $this->data["remove"] == 1){
            $sql = "DELETE FROM `responses` WHERE user_id=%s AND id = %s;";
        }
        else{
            $sql = "UPDATE `responses` SET `remove`=2 WHERE user_id=%s AND id = %s;";
        }
        $sql = vsprintf($sql, Array((int) $user_id, $this->id));
        if(mysqli_query($link, $sql)){
            return Array(true, 0);
        }
        else{
            return Array(false, 2);
        }
    }

    
    public static function GetOffersCountSQL(int $user_id)
    {
        return "SELECT COUNT(`id`) as `offers_count` FROM `responses` WHERE `vacancy_id` IN (SELECT `id` FROM `vacancies` WHERE `owner_id` = $user_id);";
    }

}

?>