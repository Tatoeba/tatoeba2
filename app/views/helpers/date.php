<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class DateHelper extends AppHelper{
	/** 
	 * Format for the date is : '%d/%m/%Y %H:%M:%S'
	 */
	function ago($date, $isTimestamp = false){
		if(!$isTimestamp){
			$year = substr($date, 0, 4);
			$month = substr($date, 5, 2);
			$day = substr($date, 8, 2);
			$hour = substr($date, 11, 2);
			$min = substr($date, 14, 2);
			
			$pureNumberDate = $year.$month.$day.','.$hour.$min;
			$timestamp = strtotime($pureNumberDate);
		}else{
			$timestamp = $date;
		}
		
		$now = time();
		$days = intval(($now-$timestamp)/(3600*24));
		$hours = intval(($now-$timestamp) / 3600);
		$minutes = intval(($now-$timestamp) / 60);
		if($days > 30){
			return date("M jS Y", $timestamp).', '.date("H:i",$timestamp);
		}elseif($days > 0){
			return sprintf(__('%s day(s) ago',true), $days);
		}elseif($hours > 0){
			return sprintf(__('%s hour(s) ago',true), $hours);
		}else{
			return sprintf(__('%s mn ago',true), $minutes);
		}
	}
}
?>
