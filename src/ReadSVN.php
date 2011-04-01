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


class SVNCommitAction extends CoderAction {

}

class ReadSVN extends BaseReadClass {

	public function process() {

		$authors = isset($this->configData['authors']) && is_array($this->configData['authors']) ? $this->configData['authors'] : null;
	
		$out = array();
		exec("svn log --xml ".$this->configData['repo'], $out);
	
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML(implode('',$out));

		$logEntryList = $xmlDoc->getElementsByTagName('logentry');
		$logEntryListLength = $logEntryList->length;
		for($pos=0; $pos<$logEntryListLength; $pos++) {
			$x = $logEntryList->item($pos);
			if (is_null($authors) || in_array($x->getElementsByTagName('author')->item(0)->textContent,$authors)) {
				$data = new SVNCommitAction();
				$data->setDateTime(new DateTime($x->getElementsByTagName('date')->item(0)->textContent));
				$this->dataManager->addData($data);
			}
		}		

	}

}
