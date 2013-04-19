<?php
/**
 * @package dbapi
 * @subpackage build
 */
$success = false;
$modx =&$object->xpdo;

// $modx->log(xPDO::LOG_LEVEL_INFO,$object->get('id')); // id category
// $modx->log(xPDO::LOG_LEVEL_INFO,$object->get('category')); // $object соответствует таблице modx_categories

// получаем имя категории альтернативным путем
// $modx->log(xPDO::LOG_LEVEL_INFO,$options['object']);
// $zzz=$modx->fromJSON($options['object']);
// $modx->log(xPDO::LOG_LEVEL_INFO,print_r($zzz,true));
// $modx->log(xPDO::LOG_LEVEL_INFO,$zzz['category']); // получили имя категории

// $modx->log(xPDO::LOG_LEVEL_INFO,xPDOTransport::PACKAGE_ACTION); // выдает: package_action

/*
$options объект содержащий параметры, вот наиболее интересные:
[package_name] = Vidlister
[name] = Vidlister
[license] = GNU GENERAL PUBLIC LICENSE...
[readme] = ....
[events] = [object Object]
[related_objects] = Array
	[Plugins] = Array
[related_object_attributes] = Array
	[Snippets] = Array
[object] =&gt; {"id":1,"parent":0,"category":"Vidlister"}
...			
 */
 
/* $object - объект текущего пакета, из которого можно получить к примеру 
содержащиеся плагины, странно не получает, хотя вроде все работало, нужно разобраться */
// foreach ($object->getCollection('modPlugin') as $plugin) {
// foreach ($object->getIterator('modPlugin') as $plugin) {
// foreach ($options->getIterator('modPlugin') as $plugin) {
	// $profile = $plugin->getOne('PluginEvents');
	// $extended = $profile->get('event');
	// $modx->log(modX::LOG_LEVEL_WARN,'Er2: '.$extended);
// }

switch($options[xPDOTransport::PACKAGE_ACTION]) {
    /* This code will execute during an install */
    case xPDOTransport::ACTION_INSTALL:
	case xPDOTransport::ACTION_UPGRADE:
        /* Assign plugins to System events */

		$cat_id=(int)$object->get('id'); // как себя поведет если категорий будет много?
		$cat_name=$object->get('category'); // имя категории
		// получаем все привязанные плагины
		$pluginObj = $modx->getIterator('modPlugin',array('category'=>$cat_id));
		if (!$pluginObj) $modx->log(xPDO::LOG_LEVEL_INFO,'cannot get plugin or no plugins');

		foreach ($pluginObj as $plugin) {
			$plugin_name = $plugin->get('name');
			// получаем события из плагина
			$plugin_events = $plugin->getMany('PluginEvents');
			foreach ($plugin_events as $event) {
				$event_name = $event->get('event');
				$modx->log(modX::LOG_LEVEL_WARN,'Plugin event: '.$event_name);
				$evtcount = $modx->getCount('modEvent', array('name' => $event_name));
				if ($evtcount != 1) {
					$evt = $modx->newObject('modEvent');
					$evt->set('name', $event_name);
					$evt->set('service', 6);
					$evt->set('groupname', $cat_name);
					if ($evt->save() == false) {
						$modx->log(modX::LOG_LEVEL_WARN,'ERROR: cant save event: '.$event_name);
					}
					else {
						$modx->log(modX::LOG_LEVEL_WARN,"Event: {$event_name} saved succesfully in modx_system_eventnames (modEvent object)");
					}
					/* 			
						INSERT INTO `modx_system_eventnames` (`id`, `name`, `service`, `groupname`)
						Где service:
						1 - Parser Service Events
						2 - Manager Access Events
						3 - Web Access Service Events
						4 - Cache Service Events
						5 - Template Service Events
						6 - User Defined Events
					*/		
				}
				else {
					$modx->log(modX::LOG_LEVEL_WARN,"Event: {$event_name} already exists in modx_system_eventnames (modEvent object)");
				}
				// так как мы и так берем данные из modPluginEvent
				// то следующая запись нам не важна
				// $intersect = $modx->newObject('modPluginEvent');
				// $intersect->set('event', $event);
				// $intersect->set('pluginid', $pluginObj->get('id'));
				// $intersect->save(); 
			}
		}
		$success = true;
	break;
	case xPDOTransport::ACTION_UNINSTALL:
		$modx->log(xPDO::LOG_LEVEL_INFO,'Uninstalling . . .');
		$success = true;
	break;		
}

return $success;