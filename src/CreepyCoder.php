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

require dirname(__FILE__).DIRECTORY_SEPARATOR.'DataManager.php';
require dirname(__FILE__).DIRECTORY_SEPARATOR.'BaseReadClass.php';
require dirname(__FILE__).DIRECTORY_SEPARATOR.'BaseWriteClass.php';
require dirname(__FILE__).DIRECTORY_SEPARATOR.'CoderAction.php';



$opts = getopt('c:');

$configXML  = file_get_contents($opts['c']);
$xmlDoc = new DOMDocument();
$xmlDoc->loadXML($configXML);

$dataManager = new DataManager();

#################################### set input

$configList = $xmlDoc->getElementsByTagName('ReadSVN');
$configListLength = $configList->length;
if ($configListLength > 0) {
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'ReadSVN.php';
	for($pos=0; $pos<$configListLength; $pos++) {
		$dataManager->addReader(new ReadSVN($configList->item($pos)));
	}
}


$configList = $xmlDoc->getElementsByTagName('ReadGitHub');
$configListLength = $configList->length;
if ($configListLength > 0) {
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'ReadGitHub.php';
	for($pos=0; $pos<$configListLength; $pos++) {
		$dataManager->addReader(new ReadGitHub($configList->item($pos)));
	}
}


#################################### process input

$dataManager->process();
#print_r($dataManager->getData());
#print(count($dataManager->getData()));


#################################### output

$configList = $xmlDoc->getElementsByTagName('WriteHourOfDayData');
$configListLength = $configList->length;
if ($configListLength > 0) {
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'WriteHourOfDayData.php';
	for($pos=0; $pos<$configListLength; $pos++) {
		$dataManager->writeData(new WriteHourOfDayData($configList->item($pos)));
	}
}

$configList = $xmlDoc->getElementsByTagName('WriteDayOfWeekData');
$configListLength = $configList->length;
if ($configListLength > 0) {
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'WriteDayOfWeekData.php';
	for($pos=0; $pos<$configListLength; $pos++) {
		$dataManager->writeData(new WriteDayOfWeekData($configList->item($pos)));
	}
}

$configList = $xmlDoc->getElementsByTagName('WriteICalData');
$configListLength = $configList->length;
if ($configListLength > 0) {
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'WriteICalData.php';
	for($pos=0; $pos<$configListLength; $pos++) {
		$dataManager->writeData(new WriteICalData($configList->item($pos)));
	}
}

$configList = $xmlDoc->getElementsByTagName('WriteDayOfWeekAndHourOfDayData');
$configListLength = $configList->length;
if ($configListLength > 0) {
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'WriteDayOfWeekAndHourOfDayData.php';
	for($pos=0; $pos<$configListLength; $pos++) {
		$dataManager->writeData(new WriteDayOfWeekAndHourOfDayData($configList->item($pos)));
	}
}

$configList = $xmlDoc->getElementsByTagName('WriteDB');
$configListLength = $configList->length;
if ($configListLength > 0) {
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'WriteDB.php';
	for($pos=0; $pos<$configListLength; $pos++) {
		$dataManager->writeData(new WriteDB($configList->item($pos)));
	}
}

