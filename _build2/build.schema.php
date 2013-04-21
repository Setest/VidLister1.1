<?php
// sets up the MODX_ defines and the paths to the xPDO core
// you'll want to change this first line to point to the actual MODx install path
define('MODX_BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
define('MODX_CORE_PATH', MODX_BASE_PATH.'core/');
define('COMPONENT_PATH', MODX_CORE_PATH.'/components/vidlister/');

require_once (MODX_CORE_PATH . 'config/config.inc.php');
include_once (MODX_CORE_PATH . 'model/modx/modx.class.php');

// create the modx object and load the modPackageBuilder class
$modx = new modX();

$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder','',false, true);

// $builder = new modPackageBuilder($modx);

    // Указатель типа базы данных (MySQL / MsSQL и т.п.)
        $manager = $this->modx->getManager();

        // Класс-генератор схем
        $generator = $manager->getGenerator();


        // Генерируем файл-XML
        // /xpdo/om/mysql/xpdogenerator.class.php
        // public function writeSchema($schemaFile, $package= '', $baseClass= '', $tablePrefix= '', $restrictPrefix= false)
        // $tablePrefix  - указываем, если хотим только те таблицы, которые начинаются с этого префикса.
        // $restrictPrefix - указывает true, если хотим получить таблицы только по префиксу
        $xml= $generator->writeSchema(COMPONENT_PATH.'/model/',  'vidlister', 'xPDOObject', '' ,$restrictPrefix=true  );

        // Создает классы и мапы (php) по схеме xml
        // $generator->parseSchema($Schema, $Path);  
		$generator->parseSchema(COMPONENT_PATH.'/model/', COMPONENT_PATH.'/model/schema/vidlister.mysql.schema.xml');
		
// build the schema, using the PackageBuilder's buildSchema function. It takes 2 parameters:
// - the location of the model directory where you want the files to generate to
// - the schema xml file
// $builder->buildSchema(COMPONENT_PATH.'/model/', COMPONENT_PATH.'/model/schema/vidlister.mysql.schema.xml');
// $builder->parseSchema(COMPONENT_PATH.'/model/', COMPONENT_PATH.'/model/schema/vidlister.mysql.schema.xml');

$modx->addPackage('vidlister', $modx->getOption('core_path').'components/vidlister/model/');
// $manager = $modx->getManager();
if ($manager->createObjectContainer('vlVideo')) {
	$modx->log(modX::LOG_LEVEL_ERROR,'Container is created!');flush();
}
else {
	$modx->log(modX::LOG_LEVEL_ERROR,'ERROR: Container NOT created!');flush();
}

echo 'Finished!';
exit();