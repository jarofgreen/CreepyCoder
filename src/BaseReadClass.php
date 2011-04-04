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


abstract class BaseReadClass {

	protected $configData;	

	/** @var DataManager **/
	protected $dataManager;

	public function __construct($configData = array()) {
		$this->configData = $configData;
	}

	public function setDataManager(DataManager $dm) {
		$this->dataManager = $dm;
	}

	/** Run code to actually get data. 
	 * Data should be represented in a class that extends CoderAction - call $this->dataManager->addData to save the data.
	 * you can also call $this->dataManager->addReader() - so one reader can poll something and spawn more readers. eg poll a GitHub account and add a new reader for each project found.
	 **/
	abstract public function process();

}
