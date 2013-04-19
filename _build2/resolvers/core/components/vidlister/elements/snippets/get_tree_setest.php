<?php
/*///////////////////////////////////////////////////

	Snippet <get_tree_setest>

	Ver. 1.23

	Copyright 2011-2012 by Stepan Prishepenko <itman116@gmail.com>

	Построения дерева меню по аналогии с listbox-single в TV параметрах,
	в котором выводится иерархически выделенное дерево ресурсов.
	Обладает возможностью выводить параметры как в обычном виде с разделителем,
	так и в виде JSON при указании соответствующего параметра. Что в дальнейшем может
	использоваться вместе с динамически изменяемым ТВ параметром <listbox-dynamic>.
	Который, в свою очередь, позволяет менять значения посредством Ajax ( используя ExtJs ),
	подчиненных параметров, в зависимости от выбранного значения родителя.
	Все параметры запуска Вы можете изучить в коде с подробными комментариями ниже.

	примеры запуска:

	из TV:
	@EVAL return $modx->runSnippet('get_tree_setest', array('parent' => 10, 'context'=>'web', 'depth'=>10, 'debug'=>0, 'style'=>0));

	из шаблона:
	[[!get_tree_setest?
	&parent=`10`
	&context=web
	&return_type=`select`
	&depth=`10`
	&debug=`1`
	&style=`1`
	]]

    примечание: параметр parent задается только типом INT, нельзя задавать список, к примеру 1,2,7

	(1.23) 2013-04-19 fix error then template is empty
	(1.22) 2013-04-16 add empty_value parameter
	(1.21) 2012-11-26 избавился от ошибки "error parse condition" используя conf['condition']
					  право же это не совсем красивое решение, но зато нет ошибки

    (1.20) 2012-09-26 внес не большие изменения, увеличил кол-во доп. полей.
	
	(1.19) 2012-07-30 добавил критерий поиска addquery по аналогии с TV параметром.
	
	(1.18) 2012-07-03 добавил параметр return_type () который возьмет на себя итоговый вывод данных, вместо использования return_tree и return_json.
					  Принимает значения: list, json, html, object, select.
					  Также добавил вывод данных в виде списка SELECT, при установленном параметре: return_type=`select`
    (1.17) 2012-04-12 исправил отображение данных при получение пустого параметра template, добавил возможность вывода в виде дерева в html
                      и дерева в виде объекта для плагина jquery_dynatree. Параметр &return_tree=`` может быть == "HTML","OBJECT",""

    (1.16) 2012-02-17 добавил возможность вытакивать дополнительные параметры, через additional, см.ниже
                      Добавил задавать параметр template также в виде "1,2,3" и соответственно возвращать данные скопом.

    (1.15) 2012-02-15 заменил empty на is_null перед построением критерия запроса,
              		  а то получалось, что если к примеру isfolder=0 то в критерий этот запрос не учитывался.
               		  Внедрил параметр $separator - он содержит символ разделителя. По умолчнию "---".

    (1.15) 2012-02-13 внесение исправлений:
            		  при вызове с параметром debug разделители устанавливаются не правильно
					  если вызыватьс параметром template то 2-й уровень вложенности также не выводится.

///////////////////////////////////////////////////*/

$start_time = microtime(true);

//$modx->initialize('web'); // не работает для скриптов getchildrenids

// список параметров - НАЧАЛО
//$conf['parent'] = isset($conf['parent']) ? (int)$conf['parent'] : 0;          // id родителя по которому строится меню
$conf['empty_value'] = isset($empty_value) ? $empty_value : true;   			// if true return "no selection" as first value
$conf['parent'] = isset($parent) ? (int)$parent : 0;                            // id родителя по которому строится меню
$conf['context'] = isset($context) ? $context : "web";                          // текущий контекст
$conf['depth'] = isset($depth) ? (int)$depth : 10;                              // глубина дерева
$conf['debug'] = isset($debug) ? (int)$debug : 0;                               // отладочные моменты
$conf['style'] = isset($style) ? (int)$style : 0;                               // стиль отображения, 1 - включен, НО при выборе из списка параметр и при включенном стиле мы видим лишнюю инфу типа <div ...>пункт меню....
$conf['separator'] = isset($separator) ? $separator : "---";                    // разделитель, по умолчанию = "---"
$conf['json'] = array();                                                        // это массив содержащий тот же вывод только в виде массива
$conf['return_json'] = isset($return_json) ? (int)$return_json : 0;             // если это значение == 1, то ответ приходит в виде Json, почему то в виде массива вернуть нельзя, приходит только строчка Array
$conf['return_tree'] = isset($return_tree) ? $return_tree : "";                 // возвращает данные в виде дерева (<ul><li>...) или в виде объекта ([{title:.....}]) для скрипта jquery_dynatree
$conf['return_type'] = isset($return_type) ? $return_type : "list";             // обобщенная ф-я объединившая return_json и return_tree, добавился тип select

