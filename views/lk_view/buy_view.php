<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">

<style>
    .submit_block{
        /* max-width: 300px; */
    }
    .submit_block img{
        width: 100px; 
        float: right;
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
                        <h5>Покупка монет</h5>
                    </div>
                    <div class="card-block">
                        <?=$notify?>
                        <form method="POST">
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <input type="hidden" name="action" value="payment"/>
                            <p><big>Стоимость 1 монеты: <b><?=$_SETTINGS['coin_price']?> ₸</b></big></p>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Сумма пополнения<br><small>В монетах</small></label>
                                <div class="col-sm-2">
                                    <input name="amount" type="number" value="100" class="form-control" placeholder="0" min="1" max="1000000" autofocus required>
                                </div>
                            </div>
                            <big>Итого к оплате: <b><span id="payment_amount"><?=(int) $_SETTINGS['coin_price'] * 100?></span> ₸</b></big>
                            <p>Ваш платеж будет принят через платежную систему <a target="_blank" href="https://paybox.money/ru_ru/individual"><b>PayBox.Money</b></a></p>
                            <div class="submit_block">
                                <button type="button" onclick="checkAmount();" class="btn btn-success">Оплатить</button>
                                <a target="_blank" href="https://paybox.money/"><img src="/img/paybox.png"/></a>
                            </div>
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
        let coin_price = <?=$_SETTINGS['coin_price']?>;
        function checkAmount(){
            if(+document.forms[0].amount.value > 0){
                document.forms[0].submit();
            }
            else{
                swal("Некорректные данные", "Введите корректную сумму поплнения", "warning");
            }
        }
        document.forms[0].amount.oninput = () => {
            if(+document.forms[0].amount.value > -1){
                document.getElementById("payment_amount").textContent = coin_price * +document.forms[0].amount.value;
            }
        };
    </script>