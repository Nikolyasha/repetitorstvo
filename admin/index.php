<?

$CURRENT_FILE = 'index';
include("init.php");

$date = null;
if(isset($_GET['graph'])){
    switch($_GET['graph']){
        case 'month':
            $date = date('Y-m-1', time());
            break;
        case "year":
            $date = date('Y-1-1', time());
            break;
    }
}

$sql = Site::GetTotalInfoSQL().Site::GetLastPaymentsSQL().Site::GetPaymentsGraphSQL($date);
list($total_balance, $employee_count, $employer_count, $vacancy_count, $last_payments, $graph_data) = MultiQuery($link, $sql);
// die(Site::GetPaymentsGraphSQL());
$total_balance = $total_balance[0]['total_balance'];
$employee_count = $employee_count[0]['employee_count']; 
$employer_count = $employer_count[0]['employer_count']; 
$vacancy_count = $vacancy_count[0]['vacancy_count'];



// die(var_dump($graph_data));

$graph = ["module" => 1];
switch($_GET['graph']){
    case "month":
        foreach(range(1, 32) as $day){
            $date = date("$day.m.Y");
            $graph[$date] = 0;
            if($date == date("t.m.Y")) break;
        }
        foreach($graph_data as $payment){
            $date = date('d.m.Y', strtotime($payment['date']));
            $graph[$date] += $payment['amount'];
        }
        break;
    case "year":
        $graph = [
            "module"                                           => 0,
            date('01.Y') => 0, date('02.Y') => 0, date('03.Y') => 0,
            date('04.Y') => 0, date('05.Y') => 0, date('06.Y') => 0,
            date('07.Y') => 0, date('08.Y') => 0, date('09.Y') => 0,
            date('10.Y') => 0, date('11.Y') => 0, date('12.Y') => 0
        ];
        foreach($graph_data as $payment){
            $date = date('m.Y', strtotime($payment['date']));
            $graph[$date] += $payment['amount'];
        }
        break;
    default:
        $graph = [
            "module"                                                 => 0,
            date('d.m.Y', strtotime("last Monday"))                  => 0,
            date('d.m.Y', strtotime("last Monday") + 86400)          => 0,
            date('d.m.Y', strtotime("last Monday") + (86400 * 2))    => 0,
            date('d.m.Y', strtotime("last Monday") + (86400 * 3))    => 0,
            date('d.m.Y', strtotime("last Monday") + (86400 * 4))    => 0,
            date('d.m.Y', strtotime("last Monday") + (86400 * 5))    => 0,
            date('d.m.Y', strtotime("last Monday") + (86400 * 6))    => 0
        ];
        
        foreach($graph_data as $payment){
            $date = date('d.m.Y', strtotime($payment['date']));
            $graph[$date] += $payment['amount'];
        }
        break;
}

$graph_module = $graph['module'];
$graph = json_encode($graph);

include("../views/admin_view/header.php"); 

include("../views/admin_view/index.php");

include("../views/admin_view/footer.php"); 

?>