// нижние параметры срабатывают только если они указаны, дополнительная проверка идет ниже
if (isset($isfolder)) $conf['condition']['isfolder'] = (int)$isfolder;                       // если указан то показываем только папки или детей, иначе все.
if (isset($published)) $conf['condition']['published'] = (int)$published;                    // опубликовано
if (isset($hidemenu)) $conf['condition']['hidemenu'] = (int)$hidemenu;                       // удаленные
if (isset($deleted)) $conf['condition']['deleted'] = (int)$deleted;                          // только скрытые, видимые или все если не указан.
if (isset($template) and !empty($template)) {                            
	// Id шаблона к которому привязан ресурс. Может буть представлен в виде "1,2,3"
	$conf['condition']['template:IN']=explode(",", $template);
}


if (isset($auto_parent)) $conf['auto_parent'] = (int)$auto_parent;             // при auto_parents==1 выясняем id текущего редактиремого ресурса и присваиваем его parent-у
if (isset($additional)) $conf['additional'] = $additional;                      // дополнительные праметры на выходе. Работает только в JSON. Параметры могут состоять только из полей таблицы
                                                                                // пример вызова " additional='template,published' ",
                                                                                // на выходе массив будет состоять из 4 полей ['value','text','template','published']
// список параметров - КОНЕЦ

//if (isset($tv_dependence)) $conf['tv_dependence'] = (int)$tv_dependence;      // TV значение поля значение которого должно меняться
//$conf['tv_dependence'] = 8;
//$conf['debug']=0;
//$conf['style']=0;
//$tv="type_of_content=1";


if ($conf['auto_parent']==1) {
   $cur_resources = $modx->resource->get('id'); // Id текущего ресурса (открытой страницы в админке)
   $conf['parent']=$cur_resources;              // установливаем родителя автоматически
}


if (isset($tv)) $conf['tv']['string'] = $tv;                  // можно задать 1-н TV параметр, например $tv='name=value' как критерий поиска
                                                              // возможно использовать операторы: <=>,=,!=,<>,LIKE,NOT LIKE,<,<=,=<,>,>=,=>
if (!empty($conf['tv']['string'])) {
// разбиваем параметр на название, условие и значение
  $operators = array( '<=>', '!=', '<>', 'LIKE', 'NOT LIKE', '<=', '=<', '>=', '=>','=', '<', '>');
    // Находим оператор, который включён
    foreach ($operators as $o) {
      if ( strpos($conf['tv']['string'], $o) !== false) {
        list($name, $value) = explode($o, $conf['tv']['string']);
        $conf['tv']=array('name'=>$name,'value'=>$value,'operator'=>$o);
        break;
      }
    }
  // строим запрос
  if (isset($conf['tv']['name'])) {$conf['tv']['value']=" ".$conf['tv']['operator']."'".$conf['tv']['value']."'";}
}


if (isset($addquery)) $conf['addquery']['string'] = $addquery;	// можно задать 1-н параметр, например $addquery='pagetitle LIKE value' как критерий поиска
																// возможно использовать операторы: <=>,=,!=,<>,LIKE,NOT LIKE,<,<=,=<,>,>=,=>
if (!empty($conf['addquery']['string'])) {
// разбиваем параметр на название, условие и значение
  $operators = array( '<=>', '!=', '<>', 'LIKE', 'NOT LIKE', '<=', '=<', '>=', '=>','=', '<', '>');
    // Находим оператор, который включён
    foreach ($operators as $o) {
      if ( strpos($conf['addquery']['string'], $o) !== false) {
        list($name, $value) = explode($o, $conf['addquery']['string']);
        $conf['addquery']=array('name'=>$name,'value'=>$value,'operator'=>$o);
        break;
      }
    }
  // строим запрос
  // if (isset($conf['addquery']['name'])) {$conf['addquery']['value']=" ".$conf['addquery']['operator']."'".$conf['addquery']['value']."'";}
}
// print_r($conf['addquery']);

