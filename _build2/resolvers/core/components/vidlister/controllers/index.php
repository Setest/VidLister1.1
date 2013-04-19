<?php
/**
 * @package vidlister
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)) . '/model/vidlister/vidlister.class.php';
$vidlister = new VidLister($modx);

return $vidlister->initialize('mgr');