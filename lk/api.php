<?
if(isset($_POST['action'])){
    include("init.php");
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
    switch($_POST['action']){
        case "setOnline":
            if($_SESSION['account_type'] == 1){
                User::SetOnline($link, $_SESSION['id']);
                die("OK");
            }
            header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403); die();
        case "OpenUserContacts":
            if ($_SETTINGS['payment_active_option'] == 'true')
                die("OK");            
            if((int) $_POST['object_id'] > 0){
                if($_SESSION['account_type'] == 2){
                    
                    if(Company::UnlockWorkerContacts($link, (int) $_POST['object_id'], $_SESSION['id'])){
                        die("OK");
                    }
                    else{                        
                        die("ERROR");
                    }
                    
                }
            }
            header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request", true, 400); die();
        case "buyUserContacts":
            if($_SETTINGS['worker_contact_price'] < 1)
                die("OK");
            if((int) $_POST['object_id'] > 0){
                if($_SESSION['account_type'] == 2){
                    if(User::Payment($link, $_SESSION['id'], $_SETTINGS['worker_contact_price'])){
                        if(Company::UnlockWorkerContacts($link, (int) $_POST['object_id'], $_SESSION['id'])){
                            die("OK");
                        }
                        else{
                            User::MoneyBack($link, $_SESSION['id'], $_SETTINGS['worker_contact_price']);
                            die("ERROR");
                        }
                    }
                    else{
                        die("LOW_BALANCE");
                    }
                }
            }
            header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request", true, 400); die();
        case "OpenCompanyContacts":
            if ($_SETTINGS['payment_active_option'] == 'true')
                die("OK");
            if((int) $_POST['object_id'] > 0){
                if($_SESSION['account_type'] == 1){
                       if(User::UnlockCompanyContacts($link, (int) $_POST['object_id'], $_SESSION['id'])){
                            die("OK");
                        }
                        else{
                            die("ERROR");
                        }
                    
                }
            }
            header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request", true, 400); die();
        case "buyCompanyContacts":
            if($_SETTINGS['company_contact_price'] < 1)
                die("OK");
            if((int) $_POST['object_id'] > 0){
                if($_SESSION['account_type'] == 1){
                    if(User::Payment($link, $_SESSION['id'], $_SETTINGS['company_contact_price'])){
                        if(User::UnlockCompanyContacts($link, (int) $_POST['object_id'], $_SESSION['id'])){
                            die("OK");
                        }
                        else{
                            User::MoneyBack($link, $_SESSION['id'], $_SETTINGS['company_contact_price']);
                            die("ERROR");
                        }
                    }
                    else{
                        die("LOW_BALANCE");
                    }
                }
            }
            header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request", true, 400); die();
        default: header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request", true, 400); die();
    }
}
?>