// настраиваем уровень отладки
if ($conf['debug']==1) {
  // помощь по отладке
  // http://forums.modx.com/index.php?topic=48597.0
  // http://rtfm.modx.com/display/revolution20/MODx.Console
  // http://rtfm.modx.com/display/xPDO20/xPDO.setLogTarget
  // http://rtfm.modx.com/display/xPDO20/xPDO.setLogLevel

    // Logging: для того чтобы он работал соблюдаем последовательность команд
    $date = date('Y-m-d_H-i');  // использовать в выводе даты : - нельзя, иначе не создается лог в файл

    //$modx->setDebug(E_WARNING);  http://webscript.ru/stories/04/04/12/4367449

    $modx->setLogLevel(modX::LOG_LEVEL_INFO);
    $modx->setLogTarget(array(
       'target' => 'FILE',
       'options' => array('filename' => "get_tree_setest_$date.log")
    ));
        //  ECHO выводит на самый верх экрана в файл не записывает
        //  FILE
        //  HTML тоже на самый верх отредактированный в HTML вид
        // setLogTarget() returns the current setting so you can set it back after logging.

        ////$modx->log(modX::LOG_LEVEL_DEBUG, 'Heres some Info'); // сохраняет или отображает текст, тип лога modX:: должен совпадать с setLogLevel
        // modX::LOG_LEVEL_INFO ошибки, ошибки WARN  show log messages with at least INFO status
        // modX::LOG_LEVEL_FATAL при записи в лог этого уровня у пользователя появляется 503 ошибка, сайт останавливается
        // modX::LOG_LEVEL_ERROR ошибки
        // modX::LOG_LEVEL_WARN  ошибки WARN
        // modX::LOG_LEVEL_DEBUG - ошибки, ошибки WARN, DEBUG
}
//echo "ddd";
if(!function_exists('getTree')){
  function getTree($id= null, $depth = 10 ,$conf) {
  // получаем дерево документов
    global $modx;

      $tree= array ();
      $tree_content= array ();
//      $out_tree=""; // содержимое дерева на выходе
      if ($id !== null) {
          if (is_array ($id)) {
              foreach ($id as $k => $v) {
                  $tree[$v]= getTree($v, $depth,&$conf);
              }
          }
          elseif ($branch=$modx->getChildIds((int)$id,1,array('context' => $conf['context']))) {
              foreach ($branch as $key => $child) {
                  if ($depth > 0 && $leaf= getTree($child, $depth--,&$conf)) {
                      $tree[$child]= $leaf;
                  } else {
                      $tree[$child]= $child;
                  }
              }
          }
      }
      return $tree;
    }
}

