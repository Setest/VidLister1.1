<?php

$success = false;

// Create a reference to MODx since this resolver is executed from WITHIN a modCategory
$modx =& $object->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_UPGRADE:
        $success = true;
        break;
	case xPDOTransport::ACTION_INSTALL:

		if (!isset($modx->vidlister) || $modx->vidlister == null) {
			$modx->addPackage('vidlister', $modx->getOption('core_path').'components/vidlister/model/');
		    $modx->vidlister = $modx->getService('vidlister', 'VidLister', $modx->getOption('core_path').'components/vidlister/model/vidlister/');
		}

		$mgr = $modx->getManager();
        $mgr->createObjectContainer('vlVideo');

		$success = true;
		break;
    case xPDOTransport::ACTION_UNINSTALL:
           $modx->log(xPDO::LOG_LEVEL_INFO,'Uninstalling . . .');
           $success = true;
           break;
}

return $success;