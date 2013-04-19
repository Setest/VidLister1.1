<?php
/**
 * Loads the home page.
 *
 * @package vidlister
 * @subpackage controllers
 */

$modx->regClientStartupScript($vidlister->config['jsUrl'].'mgr/widgets/videos.grid.js');
$modx->regClientStartupScript($vidlister->config['jsUrl'].'mgr/widgets/home.panel.js');
$modx->regClientStartupScript($vidlister->config['jsUrl'].'mgr/sections/index.js');

$output = '<div id="vidlister-panel-home-div"></div>';

return $output;