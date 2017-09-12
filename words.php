<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
	$archivo=file_get_contents("./Chat de WhatsApp con Santa 78. Luigi Bosco.txt");
	function stopwords($x){
		$swlist=file_get_contents("./lib/stopwords-es.txt");//No puedo pasar esto afuera porque no lo toma en la funcion
		$swchain="|";
		$swchain.=trim(preg_replace("#\n#", "|", $swlist));
	  return !preg_match("/^(.".$swchain."|red)$/i",$x);
	};
	$archivo=preg_replace("#\s((ja)+$|(ja)+\W$)#", "", $archivo);
	$re="#(\d+\/\d+\/\d+, \d+:\d+ - )#";
	$chat_array=preg_split($re, $archivo, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$chats=[];
	$i=0;
	foreach ($chat_array as $key => $value) {
		if ($key % 2 == 0) {
		} else {
			if (preg_match("#\:\s#", $value)) {
				$words[preg_split("#\:\s#", $value)[0]].=mb_strtolower(preg_split("#\:\s#", $value)[1],"UTF-8");//strtolower simple me caga el encoding
			}
		}
	}
// file_put_contents("./pruebas/words/words.txt", print_r($words,true));
	$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
							'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
							'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
							'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
							'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
	$topx=50;
	foreach ($words as $author => $author_words) {
		echo $author."\n";
		$author_names=preg_split("#\s#", $author);
		foreach ($author_names as $key => $name) {
			$author_words=str_replace(mb_strtolower($name,"UTF-8"), "", $author_words);
			$name = strtr( $name, $unwanted_array );
			$author_words=str_replace(mb_strtolower($name,"UTF-8"), "", $author_words);
			echo mb_strtolower($name,"UTF-8")."\n";
		}
		preg_match_all('/\pL+/u', $author_words, $author_word_array);//str_word_count me caga el encoding
		$filteredArray = array_filter($author_word_array[0], "stopwords");
		$author_counted_words_array=array_count_values($filteredArray);
		arsort($author_counted_words_array);
		$author_counted_words_array=array_slice($author_counted_words_array, 0,$topx);
		$author_counted_words_array_objectified=[];
		$n=0;
		foreach ($author_counted_words_array as $key => $value) {
			$author_counted_words_array_objectified[$n]['word']=$key;
			$author_counted_words_array_objectified[$n]['freq']=$value;
			$n++;
		}
		file_put_contents("./pruebas/words/".$author.".txt", json_encode($author_counted_words_array_objectified,JSON_PARTIAL_OUTPUT_ON_ERROR));
	}
?>