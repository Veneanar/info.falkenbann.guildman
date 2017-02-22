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

class SyncImageDownload {
    public $path;

    public function __construct($path) {
        $this->path = $path;
    }
    public function run() {
            $url = bnetAPI::buildURL('image', 'wow', [$this->path]);
            $request = new HTTPRequest($url);
            try {
                $request->execute();
            }
            catch (HTTPNotFoundException $e) {
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                return;
            }
            $savePath = WCF_DIR . 'images/wow/' . $this->path;
            if(!file_exists(dirname($savePath))) mkdir(dirname($savePath), 0777, true);
            file_put_contents($savePath, $request->getReply()['body']);
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'Bild gespeichert: '. $savePath - PHP_EOL, FILE_APPEND);
        }
    }
