<?php

$success = false;

// Create a reference to MODx since this resolver is executed from WITHIN a modCategory
$modx =& $object->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_UPGRADE:
	case xPDOTransport::ACTION_INSTALL:
		$assetsPath = $modx->getOption('assets_path').'components/vidlister/';
		$corePath = $modx->getOption('core_path').'components/vidlister/';
		
		$modx->addPackage('vidlister',$corePath);
		$modx->vidlister = $modx->getService('vidlister', 'VidLister', $modx->getOption('core_path').'components/vidlister/model/vidlister/');
		$mgr = $modx->getManager();
		
		if (isset($modx->vidlister)) {
			// удаляем таблицу modx_vidlister_videos из БД, так как при
			// обновлении на старую версию в таблицы отсутствует одно поле
			if ($mgr->removeObjectContainer('vlVideo')) {
				$modx->log(modX::LOG_LEVEL_ERROR,'Old container is dropped!');flush();
			}
			else {
				$modx->log(modX::LOG_LEVEL_ERROR,'ERROR: Can`t drop old container!');flush();
			}
		}
		
	
		
        $mgr->createObjectContainer('vlVideo');

		$success = true;
		break;
		
    case xPDOTransport::ACTION_UNINSTALL:
           $success = true;
           break;
}

return $success;