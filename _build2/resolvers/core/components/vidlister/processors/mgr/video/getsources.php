<?php
/**
 * Get a list of sources
 *
 * @package vidlister
 * @subpackage processors
 */
/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$sort = $modx->getOption('sort',$scriptProperties,'id');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');

/* build query */
$c = $modx->newQuery('vlVideo');
$c->select('id, source');
$c->groupby('source');

$count = $modx->getCount('vlVideo',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$sources = $modx->getIterator('vlVideo', $c);

/* iterate */
$list = array();
foreach ($sources as $source) {
    $source = $source->toArray();
    $list[]= $source;
}

$modx->log(MODX_LOG_LEVEL_ERROR, var_export($list, true));

return $this->outputArray($list,$count);