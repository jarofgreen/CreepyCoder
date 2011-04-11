<?php
/** 
 * Copyright (C) 2011 James (james at jarofgreen dot co dot uk)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/


class WriteDayOfWeekAndHourOfDayData extends BaseWriteClass {


	protected $graph;
	protected $csv;

	public function __construct($configData = array()) {
		if (get_class($configData) == 'DOMElement') {
			$this->graph = $configData->getAttribute('GraphFile');
			$this->csv = $configData->getAttribute('CSVFile');
		}
		parent::__construct($configData);
	}

	private $days = array('1'=>'Mon','2'=>'Tue','3'=>'Wed','4'=>'Thu','5'=>'Fri','6'=>'Sat','7'=>'Sun',);


	public function write() {

		$hourData = array();
		for ($i = 0; $i <= 23; $i++) $hourData[$i] = 0;

		$data = array();
		for ($i = 1; $i <= 7; $i++) $data[$i] = $hourData;
		
		foreach($this->dataManager->getData() as $item) {
			$data[$item->getDateTimeAs('N')][$item->getDateTimeAs('G')]++;
		}

		if ($this->graph) {
			$maxCommits = 0;
			foreach($data as $day=>$hourData) {
				foreach($hourData as $hour=>$commits) {
					$maxCommits = max($maxCommits, $commits);
				}
			}


			$xAxis = $yAxis = $sizeAxis = array();
			foreach($data as $day=>$hourData) {
				foreach($hourData as $hour=>$commits) {
					if ($commits > 0) {
						$xAxis[] = intval($hour/23*100);
						$yAxis[] = intval($day/7*100);
						$sizeAxis[] = intval($commits/$maxCommits*100);
					}
				}
			}

			$url = 'http://chart.apis.google.com/chart?cht=s&chtt=Day+And+Hour&chxt=x,y&chs=800x300'.
				'&chxr=0,0,23,1&chxl=1:||'.implode('|', array_values($this->days)).
				'&chd=t:'.implode(",", $xAxis)."|".implode(",", $yAxis)."|".implode(",", $sizeAxis);

			print "URL: $url \n\n";

			$ch = curl_init($url);
			$fp = fopen($this->graph, "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

		}

		if ($this->csv) {
 
			if (!$handle = fopen($this->csv, 'w')) {
				throw new Exception("Cannot open file");
			}

			for ($i = 0; $i <= 23; $i++) $this->fwrite($handle,",".$i);
			$this->fwrite($handle,"\n");

			foreach($data as $day=>$hourData) {
				$this->fwrite($handle,$this->days[$day].",");
				foreach($hourData as $hour=>$commits) {
					$this->fwrite($handle,$commits.",");
				}
				$this->fwrite($handle,"\r\n");
			}

			fclose($handle);
		}
	}

	private function fwrite($handle,$data) {
		if (fwrite($handle, $data) === FALSE) {
			throw new Exception("Cannot write to file");
		}
	}

}

