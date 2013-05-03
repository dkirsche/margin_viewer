<?php 
require_once 'configDB.php';
require_once 'classes/salesfiguresClass.php';
$s=new SalesFigures;
$current_margin=$s->GetCurrentMargin()*100;
echo $s->periods[$s->currentPeriod]['start']." - ".$s->periods[$s->currentPeriod]['end']." margin=".number_format(($current_margin),2)."%";


?>