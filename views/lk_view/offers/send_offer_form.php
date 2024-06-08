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
                        <h5>Отправить отклик на вакансию</a></h5>
                        <? if($vacancy['purchased'] > 0 || $_SETTINGS['send_offer_price'] < 1) { ?> 
                            <span>Отправка отклика на эту вакансию бесплатна</span>
                        <? } else { ?>
                            <span>Отправка отклика на эту вакансию стоит <?= $_SETTINGS['send_offer_price']; ?> монет</span>
                        <? } ?>
                    </div>
                    <div class="card-block">
                        <form method="POST" action="/lk/requests.php">
                            <input type="hidden" name="action" value="send_offer"/>
                            <input type="hidden" id="redirect" value="<?= $_SERVER['HTTP_REFERER']; ?>"/>
                            <input type="hidden" name="vacancy_id" value="<? echo($_GET['vacancy']); ?>"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Вакансия</label>
                                <div class="col-sm-10">
                                    <div class="form-control-static">
                                        <b><? echo($vacancy['name']); ?></b> <a target="blank" href="/vacancy/<? echo($_GET['vacancy']); ?>">(Открыть вакансию)</a>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Текст предложения</label>
                                <div class="col-sm-10">
                                    <textarea name="offer_request" rows=10 type="text" class="form-control" maxlength="2500" autofocus required>Этот пользователь прислал вам предложение</textarea>
                                </div>
                            </div>

                            <button type="button" onclick="sendOfferRequest();" class="btn btn-success">Отправить</button>
                            <button type="button" onclick="cancelOfferRequest();" class="btn btn-danger">Отмена</button>
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

    <script> 
        let redirect = "<? echo($_GET['redirect']); ?>"; 
        let offerPrice = <?= $vacancy['purchased'] > 0 ? 0 : $_SETTINGS['send_offer_price'] ?>;
    </script>