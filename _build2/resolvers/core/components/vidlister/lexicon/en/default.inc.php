<?php
/**
 * Default English Lexicon Entries for VidLister
 *
 * @package vidlister
 * @subpackage lexicon
 */
$_lang['vidlister'] = 'VidLister';
$_lang['vidlister.desc'] = 'Show your youtube videos on your site';

$_lang['vidlister.import'] = 'Import videos';
$_lang['vidlister.import.started'] = 'Started [[+source]] import for [[+user]]';
$_lang['vidlister.import.complete'] = 'Imported [[+total]] [[+source]] videos ([[+new]] new) for user [[+user]].';
$_lang['vidlister.import.err'] = 'Import failed.';
$_lang['vidlister.import.err.client'] = 'REST client unavailable.';
$_lang['vidlister.import.current'] = 'Imported page #[[+page]] from [[+pages]], count records: ';

$_lang['vidlister.video'] = 'Video';
$_lang['vidlister.videos'] = 'Videos';
$_lang['vidlister.video.new'] = 'New video';
$_lang['vidlister.video.update'] = 'Update video';
$_lang['vidlister.video.remove'] = 'Remove video';
$_lang['vidlister.video.remove.confirm'] = 'Are you sure you want to remove this video?';
$_lang['vidlister.video.error.nf'] = 'Video not found';
$_lang['vidlister.video.active'] = 'Active';
$_lang['vidlister.video.id'] = 'Video ID';
$_lang['vidlister.video.name'] = 'Name';
$_lang['vidlister.video.description'] = 'Description';
$_lang['vidlister.video.keywords'] = 'Keywords';
$_lang['vidlister.video.source'] = 'Source';
$_lang['vidlister.video.author'] = 'Author';
$_lang['vidlister.video.duration'] = 'Duration';
$_lang['vidlister.video.duration.seconds'] = 'Seconds';
$_lang['vidlister.video.advanced'] = 'Advanced';
$_lang['vidlister.video.jsondata'] = 'JSON data';
$_lang['vidlister.video.created'] = 'Created date';
$_lang['vidlister.video.topic'] = 'Topic';

/* Settings */
$_lang['setting_vidlister.video.topic.getdatatype'] = 'Type of get data';
$_lang['setting_vidlister.video.topic.getdatatype_desc'] = 'Set the appropriate numeric value:
<i>
	<ul>
		<li>1-getResources</li>
		<li>2-get_tree_setest</li> 
	</ul>
<i>';
$_lang['setting_vidlister.video.topic.parent'] = 'Topic';
$_lang['setting_vidlister.video.topic.parent_desc'] = 'Set Id parent of topics.';

/* YouTube */
$_lang['setting_vidlister.vlYoutube.user'] = 'Login';
$_lang['setting_vidlister.vlYoutube.user_desc'] = 'Username (channel)';
$_lang['setting_vidlister.vlYoutube.active'] = 'Activate';
$_lang['setting_vidlister.vlYoutube.active_desc'] = 'Activate a source';

/* Vimeo */
$_lang['setting_vidlister.vlVimeo.active'] = 'Activate';
$_lang['setting_vidlister.vlVimeo.active_desc'] = 'Activate a source';
$_lang['setting_vidlister.vlVimeo.consumer_key'] = 'Сonsumer key';
$_lang['setting_vidlister.vlVimeo.consumer_key_desc'] = 'Vimeo api key';
$_lang['setting_vidlister.vlVimeo.consumer_secret'] = 'Сonsumer secret';
$_lang['setting_vidlister.vlVimeo.consumer_secret_desc'] = 'Vimeo api secret';
$_lang['setting_vidlister.vlVimeo.user'] = 'Login';
$_lang['setting_vidlister.vlVimeo.user_desc'] = 'Username (channel)';



/* Properties */
$_lang['vidlister.properties.active'] = 'Activate imported videos';
$_lang['vidlister.properties.youtubeuser'] = 'Youtube username';
$_lang['vidlister.properties.vimeouser'] = 'Vimeo username';
$_lang['vidlister.properties.vimeokey'] = 'Vimeo api key (consumer key)';
$_lang['vidlister.properties.vimeosecret'] = 'Vimeo api secret (comsumer secret)';
$_lang['vidlister.properties.tpl'] = 'Video item chunk (template)';
$_lang['vidlister.properties.where'] = 'A JSON-style expression of criteria';
$_lang['vidlister.properties.limit'] = 'Max items per page';
$_lang['vidlister.properties.offset'] = 'First item (0 = first)';
$_lang['vidlister.properties.totalvar'] = 'TotalVar name for getPage';
$_lang['vidlister.properties.scripts'] = 'Add js/css (PrettyPhoto) scripts to head';