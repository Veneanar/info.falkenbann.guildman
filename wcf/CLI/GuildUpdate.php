<?php
require_once('conf.php');
\wcf\system\cronjob\GuildUpdateCronjob::directExecute($wcfdir);
?>