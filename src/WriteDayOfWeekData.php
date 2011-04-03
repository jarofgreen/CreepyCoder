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


class WriteDayOfWeekData extends BaseWriteClass {

	public function write() {

		$data = array();
		// these are set in the order we want them to appear in the output.
		$data['Mon'] = 0;
		$data['Tue'] = 0;
		$data['Wed'] = 0;
		$data['Thu'] = 0;
		$data['Fri'] = 0;
		$data['Sat'] = 0;
		$data['Sun'] = 0;
		
		foreach($this->dataManager->getData() as $item) {
			$data[$item->getDateTimeAs('D')]++;
		}

		if (isset($this->configData['graphfile']) && $this->configData['graphfile']) {
			$maxCommits = max($data);
			$dataArray = $labelArray = array();
			foreach($data as $day=>$commits) {
				$dataArray[] = intval($commits / $maxCommits * 100);
				$labelArray[] = $day;
			}
			$url = 'http://chart.apis.google.com/chart?cht=bvg&chtt=Day+Of+Week&chs=220x300&chxt=x&chxl=0:|'.implode('|',$labelArray).'&chd=t:'.implode(',',$dataArray);

			print "URL: $url \n\n";

			$ch = curl_init($url);
			$fp = fopen($this->configData['graphfile'], "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

		}

		if (isset($this->configData['file']) && $this->configData['file']) {
 
			if (!$handle = fopen($this->configData['file'], 'w')) {
				throw new Exception("Cannot open file");
			}

			$this->fwrite($handle,"Day,Events\r\n");

			foreach($data as $day=>$commits) {
				$this->fwrite($handle,$day.",".$commits."\r\n");
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

