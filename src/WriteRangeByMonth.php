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


class WriteRangeByMonth extends BaseWriteClass {


	protected $graph;
	protected $csv;

	public function __construct($configData = array()) {
		if (get_class($configData) == 'DOMElement') {
			$this->graph = $configData->getAttribute('GraphFile');
			$this->csv = $configData->getAttribute('CSVFile');
		}
		parent::__construct($configData);
	}

	public function write() {

		$data = array();
		
		foreach($this->dataManager->getData() as $item) {
			$v = $this->commitToDateIdx($item);
			if (!isset($data[$v])) $data[$v] = 0;
			$data[$v]++;
		}

		$minDate = min(array_keys($data));
		$maxDate = max(array_keys($data));


		if ($this->graph) {
			$maxCommits = max($data);
			$dataArray = $labelArray = array();
			for($i = $minDate; $i <= $maxDate; $i++) {
				$dataArray[$i] = 0;
				$labelArray[$i] = '';
			}
			foreach(array($minDate, $maxDate, intval($minDate + ($maxDate - $minDate)*.25),intval($minDate + ($maxDate - $minDate)*.50),intval($minDate + ($maxDate - $minDate)*.75)) as $pos) {
				$labelArray[$pos] = $this->dateIdxToDateString($pos);
			}
			foreach($data as $date=>$commits) {
				$dataArray[$date] = intval($commits / $maxCommits * 100);
			}
			$url = 'http://chart.apis.google.com/chart?cht=bvg&chtt=Range+By+Month'.
				'&chbh='.(intVal(350/count($dataArray))-1).',1&chs=500x300'.
				'&chxt=x&chxl=0:|'.implode('|',$labelArray).'&chd=t:'.implode(',',$dataArray);

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

			$this->fwrite($handle,"Day,Events\r\n");

			foreach($data as $idx=>$commits) {
				$this->fwrite($handle,$this->dateIdxToDateString($idx).",".$commits."\r\n");
			}

			fclose($handle);
		}
	}

	private function fwrite($handle,$data) {
		if (fwrite($handle, $data) === FALSE) {
			throw new Exception("Cannot write to file");
		}
	}

	private function commitToDateIdx(CoderAction $item) {
		return intval($item->getDateTimeAs('Y')*12) + intval($item->getDateTimeAs('n')) - 1;
	}

	private function dateIdxToDateString($idx) {
		$year = intval($idx / 12);
		$month = ($idx%12)+1;
		return $month."/".$year;
	}

}

