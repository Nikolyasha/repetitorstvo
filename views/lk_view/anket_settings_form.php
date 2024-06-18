<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">
<!-- for video -->
<link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />

<style>
    .action_column{
        text-align: center !important;
    }
    .action_column button{
        font-size: 16px;
    }
    .action_column i{
        padding-left: 5px;
    }
    .td-warp{
        white-space: normal;
    }
    td{
        vertical-align: middle !important;
    }

    .border-checkbox {
        display: none;
    }
    .border-checkbox-section .border-checkbox-group .border-checkbox-label::after {
        content: "";
        display: block;
        width: 5px;
        height: 11px;
        opacity: .9;
        border-right: 2px solid #eee;
        border-top: 2px solid #eee;
        position: absolute;
        left: 5px;
        top: 11px;
        -webkit-transform: scaleX(-1) rotate(135deg);
        transform: scaleX(-1) rotate(135deg);
        -webkit-transform-origin: left top;
        transform-origin: left top;
    }
    .border-checkbox-section .border-checkbox-group .border-checkbox-label::before {
        content: "";
        display: block;
        border: 2px solid #1abc9c;
        width: 20px;
        height: 20px;
        position: absolute;
        left: 0
    }
    .element-margin{
        margin-top: 5px;
    }
    .vacancy_types_list{
        border-radius: 3px;
        padding: 15px;
        height: 200px;
        overflow: auto;
        background: white !important;
    }

    .vacancy_types_list div{
        display: block !important;
        border-left: rgba(0,0,0,.15) 1px solid;
        padding-left: 10px;
    }

    .bm_select_img{
        cursor: pointer;
    }
    .bm_select_img_input{
        opacity: 0;
        cursor: pointer;
        z-index: -1;
        position: absolute;
    }
    .bm_select_img_input:hover{
        background-color: #146a96;
    }
    .bm_file_info{
        color: rgb(39, 39, 39);
        font-size: small;
    }
    #uploaded_files li{
        list-style: none;
        margin: 0;
    }

    #bm_uploaded_certificates li{
        list-style: none;
        margin: 0;
    }

    .bm_file_uploading::before{
        content: url(/img/upload.gif);
        position: relative;
        top: 3px;
        padding-right: 4px;
    }

    #uploaded_files{
        padding: 0;
        margin: -15px 0 10px 0;
    }

    #bm_uploaded_certificates{
        padding: 0;
        margin: -15px 0 10px 0;
    }

    .bm_uploaded_files__item{
        display: flex;
        align-items: center;
    }

    .bm_uploaded_files__item_name{
        margin-left: 5px;
    }
    .bm_anket_photos{
        margin-top: 20px;
    }

    .bm_anket_photos:first-child(){
        margin-top: 0;
    }

    .bm_rm_button{
        font-weight: blod;
        color: red;
        cursor: pointer;
        text-decoration: underline;
    }

    .bm_mini_photo{
        width: 100px;
        height: 100px;
        border-radius: 5px;
        object-fit: cover;
        margin-top: 10px;
    }
    .mini_photo {
        width: 200px;
        height: 200px;
        border-radius: 5px;
        object-fit: cover;
        /* margin-top: 10px; */
        margin-right: 10px;
    }
    .bm_uploaders{
        display: flex;
        flex-direction: column;
    }
    .bm_uploaded_files__item{
        display: flex;
        align-items: center;
    }
    .bm_uploaded_files__item_info{
        margin-left: 10px;
    }
    .activation_block{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    @media (max-width: 520px) {
        .activation_block{
            display: flex;
            flex-direction: column;
            text-align: center;
        }
        .activation_block button{
            margin-top: 10px;   
        }
    }
</style>

<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <? switch($_SESSION['account_type']){
            case 1:
                include("../views/lk_view/menu_worker.php"); 
                break;
            case 2:
                include("../views/lk_view/menu_company.php"); 
                break;
            default:
                include("../views/lk_view/menu_hide.php"); 
                break;
        }
        ?>
    </div>

    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="card">
                    <div class="card-header">
                        <h5>Настройки анкеты</h5>
                        <span>Не забудьте сохранить изменения</span>
                    </div>
                    <div class="card-block">
                        <? echo($notify); ?>
                        <? if($user['activation'] == 0) { ?> 
                        <div class="alert alert-warning border-info activation_block">
                            Внимание! Ваша анкета не активированна <button class="btn btn-info" onclick="activateAnket();">Активировать сейчас</button>
                        </div>
                        <? } ?>
                        <form method="POST" action="./anket_settings.php" id="main_form" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="anket_edit"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <h4 class="sub-title">Основные параметры</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Имя</label>
                                <div class="col-sm-10">
                                    <input value="<? echo $user['first_name']; ?>" name="worker_first_name" type="text" class="form-control" placeholder="" minlength="1" maxlength="50" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Фамилия</label>
                                <div class="col-sm-10">
                                    <input value="<? echo $user['last_name']; ?>" name="worker_last_name" type="text" class="form-control" placeholder="" minlength="1" maxlength="50" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Ваш город</label>
                                <div class="col-sm-10">
                                    <select name="worker_city" class="form-control" required>'
                                        <?foreach($city_list as $item){
                                            echo('<option value="'.$item['id'].'">'.$item['name'].'</option>');
                                        }?>
                                    </select> 
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Пол</label>
                                <div class="col-sm-10 form-radio" style="margin-top: 5px;">
                                    <div class="radio radiofill radio-inline">
                                        <label>
                                            <input type="radio" value="0" name="worker_sex" required>
                                            <i class="helper"></i>Женский
                                        </label>
                                    </div>
                                    <div class="radio radiofill radio-inline">
                                        <label class="radio-info">
                                            <input type="radio" value="1" name="worker_sex" required>
                                            <i class="helper"></i>Мужской
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Возраст</label>
                                <div class="col-sm-2">
                                    <input readonly value="<?echo $user['age']; //$res = abs(strtotime(date('Y-m-d'))-strtotime($user['birthday']));  echo date('y', $res)-70; ?>" name="worker_age" type="number" class="form-control" placeholder="Возраст" min="14" max="99">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Дата рождения</label>
                                <div class="col-sm-2">
                                    <input onchange="setAge()" value="<? echo $user['birthday']; ?>" name="worker_birthday" type="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Опишите ваш опыт работы</label>
                                <div class="col-sm-10">
                                    <textarea name="worker_exp_desc" rows=5 type="text" class="form-control" placeholder="Прошлые места работы, чем занимались и так далее" maxlength="500" autofocus><? echo $user['experience']; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Статус анкеты</label>
                                <div class="col-sm-10">
                                    <select name="worker_status" class="form-control" required>'
                                        <option value="0">Занят</option>
                                        <option value="1">Ищу работу</option>
                                    </select> 
                                </div>
                            </div>

                            <h4 class="sub-title">Контакты</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Контактный телефон</label>
                                <div class="col-sm-10">
                                    <input value="<? echo $user['phone']; ?>" name="worker_phone" type="tel" class="form-control" placeholder="" minlength="1" maxlength="50" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Viber</label>
                                <div class="col-sm-10">
                                    <input value="<? echo $user['viber']; ?>" name="worker_viber" type="tel" maxlength="32" class="form-control" placeholder="Необязательно, только номер телефона">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Telegram</label>
                                <div class="col-sm-10">
                                    <input value="<? echo $user['telegram']; ?>" name="worker_telegram" type="text" maxlength="32" class="form-control" placeholder="Необязательно">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">WhatsApp</label>
                                <div class="col-sm-10">
                                    <input value="<? echo $user['whatsapp']; ?>" name="worker_whatsapp" type="tel" maxlength="32" class="form-control" placeholder="Необязательно, только номер телефона">
                                </div>
                            </div>

                            <h4 class="sub-title">Предпочтения по работе</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Удобное время работы</label>
                                <div class="col-sm-2">
                                    <input value="<? echo explode("-", $user['time_range'])[0]; ?>" name="worker_time_start" type="text" class="form-control" placeholder="08:00" pattern="[0-2][0-9][:][0-6][0-9]" required>
                                </div>
                                <div class="col-sm-2">
                                    <input value="<? echo explode("-", $user['time_range'])[1]; ?>" name="worker_time_end" type="text" class="form-control" placeholder="18:00" pattern="[0-2][0-9][:][0-6][0-9]" required>
                                </div>
                            </div>
                            <div>
                                <div id="longtime_work" class="form-group row">
                                    <label class="col-sm-2 col-form-label">Удобные рабочие дни</label>
                                    <div class="col-sm-10 border-checkbox-section element-margin" id="week_days">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox" type="checkbox" id="work_monday" name="work_monday">
                                            <label class="border-checkbox-label" for="work_monday">Понедельник</label>
                                        </div>
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox" type="checkbox" id="work_tuesday" name="work_tuesday">
                                            <label class="border-checkbox-label" for="work_tuesday">Вторник</label>
                                        </div>
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox" type="checkbox" id="work_wednesday" name="work_wednesday">
                                            <label class="border-checkbox-label" for="work_wednesday">Среда</label>
                                        </div>
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox" type="checkbox" id="work_thursday" name="work_thursday">
                                            <label class="border-checkbox-label" for="work_thursday">Четверг</label>
                                        </div>
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox" type="checkbox" id="work_friday" name="work_friday">
                                            <label class="border-checkbox-label" for="work_friday">Пятница</label>
                                        </div>
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox" type="checkbox" id="work_saturday" name="work_saturday">
                                            <label class="border-checkbox-label" for="work_saturday">Суббота</label>
                                        </div>
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <input class="border-checkbox" type="checkbox" id="work_sunday" name="work_sunday">
                                            <label class="border-checkbox-label" for="work_sunday">Воскресенье</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div id="longtime_work" class="form-group row">
                                    <label class="col-sm-2 col-form-label">Предпочитаемые профессии</label>
                                    <div class="col-sm-10 border-checkbox-section element-margin vacancy_types_list" id="vacancy_types_list">
                                        <? foreach($vacancy_types as $vacancy_type) { ?>
                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                <input class="border-checkbox" type="checkbox" id="vacancy_type_<? echo $vacancy_type['id']; ?>" name="vacancy_type_<? echo $vacancy_type['id']; ?>">
                                                <label class="border-checkbox-label" for="vacancy_type_<? echo $vacancy_type['id']; ?>"><? echo $vacancy_type['vacancy_type_name']; ?></label>
                                            </div>
                                        <? } ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Жалаемая оплата за час</label>
                                <div class="col-sm-10">
                                    <input value="<? echo $user['min_salary']; ?>" name="worker_salary_per_hour" type="text" class="form-control" placeholder="0" maxlength="11" required>
                                </div>
                            </div>
                            

                            <h4 class="sub-title">О себе</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Кратко о себе</label>
                                <div class="col-sm-10">
                                    <textarea name="worker_about" rows=5 type="text" class="form-control" placeholder="Коротко расскажите о себе и своих навыках" maxlength="2500" autofocus><? echo $user['about']; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Внешние данные</label>
                                <div class="col-sm-10">
                                    <textarea name="worker_view" rows=5 type="text" class="form-control" placeholder="Опишите ваш внешний вид (рост, расса, телосложение, цвет кожи и так далее)" maxlength="2500" autofocus><? echo $user['view']; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Особые навыки</label>
                                <div class="col-sm-10">
                                    <textarea name="worker_special" rows=5 type="text" class="form-control" placeholder="Опишите ваши особые навыки (при наличии)" maxlength="2500" autofocus><? echo $user['special']; ?></textarea>
                                </div>
                            </div>

                            <h4 class="sub-title">Дополнительная информация</h4>
                            <!-- CUSTOM AREA -->
                            
                            <?
                                $extra_params = [];
                                foreach(json_decode($user['extra_fields']) as $param){
                                    // $extra_params[$param->name] = (int) $param->value;
                                    $extra_params[$param->name] = $param->value;                                                                        
                                }
                                
                                foreach($filters as $filter){ 
                                    $optArr = explode(';', $filter['options']);?>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label"><? echo $filter['display']; ?></label>                                                                               
                                            <? switch($filter['type']) { 
                                                case 0: 
                                                    if (count($optArr) > 2) {?>
                                                        <div class="col-sm-10 border-checkbox-section element-margin">                                                            
                                                            <? $i = 0; foreach(explode(";", $filter['options']) as $option) { 
                                                                $multipleCheckBox = [];
                                                                foreach(json_decode($user['extra_fields']) as $param){
                                                                    if ($param->name == $filter['name'])
                                                                        $multipleCheckBox[$option] += (int) $param->$option;                                                                                                                                        
                                                                }
                                                                // print_r($multipleCheckBox)?>
                                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                                <div class="border-checkbox-group border-checkbox-group-primary">
                                                                    <input class="border-checkbox" type="checkbox" id="extra_<? echo $filter['name']; ?>_<?echo $i;?>" name="extra_<? echo $filter['name']; ?>_<?echo $i;?>" <? if($multipleCheckBox[$option] == 1) echo("checked") ?>>
                                                                    <label class="border-checkbox-label" for="extra_<? echo $filter['name'];?>_<?echo $i;?>"><? echo($option); ?></label>
                                                                </div>
                                                            </div>
                                                            <? $i++; } ?>
                                                        </div>
                                                    <? } else {?>
                                                        <div class="col-sm-10 border-checkbox-section element-margin">
                                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                                <div class="border-checkbox-group border-checkbox-group-primary">                                                                
                                                                    <input class="border-checkbox" type="checkbox" id="extra_<? echo $filter['name']; ?>" name="extra_<? echo $filter['name']; ?>" <? if($extra_params[$filter['name']] == 1) echo("checked") ?>>
                                                                    <label class="border-checkbox-label" for="extra_<? echo $filter['name']; ?>"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <? } ?>
                                                    <? break; 
                                                case 1: ?>
                                                    <div class="col-sm-10 form-radio" style="margin-top: 5px;">
                                                        <? $i = 0; foreach(explode(";", $filter['options']) as $option) { ?>
                                                        <div class="radio radiofill radio-inline">
                                                            
                                                            <label>
                                                                
                                                                <input type="radio" value="<? echo $i; ?>" name="extra_<? echo $filter['name']; ?>" <? if($extra_params[$filter['name']] == $i) echo('checked="checked"'); ?>>
                                                                <i class="helper"></i><? echo($option); ?>
                                                            </label>
                                                        </div>
                                                        <? $i++; } ?>
                                                    </div>
                                                    <? break; 
                                                case 2: ?>
                                                    <div class="col-sm-10">
                                                        <select name="extra_<? echo $filter['name']; ?>" class="form-control">
                                                            <? $i = 0; foreach(explode(";", $filter['options']) as $option) { ?>
                                                                <option value="<? echo $i; ?>" <? if($extra_params[$filter['name']] == $i) echo("selected"); ?>><? echo($option); ?></option>
                                                            <? $i++; } ?>
                                                        </select>
                                                    </div>
                                                    <? break;
                                                case 3: ?>
                                                <? $phot = [];
                                                    foreach(json_decode($user['extra_fields']) as $param){
                                                        if ($param->name == $filter['name'])                                                          
                                                            $phot[$param->name] = explode(",", $param->value);                                                                                                                                       
                                                    }?>
                                                        <? if ($filter['name'] == "portfolio") { ?>
                                                            <div class="col-sm-10">                                                                
                                                                <?if (!empty($phot[$filter['name']])) {?>
                                                                <input id="inpUrl_<?echo $filter['name']?>" style="margin-bottom: 10px;" value="<?echo !strripos(end($phot[$filter['name']]), "/") ? "" : end($phot[$filter['name']])?>" name="inpUrl_<?echo $filter['name']?>" type="text" class="form-control" placeholder="Введите ссылку с youtube" onchange="viewVideo(this.value)">
                                                                 
                                                                <video id="my-video" width="600" <?echo !strripos(end($phot[$filter['name']]), "/") ? "" : 'controls' ?> class="video-js" data-setup='
                                                                {                                                                
                                                                    "techOrder": ["youtube"],
                                                                    "sources": [{
                                                                        "type": "video/youtube",
                                                                        "src": "<?echo !strripos(end($phot[$filter['name']]), "/") ? "https://www.youtube.com/watch?v=qt9-2_9LxHk" : end($phot[$filter['name']])?>"
                                                                        }]
                                                                }
                                                                '>                                                                
                                                                </video>
                                                                <?} else {?>
                                                                    <input id="inpUrl_<?echo $filter['name']?>" style="margin-bottom: 10px;" value="" name="inpUrl_<?echo $filter['name']?>" type="text" class="form-control" placeholder="Введите ссылку с youtube" onchange="viewVideo(this.value)">
                                                                 
                                                                <video id="my-video" width="600" class="video-js" data-setup='
                                                                {                                                                
                                                                    "techOrder": ["youtube"],
                                                                    "sources": [{
                                                                        "type": "video/youtube",
                                                                        "src": "https://www.youtube.com/watch?v=qt9-2_9LxHk"
                                                                        }]
                                                                }
                                                                '>                                                                
                                                                </video>
                                                                <?}?>                                                                                                                                                                                          
                                                            </div>
                                                            <?} ?>
                                                            <br>
                                                            
                                                            <input class="" type="file" name="extra_photos_<?echo $filter['name']?>[]" placeholder="Выберите фотографии" accept="image/*" id="imgInp_<?echo $filter['name']?>" onchange="preview('<?=$filter['name']?>')">
                                                        
                                                            <div style="display: flex;" id="imgForm_<?echo $filter['name']?>">
    
                                                            <? if (!empty($phot[$filter['name']])) {                                                                 
                                                                foreach($phot[$filter['name']] as $val){
                                                                    if (strripos($val, "/") == true)
                                                                        continue;
                                                                                                                                
                                                                    if (($val != "")) {?>
                                                                        <input id="<? echo $val; ?>" type="hidden" name="extra_remove_photos_<?echo $filter['name']?>[]" value="rm"/>                                                             
                                                                        <div id="photo_<?echo $filter['name']?>">
                                                                            <input type="hidden" name="extra_photos_name_<?echo $filter['name']?>[]" value="<? echo $val; ?>"/>                                                                                                                            
                                                                            <a target="_blank" href="/img/filter_photos/<? echo $val; ?>"><img src="/img/filter_photos/<? echo $val; ?>" class="mini_photo"></a>
                                                                            <span><?echo $val;?></span>
                                                                            <span onclick="removeImg(this)" class="bm_rm_button">Удалить</span>                                                                    
                                                                        </div>                                                                                                                                                                                    
                                                                    <?}
                                                                }
                                                            }?>
                                                            </div>
                                                        
                                                        
                                                    <? break;
                                            } ?>
                                    </div>
                                    
                                <?}
                            ?>                                                      
                            <input type="hidden" name="photos" value="<? echo($user['photos']); ?>"/>
                        </form>

                        <h4 class="sub-title">Фотографии профиля</h4>
                        <div class="bm_input">
                            <ul id="uploaded_files">
                                <? 
                                if($user['photos'] != ""){
                                    $photo_id = 1;
                                    foreach(explode(",", $user['photos']) as $photo) { ?>
                                        <li photo-id="<? echo $photo_id; ?>" class="bm_uploaded_files__item">
                                            <a target="_blank" href="/img/avatars/<? echo $photo; ?>"><img src="/img/avatars/<? echo $photo; ?>" class="bm_mini_photo"></a>
                                            <div class="bm_uploaded_files__item_info"><div class="bm_uploaded_files__item_name"><? echo $photo; ?></div><div style="margin-left: 5px;"><span onclick="bm_removePhoto(this);" class="bm_rm_button">Удалить</span></div></div>
                                        </li>
                                    <? $photo_id++; }} ?>
                            </ul>
                            <form id="bm_anket_photos" method="post" enctype="multipart/form-data">
                                <input name="action" value="upload_photo" type="hidden"/>
                                <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                                <input class="bm_select_img_input" type="file" name="bm_photos[]" placeholder="Выберите фотографии" accept="image/*" id="bm_anket_photos_input" multiple>
                                <label for="bm_anket_photos_input" class="btn btn-info" type="button">Загрузить фотографии</label>
                            </form>
                        </div>

                        <button type="button" onclick="checkPass();" class="btn btn-success">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="../bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="../bower_components/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../bower_components/popper.js/js/popper.min.js"></script>
<script type="text/javascript" src="../bower_components/bootstrap/js/bootstrap.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="../bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
<!-- modernizr js -->
<script type="text/javascript" src="../bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="../bower_components/modernizr/js/css-scrollbars.js"></script>
    <!-- classie js -->
    <script type="text/javascript" src="../bower_components/classie/js/classie.js"></script>
    <!-- data-table js -->
    <script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../assets/pages/data-table/js/jszip.min.js"></script>
    <script src="../assets/pages/data-table/js/pdfmake.min.js"></script>
    <script src="../assets/pages/data-table/js/vfs_fonts.js"></script>
    <script src="../bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="../bower_components/i18next/js/i18next.min.js"></script>
    <script type="text/javascript" src="../bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js"></script>
    <script type="text/javascript" src="../bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js"></script>
    <script type="text/javascript" src="../bower_components/jquery-i18next/js/jquery-i18next.min.js"></script>
    <!-- Custom js -->
    <script src="../assets/pages/data-table/js/data-table-custom.js"></script>
    <script type="text/javascript" src="../assets/js/script.js"></script>
    <script src="../assets/js/pcoded.min.js"></script>
    <script src="../assets/js/demo-12.js"></script>
    <script src="../assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="../assets/js/jquery.mousewheel.min.js"></script>
    
    <script src="../js/anket_settings.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

    <!-- for video -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/8.15.0/video.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-youtube/3.0.1/Youtube.min.js"></script>
    <script src="https://unpkg.com/youtube-video-id@latest/dist/youtube-video-id.min.js"></script>
    

    <script>
        const max_photos = <?=$_SETTINGS['max_profile_photos_option']?$_SETTINGS['max_profile_photos_option']:"5"?>;
        form.worker_city.value = <? echo $user['city']; ?>;
        form.worker_sex.value = <? echo $user['sex']; ?>;        

        form.worker_status.value = <? echo $user['status']; ?>;

        "<? echo $user['week_days']; ?>".split(",").forEach(el => {
            document.getElementById("week_days").children[+el-1].getElementsByTagName("input")[0].checked = true;
        });       
        
        "<? echo $user['job_types']; ?>".split(",").forEach(el => {
            document.getElementById("vacancy_types_list").children[+el-1].getElementsByTagName("input")[0].checked = true;
        });
        let anket_price = '<?=$_SETTINGS['vacancy_price']?>';


        function setAge() {
            //alert(new Date())
            const dateOfBirth = new Date(form.worker_birthday.value);
            
            form.worker_age.value = Math.floor(Math.abs(new Date() - new Date(dateOfBirth)) / (1000 * 60 * 60 * 24 * 365));
        }
    </script>