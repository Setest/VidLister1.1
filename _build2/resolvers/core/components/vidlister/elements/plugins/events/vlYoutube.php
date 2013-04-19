<?php
$events = array();

// старый вариант добавления событий
// $events['OnVidListerImport'] = $modx->newObject('modPluginEvent');
// $events['OnVidListerImport']->fromArray(array(
    // 'event' => 'OnVidListerImport',
    // 'priority' => 0,
    // 'propertyset' => 0
// ),'',true,true);

// return $events;

$events['OnVidListerImport'] = array('priority' => 0,'propertyset' => 0);
// что бы добавить еще событие расскоментируйте следующую строку
// $events['OnVidListerImport2'] = array('priority' => 0,'propertyset' => 0);

return $events;