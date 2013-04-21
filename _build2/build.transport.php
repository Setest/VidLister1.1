<?php

$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);
 
/* задаем имя пакета */
define('PKG_NAME','Vidlister');
define('PKG_NAME_LOWER','vidlister');
define('NAMESPACE_NAME', PKG_NAME_LOWER);
define('PKG_CATEGORY', PKG_NAME);
define('PKG_VERSION','1.1.1');
define('PKG_RELEASE','beta1');
 
/* задаем пути для упаковщика */
$root = dirname(dirname(__FILE__)).'/';
$dir_build="_build2";

// $assets_comp_path=$root.'assets/components/'.PKG_NAME_LOWER;
// $core_comp_path=$root.'core/components/'.PKG_NAME_LOWER;

$assets_comp_path=$root.$dir_build.'/resolvers/assets/components/'.PKG_NAME_LOWER;
$core_comp_path=$root.$dir_build.'/resolvers/core/components/'.PKG_NAME_LOWER;


$sources = array(
    'root' => $root,
    'build' => $root.$dir_build.'/',
    'data' => $root.$dir_build.'/data/',
    'resolvers' => $root.$dir_build.'/resolvers/',
	
    // 'assets' => $root.$dir_build.'/resolvers/assets/components/',
    // 'core' => $root.$dir_build.'/resolvers/core/components/',	 

    'assets' => $assets_comp_path,
    'core' => $core_comp_path,	
	
    // 'resolver_assets' => $assets_comp_path,
    // 'resolver_core' => $core_comp_path,
    'elements' => $core_comp_path.'/elements/',
    'plugins' => $core_comp_path.'/elements/plugins/',
    'snippets' => $core_comp_path.'/elements/snippets/',
    'chunks' => $core_comp_path.'/elements/chunks/',
    'lexicon' => $core_comp_path.'/lexicon/',
    'docs' => $core_comp_path.'/docs/',
	
    // 'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    // 'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
	
    // 'source_assets' => $root.$dir_build.'/resolvers/assets',
    // 'source_core' => $root.$dir_build.'/resolvers/core',
	
    'source_assets' => $assets_comp_path,
    'source_core' => $core_comp_path, 
	
);
unset($root);

require_once $sources['build'].'includes/functions.php'; 
/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
 
$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
 
echo $sources['lexicon']; 
 
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');

/*------------== Добавляем пространство имен ==-----------------*/
// нет нужды так как уже зарегистрированно в строке $builder->registerNamespace
// $namespace = $modx->newObject('modNamespace');
// $namespace->set('name',PKG_NAME_LOWER);
// $namespace->set('path','{core_path}components/'.PKG_NAME_LOWER.'/');
// $vehicle = $builder->createVehicle($namespace,array(
    // xPDOTransport::UNIQUE_KEY => 'name',
    // xPDOTransport::PRESERVE_KEYS => true,
    // xPDOTransport::UPDATE_OBJECT => true,
// ));
// $builder->putVehicle($vehicle);
// $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.PKG_NAME_LOWER.' namespace.');flush();
// unset($vehicle,$namespace);


/*------------== Создаем категорию ==-----------------*/
// чтобы запихать плагин в категорию и дать красивый вид имени
// в общем списке пакетов при установке


// $modx->log(modX::LOG_LEVEL_INFO,file_get_contents($sources['docs'].'changelog.txt'));flush();

$modx->log(modX::LOG_LEVEL_INFO,'Create category...');flush();
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_CATEGORY);


/*------------== Добавляем пункт в меню ==-----------------*/
$menu = include $sources['data'].'transport.menu.php';
$vehicle= $builder->createVehicle($menu,array (
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'text',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Action' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
        ),
    ),
));
$builder->putVehicle($vehicle);
unset($vehicle,$action,$menu);
$modx->log(modX::LOG_LEVEL_INFO,'Added item in menu.'); flush();

/*------------== Добавляем системные переменные ==-----------------*/
/* load system settings */
$add_settings_from="jsonfile"; 
	// use "jsonfile" if you have file /data/properties/system_settings.json
	// or "modx", if you want to extract setting from modx directly
