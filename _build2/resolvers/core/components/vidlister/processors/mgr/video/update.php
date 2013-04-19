<?php
$video = $modx->getObject('vlVideo', array('id' => $scriptProperties['id']));
if(!is_object($video)) {
    $video = $modx->newObject('vlVideo');
    $video->set('created', time());
}
$video->fromArray($scriptProperties);
$video->set('updated', time());
if ($video->save()) {
    $video = $video->toArray('', true);
	return $modx->error->success('', $video);
}
else {
	return $modx->error->failure('');
}