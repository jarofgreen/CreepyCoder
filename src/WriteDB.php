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


class WriteDB extends BaseWriteClass {


	protected $dsn;
	protected $userName = "cc";
	protected $password = "cc";
	protected $tableName = "data";
	protected $timeFieldName = "data";


	public function __construct($configData = array()) {
		if (get_class($configData) == 'DOMElement') {
			$this->dsn = $configData->getAttribute('DSN');
			// TODO: Only
			if ($configData->hasAttribute('UserName')) $this->userName = $configData->getAttribute('UserName');
			if ($configData->hasAttribute('Password')) $this->password = $configData->getAttribute('Password');
			if ($configData->hasAttribute('TableName')) $this->tableName = $configData->getAttribute('TableName');
			if ($configData->hasAttribute('TimeFieldName')) $this->timeFieldName = $configData->getAttribute('TimeFieldName');
		}
		parent::__construct($configData);
	}

	public function write() {

		$pdo = new PDO($this->dsn, $this->userName, $this->password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stat = $pdo->prepare("INSERT INTO ".$this->tableName." (".$this->timeFieldName.") VALUES (:d)");

		foreach($this->dataManager->getData() as $item) {
			$stat->bindValue('d', $item->getDateTimeAs('Y-m-d H:i:s'), PDO::PARAM_STR);
			$stat->execute();
		}

	}

}

