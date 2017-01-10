<?php
require_once('global.php');
use wcf\system\wow\bnetAPI;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterList;

error_reporting(E_ALL);
ini_set("display_errors", 1);
//bnetAPI::updateGuild();
//bnetAPI::updateGuildMemberList();
//bnetAPI::updateCharacter(['Aiox-Forscherliga','Veneanar-Forscherliga']);
$myChar  = new WowCharacter('Veneanar-Forscherliga');

echo $myChar->getAvatar()->getImageTag();

$wowCharList = new WowCharacterList();
$wowCharList->getConditionBuilder()->add("charID LIKE ?", ['Ai%']);
$wowCharList->sqlLimit = 10;
$wowCharList->readObjects();

echo "Leider geil";


?>