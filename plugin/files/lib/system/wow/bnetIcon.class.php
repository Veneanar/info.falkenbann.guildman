<?php
namespace wcf\system\wow;
use wcf\system\wow\exception\AuthenticationFailure;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;
use wcf\system\exception\LoggedException;
use wcf\system\exception\HTTPNotFoundException;
use wcf\util\exception\HTTPException;
use wcf\system\exception\Exception;
use wcf\system\exception\SystemException;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\util\HTTPRequest;
/**
 * access to blizzard cdn
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

final class bnetIcon {

    /**
     * Creates a wow icon URL
     * @param string    $name              name of the icon. if .jpg is missing, it's will be added.
     * @param integer   $size             size of the icon. valid values: 18,36,56. if omited 36 is used.
     * @param string    $game           game string: wow, d3, sc2
     * @throws AuthenticationFailure
     * @return string
     */
    static public function buildURL($name, $size=36, $game = 'wow') {
        $host = '';
        if (strpos($name, '.jpg')===false) $name = $name . '.jpg';
        if (GMAN_BNET_KEY == '') throw new AuthenticationFailure('Missing battle.net API Key! Settings -> Gman -> battle.net');
        if (GMAN_BNET_REGION == 'eu.api.battle.net') {
            $host = 'http://media.blizzard.com/'.$game.'/icons/'.$size.'/';
        }
        elseif (GMAN_BNET_REGION == 'us.api.battle.net') {
            $host = 'http://media.blizzard.com/'.$game.'/icons/'.$size.'/';
        }
        elseif (GMAN_BNET_REGION == 'kr.api.battle.net') {
            $host = 'http://media.blizzard.com/'.$game.'/icons/'.$size.'/';
        }
        elseif (GMAN_BNET_REGION == 'tw.api.battle.net') {
            $host = 'http://media.blizzard.com/'.$game.'/icons/'.$size.'/';
        }
        else {
            $host = 'http://media.blizzard.com/'.$game.'/icons/'.$size.'/';
        }
        return $host . $name;
    }

    /**
     * Downloads a wow icon from mdia.blizzard.com JPG only
     * @param string $name              name of the icon. if .jpg is missing, it's will be added.
     * @param integer $size             size of the icon. valid values: 18,36,56. if omited 36 is used.
     * @return bool
     */
    static public function download($name, array $sizes=null) {
        if (empty($sizes)) $sizes = [18,36,56];
        if (strpos($name, '.jpg')===false) $name = $name . '.jpg';
        foreach ($sizes as $size) {
            $url = static::buildURL($name, $size);
            $request = new HTTPRequest($url);
            try {
                $request->execute();
            }
            catch (HTTPNotFoundException $e) {
                if (ENABLE_DEBUG_MODE) @file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                return false;
            }
            catch (HTTPException $e) {
                if (ENABLE_DEBUG_MODE) @file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                return false;
            }
            catch (SystemException $e) {
                if (ENABLE_DEBUG_MODE) @file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** Cannot reach media.blizzard.com for '. $url . PHP_EOL, FILE_APPEND);
                return false;
            }
            $savePath = WCF_DIR . 'images/wow/'.$size.'/'.$name;
            if(!file_exists(dirname($savePath))) mkdir(dirname($savePath), 0777, true);
            file_put_contents($savePath, $request->getReply()['body']);
        }
        return true;
    }
}