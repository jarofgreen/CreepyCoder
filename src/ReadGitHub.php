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


require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ReadGit.php';


class ReadGitHub extends BaseReadClass {

	public function process() {

		
		$this->readBranchFromRepository($this->configData['userName'],$this->configData['repository'],$this->configData['branch']);

	}

	private function readBranchFromRepository($userName, $repository, $branch) {

		$authors = isset($this->configData['authors']) && is_array($this->configData['authors']) ? $this->configData['authors'] : null;

		$page = 1;
		$gotResultsLastTime = false;

		while($gotResultsLastTime || $page == 1) {

		        $ch = curl_init();
		        curl_setopt($ch, CURLOPT_URL, "http://github.com/api/v2/json/commits/list/".$userName."/".$repository."/".$branch."?page=".$page);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		        $output = curl_exec($ch);
		        curl_close($ch); 

			$data = json_decode($output);				

			$page++;
			if ($data->commits && count($data->commits) > 0) {
				$gotResultsLastTime = true;
				foreach($data->commits as $idx=>$commit) {
					if (is_null($authors) || in_array($commit->author->email,$authors)) {
						$data = new GitCommitAction();
						$data->setDateTime(new DateTime($commit->authored_date));
						$this->dataManager->addData($data);
					}
				}
			} else {
				$gotResultsLastTime = false;
			}

			
		}

	}

}

