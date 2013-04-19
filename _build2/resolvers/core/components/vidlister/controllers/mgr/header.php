<?php
/**
 * Loads the header for mgr pages.
 *
 * @package vidlister
 * @subpackage controllers
 */
$modx->regClientStartupScript($vidlister->config['jsUrl'].'mgr/vidlister.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    VidLister.config = '.$modx->toJSON($vidlister->config).';
});
</script>');

return '';