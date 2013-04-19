<?php
$video = $modx->getObject('vlVideo', array('id' => $_REQUEST['id']));
if(is_object($video) && $video->remove()) {
    return $modx->error->success('');
}
else {
	return $modx->error->failure($modx->lexicon('vidlister.video.error.nf'));
}