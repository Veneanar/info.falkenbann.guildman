<?php
require_once('global.php');
use wcf\system\wow\bnetAPI;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\background\BackgroundQueueHandler;

error_reporting(E_ALL);
ini_set("display_errors", 1);
//bnetAPI::updateGuild();
//bnetAPI::updateGuildMemberList();
bnetAPI::updateCharacter([
        [
            'charID' =>'Aiox-Forscherliga',
            'bnetUpdate' => 10
        ],
        [
            'charID' => 'Veneanar-Forscherliga',
            'bnetUpdate' => 10
        ],
    ]);
// WowCharacterAction::bulkUpdate();
//while (BackgroundQueueHandler::getInstance()->getRunnableCount() > 0)
    BackgroundQueueHandler::getInstance()->performNextJob();
$myChar  = new WowCharacter('Veneanar-Forscherliga');
echo $myChar->getAvatar()->getImageTag();

//$wowCharList = new WowCharacterList();
//$wowCharList->getConditionBuilder()->add("charID LIKE ?", ['Ai%']);
//$wowCharList->sqlLimit = 10;
//$wowCharList->readObjects();

// \wcf\system\cronjob\GuildCharUpdateCronjob::directexecute();
echo "Leider geil";


?>

<!--<script data-relocate="true">
    require(['WoltLabSuite/GMan/Ui/Character/Search/Input'], function(UiCharacterSearchInput) {
        new UiCharacterSearchInput(elBySel('input[name="charsearch"]'));
    });
</script>-->
