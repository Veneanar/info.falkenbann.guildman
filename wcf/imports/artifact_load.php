<?php
require_once('../global.php');
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\HTTPRequest;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\wow\bnetAPI;
use wcf\data\wow\spell\WowSpell;

$importarray = JSON::decode(file_get_contents("artifactTraits.json"));
foreach ($importarray as $artifactID => $spellData) {
    $spellData = array_count_values($spellData);
    $countVar = count($spellData);
    $lastSpellID = 0;
    $rank = 0;
    foreach($spellData as $spellID => $rankcount) {
        if ($rankcount == 1 and $countVar > 1) {
            $rank++;
            echo "\033[34m Artifact trait has spells for each rank\033[0m" . PHP_EOL;
            createRow($rankcount, $spellID, $artifactID, $rank);
        }
        else {
            createRow($rankcount, $spellID, $artifactID);
        }
    }
}


function createRow($rankcount, $spellID, $artifactID, $rank = 0) {
    echo "ArtifactID ". $artifactID. "(".$spellID."):";
    $url = bnetAPI::buildURL('spell', 'wow', ['id' => $spellID]);
    $request = new HTTPRequest($url);
    try {
        $request->execute();
    }
    catch (HTTPNotFoundException $e) {
        echo  "\033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
        if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
    }
    $reply = $request->getReply();
    $bnetData = JSON::decode($reply['body'], true);
    echo " ". $bnetData['name'] . PHP_EOL;
    $rankData = [];
    $match = '';
    if ($rankcount > 1) {
        $starttext = $bnetData['description'];
        preg_match_all("/\d+/", $starttext, $matches);
        $t = end($matches);
        if (!empty($t)) $match = end($matches)[0];
        if (!empty($match)) {
            echo $bnetData['description'] . PHP_EOL;
            echo "set variable to:" . $match . PHP_EOL;
            for($i = 1; $i <= $rankcount; ++$i) {
                $rankData[$i] = "NEWVAL";
            }
        }
        else {
            echo "\033[34m keep Description: \033[0m" . $bnetData['description'] . PHP_EOL;
        }
    }
    $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_spells
                            (spellID, enchantID, spellName, bnetData, bnetUpdate)
                VALUES      (?,0,?,?,?)
                ON DUPLICATE KEY UPDATE
                            spellName = VALUES(spellName),
                            bnetData = VALUES(bnetData),
                            bnetUpdate = VALUES(bnetUpdate)";
    $statement = WCF::getDB()->prepareStatement($sql);
    $statement->execute([
            $spellID,
            '',
            '',
            0,
        ]);
    $sql = "INSERT INTO ARTIFACT_WORKING
                            (spellID, artifactID, artifactRank, spellName, spellRanks, overridePattern, overrideData, t_comment)
                VALUES      (?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            spellRanks = VALUES(spellRanks)";
    $statement = WCF::getDB()->prepareStatement($sql);
    $statement->execute([
            $spellID,
            $artifactID,
            $rank,
            $bnetData['name'],
            $rankcount,
            $match,
            JSON::encode($rankData),
            $bnetData['description'] . " URL: http://www.wowhead.com/spell=" . $spellID . "?rank=1"
    ]);
    echo "\033[32m update done. \033[0m" . PHP_EOL;
}

?>