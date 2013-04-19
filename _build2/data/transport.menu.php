<?php
/**
 * Adds modActions and modMenus into package
 *
 * @package vidlister
 * @subpackage build
 */
$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => NAMESPACE_NAME,
    'parent' => 0,
    'controller' => 'index',
    'haslayout' => 1,
    'lang_topics' => NAMESPACE_NAME.':default,file',
    'assets' => '',
),'',true,true);

/* load menu into action */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'parent' => 'components',
    'text' => NAMESPACE_NAME,
    'description' => NAMESPACE_NAME.'.desc',
    'icon' => '',
    'menuindex' => '0',
    'params' => '',
    'handler' => '',
),'',true,true);
$menu->addOne($action);

return $menu;