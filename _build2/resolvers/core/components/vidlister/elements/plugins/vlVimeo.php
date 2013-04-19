<?php
if($modx->event->name == 'OnVidListerImport')
{
    $consumer_key = $modx->getOption('vidlister.vlVimeo.consumer_key', $scriptProperties, '');
    $consumer_secret = $modx->getOption('vidlister.vlVimeo.consumer_secret', $scriptProperties, '');
    $user = $modx->getOption('vidlister.vlVimeo.user', $scriptProperties, ''); //get user name(s)
    $active = $modx->getOption('vidlister.vlVimeo.active', $scriptProperties, false); //make imported videos inactive by default
    $source = 'vimeo';

    if(!empty($consumer_key) && !empty($consumer_secret) && !empty($user))
    {
        $modx->getService('vidlister','VidLister',$modx->getOption('vidlister.core_path',null,$modx->getOption('core_path').'components/vidlister/').'model/vidlister/',$scriptProperties);
        $modx->lexicon->load('vidlister:default');
        $users = explode(',', $user); //user names are comma separated

        require_once($modx->getOption('core_path').'components/vidlister/model/vimeo/vimeo.class.php');
        $vimeo = new phpVimeo($consumer_key,$consumer_secret);

        foreach($users as $user)
        {
            $modx->log(modx::LOG_LEVEL_WARN, $modx->lexicon('vidlister.import.started', array('source' => $source, 'user' => $user)));

            $videos = $vimeo->call('vimeo.videos.getUploaded', array('user_id' => $user));
            $pages = ceil( (int)$videos->videos->total / (int)$videos->videos->perpage);

            //new/total counter for current user
            $newVids = 0;
            $totalVids = 0;

            for($i = 1; $i <= $pages; $i++)
            {
                $videos = $vimeo->call('vimeo.videos.getUploaded', array('user_id' => $user, 'page' => $i, 'full_response' => true));
                foreach($videos->videos->video as $video)
                {
                    $vid = $modx->getObject('vlVideo', array('source' => $source, 'videoId' => $video->id));
                    if(!is_object($vid))
                    {
                        //not found, so create new video and set all fields
                        $vid = $modx->newObject('vlVideo');

                        $tags = array();
                        if(isset($video->tags)) {
                            foreach($video->tags->tag as $tag) {
                                $tags[] = $tag->_content;
                            }
                        }

                        $vid->fromArray(array(
                            'active' => (int)$active,
                            'created' => strtotime($video->upload_date),
                            'updated' => strtotime($video->modified_date),
                            'source' => $source,
                            'videoId' =>  $video->id,
                            'name' => $video->title,
                            'description' => $video->description,
                            'author' => $user,
                            'keywords' => implode(',', $tags),
                            'duration' => $video->duration,
                            'jsondata' => array()
                        ));
                        $newVids++;
                    }
                    else
                    {
                        //existing video, so don't overwrite name/description/keywords
                        $vid->fromArray(array(
                            'updated' => strtotime($video->modified_date),
                            'author' => $user,
                            'duration' => $video->duration,
                            'jsondata' => array_merge(
                                $vid->get('jsondata'),
                                array()
                            )
                        ));
                    }
                    $vid->save();

                    //get image
                    file_put_contents(
                        $modx->getOption('assets_path').'components/vidlister/images/'.$vid->get('id').'.jpg',
                        file_get_contents(end($video->thumbnails->thumbnail)->_content)
                    );

                    $ids[] = $vid->get('id'); //add to found/created ID's array
                    $totalVids++;
                }
            }

            $modx->log(modx::LOG_LEVEL_INFO, $modx->lexicon('vidlister.import.complete', array('user' => $user, 'source' => $source, 'total' => $totalVids, 'new' => $newVids)));

            //remove all videos not found in XML
            $delVideos = $modx->getCollection('vlVideo', array('source' => $source, 'author' => $user, 'id NOT IN('.implode(',', $ids).')'));
            foreach($delVideos as $delVideo) {
                $delVideo->remove();
            }
        }

    }
}
return;