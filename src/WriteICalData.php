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


class WriteICalData extends BaseWriteClass {

	protected $file;

	public function __construct($configData = array()) {
		if (get_class($configData) == 'DOMElement') {
			$this->file = $configData->getAttribute('File');
		}
		parent::__construct($configData);
	}

	public function write() {

		if (!$handle = fopen($this->file, 'w')) {
			throw new Exception("Cannot open file");
		}

		$this->fwrite($handle,"BEGIN:VCALENDAR\r\n");
		$this->fwrite($handle,"VERSION:2.0\r\n");
		$this->fwrite($handle,"PRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\n");

		foreach($this->dataManager->getData() as $i=>$item) {
			$this->fwrite($handle,"BEGIN:VEVENT\r\n");
			$this->fwrite($handle,"UID:uid".$i."@example.com\r\n");
			$this->fwrite($handle,"SUMMARY: Event\r\n");
			$this->fwrite($handle,"DTSTAMP:".$item->getDateTimeAs("Ymd\THis\Z")."\r\n");
			$this->fwrite($handle,"DTSTART:".$item->getDateTimeAs("Ymd\THis\Z")."\r\n");
			$this->fwrite($handle,"DTEND:".$item->getDateTimeAs("Ymd\THis\Z")."\r\n");
			$this->fwrite($handle,"END:VEVENT\r\n");
		}

		$this->fwrite($handle,"END:VCALENDAR\r\n");
	
		fclose($handle);

	}

	private function fwrite($handle,$data) {
		if (fwrite($handle, $data) === FALSE) {
			throw new Exception("Cannot write to file");
		}
	}

}

