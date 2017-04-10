<?php
require_once('global.php');
use wcf\system\wow\bnetAPI;
use wcf\system\wow\bnetUpdate;
use wcf\system\WCF;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\data\guild\bosskill\CharBosskillList;
use wcf\data\guild\bosskill\CharBosskill;
use wcf\data\object\type\ObjectTypeCache;
//
// Characterverwaltung -> Gruppen anzeige
// Charverwaltung -> gruppenfunktionalität

// ArmoryList u. View
//


error_reporting(E_ALL);
ini_set("display_errors", 1);
//bnetAPI::updateGuild();
// bnetAPI::updateGuildMemberList();
//bnetAPI::updateCharacter([
//        [
//            'charInfo' => [
//                    'name'  => 'Aiox',
//                    'realm' => 'forscherliga',
//                    ],
//            'bnetUpdate' => 10
//        ],
//        [
//            'charInfo' => [
//                    'name'  =>   'Veneanar',
//                    'realm' => 'forscherliga',
//                    ],
//            'bnetUpdate' => 10
//        ],
        //[
        //    'charInfo' => [
        //            'name'  =>   'Goriox',
        //            'realm' => 'die-nachtwache',
        //            ],
        //    'bnetUpdate' => 10
        //],
//    ]);
 // WowCharacterAction::bulkUpdate();
//while (BackgroundQueueHandler::getInstance()->getRunnableCount() > 0)
//    BackgroundQueueHandler::getInstance()->performNextJob();
//\wcf\system\wow\bnetAPI::updateRaidBosses();

            //$guild = GuildRuntimeChache::getInstance()->getCachedObject();


//$myChar  = WowCharacter::getByCharAndRealm('Avenaro', 'forscherliga');

//echo $myChar->getAvatar()->getImageTag();

//$wowCharList = new WowCharacterList();
//$wowCharList->getConditionBuilder()->add("charID LIKE ?", ['Ai%']);
//$wowCharList->sqlLimit = 10;
//$wowCharList->readObjects();

// \wcf\system\cronjob\GuildCharUpdateCronjob::directexecute();

//echo "<pre>"; var_dump($guild->getStatisticCategorys()); echo "</pre>";
//$guild = GuildRuntimeChache::getInstance()->getCachedObject();
//$zone = [];
//foreach ($guild->getStatisticZoneIDs() as $zoneID) {
//    $bosskillList = new CharBosskillList();
//    $bosskillList->getConditionBuilder()->add("charID = ?", [329]);
//    $bosskillList->getConditionBuilder()->add("zoneID = ?", [$zoneID]);
//    $bosskillList->readObjects();
//    $bosses = [];
//    $bosseIDs = [];
//    foreach ($bosskillList->getObjects() as $bosskill) {
//        if (!in_array($bosskill->bossID, $bosseIDs)) {
//            $bosses[$bosskill->bossID] = [
//                'boss' => $bosskill->GetBoss(),
//                'modes' => [ [
//                    'difficulty' => $bosskill->difficulty,
//                    'killDate'   => $bosskill->killDate,
//                    'quantity'   => $bosskill->quantity,
//                    'icon'       => WCF::getPath()  . 'images/wow/difficulty_'. substr($bosskill->difficulty, strrpos($bosskill->difficulty, '.') +1) . ".png",
//                    'lastupdate' => $bosskill->lastupdate,
//                    ] ]
//                ];
//            $bosseIDs[] = $bosskill->bossID;
//        }
//        else {
//            $bosses[$bosskill->bossID]['modes'][] = [
//                    'difficulty' => $bosskill->difficulty,
//                    'killDate'   => $bosskill->killDate,
//                    'quantity'   => $bosskill->quantity,
//                    'icon'       => WCF::getPath() . 'images/wow/difficulty_'. substr($bosskill->difficulty, strrpos($bosskill->difficulty, '.') +1) . ".png",
//                    'lastupdate' => $bosskill->lastupdate,
//                    ];
//            }
//        }
//    $zone[] = [
//        'id' => $zoneID,
//        'name' => WCF::getLanguage()->get('wcf.global.gman.zone.'. $zoneID),
//        'bosses' => $bosses
//        ];
//}

//echo WCF::getTPL()->fetch('_bossKill', 'wcf', ['zoneList' => $zone]);
//echo "<pre>"; var_dump($zone); echo "</pre>";
//echo "<pre>"; var_dump($zone); echo "</pre>";
//echo "Leider geil";

//$charObject = new WowCharacter(127);
//$characterAction = new WowCharacterAction([$charObject], 'updateData');
//$characterAction->executeAction();
bnetUpdate::updateRealms();
//$charListObject = new WowCharacterList();
//$charListObject->sqlLimit = 2;
//$charList = $charListObject->getGuildCharacters();
//echo "count: " . count($charList);
//bnetUpdate::updateCharacter($charList, true);

?>

<!--<script data-relocate="true">
    require(['WoltLabSuite/GMan/Ui/Character/Search/Input'], function(UiCharacterSearchInput) {
        new UiCharacterSearchInput(elBySel('input[name="charsearch"]'));
    });
</script>-->
