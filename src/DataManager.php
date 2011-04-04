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

class DataManager {

	protected $readers = array();

	protected $data = array();

	/** Adds readers to list. Readers are not run yet. **/
	public function addReader(BaseReadClass $reader) {
		$reader->setDataManager($this);
		$this->readers[] = $reader;
	}

	/** Runs all readers, including any new ones that get added to the list as existing readers run. **/
	public function process() {
		$i = 0;
		// list of readers may increase when we are inside this loop so we must check loop limits every iteration.
		while($i < count($this->readers)) {
			$this->readers[$i]->process();
			$i++;
		}
	}

	/** called by a runner to store a piece of data **/
	public function addData(CoderAction $action) {
		$this->data[] = $action;
	}

	public function getData() { return $this->data; }

	/** takes a writer and runs it immediately - no waiting list **/
	public function writeData(BaseWriteClass $writer) {
		$writer->setDataManager($this);
		$writer->write();
	}

}



