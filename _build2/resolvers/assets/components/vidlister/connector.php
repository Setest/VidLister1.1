<?php
/**
 * VidLister Connector
 *
 * @package vidlister
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('vidlister.core_path',null,$modx->getOption('core_path').'components/vidlister/');
require_once $corePath.'model/vidlister/vidlister.class.php';
$modx->vidlister = new VidLister($modx);

$modx->lexicon->load('vidlister:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->vidlister->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));