<?php
require_once('conf.php');
\wcf\system\cronjob\AllCharactersUpdateCronjob::directExecute($wcfdir);
?>