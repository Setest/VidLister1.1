<?php
$settings = array();

switch ($add_settings_from) {
	case "jsonfile":
		$dir=$sources['data'];
		$prop_file=$dir.'properties/system_settings.json';
		if (is_file($prop_file)){
			$properties = file_get_contents($prop_file);
			// $modx->log(modX::LOG_LEVEL_INFO,'PPP:'.$properties);flush();
			if (!empty($properties)){
				// $modx->log(modX::LOG_LEVEL_INFO,print_r($properties,true));flush();
				$settings =json_decode($properties,true); // принимает только массив, вполне подходят экспортные параметры в UTF8-without BOM
				$modx->log(modX::LOG_LEVEL_INFO,"Insert system properties from JSON file: ".print_r($settings,true));flush();
				unset($properties);
			}
		}
		else {
			$modx->log(modX::LOG_LEVEL_INFO,"ERROR: can`t insert system properties from JSON file, file not exist.");flush();
		}
	break;
	case "modx":
		$sys_prop = $modx->getCollection('modSystemSetting', array(
			  'namespace' => NAMESPACE_NAME
		));
		if ($sys_prop){
			$modx->log(modX::LOG_LEVEL_INFO,"Insert system properties from MODx");flush();
			foreach($sys_prop as $prop){
				// print_r($prop->toArray());
				$modx->log(modX::LOG_LEVEL_INFO,"Insert: ".$prop->key);flush();
				$settings[]=$prop->toArray();
			}
			unset($sys_prop);	
		}
		else {
			$modx->log(modX::LOG_LEVEL_INFO,"ERROR: can`t insert system properties from MODx, object is empty.");flush();
		}

	break;
	
}

// получаем итоговый массив настроек для последующего добавления его в пакет
$settings_obj = array();

$modx->log(modX::LOG_LEVEL_INFO,"Itog system settings: ".PHP_EOL.(json_encode($settings)).PHP_EOL);flush();

foreach($settings as $cur_settings){
	// $modx->log(modX::LOG_LEVEL_INFO,print_r($cur_settings,true));flush();
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray($cur_settings,'',true,true);
	$settings_obj[]=$setting;
	unset($setting);
}


unset($settings);
return $settings_obj;