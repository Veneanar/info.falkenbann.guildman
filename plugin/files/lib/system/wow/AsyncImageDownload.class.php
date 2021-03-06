<?php
namespace wcf\system\wow;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\Exception;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;
use wcf\util\JSON;

/**
 * downloads a battle.net image Async (pthreads needed)
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class AsyncImageDownload extends \Thread{
    public $path;
    public $name;

    public function __construct($path, $name) {
        $this->path = $path;
        $this->name = $name;
    }
    public function run() {
            $url = bnetAPI::buildURL('image', 'wow', [$this->path]);
            $reply = null;
            try {
                $reply = @file_get_contents($url);
                if ($reply === false) {
                    echo $this->name ." Image:\033[31m FAILED \033[0m (".$url . ")" . PHP_EOL;
                    return;
                }
            }
            catch (Exception $e) {
                echo $this->name ." Image:\033[31m FAILED \033[0m (".$url . ")" . PHP_EOL;
                return;
            }
            $savePath = WCF_DIR . 'images/wow/' . $this->path;
            if(!file_exists(dirname($savePath))) mkdir(dirname($savePath), 0777, true);
            file_put_contents($savePath, $reply);
            echo $this->name ." Image:\033[32m OK \033[0m (".$this->path . ")". PHP_EOL;
        }
    }
