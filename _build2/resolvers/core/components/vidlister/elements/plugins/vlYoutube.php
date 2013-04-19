<?php

/**
 * plugin vlYoutube <VidLister> 
 
 * EditedBy: Stepan Prishepenko (Setest) <itman116@gmail.com>
 
 * Version: 1.0.0 (2013-04-19) public initial
 *
**/

$log=0;

ini_set("max_execution_time", "600"); // включаем 10 минут на ограничение работы скрипта

////////////////////////////////--=LOG=--///////////////////////////////////
// при установлении лога в консоль при импорте ничего выводится не будет, все попадет в файл
$pluginName = &$modx->event->activePlugin;
if ($log==1){
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
    $date = date('Y-m-d____H-i-s');  // использовать в выводе даты : - нельзя, иначе не создается лог в файл
    $modx->setLogTarget(array(
       'target' => 'FILE',
       'options' => array('filename' => "{$pluginName}_$date.log")
//       'options' => array('filename' => "global_$date.log")
    ));
$start_time_global = microtime(true); //общее время выполнения скрипта
}
////////////////////////////////--=LOG=--///////////////////////////////////
if($modx->event->name == 'OnVidListerImport')
{
    $user = $modx->getOption('vidlister.vlYoutube.user', $scriptProperties, ''); //get user name(s)
    $active = $modx->getOption('vidlister.vlYoutube.active', $scriptProperties, false); //make imported videos inactive by default
    // $user = $modx->getOption('user', $scriptProperties, ''); //get user name(s)
    // $active = $modx->getOption('active', $scriptProperties, false); //make imported videos inactive by default
	$count_pages=25;
    if(!empty($user))
    {
        $modx->getService('vidlister','VidLister',$modx->getOption('vidlister.core_path',null,$modx->getOption('core_path').'components/vidlister/').'model/vidlister/',$scriptProperties);
        $modx->lexicon->load('vidlister:default');

        if (empty($modx->rest))
        {
            $modx->getService('rest','rest.modRestClient');
            $loaded = $modx->rest->getConnection();
            if (!$loaded) return $modx->lexicon('vidlister.import.err.client');
        }

        $users = explode(',', $user); //user names are comma separated
		if ($log==1) $modx->log(modX::LOG_LEVEL_INFO, "USERS: {$users}");
        foreach($users as $user)
        {
            $modx->log(MODx::LOG_LEVEL_WARN, $modx->lexicon('vidlister.import.started', array('source' => 'Youtube', 'user' => $user)));

            @$response = $modx->rest->request('http://gdata.youtube.com','/feeds/api/users/'.$user.'/uploads','GET', array('max-results' => 1), array())->response;
            //@ to prevent PHP notice about $xml being empty (???)
            if(empty($response))
            {
                $modx->log(MODX_LOG_LEVEL_ERROR,  $modx->lexicon('vidlister.import.err'));
                continue; //response was empty, so go to next user
            }

            //create SimpleXmlElement
            $xmlvideos = simplexml_load_string($response);
            $openSearch = $xmlvideos->children('http://a9.com/-/spec/opensearchrss/1.0/');

            //calculate total number of "pages" (each feed only lists $count_pages videos)
            $totalVids = $openSearch->totalResults;
            $pages = ceil($totalVids/$count_pages);

            //every movie not in this array will be deleted after import (no longer on Youtube)
            $ids = array();

            //new/total counter for current user
            $newVids = 0;

            //start at first video
            $startIndex = 1;

            //get videos of each page
            for($page=1; $page <= $pages; $page++)
            {
                @$response = $modx->rest->request('http://gdata.youtube.com','/feeds/api/users/'.$user.'/uploads','GET', array('max-results' => $count_pages,'start-index' => $startIndex), array())->response;
                $xmlvideos = simplexml_load_string($response);
				// rsort($xmlvideos->entry);
				// $xmlvideos->entry->ksort();
				// $xmlarr=$xmlvideos->entry->toArray();
  // $obj = new stdClass;
    // $obj->foo = new stdClass;
    // $obj->foo->baz = 'baz';
    // $obj->bar = 'bar';
	//// $array = (array) $xmlvideos;			
				
    // function objectToArray( $object )
    // {
        // if( !is_object( $object ) && !is_array( $object ) )
        // {
            // return $object;
        // }
        // if( is_object( $object ) )
        // {
            // $object = get_object_vars( $object );
        // }
        // return array_map( 'objectToArray', $object );
    // }

    /*** преобразование объекта в массив ***/
    // $array = objectToArray( $xmlvideos );				
	// krsort($array['entry']);	
				// $modx->log(MODx::LOG_LEVEL_WARN, "Импортируется $page страница из $pages, содержит записей:".count($xmlvideos->entry));
				// $modx->lexicon('vidlister.import.current',array('page'=>$page,'pages'=>$pages)).count($xmlvideos->entry)
				$modx->log(MODx::LOG_LEVEL_WARN, $modx->lexicon('vidlister.import.current',array('page'=>$page,'pages'=>$pages)).count($xmlvideos->entry));
// return "тут";				
				if ($log==1) $modx->log(modX::LOG_LEVEL_INFO, "xmlvideos:".print_r($xmlarr,true));
				// сортируем содержимое объекта так чтобы новые записи были в самом конце
                //loop through video entries
                foreach($xmlvideos->entry as $xmlvideo)
                {
                    //next 2 lines allow to get namespace data in media: and yt: namespace
                    $media = $xmlvideo->children('http://search.yahoo.com/mrss/');
                    $yt = $media->children('http://gdata.youtube.com/schemas/2007');

					//if ($log==1) $modx->log(modX::LOG_LEVEL_INFO, "MEDIA:".print_r($media,true));

                    //get existing video
					$flashUrl='';
					$gppUrl='';
                    $video = $modx->getObject('vlVideo', array('source' => 'youtube', 'videoId' => str_replace('http://gdata.youtube.com/feeds/api/videos/', '', $xmlvideo->id)));
                    if(!is_object($video))
                    {
                        //not found, so create new video and set all fields
						if ($log==1) $modx->log(modX::LOG_LEVEL_INFO, "MEDIA:".$media->group->keywords);
						
						if (is_object($media->group->content[0])) $flashUrl=(string)$media->group->content[0]->attributes()->url;
						if (is_object($media->group->content[1])) $gppUrl=(string)$media->group->content[1]->attributes()->url;
                        
                        $video = $modx->newObject('vlVideo');
                        $video->fromArray(array(
                            'active' => (int)$active,
                            'created' => strtotime($xmlvideo->published),
                            'updated' => strtotime($xmlvideo->updated),
                            'source' => 'youtube',
                            'videoId' =>  str_replace('http://gdata.youtube.com/feeds/api/videos/', '', $xmlvideo->id),
                            'name' => $xmlvideo->title,
                            'description' => $xmlvideo->content,
                            'author' => $xmlvideo->author->name,
                            'keywords' => $media->group->keywords,
                            'duration' => $yt->duration->attributes()->seconds,
                            'jsondata' => array(
                                //'flashUrl' => (string)$media->group->content[0]->attributes()->url,
                                //'3gppUrl' => (string)$media->group->content[1]->attributes()->url
								'flashUrl' => $flashUrl,
								'3gppUrl' => $gppUrl								
                            )
                        ));
                        $newVids++;
                    }
                    else
                    {
                        //existing video, so don't overwrite name/description/keywords
						if (is_object($media->group->content[0])) $flashUrl=(string)$media->group->content[0]->attributes()->url;
						if (is_object($media->group->content[1])) $gppUrl=(string)$media->group->content[1]->attributes()->url;
                        $video->fromArray(array(
                            'updated' => strtotime($xmlvideo->updated),
							'created' => strtotime($xmlvideo->published),
                            'author' => $xmlvideo->author->name,
							'name' => $xmlvideo->title,
                            'description' => $xmlvideo->content,
                            'keywords' => $media->group->keywords,
                            'duration' => $yt->duration->attributes()->seconds,
                            'jsondata' => array_merge(
                                $video->get('jsondata'),
                                array(
                                    'flashUrl' => $flashUrl,
                                    '3gppUrl' => $gppUrl
                                )
                            )
                        ));
                    }
                    $video->save();

                    //get image
                    file_put_contents(
                        $modx->getOption('assets_path').'components/vidlister/images/'.$video->get('id').'.jpg',
                        file_get_contents($media->group->thumbnail[0]->attributes()->url)
                    );

                    $ids[] = $video->get('id'); //add to found/created ID's array
                    $startIndex++;
                }
            }

            $modx->log(modx::LOG_LEVEL_INFO, $modx->lexicon('vidlister.import.complete', array('user' => $user, 'source' => 'Youtube', 'total' => $totalVids, 'new' => $newVids)));

            //remove all videos not found in XML
            $delVideos = $modx->getCollection('vlVideo', array('source' => 'youtube', 'author' => $user, 'id NOT IN('.implode(',', $ids).')'));
            foreach($delVideos as $delVideo)
            {
                $delVideo->remove();
            }
        }
    }
}
return;