<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">
<link rel="stylesheet" type="text/css" href="../bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />

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
                        <h5>Ответить на отклик работника</a></h5>
                        <span>Ваш ответ придет отправителю</span>
                    </div>
                    <div class="card-block">
                        <form method="POST" action="offers.php">
                            <input type="hidden" name="action" value="reply"/>
                            <input type="hidden" name="offer_id" value="<? echo($_GET['reply']); ?>"/>
                            <input type="hidden" name="vacancy_id" value="<? echo($_GET['vacancy']); ?>"/>
                            <input type="hidden" name="redirect" value="<? echo($_GET['redirect']); ?>"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <h4 class="sub-title">Информация о предложении</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Отправитель</label>
                                <div class="col-sm-10">
                                    <div class="form-control-static">
                                        <b><? echo($reply['worker_name']); ?></b> <a target="blank" href="/anket/<? echo($reply['user_id']); ?>">(Открыть профиль)</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Вакансия</label>
                                <div class="col-sm-10">
                                    <div class="form-control-static">
                                        <b><? echo($reply['vacancy_name']); ?></b> <a target="blank" href="/vacancy/<? echo($reply['vacancy_id']); ?>">(Открыть вакансию)</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Дата и время отправки</label>
                                <div class="col-sm-10">
                                    <div class="form-control-static">
                                        <b><? 
                                        $date = date_parse($reply['date']);
                                        echo(vsprintf("%02d %s %04d в %02d:%02d", Array(
                                            $date["day"], 
                                            $MONTHES[$date["month"]], 
                                            $date["year"], 
                                            $date["hour"], 
                                            $date["minute"]
                                        )));
                                        ?></b>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Текст предложения</label>
                                <div class="col-sm-10">
                                    <div class="form-control-static">
                                        <? echo($reply['offer']); ?>
                                    </div>
                                </div>
                            </div>
                        
                            <h4 class="sub-title">Ответ на предложение</h4>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Решение</label>
                                <div class="col-sm-10 form-radio" style="margin-top: 5px;">
                                    <div class="radio radiofill radio-success radio-inline">
                                        <label class="radio-info">
                                            <input type="radio" value=1 name="offer_status" checked="checked">
                                            <i class="helper"></i>Принять
                                        </label>
                                    </div>
                                    <div class="radio radiofill radio-danger radio-inline">
                                        <label>
                                            <input type="radio" value=2 name="offer_status">
                                            <i class="helper"></i>Отклонить
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Ответ на отклик</label>
                                <div class="col-sm-10">
                                    <textarea name="offer_reply" rows=10 type="text" class="form-control" maxlength="2500" autofocus required></textarea>
                                </div>
                            </div>

                            <button type="button" onclick="sendOfferReply();" class="btn btn-success">Отправить</button>
                            <button type="button" onclick="cancelOfferReply();" class="btn btn-danger">Отмена</button>
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