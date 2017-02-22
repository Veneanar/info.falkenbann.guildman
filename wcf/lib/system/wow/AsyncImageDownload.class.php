<?php
namespace wcf\system\wow;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\Exception;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;
use wcf\util\JSON;

/**
 * bnetImage short summary.
 *
 * bnetImage description.
 *
 * @version 1.0
 * @author jarau
 */

class AsyncImageDownload extends \Thread{
    public $path;

    public function __construct($path, $charID) {
        $this->path = $path;
        $this->charID = $charID;
    }
    public function run() {
            $url = bnetAPI::buildURL('image', 'wow', [$this->path]);
            $reply = null;
            try {
                $reply = @file_get_contents($url);
                if ($reply === false) {
                    echo $this->charID . "Image:\033[31m FAILED \033[0m (".$url . ")" . PHP_EOL;
                    return;
                }
            }
            catch (Exception $e) {
                echo $this->charID . "Image:\033[31m FAILED \033[0m (".$url . ")" . PHP_EOL;
                return;
            }
            $savePath = WCF_DIR . 'images/wow/' . $this->path;
            if(!file_exists(dirname($savePath))) mkdir(dirname($savePath), 0777, true);
            file_put_contents($savePath, $reply);
            echo $this->charID . " Image:\033[32m OK \033[0m (".$this->path . ")". PHP_EOL;
        }
    }
