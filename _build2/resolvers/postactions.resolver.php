<?php

$success = false;

// Create a reference to MODx since this resolver is executed from WITHIN a modCategory
$modx =& $object->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_UPGRADE:
        // $success = true;
        // break;
	case xPDOTransport::ACTION_INSTALL:
	
		$assetsPath = $modx->getOption('assets_path').'components/vidlister/';
		$corePath = $modx->getOption('core_path').'components/vidlister/';
		
		// т.к. схема генерируется на основе имеющегося файла схемы,
		// то когда мы обновляем компонент старый файлы не удаляются и не замещаются
		// поэтому удаляем их в ручную. А сама схема создастся из другого резольвера
		// который пойдет самым последним в выполнении

		$delParameters=array(
		   'deleteTop' => true,
		   'extensions' => false,
		   // 'delete_exclude_items' => array('george.mov','buddies.flv'),
		   // 'delete_exclude_patterns' => '/fun/i',
		);
		
		$modx->cacheManager->deleteTree($assetsPath,$delParameters);		
		$modx->cacheManager->deleteTree($corePath,$delParameters);		
	
		// $corePath = $modx->getOption('core_path').'components/vidlister/model/';
		
		// if (!isset($modx->vidlister) || $modx->vidlister == null) {
			// $modx->addPackage('vidlister',$corePath);
		    // $modx->vidlister = $modx->getService('vidlister', 'VidLister', $modx->getOption('core_path').'components/vidlister/model/vidlister/');
		// }

		// $mgr = $modx->getManager();
        // $mgr->createObjectContainer('vlVideo');

		$success = true;
		break;
		
    case xPDOTransport::ACTION_UNINSTALL:
		$modx->log(xPDO::LOG_LEVEL_INFO,'Uninstalling . . .');

		$modx->addPackage('vidlister', $modx->getOption('core_path').'components/vidlister/model/');
		$mgr = $modx->getManager();
		if ($mgr->removeObjectContainer('vlVideo')) {
			$modx->log(modX::LOG_LEVEL_ERROR,'Old container is dropped!');flush();
		}
		else {
			$modx->log(modX::LOG_LEVEL_ERROR,'ERROR: Can`t drop old container!');flush();
		}

           $success = true;
           break;
}

return $success;