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
                        <h5>Создать новую вакансию</a></h5>
                        <span>Стоимость создания вакансии - <?= $_SETTINGS['vacancy_price'] ?> монет</span>
                    </div>
                    <div class="card-block">
                        <form method="POST" action="./vacancies.php">
                            <input type="hidden" name="action" value="create"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <h4 class="sub-title">Основные параметры</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Название вакансии</label>
                                <div class="col-sm-10">
                                    <input name="vacancy_name" type="text" class="form-control" placeholder="Проморутер" minlength="1" maxlength="50" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Город</label>
                                <div class="col-sm-10">
                                    <select name="vacancy_city" class="form-control">'
                                        <?foreach($city_list as $item){
                                            echo('<option value="'.$item['id'].'">'.$item['name'].'</option>');
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Вакансия</label>
                                <div class="col-sm-10">
                                    <select onchange="another()" name="vacancy_type" class="form-control">'
                                        <?foreach($vacancy_types as $item){
                                            echo('<option value="'.$item['id'].'">'.$item['vacancy_type_name'].'</option>');
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div style="display: none;" id="type_another" class="form-group row">
                                <label class="col-sm-2 col-form-label">Введите свое название вакансии</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="vacancy_type_another" value="<?echo $vacancy['type_another']?>">                                   
                                </div>                                                                                                                            
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Требуемый пол работника</label>
                                <div class="col-sm-10 form-radio" style="margin-top: 5px;">
                                    <div class="radio radiofill radio-inline">
                                        <label>
                                            <input type="radio" value=0 name="vacancy_sex" checked="checked">
                                            <i class="helper"></i>Без разницы
                                        </label>
                                    </div>
                                        <div class="radio radiofill radio-inline">
                                        <label class="radio-info">
                                            <input type="radio" value=1 name="vacancy_sex">
                                            <i class="helper"></i>Мужской
                                        </label>
                                    </div>
                                    <div class="radio radiofill radio-inline">
                                        <label>
                                            <input type="radio" value=2 name="vacancy_sex">
                                            <i class="helper"></i>Женский
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Возрастное ограничение</label>
                                <div class="col-sm-2">
                                    <input name="vacancy_age_min" type="number" class="form-control" placeholder="Возраст от" min="14" max="99">
                                </div>
                                <div class="col-sm-2">
                                    <input name="vacancy_age_max" type="number" class="form-control" placeholder="Возраст до" min="14" max="99">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Требуемый опыт работы</label>
                                <div class="col-sm-10">
                                    <select name="vacancy_experience" class="form-control">
                                        <option value="0">Опыт не требуется</option>
                                        <option value="1">Опыт от 1 года</option>
                                        <option value="2">Опыт от 2 лет</option>
                                        <option value="3">Опыт от 3 лет</option>
                                        <option value="4">Опыт от 4 лет</option>
                                        <option value="5">Опыт от 5 лет</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Вакантных мест</label>
                                <div class="col-sm-3">
                                    <input name="vacancy_workplace_count" type="number" class="form-control" placeholder="Количество нанимаемых работников" value=1 min=1 max=1000>
                                </div>
                            </div>

                            <h4 class="sub-title">Время на работе</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Длительность работы</label>
                                <div class="col-sm-10">
                                    <select id="vacancy_duration" onchange="createVacancyDurationChangeHandler();" name="vacancy_duration" class="form-control">
                                        <option value="0">Постоянная</option>
                                        <option value="1">Временная</option>
                                        <option value="2">Единоразовая</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Время работы (события)</label>
                                <div class="col-sm-2">
                                    <input name="vacancy_time_start" type="text" class="form-control" placeholder="08:00" pattern="[0-2][0-9][:][0-6][0-9]">
                                </div>
                                <div class="col-sm-2">
                                    <input name="vacancy_time_end" type="text" class="form-control" placeholder="18:00" pattern="[0-2][0-9][:][0-6][0-9]">
                                </div>
                            </div>
                            <div>
                                <div id="longtime_work" class="form-group row">
                                    <label class="col-sm-2 col-form-label">Рабочие дни</label>
                                    <div class="col-sm-10 border-checkbox-section element-margin">
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
                                <div id="shorttime_work" style="display: none;" class="form-group row">
                                    <label class="col-sm-2 col-form-label">Временной промежуток</label>
                                    <div class="element-margin" style="margin-left:15px">от</div>
                                    <div class="col-sm-2">
                                        <input name="vacancy_date_start" class="form-control" type="date">
                                    </div>
                                    <div class="element-margin" style="margin-left:15px">до</div>
                                    <div class="col-sm-2">
                                        <input name="vacancy_date_end" class="form-control" type="date">
                                    </div>
                                </div>
                                <div id="onetime_work" style="display: none;" class="form-group row">
                                    <label class="col-sm-2 col-form-label">Дата мероприятия</label>
                                    <div class="col-sm-2">
                                        <input name="vacancy_date" class="form-control" type="date">
                                    </div>
                                </div>
                            </div>
                            
                            <h4 class="sub-title">Оплата</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Оплата за час</label>
                                <div class="col-sm-10">
                                    <input name="vacancy_salary_per_hour" type="number" class="form-control" placeholder="0" max="1000000">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Оплата за день</label>
                                <div class="col-sm-10">
                                    <input name="vacancy_salary_per_day" type="number" class="form-control" placeholder="0" max="1000000">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Оплата за месяц</label>
                                <div class="col-sm-10">
                                    <input name="vacancy_salary_per_month" type="number" class="form-control" placeholder="0" max="1000000">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Переодичность выплат</label>
                                <div class="col-sm-10">
                                    <select name="vacancy_payment_type" class="form-control">
                                        <option value="0">Каждый день</option>
                                        <option value="1">Каждую неделю</option>
                                        <option value="2">Каждый месяц</option>
                                    </select>
                                </div>
                            </div>

                            <h4 class="sub-title">Описание вакансии</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Краткое описание</label>
                                <div class="col-sm-10">
                                    <input name="vacancy_desc_min" type="text" class="form-control" placeholder="Краткое описание вакансии" maxlength="100" autofocus required></input>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Полное описание</label>
                                <div class="col-sm-10">
                                    <textarea name="vacancy_desc" rows=5 type="text" class="form-control" placeholder="Развернутое описание вакансии" maxlength="2500" autofocus required></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Контактные данные</label>
                                <div class="col-sm-10">
                                    <textarea name="vacancy_contacts" rows=5 type="text" class="form-control" placeholder="Контакты для обращения" maxlength="2500" autofocus required></textarea>
                                </div>
                            </div>

                            <h4 class="sub-title">Дополнительная информация</h4>
                            <!-- CUSTOM AREA -->
                            <? 
                            foreach($extra_field_list as $field) { ?>
                            <? $optArr = explode(';', $field['options']);?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><? echo $field['display']; ?></label>
                                    
                                        <? switch($field['type']) { 
                                            case 0: 
                                                if (count($optArr) > 2) {?>
                                                        <div class="col-sm-10 border-checkbox-section element-margin">                                                            
                                                            <? $i = 0; foreach(explode(";", $field['options']) as $option) { ?>
                                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                                <div class="border-checkbox-group border-checkbox-group-primary">
                                                                    <input class="border-checkbox" type="checkbox" id="extra_<? echo $field['name']; ?>_<?echo $i;?>" name="extra_<? echo $field['name']; ?>_<?echo $i;?>">
                                                                    <label class="border-checkbox-label" for="extra_<? echo $field['name'];?>_<?echo $i;?>"><? echo($option); ?></label>
                                                                </div>
                                                            </div>
                                                            <? $i++; } ?>
                                                        </div>
                                                <? } else {?>
                                                        <div class="col-sm-10 border-checkbox-section element-margin">
                                                            <div class="border-checkbox-group border-checkbox-group-primary">
                                                                <div class="border-checkbox-group border-checkbox-group-primary">                                                                
                                                                    <input class="border-checkbox" type="checkbox" id="extra_<? echo $field['name']; ?>" name="extra_<? echo $field['name']; ?>">
                                                                    <label class="border-checkbox-label" for="extra_<? echo $field['name']; ?>"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <? } ?>
                                                <? break; 
                                            case 1: ?>
                                                <div class="col-sm-10 form-radio" style="margin-top: 5px;">
                                                    <? $i = 0; foreach(explode(";", $field['options']) as $option) { ?>
                                                    <div class="radio radiofill radio-inline">
                                                        <label>
                                                            <input type="radio" value="<? echo $i; ?>" name="extra_<? echo $field['name']; ?>">
                                                            <i class="helper"></i><? echo($option); ?>
                                                        </label>
                                                    </div>
                                                    <? $i++; } ?>
                                                </div>
                                                <? break; 
                                            case 2: ?>
                                                <div class="col-sm-10">
                                                    <select name="extra_<? echo $field['name']; ?>" class="form-control">
                                                        <? $i = 0; foreach(explode(";", $field['options']) as $option) { ?>
                                                            <option value="<? echo $i; ?>"><? echo($option); ?></option>
                                                        <? $i++; } ?>
                                                    </select>
                                                </div>
                                                <? break;
                                        } ?>
                                </div>
                            <? } ?>
                            <!-- CUSTOM AREA -->

                            <button type="button" onclick="verifryForm();" class="btn btn-success">Создать</button>
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
        function another(){
            let form = document.forms[0];
            
            if (form.vacancy_type.value == 105){
                document.getElementById('type_another').style.display = '';
            }
            else {
                document.getElementById('type_another').style.display = 'none';
            }
        }
        let payment_active = '<?= $_SETTINGS['payment_active_option']?>';
        let vacancy_price = <? echo $_SETTINGS['vacancy_price']; ?>;
        let action = 'create';
        let edit_payment = '<?= $_SETTINGS['active_vacancy_edit_option']?>';
    </script>