if(!function_exists('create_tree')){
  function create_tree($arr, $conf, $return = true) {
    global $modx;
    // global $export_tree_arr;
      
	  // $export_tree_arr=array(); //массив содержащий все данные, на основе полного массива

      $out = array(); // итоговый массив
      $out2 = array(); // итоговый массив для построения дерева
      $oldtab = "    "; // пробелы замещающиеся разделитель
      $separator = $conf['separator']; // символ разделитель
      $query_count=""; // кол-во запросов
      $line_count=0; //кол-во отображенных записей

      $lines = explode("\n", print_r($arr, true)); // превращаем выход в массив


      if ($conf['debug']==1) {
          $modx->log(modX::LOG_LEVEL_INFO, var_dump($lines));
          // $modx->log(modX::LOG_LEVEL_INFO, "ZZZZ: ".print_r($arr));
      }

      // формируем запрос совместно с проверками, эта проверка бы ла ранее в foreach, в результате эти переменные после прохождения
      // одного цикла, становились == 1.
      // if (isset($conf['condition']['isfolder'])) {$conf['condition']['isfolder']=array('isfolder' => (int)$conf['condition']['isfolder']);}
      // if (isset($conf['condition']['published'])) {$conf['condition']['published']=array( 'published' => (int)$conf['condition']['published']);}
      // if (isset($conf['condition']['hidemenu'])) {$conf['condition']['hidemenu']=array( 'hidemenu' => (int)$conf['condition']['hidemenu']);}
      // if (isset($conf['condition']['deleted'])) {$conf['condition']['deleted']=array( 'deleted' => (int)$conf['condition']['deleted']);}
      // if (!empty($conf['condition']['template'])) {$conf['condition']['template']=array( 'template:IN' => explode(",", $conf['condition']['template']));}
 

      foreach ($lines as $line) {

      if ($conf['debug']==1) {
          $modx->log(modX::LOG_LEVEL_INFO, $line);
      }

          //garbage lines
          if (in_array(trim($line), array("Array", "(", ")", ""))) continue;

          //indents - отступы (разделители)
          $indent = "";
          $indents = floor((substr_count($line, $oldtab) - 1) / 2);
          if ($indents > 0) { for ($i = 0; $i < $indents; $i++) { $indent .= $separator; } }

          // удаляем из строки лишние символы, практически никакой разницы между способами нет.
  //        $line=trim(preg_replace("/(Array|=>|\[|\])/si","",$line));
//echo $line;
          $line = str_replace(array("Array", "[", "]", "=>"), "", $line);
//echo (int)$line."\n";
          if (!empty($line)) {
            // если содержимое не пустое

            // вытаскиваем родителя текущего элемента
            $parent=$modx->getParentIds((int)$line,1, array('context' => $conf['context']));

            // заполняем критерий для коллективного запроса, что б не вытаскивать лишнее и не мусорить в коде
			$resource_name='modResource'; // обозначаем в переменной отдельно тк используется также в др месте.
            $criteria = $modx->newQuery($resource_name);
            // return;

            //внедряем выборку по TV
            if (isset($conf['tv']['name'])) {
                $criteria->innerJoin('modTemplateVarResource', 'TemplateVarResources');
                $criteria->innerJoin('modTemplateVar', 'TemplateVar', '`TemplateVar`.`id` = `TemplateVarResources`.`tmplvarid`');

                $criteria->where(array(
                    'TemplateVar.name' => $conf['tv']['name']  // указываем имя TV параметра
                    ,'TemplateVarResources.value'.$conf['tv']['value'] // указываем значение TV параметра
                ));
            }

			//внедряем выборку по критерию введенному вручную
            if (isset($conf['addquery'])) {
                $criteria->andCondition(array(
                    "{$conf['addquery']['name']}:{$conf['addquery']['operator']}" => $conf['addquery']['value']
                ));
            }			
			
            $criteria->where(array(
                'parent' => (int)$parent[0]
                ,'context_key' => $conf['context']
                // ,$conf['isfolder']
                // ,$conf['published']
                // ,$conf['hidemenu']
                // ,$conf['deleted']
                // ,$conf['template']
            ));

            if (isset($conf['condition'])) {
                $criteria->andCondition($conf['condition']);
            }	
// print_r($conf['condition']);			
			
            $criteria->select(array(
                "`$resource_name`.`id` as `id`"           // иначе ругается на непонятно откуда взявшееся id, хоть и не должен так поступать
                ,'pagetitle'
                ,'isfolder'
                ,'published'
                ,'deleted'
                ,'hidemenu'
                ,'template'
                ,'parent'
                ,'alias'
                ,'uri'
            ));
			// $criteria->limit(2);
			// $criteria->prepare(); echo $criteria->toSQL(); return;
            $debug="";
            if ((is_null($parents)) or (!array_key_exists($parent[0], $parents))) {
              if ($stmt = $criteria->prepare() and $stmt->execute() and $result = $stmt->fetchAll(PDO::FETCH_ASSOC))
              {
                // если запрос выполнился успешно
                if ($conf['debug']==1) {
                  $div_begin="";
                  $div_end="";
                  $style="
                   display: inline;margin: 0px;padding: 0px;
                   color:#000099 !important;
                   font-weight: bold !important;
                   text-decoration: none !important;
                   font-style: normal !important;
                  ";
                  if ($conf['style']==1) {$div_begin="<div style='$style'>"; $div_end="</div>";} // включаем стиль
                  $debug=$div_begin.$criteria->toSQL().$div_end;
                  $query_count++;
                } // проверочная строка строящегося запроса
                {
                    foreach ($result as $n => $val){
                      $parents[$parent[0]][$val['id']]['id'] = $val['id'];
                      $parents[$parent[0]][$val['id']]['pagetitle'] = $val['pagetitle'];
                      $parents[$parent[0]][$val['id']]['isfolder'] = $val['isfolder'];
                      $parents[$parent[0]][$val['id']]['published'] = $val['published'];
                      $parents[$parent[0]][$val['id']]['deleted'] = $val['deleted'];
                      $parents[$parent[0]][$val['id']]['hidemenu'] = $val['hidemenu'];
                      $parents[$parent[0]][$val['id']]['template'] = $val['template'];
                      $parents[$parent[0]][$val['id']]['parent'] = $val['parent'];
                      $parents[$parent[0]][$val['id']]['alias'] = $val['alias'];
                      $parents[$parent[0]][$val['id']]['uri'] = $val['uri'];
                    }
                }
              }
            }
            // теперь вытаскиваем из родителя необходимые данные
            $pagetitle=$parents[$parent[0]][(int)$line]['pagetitle'];
            $id=$parents[$parent[0]][$val['id']]['id'];
          }
          else {continue;} // переходим к следующей линии, если запрос оказался неудачным

          // включаем стиль
          if ($conf['style']==1) {
            // добавляем красоту
            $style="display: inline;margin: 0px;padding: 0px;";
            if ($parents[$parent[0]][(int)$line]['isfolder']==1) {$style.="font-weight: bold;";};  // указываем корневые элементы
            if ($parents[$parent[0]][(int)$line]['deleted']==1) {$style.="text-decoration: line-through; color: #FF0000;";};  // удаленные
            if ($parents[$parent[0]][(int)$line]['hidemenu']==1) {$style.="font-style: italic; color: #AAA; ";};  // скрытые
            if ($parents[$parent[0]][(int)$line]['published']==0) {$style.="color: #AAA;";};  // не опубликованные
            $div_begin="<div style='$style'>"; $div_end="</div>";
            // $div_begin="X"; $div_end="Y";
          }
          $cur_id=(int)$line; // выдергиваем из строки текущий id причем строка имеет вид, к примеру "16  16"
          $line ="";
          if ($conf['debug']==1) {
            if  (!empty($debug)) {
            //                $line = $debug . "</br>".$indent.$separator." ";
            $indent= $debug . "</br>".$indent.$separator." ";
            //              $line = $debug . "</br>".$indent." ";
            }
            else {
              $indent.=$separator;
            }
          }

          if  (empty($pagetitle)) continue; // будем считать что ели эта запись не удовлетворилась условиям поиска то она пустая, поэтому мы ее пропустим
            $line.=$div_begin.$indent." ".$pagetitle.$div_end."==".$cur_id."||"; // формируем строку для дальнейшего вывода

          // $export_tree_arr[$cur_id]=array('pagetitle'=>$pagetitle);
// return;

          if (!is_null($conf['additional'])) {
            // если есть доп поля, то разбиваем их на ","
            $additional = explode(",", $conf['additional']);
            // $conf_return=array('text'=>htmlspecialchars($div_begin.$indent." ".$pagetitle.$div_end, ENT_QUOTES, "UTF-8"), 'value'=>$cur_id);
            $conf_return=array('text'=>$div_begin.$indent." ".$pagetitle.$div_end, 'value'=>$cur_id);
            foreach ($additional as $key => $value){
              $additional_return[$value]=$parents[$parent[0]][$cur_id][$value]; // перебираем все вложения и строим дополнительный массив
            }
            $conf['json'][]=array_merge($conf_return,$additional_return); // объединяем основной массив и дополнительный

          }
          else {
            $conf['json'][]=array('text'=>$div_begin.$indent." ".$pagetitle.$div_end, 'value'=>$cur_id);
          }

//            $conf['json'][]=array('text'=>$div_begin.$indent." ".$pagetitle.$div_end, 'value'=>$cur_id);
            $out[] = $line;     // добавляем в основной массив
            $out2[$cur_id]=$pagetitle;
            $line_count++;
          }


          ///////////////////////////////////////////////////
          // Displays a multi-dimensional array as a HTML List (Tree structure).
          //  print_r ($out2);
          // Эта часть отвечает за вывод дерева в виде HTML и OBJECT
          if(!function_exists('displayTree')){
          function displayTree($var,$out2) {
               $newline = "\n";
               foreach($var as $key => $value) {
                   if (is_array($value) || is_object($value)) {
                       if (!is_null($out2[(int)$key])) {$value = $newline . "<li id='$key'>{$out2[(int)$key]} <ul>" . displayTree($value,$out2) . "</ul></li>";}
                       else {$value = $newline . displayTree($value,$out2);}
                   }
                   if (is_array($var)) {
                       if (!stripos($value, "<li")) {
                          if (!is_null($out2[(int)$key])) { $output .= "<li id='$key'>" . $out2[(int)$key] . "</li>" . $newline; }
                       }
                       else {
                          $output .= $value . $newline;
                       }
                   }
               }
               return $output;
          }}

          if(!function_exists('displayTreeOb')){
          function displayTreeOb($var,$out2) {
               $newline = " ";
               foreach($var as $key => $value) {
                   if (is_array($value) || is_object($value)) {
                     if (is_null($out2[(int)$key])) { $value = $newline . displayTreeOb($value,$out2); }
                     else {$value = $newline . "{ 'data': '{$out2[(int)$key]}', 'attr' : { 'id' :'$key','class':''}, 'children': [" . displayTreeOb($value,$out2) . "] },";}
                   }
                   if (is_array($var)) {
                       if (!stripos($value, "{ 'data':")) {
                          //if (!is_null($out2[(int)$key])) { $output .= "{ 'data': '" . $out2[(int)$key] . "', 'attr' : { 'id' :'$key','class':'jstree-checked'}}," . $newline; }
                          if (!is_null($out2[(int)$key])) { $output .= "{ 'data': '" . $out2[(int)$key] . "', 'attr' : { 'id' :'$key','class':''}}," . $newline; }
                       }
                       else {
                          $output .= $value . $newline;
                       }
                   }
               }
          //     return "[".$output."]";
               return $output;
          }}

          //$conf['return_tree']='HTML';
          switch ($conf['return_tree']) {
            case 'HTML':
             $conf['return_tree'] = "<ul>".displayTree($arr,$out2)."<ul>";
            break;

            case 'OBJECT':
          //   $conf['return_tree'] = displayTreeOb($arr,$out2);
//             $conf['return_tree'] = "[".displayTreeOb($arr,$out2)."]";
             $conf['return_tree'] = str_replace(", ]", "]", "[".displayTreeOb($arr,$out2)."]" );
            break;
          }

		  // дублирующая функция, в след. версии (1.19) верх проверку нужно будет убрать чтоб не делать 
		  // двойную работу 
			switch ($conf['return_type']) {
				case "html":
					$conf['return_tree'] = "<ul>".displayTree($arr,$out2)."<ul>";
				break;			  
				case "object":
					$conf['return_tree'] = str_replace(", ]", "]", "[".displayTreeOb($arr,$out2)."]" );
				break;			  
			}
		  
          //echo $out_tree;
          ///////////////////////////////////////////////////

          $out = implode("\n", $out) . "\n";

          // отображаем количество запросов и строк
          if ($conf['debug']==1) {$out="||\n запросов к БД: $query_count==||\n строк: $line_count ==||\n".$out;
                                  $conf['json'][]=array('text'=>"\n запросов к БД: $query_count", 'value'=>"");
                                  $conf['json'][]=array('text'=>"\n строк: $line_count", 'value'=>"");
          }

          if ($return == true) return $out;
  }
}