$settings = include $sources['data'].'transport.settings.php';
$attributes = array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
if (!is_array($settings)) { $modx->log(modX::LOG_LEVEL_ERROR,'Adding settings failed.'); }
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting,$attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($settings).' system settings.'); flush();
unset($settings,$setting,$attributes);


/*------------== Добавляем плагин ==-----------------*/
/*-----------== вместе с событиями ==-----------------*/
$modx->log(modX::LOG_LEVEL_INFO,'Packaging in plugins...');flush();
$plugin = include $sources['data'].'transport.plugins.php';
if (empty($plugin)) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Could not package in plugins.');flush();
}
else {
	/* add plugin to category */
	$category->addMany($plugin);
    $modx->log(xPDO::LOG_LEVEL_INFO, count($plugin).' plugins with events added to packages succesfully!'); flush();
}
unset($plugin);
// return;


/*------------== Добавляем сниппеты ==-----------------*/
$snippets = include $sources['data'].'transport.snippets.php';
if (!is_array($snippets)) {
    $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in snippets.');
} else {
    $category->addMany($snippets);
    $modx->log(modX::LOG_LEVEL_INFO,'In package add '.count($snippets).' snippets.');
}
unset($snippets);

/*------------== Добавляем чанки ==-----------------*/
$chunks = include $sources['data'].'transport.chunks.php';
if (!is_array($chunks)) {
    $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in chunks.');
} else {
    $category->addMany($chunks);
    $modx->log(modX::LOG_LEVEL_INFO,'In package add '.count($chunks).' chunks.');
}
unset($chunks);



/*------------== Устанавливаем атрибуты пакета ==-----------------*/
$attributes = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Plugins' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'PluginEvents' => array(
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => false,
                    xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
                ),
            ),
        ),
    )
);
// $vehicle = $builder->createVehicle($plugin, $attributes);
$vehicle = $builder->createVehicle($category, $attributes);

/*------------== Добавление файловых резольверов (Resolvers) ==-----------------*/
$modx->log(modX::LOG_LEVEL_INFO,'Adding file resolvers to category...');flush();

// добавляем события в систему которые с которыми работает наш плагин
$modx->log(modX::LOG_LEVEL_INFO,'Adding resolve.plugin_events.php');flush();
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'].'resolve.plugin_events.php',
));

// добавим наш компонент в систему
$modx->log(modX::LOG_LEVEL_INFO,'Adding postactions.resolver.php');flush();
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'].'postactions.resolver.php',
));

$modx->log(modX::LOG_LEVEL_INFO,'Adding ASSETS');flush();
$vehicle->resolve('file',array(
    'source' => $sources['assets'],
    'target' => "return MODX_ASSETS_PATH.'components/';",
));
$modx->log(modX::LOG_LEVEL_INFO,'Adding CORE');flush();

$vehicle->resolve('file',array(
    'source' => $sources['core'],
    'target' => "return MODX_CORE_PATH.'components/';",
));

// специальный резольвер который срабатывает последним после установки
// компонента в систему, чтобы мы могли обновить таблицы БД по необходимости
$modx->log(modX::LOG_LEVEL_INFO,'Adding afteractions.resolver.php');flush();
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'].'afteractions.resolver.php',
));


$builder->putVehicle($vehicle);

/*------------== Добавляем информацию к пакету ==-----------------*/
$modx->log(modX::LOG_LEVEL_INFO,'Adding package attributes and setup options...');flush();
$builder->setPackageAttributes(array(
    'package_name' => PKG_NAME,
    'name' => PKG_NAME,
    'license' => file_get_contents($sources['docs'].'license.txt'),
    'readme' => file_get_contents($sources['docs'].'readme.txt'),
    'changelog' => file_get_contents($sources['docs'].'changelog.txt'),
	// яркий пример QIP
	// https://github.com/splittingred/Quip/blob/develop/_build2/build.transport.php
    // 'setup-options' => array(
        // 'source' => $sources['build'].'setup.options.php',
    // ),
));




 
/*------------== Упаковываем ==-----------------*/
/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');flush();
$builder->pack();
 
$tend= explode(" ", microtime());
$tend= $tend[1] + $tend[0];
$totalTime= sprintf("%2.4f s",($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");flush();
exit ();