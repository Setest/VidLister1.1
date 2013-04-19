<?php
/**
 * Get a list of videos
 *
 * @package vidlister
 * @subpackage processors
 */
/* setup default properties */
// echo 555; return;
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$sort = $modx->getOption('sort',$scriptProperties,'created');
$dir = $modx->getOption('dir',$scriptProperties,'DESC');
$query = $modx->getOption('query',$scriptProperties,'');

// 2013-03-01 объединил запрос с modResources чтобы брать данные для раздела Topic
// появившийся для разделения видео на группы

$c = $modx->newQuery('vlVideo');
$c->setClassAlias('vlVideo');
$c->select(array(
	$modx->getSelectColumns('vlVideo', $c->getAlias(), '')
	,'topic_val' => $modx->getSelectColumns('modResource', 'topic', '', array('pagetitle'))
));

$c->leftJoin('modResource', 'topic', array('topic.id = vlVideo.topic'));
// $c->prepare();echo $c->toSQL();
// return $c->toSQL();

$count = $modx->getCount('vlVideo',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$videos = $modx->getIterator('vlVideo', $c);


/* iterate */
$list = array();
foreach ($videos as $video) {
    $video = $video->toArray();
	$video['topic']=$video['topic_val'];
    $video['jsondata'] = $modx->toJSON($video['jsondata']);
    $list[]= $video;
}
return $this->outputArray($list,$count);