// return;
$return = create_tree (getTree(&$conf['parent'],&$conf['depth'],&$conf),&$conf);
$exec_time = microtime(true) - $start_time;

//echo "666<br />";
//echo json_encode($return);
//return '';
//echo ($conf['return_tree']);
//var_dump (json_decode($conf['return_tree'],true));

// отображаем время запроса
if ($conf['debug']==1) {
	$modx->log(modX::LOG_LEVEL_INFO, $return);
	$return="время на скрипт: ".$exec_time."==".$return;
	$conf['json'][]=array('text'=>"время на скрипт: $exec_time", 'value'=>"");
}

// добавляем пустое значение для того чтобы при открытии поля 1-е значение не ставилось по умолчанию
if ($conf['empty_value']){
	$return="ничего не выбрано==||".$return;
	array_unshift($conf['json'], array('text'=>"ничего не выбрано", 'value'=>""));  // добавляем в первый элемент массива
}
// посылаем ответ в зависимости от типа запроса:
switch ($conf['return_type']) {
	case "list":
	break;
	case "json":
	// print_r($conf['json']);
		$return=json_encode($conf['json']); 
	break;
	case "html":
	case "object":
		$return = $conf['return_tree'];	
	break;	
	case "select":
		$return_select="";
		$return = explode("||", $return);
		foreach ($return as $value) {
		  // if ( strpos($conf['tv']['string'], $o) !== false) {
			list($value_content, $value_id) = explode("==", $value);
			$return_select.="<option value='{$value_id}'>{$value_content}</option>";
			// break;
		  // }
		}
	$return = $return_select;
	break;		
}

//if (!empty($conf['return_tree'] )) { $return = json_encode(json_decode($conf['return_tree'],true)); } // отправляем построившееся дерево
if (!empty($conf['return_tree'] )) { $return = $conf['return_tree']; } // отправляем построившееся дерево
if ($conf['return_json']==1) { $return=json_encode($conf['json']); } // почему то в виде массива вернуть нельзя, приходит только строчка Array, поэтому конвертим в строку


//return $return;
echo $return;
return '';