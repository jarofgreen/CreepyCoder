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

require dirname(__FILE__).DIRECTORY_SEPARATOR.'ReadSVN.php';


$dataManager = new DataManager();

$dataManager->addReader(new ReadSVN(array('repo'=>'https://elastik.svn.sourceforge.net/svnroot/elastik/trunk','authors'=>array('jarofgreen'))));

$dataManager->process();

print_r($dataManager->getData());

print(count($dataManager->getData()));

require dirname(__FILE__).DIRECTORY_SEPARATOR.'WriteHourOfDayData.php';

$dataManager->writeData(new WriteHourOfDayData(array()));

