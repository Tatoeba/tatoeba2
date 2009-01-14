<?php
class DateHelper extends AppHelper{
	/** 
	 * Format for the date is : '%d/%m/%Y %H:%M:%S'
	 */
	function ago($date){
		$year = substr($date, 0, 4);
		$month = substr($date, 5, 2);
		$day = substr($date, 8, 2);
		$hour = substr($date, 11, 2);
		$min = substr($date, 14, 2);
		
		$pureNumberDate = $year.$month.$day.','.$hour.$min;
		$timestamp = strtotime($pureNumberDate);
		
		$now = time();	
		$days = intval(($now-$timestamp)/(3600*24));
		$hours = intval(($now-$timestamp) / 3600);
		$minutes = intval(($now-$timestamp) / 60);
		if(intval($now-$timestamp) > intval(3600*24*7)){
			return date("M jS Y", $timestamp).', '.date("H:i",$timestamp);
		}elseif($days > 0){
			return sprintf(__('%s day(s) ago',true), $days);
		}elseif($hours > 0){
			return sprintf(__('%s hour(s) ago',true), $hours);
		}else{
			return sprintf(__('%s mn(s) ago',true), $minutes);
		}
	}
}
?>