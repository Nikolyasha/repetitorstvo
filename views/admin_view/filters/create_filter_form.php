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
                        <h5>Создать кастомный фильтр</h5>
                        <span>Не забудьте сохранить изменения</span>
                    </div>
                    <div class="card-block">
                        <form method="POST" action="create_filter.php">
                            <input type="hidden" value="create" name="action">
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <h4 class="sub-title">Настройки фильтра</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Имя фильтра</label>
                                <div class="col-sm-8">
                                    <input name="filter_name" type="text" class="form-control" placeholder="need_auto" minlength="1" maxlength="50" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Отображаемое имя</label>
                                <div class="col-sm-8">
                                    <input name="filter_display_name" type="text" class="form-control" placeholder="Требуется авто" minlength="1" maxlength="50" autofocus required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Тип фильтра</label>
                                <div class="col-sm-8">
                                    <select name="filter_type" class="form-control">'
                                        <?
                                            for($i = 0; count($FILTER_TYPES) > $i; $i++){
                                                echo('<option value="'.$i.'">'.$FILTER_TYPES[$i].'</option>');
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Тип объекта</label>
                                <div class="col-sm-8">
                                    <select name="filter_object_type" class="form-control">'
                                        <?
                                            for($i = 0; count($FILTER_OBJ_TYPES) > $i; $i++){
                                                echo('<option value="'.$i.'">'.$FILTER_OBJ_TYPES[$i].'</option>');
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Опции фильтра</label>
                                <div class="col-sm-8">
                                    <textarea name="filter_options" rows=10 type="text" class="form-control" maxlength="1000" autofocus required></textarea>
                                </div>
                            </div>

                            <button type="button" onclick="createFilter();" class="btn btn-success">Отправить</button>
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

    <script> let redirect = "<? echo($_GET['redirect']); ?>"; </script>