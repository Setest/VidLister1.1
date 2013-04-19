<?php
/**
 * Default English Lexicon Entries for VidLister
 *
 * @package vidlister
 * @subpackage lexicon
 */
$_lang['vidlister'] = 'VidLister';
$_lang['vidlister.desc'] = 'Показывает YouTube видео на Вашем сайте';

$_lang['vidlister.import'] = 'Импорт видео';
$_lang['vidlister.import.started'] = 'Импорт с [[+source]] для [[+user]]';
$_lang['vidlister.import.complete'] = 'Всего импортированно [[+total]] [[+source]] видео ([[+new]] новых) для пользователя [[+user]].';
$_lang['vidlister.import.err'] = 'Импорт не удался.';
$_lang['vidlister.import.err.client'] = 'REST client недоступен.';
$_lang['vidlister.import.current'] = 'Импортируется [[+page]] страница из [[+pages]], содержит записей: ';

$_lang['vidlister.video'] = 'Видео';
$_lang['vidlister.videos'] = 'Видео';
$_lang['vidlister.video.new'] = 'Новое видео';
$_lang['vidlister.video.update'] = 'Обновить видео';
$_lang['vidlister.video.remove'] = 'Удалить видео';
$_lang['vidlister.video.remove.confirm'] = 'Вы уверенны что хотите удалить это видео?';
$_lang['vidlister.video.error.nf'] = 'Видео не найденно';
$_lang['vidlister.video.active'] = 'Активно';
$_lang['vidlister.video.id'] = 'Video ID';
$_lang['vidlister.video.name'] = 'Имя';
$_lang['vidlister.video.description'] = 'Описание';
$_lang['vidlister.video.keywords'] = 'Ключевые слова';
$_lang['vidlister.video.source'] = 'Источник';
$_lang['vidlister.video.author'] = 'Автор';
$_lang['vidlister.video.duration'] = 'Продолжительность';
$_lang['vidlister.video.duration.seconds'] = 'Секунд';
$_lang['vidlister.video.advanced'] = 'Продвинутые';
$_lang['vidlister.video.jsondata'] = 'JSON data';
$_lang['vidlister.video.created'] = 'Дата создания';
$_lang['vidlister.video.topic'] = 'Рубрика';

/* Settings */
$_lang['setting_vidlister.video.topic.getdatatype'] = 'Способ выборки данных';
$_lang['setting_vidlister.video.topic.getdatatype_desc'] = 'Впишите соответствующее цифровое значение:
<i>
	<ul>
		<li>1-getResources</li>
		<li>2-get_tree_setest</li>
	</ul>
<i>';
$_lang['setting_vidlister.video.topic.parent'] = 'Рубрика';
$_lang['setting_vidlister.video.topic.parent_desc'] = 'Укажите Id элемента являющийся родителем всех рубрик.';

/* YouTube */
$_lang['setting_vidlister.vlYoutube.user'] = 'Login';
$_lang['setting_vidlister.vlYoutube.user_desc'] = 'Имя пользователя (канала)';
$_lang['setting_vidlister.vlYoutube.active'] = 'Активировать';
$_lang['setting_vidlister.vlYoutube.active_desc'] = 'Активировать источник';

/* Vimeo */
$_lang['setting_vidlister.vlVimeo.active'] = 'Активировать';
$_lang['setting_vidlister.vlVimeo.active_desc'] = 'Активировать источник';
$_lang['setting_vidlister.vlVimeo.consumer_key'] = 'Сonsumer key';
$_lang['setting_vidlister.vlVimeo.consumer_key_desc'] = 'Vimeo api ключ';
$_lang['setting_vidlister.vlVimeo.consumer_secret'] = 'Сonsumer secret';
$_lang['setting_vidlister.vlVimeo.consumer_secret_desc'] = 'Vimeo api secret';
$_lang['setting_vidlister.vlVimeo.user'] = 'Login';
$_lang['setting_vidlister.vlVimeo.user_desc'] = 'Имя пользователя (канала)';



/* Properties */
$_lang['vidlister.properties.active'] = 'Активировать источник';
$_lang['vidlister.properties.youtubeuser'] = 'Имя пользователя на Youtube';
$_lang['vidlister.properties.vimeouser'] = 'Имя пользователя на Vimeo';
$_lang['vidlister.properties.vimeokey'] = 'Vimeo api key (consumer key)';
$_lang['vidlister.properties.vimeosecret'] = 'Vimeo api secret (comsumer secret)';
$_lang['vidlister.properties.tpl'] = 'Video item chunk (template)';
$_lang['vidlister.properties.where'] = 'A JSON-style expression of criteria';
$_lang['vidlister.properties.limit'] = 'Максимальное кол-во элементов на странице';
$_lang['vidlister.properties.offset'] = 'Первый элемент (0 = first)';
$_lang['vidlister.properties.totalvar'] = 'TotalVar имя для getPage';
$_lang['vidlister.properties.scripts'] = 'Добавить js/css (PrettyPhoto) скрипты для Lighbox';
