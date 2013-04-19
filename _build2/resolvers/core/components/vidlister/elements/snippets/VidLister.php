<?php
/**
 * snippet VidLister <VidLister> 
 
 * EditedBy: Stepan Prishepenko (Setest) <itman116@gmail.com>
 
 * Version: 1.0.1 (09.04.2013) добавил критерий поиска, если указан раздел topic, много изменений в самом компоненте со стороны менеджера
 * Version: 1.0.0 (08.04.2013) получаем список импортированных видеороликов с youtube если картинка на существует, то она берется напрямую с youtube
 *
**/

$modx->getService('vidlister','VidLister',$modx->getOption('vidlister.core_path',null,$modx->getOption('core_path').'components/vidlister/').'model/vidlister/',$scriptProperties);

$modx->lexicon->load('vidlister:default');

//settings
$tpl = $modx->getOption('tpl', $scriptProperties, '{"youtube":"vlYoutube","vimeo":"vlVimeo"}');
$scripts = $modx->getOption('scripts', $scriptProperties, '1');
$sortby = $modx->getOption('sortby', $scriptProperties, 'created');
$sortdir = $modx->getOption('sortdir', $scriptProperties, 'DESC');

// 2013-03-01 добавляем китерий выбора topic
$topic = $_GET["topic"]?(int)$_GET["topic"]:0;


//template per source set using JSON
$tpls = $modx->fromJSON($tpl);

$where = $modx->getOption('where', $scriptProperties, '');
$where = !empty($where) ? $modx->fromJSON($where) : array();

if (!empty($topic)) $where["topic"]=$topic; // добавил 2013-03-01

//getPage setings
$limit = $modx->getOption('limit', $scriptProperties, 10);
$offset = $modx->getOption('offset', $scriptProperties, 0);
$totalVar = $modx->getOption('totalVar', $scriptProperties, 'total');

if (in_array(strtolower($sortby),array('random','rand()','rand'))) {
    $sortby = 'RAND()';
    $sortdir = '';
}

if($scripts)
{
    $modx->regClientStartupHTMLBlock('<link rel="stylesheet" type="text/css" href="assets/components/vidlister/js/web/prettyphoto/css/prettyPhoto.css" />');
    $modx->regClientStartupScript('assets/components/vidlister/js/web/prettyphoto/js/jquery.prettyPhoto.js');
    $modx->regClientStartupHTMLBlock('<script type="text/javascript">
        $(document).ready(function(){
            $("a[rel^=\'prettyPhoto\']").prettyPhoto({
autoplay: true,social_tools: \'\'
									});
        });
      </script>');
}

$output = '';

$c = $modx->newQuery('vlVideo');

//criteria 
if (!empty($where)) {
    $c->where($where);
}
$c->andCondition(array('active' => 1));

//set placeholder for getPage
$modx->setPlaceholder($totalVar, $modx->getCount('vlVideo', $c));

$c->sortby($sortby, $sortdir);
$c->limit($limit, $offset);

$idx = 0; //index
$videos = $modx->getCollection('vlVideo', $c);
foreach($videos as $video)
{
//$video2 = $video->toArray();
    $duration = $video->duration();

    $video = $video->toArray();
	// print_r($video);die;
    $source = $video['source'];
    $videoId = $video['videoId'];
    $video['duration'] = $duration;
	// проверить на наличие файла, если его нет, то вставлять картинку напрямую с youtube
	// http://img.youtube.com/vi/bQVoAWSP7k4/0.jpg
	$filename=$modx->getOption('assets_url').'components/vidlister/images/'.$video['id'].'.jpg';
	if (!file_exists($filename)) {
		$filename="http://img.youtube.com/vi/$videoId/0.jpg";
	}
    $video['image'] = $filename; 
    $video['idx'] = $idx; //index

    if(isset($tpls[$source]))
    {
        $output .= $modx->getChunk($tpls[$source], $video);
    }
    else
    {
        $output .= $modx->getChunk($tpl, $video);
    }
    $idx++;
}

return $output;