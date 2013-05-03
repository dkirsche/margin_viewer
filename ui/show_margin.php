<?php 
//this is a hack for Dan to view margins. There is no user interface, dates must be changed in this file.
require_once 'configDB.php';
require_once 'classes/salesfiguresClass.php';
$s=new SalesFigures;
?>
<table>
<tr>
<?php
for ($i = 0; $i <= 2; $i++) {
	$year=2010+$i;
	echo "<td><b>{$year}</b><br>";
	for ($z = 1; $z <= 3; $z++) {
		$period=$z;
		$current_margin=$s->GetMargin($year,$period)*100;
		echo $s->periods[$period]['start']." - ".$s->periods[$period]['end']." margin=".number_format(($current_margin),2)."%<BR>";
	}
	echo"</td>";
}

?></tr></table>