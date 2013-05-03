<?php
//View sales figures by period.
//Includes margin calculations
//used in dashboard to get a comprehensive spanshot of the business
//Author:Dan Kirsche
//Created: March 2011

class SalesFigures{
	public $periods=array();
	public $currentPeriod;
	
	public function SalesFigures(){
		$this->GetPeriodDates();
		$this->currentPeriod=$this->GetCurrentPeriod();
		//echo "currentPeriod=".$this->currentPeriod;
	}
	public function SaveSales($year,$period,$startDate,$endDate){
		$query = mssql_init("webdata_savesales");
		$param1 = $year;
		$param2 = $period;
		$param3 = $startDate;
		$param4 = $endDate;

		mssql_bind($query, "@year", $param1, SQLINT4);
		mssql_bind($query, "@period", $param2, SQLINT4);
		mssql_bind($query, "@datestart", $param3, SQLVARCHAR);
		mssql_bind($query, "@dateend", $param4, SQLVARCHAR);
		mssql_execute($query);

	}

	public function GetPeriodDates(){
		$i=0;
		for($i=1;$i<=3;$i++){
			$colName="period{$i}start";
			$sql="select value from webdata1 where type='{$colName}'";
			if(!($result=mssql_query($sql))){
            	return false;
       	 	}

        	if(!($row=mssql_fetch_array($result,MSSQL_ASSOC))){
            	return false;
        	}
        	$this->periods[$i]["start"]=$row["value"];
        }
		for($i=1;$i<=3;$i++){
			$colName="period{$i}end";
			$sql="select value from webdata1 where type='{$colName}'";
			if(!($result=mssql_query($sql))){
            	return false;
       	 	}

        	if(!($row=mssql_fetch_array($result,MSSQL_ASSOC))){
            	return false;
        	}
        	$this->periods[$i]["end"]=$row["value"];
        }
	}
	
	//return which period we are currently in
	public function GetCurrentPeriod(){
		$today=mktime();
		if($today>strtotime($this->periods[1]["start"]."/".date('Y')) && $today<(strtotime($this->periods[1]["end"]."/".date('Y'))+86400))
			return 1;
		if($today>strtotime($this->periods[2]["start"]."/".date('Y')) && $today<(strtotime($this->periods[2]["end"]."/".date('Y'))+86400))
			return 2;
		if($today>strtotime($this->periods[3]["start"]."/".date('Y')) && $today<(strtotime($this->periods[3]["end"]."/".date('Y'))+86400))
			return 3;
	}
	
	//calculate previos 3 periods and store in db.
	public function CalcSales(){
		$j=0;
		$year=date('Y');
		$period=$this->currentPeriod;
		for($j=1;$j<4;$j++){
			$dateStart=$this->periods[$period]["start"]."/".$year;
			$dateEnd=$this->periods[$period]["end"]."/".$year;
			echo "from".$dateStart." to ".$dateEnd."<br>";
			$this->SaveSales($year,$period,$dateStart,$dateEnd);
			$this->PreviousPeriod($year,$period);
		}
	}
	
	public function PreviousPeriod(&$year,&$period){
		if($period==1){
			$period=3;
			$year=$year-1;
		}
		else
			$period=$period-1;
	}
	public function GetCurrentMargin(){
		
		return $this->GetMargin(date('Y'),$this->currentPeriod);
		
	}
	public function GetMargin($year,$period){
		$sql="select charges,refunds,shippingcost,productcost,btlcount from salesfigures where period={$period} and year={$year}";

		if(!($result=mssql_query($sql))){
            return false;
       	 }

        if(!($row=mssql_fetch_array($result,MSSQL_ASSOC))){
           	return false;
        }
        $profit=$row["charges"]-$row["refunds"]-$row["shippingcost"]-$row["productcost"]-($row["btlcount"]*.46);
        $margin=$profit/($row["charges"]-$row["refunds"]-$row["shippingcost"]);
	return	$margin;
		
	}
}
?>