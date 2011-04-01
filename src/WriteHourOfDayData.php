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

		print_r($data);
	}

}

