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


class GitCommitAction extends CoderAction {

}

class ReadGit extends BaseReadClass {

	protected $directory;

	protected $authors;

	public function __construct($configData = array()) {
		if (get_class($configData) == 'DOMElement') {
			$this->directory = $configData->getAttribute('Directory');
			$list = $configData->getElementsByTagName('Author');
			if ($list->length > 0) {
				$this->authors = array();
				for($pos=0; $pos<$list->length ; $pos++) {
					$this->authors[] = $list->item($pos)->getAttribute('Email');
				}
			}
		}
		parent::__construct($configData);
	}

	public function process() {

		$commits = array();

		################# Find all commits
		$out = array();
		exec("cd ".$this->directory."; git branch", $out);
		
		foreach($out as $line) {
			$line = trim($line);
			if (substr($line,0,1) == '*') $line = substr($line, 1);

			$log = array();
			exec("cd ".$this->directory."; git log ".$line, $log);

			$lastCommit = $lastEmail = $lastDate = null;

			foreach($log as $logLine) {
				if (substr($logLine,0,7) == 'commit ') {
					if ($lastCommit && $lastDate && $lastEmail) {
						$commits[$lastCommit] = array('date'=>$lastDate,'email'=>$lastEmail);
					}
					$lastCommit = trim(substr($logLine, 8));
				} else if (substr($logLine,0,7) == 'Author:') {
					$bits = explode('<', $logLine);
					$bits = explode('>', $bits[1]);
					$lastEmail = $bits[0];
				} else if (substr($logLine,0,5) == 'Date:') {
					$lastDate = substr($logLine,5);
				}

			}

		}

		//var_dump($commits);

		######### now push commits to data manager

		foreach($commits as $commit) {
			if (in_array($commit['email'], $this->authors)) {
				$data = new GitCommitAction();
				$data->setDateTime(new DateTime($commit['date']));
				$this->dataManager->addData($data);
			}
		}

	}


}
