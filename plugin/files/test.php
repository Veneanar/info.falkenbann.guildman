<?php
require_once('global.php');
use wcf\system\wow\bnetAPI;

error_reporting(E_ALL);
ini_set("display_errors", 1);
bnetAPI::updateGuild();
bnetAPI::updateGuildMemberList();
bnetAPI::updateCharacter(['Aiox-Forscherliga','Veneanar-Forscherliga']);

echo "Leider geil";
?>