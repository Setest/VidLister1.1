<?php
/**
 * Get a list of sources
 *
 * @package vidlister
 * @subpackage processors
 */
/* setup default properties */
// $isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
// $isLimit = $modx->getOption('limit',$scriptProperties,100);
$limit = $modx->getOption('limit',$scriptProperties,100);
$sort = $modx->getOption('sort',$scriptProperties,'menuindex');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');


$criteria = $modx->newQuery('modResource');
$criteria->setClassAlias('res');
$criteria->select(array(
		 'id'
		,'pagetitle as topic'
));
$criteria->where(array(
    // 'res.template:IN' => explode(",", $needtemplate) // вытаскиваем документы с шаблоном НОВОСТЬ
	 'res.deleted' => false
    ,'res.hidemenu' => false
    ,'res.published' => true
    // ,'res.parent' => 9
));

$parent=$modx->getOption('vidlister.video.topic.parent');
if (isset($parent)){
	// get parents of topics
	$criteria->andCondition(array(
		'res.parent' => $parent
	));
}


////////////////
// если в системный переменных выбран тип вывода get_tree_setest
if ($modx->getOption('vidlister.video.topic.getdatatype')==2){
	$arr = $modx->runSnippet('get_tree_setest', array('empty_value' => false, 'parent' => $parent, 'context'=>'web', 'additional'=>'', 'template'=>'', 'separator'=>"-", 'depth'=>11, 'debug'=>0, 'style'=>0, 'return_type'=>'json'));
	$itog_array=json_decode($arr,true); // true - возвращает массив, false - объект
	foreach($itog_array as $data){
		$itog_array2[]=array('id'=>$data['value'], 'topic'=>$data['text']);
	}
	return $this->outputArray($itog_array2,count($itog_array));
}

////////////////// 
//{"total":"16","results":[{"text":"\u043d\u0438\u0447\u0435\u0433\u043e \u043d\u0435 \u0432\u044b\u0431\u0440\u0430\u043d\u043e","value":""},{"text":" ie6nomore","value":5,"":null},{"text":" sitemap","value":7,"":null},{"text":" \u0420\u0430\u0437\u0434\u0435\u043b\u044b","value":9,"":null},{"text":"- \u0412\u0441\u0435","value":12,"":null},{"text":"- \u0421\u043f\u043e\u0440\u0442\u0438\u0432\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e","value":10,"":null},{"text":"- \u0420\u0435\u043a\u043b\u0430\u043c\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e","value":17,"":null},{"text":"- \u041c\u0443\u0437\u044b\u043a\u0430\u043b\u044c\u043d\u044b\u0435 \u043a\u043b\u0438\u043f\u044b","value":11,"":null},{"text":"- \u0421\u0442\u0443\u0434\u0438\u0439\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e","value":13,"":null},{"text":"- \u0421\u044e\u0436\u0435\u0442\u044b","value":14,"":null},{"text":"- \u041a\u043e\u0440\u043e\u0442\u043a\u043e\u043c\u0435\u0442\u0440\u0430\u0436\u043d\u044b\u0435 \u0444\u0438\u043b\u044c\u043c\u044b","value":15,"":null},{"text":"- \u0421\u0432\u0430\u0434\u0435\u0431\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e","value":16,"":null},{"text":"- \u0422\u0435\u043b\u0435\u0432\u0438\u0437\u0438\u043e\u043d\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e","value":20,"":null},{"text":"- \u041f\u0440\u043e\u0434\u0430\u043a\u0448\u043d \u0432\u0438\u0434\u0435\u043e","value":18,"":null},{"text":"- \u0420\u043e\u043b\u0438\u043a\u0438 \u043e \u041a\u0430\u0437\u0430\u043d\u0438","value":21,"":null},{"text":"- \u0420\u0430\u0437\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e","value":19,"":null}]}
//{"total":"11","results":[{"id":"10","topic":"\u0421\u043f\u043e\u0440\u0442\u0438\u0432\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e"},{"id":"17","topic":"\u0420\u0435\u043a\u043b\u0430\u043c\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e"},{"id":"11","topic":"\u041c\u0443\u0437\u044b\u043a\u0430\u043b\u044c\u043d\u044b\u0435 \u043a\u043b\u0438\u043f\u044b"},{"id":"13","topic":"\u0421\u0442\u0443\u0434\u0438\u0439\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e"},{"id":"14","topic":"\u0421\u044e\u0436\u0435\u0442\u044b"},{"id":"15","topic":"\u041a\u043e\u0440\u043e\u0442\u043a\u043e\u043c\u0435\u0442\u0440\u0430\u0436\u043d\u044b\u0435 \u0444\u0438\u043b\u044c\u043c\u044b"},{"id":"16","topic":"\u0421\u0432\u0430\u0434\u0435\u0431\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e"},{"id":"20","topic":"\u0422\u0435\u043b\u0435\u0432\u0438\u0437\u0438\u043e\u043d\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e"},{"id":"18","topic":"\u041f\u0440\u043e\u0434\u0430\u043a\u0448\u043d \u0432\u0438\u0434\u0435\u043e"},{"id":"21","topic":"\u0420\u043e\u043b\u0438\u043a\u0438 \u043e \u041a\u0430\u0437\u0430\u043d\u0438"},{"id":"19","topic":"\u0420\u0430\u0437\u043d\u043e\u0435 \u0432\u0438\u0434\u0435\u043e"}]}

$count = $modx->getCount('modResource',$criteria); 
$criteria->sortby($sort,$dir);
if ($limit) $criteria->limit($limit); 

if ($stmt = $criteria->prepare() and $stmt->execute() and $result = $stmt->fetchAll(PDO::FETCH_ASSOC))
{
	foreach ($result as $n => $val){
		$list[]= $val;
	}
}

// $modx->log(MODX_LOG_LEVEL_ERROR, var_export($list, true));
return $this->outputArray($list,$count);