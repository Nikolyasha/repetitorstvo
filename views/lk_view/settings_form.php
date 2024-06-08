<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">

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
                        <h5>Настройки компании</h5>
                        <span>Не забудьте сохранить изменения</span>
                    </div>
                    <div class="card-block">
                        <? echo($notify); ?>
                        <form method="POST" action="./settings.php" id="main_form" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="company_edit"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <h4 class="sub-title">Основная информация</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Название</label>
                                <div class="col-sm-10">
                                    <input name="company_name" type="text" class="form-control" minlength="1" maxlength="50" value="<? echo($company['company_name']); ?>" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Статус компании</label>
                                <div class="col-sm-10">
                                    <select name="company_status" class="form-control">
                                        <option value="0" <?= $company['company_status'] == 0 ? "selected" : "" ?>>Неактивна (скрывает вакансии)</option>
                                        <option value="1" <?= $company['company_status'] == 1 ? "selected" : "" ?>>Активна</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Тип компании</label>
                                <div class="col-sm-10">
                                    <select name="company_type" class="form-control">
                                        <?foreach($company_types as $item){
                                            echo('<option value="'.$item['id'].'" '.($company['type'] == $item['id'] ? "selected" : "").'>'.$item['name'].'</option>');
                                        }?>
                                    </select>
                                </div>
                            </div>

                            <h4 class="sub-title">Местоположение</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Город</label>
                                <div class="col-sm-10">
                                    <select name="company_city" class="form-control">
                                        <?foreach($city_list as $item){
                                            echo('<option value="'.$item['id'].'" '.($company['city_id'] == $item['id'] ? "selected" : "").'>'.$item['name'].'</option>');
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Адрес офиса</label>
                                <div class="col-sm-10">
                                    <input name="company_office_adress" type="text" class="form-control" minlength="1" maxlength="50" value="<? echo($company['office_adress']); ?>" autofocus required>
                                </div>
                            </div>

                            <h4 class="sub-title">Представитель</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Имя</label>
                                <div class="col-sm-10">
                                    <input name="company_owner_name" type="text" class="form-control" minlength="1" maxlength="50" value="<? echo($company['owner_name']); ?>" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Телефон</label>
                                <div class="col-sm-10">
                                    <input name="company_owner_phone" type="phone" class="form-control" minlength="1" maxlength="50" value="<? echo($company['owner_phone']); ?>" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Должность</label>
                                <div class="col-sm-10">
                                    <input name="company_owner_status" type="text" class="form-control" minlength="1" maxlength="50" value="<? echo($company['owner_status']); ?>" autofocus required>
                                </div>
                            </div>

                            <h4 class="sub-title">Описание</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">О компании</label>
                                <div class="col-sm-10">
                                    <textarea name="company_desc" rows=5 type="text" class="form-control" placeholder="Развернутое описание вакансии" maxlength="2500" autofocus required><? echo($company['company_desc']); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Контактные данные</label>
                                <div class="col-sm-10">
                                    <textarea name="company_contacts" rows=5 type="text" class="form-control" placeholder="Развернутое описание вакансии" maxlength="2500" autofocus required><? echo($company['company_contacts']); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Логотип</label>
                                <div class="col-sm-10">
                                    <input name="company_logo" type="file" class="form-control">
                                </div>
                            </div>
                            <h4 class="sub-title">Дополнительная информация</h4>
                            <?
                                 $extra_params = [];
                                 foreach(json_decode($company['extra_params']) as $param){
                                     $extra_params[$param->name] = (int) $param->value;                                                                       
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
                                                                 foreach(json_decode($company['extra_params']) as $param){
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
                                             } ?>
                                     </div>
                                     
                                 <?}
                            ?>                            
                        
                            <button type="button" onclick="checkPassCompany();" class="btn btn-success">Сохранить</button>
                        </form>                        
                    </div>

                    <div class="card-header">
                        <h5>Настройки аккаунта</h5>
                        <span>Не забудьте сохранить изменения</span>
                    </div>
                    <div class="card-block">                        
                        <form method="POST" action="./settings.php">
                            <input type="hidden" name="action" value="change_passwd"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <h4 class="sub-title">Смена пароля</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Старый пароль</label>
                                <div class="col-sm-10">
                                    <input name="old_pass" type="password" class="form-control" minlength="1" maxlength="50" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Новый пароль</label>
                                <div class="col-sm-10">
                                    <input name="new_pass" type="password" class="form-control" minlength="1" maxlength="50" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Повтор пароля</label>
                                <div class="col-sm-10">
                                    <input name="new_pass_repeat" type="password" class="form-control" minlength="1" maxlength="50" required>
                                </div>
                            </div>

                            <button type="button" onclick="checkPassAccount();" class="btn btn-success">Сохранить</button>
                        </form>                        
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
    
    
    <script>
        function checkPassCompany() {
            let form = document.forms[0];
            if(!form.checkValidity()){
                swal("Форма заполнена некорректно");
                return;
            }
            form.submit();
        }

        function checkPassAccount() {
            let form = document.forms[1];
            if(form.old_pass.value.length < 6){
                swal("Нужно указать старый пароль");
                return;
            }
            if(form.new_pass.value.length < 6){
                swal("Новый пароль слишком короткий");
                return;
            }
            if(form.new_pass.value != form.new_pass_repeat.value){
                swal("Пароли не совпадают");
                return;
            }
            form.submit();
        }        
    </script>