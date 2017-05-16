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
use wcf\data\guild\tracking\Tracking;
use wcf\system\cronjob\CharacterTrackingCronjob;
//
// Characterverwaltung -> Gruppen anzeige
// Charverwaltung -> gruppenfunktionalität

// ArmoryList u. View
//


error_reporting(E_ALL);
ini_set("display_errors", 1);


                //$t = JSON::decode($this->data[$name]);
                //$enchant = 0;
                //$transmog = 0;
                //$bonus = isset($t['bonusLists']) ? $t['bonusLists'] : [];
                //$context = isset($t['context']) ? $t['context'] : '';
                //if (isset($t['tooltipParams']['enchant'])) $enchant = $t['tooltipParams']['enchant'];
                //if (isset($t['tooltipParams']['transmogItem'])) $transmog = $t['tooltipParams']['transmogItem'];
                //if (isset($t['artifactId']) && $t['artifactId'] > 0) {
                //    $artifactTraits= isset($t['artifactTraits']) ? $t['artifactTraits'] : [];
                //    $relics = isset($t['relics']) ? $t['relics'] : [];
                //    $itemLevel = isset($t['itemLevel']) ? $t['itemLevel'] : [];
                //    //echo "<pre>"; var_dump($t); "</pre>"; die();
                //    $this->items[$name] = new ViewableArtifact(new WowItem($t['id']), '', $bonus, $relics, $artifactTraits, $itemLevel, $enchant, $transmog, []);



$vene = new WowCharacter(318);
//$action = new WowCharacterAction([$vene], 'updateData');
//$action->executeAction();

$weappon = $vene->getEquip()->getItem('mainHand');
echo "<pre>"; var_dump($weappon); "</pre>";


//$tracking = new Tracking(1);
//echo $tracking->renderTemplate(new WowCharacter(294));


////echo time();
$cronjob = new CharacterTrackingCronjob();
$cronjob->directExecute();
//echo "<br>allet kla<br>" . time() ;
?>

<!--<script data-relocate="true">
    require(['WoltLabSuite/GMan/Ui/Character/Search/Input'], function(UiCharacterSearchInput) {
        new UiCharacterSearchInput(elBySel('input[name="charsearch"]'));
    });
</script>-->
