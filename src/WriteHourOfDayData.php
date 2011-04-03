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


class WriteHourOfDayData extends BaseWriteClass {

	public function write() {

		$data = array();
		for ($i = 0; $i <= 23; $i++) $data[$i] = 0;

		foreach($this->dataManager->getData() as $item) {
			$data[intval($item->getDateTimeAs('G'))]++;
		}

		if (isset($this->configData['graphfile']) && $this->configData['graphfile']) {
			$maxCommits = max($data);
			$dataArray = $labelArray = array();
			foreach($data as $day=>$commits) {
				$dataArray[] = intval($commits / $maxCommits * 100);
				$labelArray[] = $day;
			}
			$url = 'http://chart.apis.google.com/chart?cht=bvg&chtt=Hour+Of+Day&chs=700x400&chbh=18&chxt=x&chxl=0:|'.implode('|',$labelArray).'&chd=t:'.implode(',',$dataArray);

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

			$this->fwrite($handle,"Hour,Events\r\n");

			foreach($data as $hour=>$commits) {
				$this->fwrite($handle,$hour.",".$commits."\r\n");
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

