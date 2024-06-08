<?

class Filter{

    public $id;
    public $data;

    public function __construct($link, $id){
        $sql = "SELECT * FROM `extra_filters` WHERE `id` = '".$id."'";
        $response = mysqli_query($link, $sql);
        
        if($response){
            $response = mysqli_fetch_array($response, MYSQLI_ASSOC);
            $this->id = $response['id'];
            $this->data = $response;
        }
        else{
            $this->id = -1;
        }
    }

    public static function CreateFilter($link, $object_type, $name, $display, $type, $options){
        $tables = Array("vacancies", "workers", "companies");
        $sql = "INSERT INTO `extra_filters`(`id`, `object_type`, `name`, `display`, `type`, `options`) 
                VALUES (NULL,'".$object_type."','".$name."','".$display."','".$type."','".$options."');
                UPDATE `".$tables[(int) $object_type]."` SET `extra_params` = JSON_ARRAY_APPEND(
                    `extra_params`, '$',
                    JSON_MERGE(
                        JSON_OBJECT('name', '".$name."'),
                        JSON_OBJECT('value', 0)
                    )
                );";
        $response = MultiQuery($link, $sql);
        if($response === False){
            return Array(false, mysqli_error($link));
        }
        else{
            return Array(true);
        }
    }

    public function EditFilter($link, $display, $type, $options){
        $sql = "UPDATE `extra_filters` SET `display`='".$display."', `type`='".$type."', `options`='".$options."' WHERE `id`='".$this->id."';";
        $response = mysqli_query($link, $sql);
        if($response){
            return Array(true);
        }
        else{
            return Array(false, mysqli_error($link));
        }
    }

    public function DeleteFilter($link){
        $sql = "DELETE FROM `extra_filters` WHERE `id`='".$this->id."';";
        $response = mysqli_query($link, $sql);
        if($response){
            return Array(true);
        }
        else{
            return Array(false, 0);
        }
    }

    // SQL functions
    public static function GetFilterListSQL(){
        return "SELECT * FROM `extra_filters`;";
    }
    public static function GetFilterListByObjectTypeSQL($type){
        $type = (int) $type;
        return "SELECT * FROM `extra_filters` WHERE `object_type` = $type;";
    }
}

?>