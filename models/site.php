<?

class Site{
    public static function GetOptionSQL($name){
        return "SELECT `value` FROM `site_settings` WHERE `name` = '$name';";
    }

    public static function GetAllOptions($link){
        $sql = "SELECT `name`, `value` FROM `site_settings`;";
        $result = mysqli_fetch_all(mysqli_query($link, $sql), MYSQLI_ASSOC);
        $options = [];
        foreach($result as $option){
            $options[$option['name']] = $option['value'];
            if(ctype_digit($option['value'])){
                $options[$option['name']] = (int) $option['value'];
            }
        }
        return $options;
    }

    public static function GetTotalInfoSQL(){
        $sql = "SELECT SUM(`balance`) as `total_balance` FROM `users`;" .
               "SELECT COUNT(*) as `employee_count` FROM `users` WHERE `type` = 1;" .
               "SELECT COUNT(*) as `employer_count` FROM `users` WHERE `type` = 2;" .
               "SELECT COUNT(*) as `vacancy_count` FROM `vacancies`;";
        return $sql;
    }
    public static function GetLastPaymentsSQL(){
        return "SELECT `payments`.`id`, `owner_id`, `name`, `type`, `amount`, `payment_id`, `date` FROM `payments` INNER JOIN `users` ON (`payments`.`owner_id` = `users`.`id`) ORDER BY `id` DESC LIMIT 15;";
    }
    public static function GetPaymentsGraphSQL($date = null){
        if($date == null) 
            $date = date('Y-m-d', strtotime("last Monday"));
        return "SELECT `amount`, `date` FROM `payments` WHERE `date` > '$date' ORDER BY `id` DESC";
    }
}

?>