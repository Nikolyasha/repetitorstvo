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
        <? include("../views/admin_view/menu.php"); ?>
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
                        <form method="POST" action="" id="main_form" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="company_edit"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <h4 class="sub-title">Основная информация</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Название</label>
                                <div class="col-sm-10">
                                    <input name="company_name" type="text" class="form-control" minlength="5" maxlength="50" value="<? echo($company['company_name']); ?>" autofocus required>
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
                                // print_r($filters);
                                foreach($filters as $filter){ 
                                    if($filter['type'] == 0) { ?>                                    
                                        <div>
                                            <div id="extra_<? echo $filter['name']; ?>_box" class="form-group row">
                                                <label class="col-sm-2 col-form-label"><? echo $filter['display']; ?></label>
                                                <div class="col-sm-10 border-checkbox-section element-margin">
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input class="border-checkbox" type="checkbox" id="extra_<? echo $filter['name']; ?>" name="extra_<? echo $filter['name']; ?>">
                                                        <label class="border-checkbox-label" for="extra_<? echo $filter['name']; ?>"><? echo explode(";", $filter['options'])[1]; ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <? } else if($filter['type'] == 1) { ?> 

                                    <?  } else {  ?>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label"><? echo $filter['display']; ?></label>
                                            <div class="col-sm-10">
                                                <select name="extra_<? echo $filter['name']; ?>" class="form-control">
                                                    <? 
                                                        $fieldOptions = explode(";", $filter['options']);
                                                        for($i = 0; $i < count($fieldOptions); $i++){
                                                            echo("<option value='$i'>{$fieldOptions[$i]}</option>");
                                                        } 
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    <? }
                                }
                            ?>
                            <script>
                                JSON.parse(<? echo json_encode($company['extra_params']); ?>).forEach(el => {
                                    let field = document.getElementsByName("extra_" + el['name']);
                                    if(field.length > 0){
                                        if(field[0].type == "checkbox"){
                                            field[0].checked = +el['value'];
                                        }
                                        else{
                                            field[0].value = +el['value'];
                                        }
                                    }
                                });
                                
                            </script>
                            <button type="button" onclick="checkPass();" class="btn btn-success">Сохранить</button>
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
        function checkPass(){
            let form = document.forms[0];
            if(!form.checkValidity()){
                swal("Форма заполнена некорректно");
                return;
            }
            form.submit();
        }
    </script>