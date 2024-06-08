<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">
<link rel="stylesheet" type="text/css" href="../bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />

<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <? include("../views/admin_view/menu.php"); ?>
    </div>

    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="card">
                    <div class="card-header">
                        <h5>Параметры сайта</h5>
                        <span>Не забудьте сохранить изменения</span>
                    </div>
                    <div class="card-block">
                        <? echo($notify); ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="edit"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <? foreach($elements as $element){ ?>
                                <? if ($element['name'] == "payment_active_option" || $element['name'] == "active_vacancy_edit_option"){ ?>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label"><?=$element['display_name']?><br><small><?=$element['name']?></small></label>                                        
                                        <div class="col-sm-8 form-radio">                                                                          
                                            <div class="radio radiofill radio-inline">
                                                <label>
                                                    <input type="radio" value="true" name="<?=$element['name']?>" required>
                                                    <i class="helper"></i>true
                                                </label>
                                            </div>
                                            <div class="radio radiofill radio-inline">
                                                <label class="radio-info">
                                                    <input type="radio" value="false" name="<?=$element['name']?>" required>
                                                    <i class="helper"></i>false
                                                </label>                                        
                                            </div>                                            
                                        </div>
                                    </div>  
                                <? } else {?>                                
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><?=$element['display_name']?><br><small><?=$element['name']?></small></label>
                                    <div class="col-sm-8">
                                        <input name="<?=$element['name']?>" type="text" value="<?=$element['value']?>" class="form-control" placeholder="0" min="0" max="1000000" autofocus required>
                                    </div>
                                </div>
                                <? }?>
                            <? } ?>
                            <button type="sumbit" class="btn btn-success">Отправить</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>


    <script> 
        let redirect = "<? echo($_GET['redirect']); ?>";
        let form = document.forms[0];

        form.payment_active_option.value = <? foreach ($elements as $element) {
            if ($element['name'] == 'payment_active_option')
                echo $element['value'];
        } ?>;
        form.active_vacancy_edit_option.value = <? foreach ($elements as $element) {
            if ($element['name'] == 'active_vacancy_edit_option')
                echo $element['value'];
        } ?>;                                                
    </script>