#!/usr/bin/php
<?php
require_once('conf.php');
\wcf\system\cronjob\GuildCharactersUpdateCronjob::directExecute($wcfdir);
?>