<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
$chats_folder="./pruebas/words/";
$lista_archivos=glob($chats_folder."*.txt");

$replace_array = array(    'ú'=>'ú', 'á'=>'á','í'=>'í','ó'=>'ó','é'=>'é','ñ'=>'ñ');
?>
<!doctype html/>
<html>
	<head>
		<title>word cloud</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="styles/lastHundred.css"/>
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<script src="./lib/d3-cloud-master/build/d3.layout.cloud.js" charset="utf-8"></script>
	</head>
	<body>
<script type="text/javascript">
function pick_author_words(author){
	console.log(author.value);
	console.log(author.innerHTML);
	var filename="./pruebas/words/"+author.innerHTML+".txt";
	d3.json(filename,function(error,data){
		console.log(data);
	})
}

</script>
<select name="author" onchange="pick_author_words(document.getElementsByName('author')[0].selectedOptions[0]);">
<option value="">-----------------</option>
<?php
foreach($lista_archivos as $key => $value){
echo '<option value="'.$key.'">'.htmlentities(strtr(str_replace(".txt", "", basename($value)), $replace_array)).'</option>';
}
?>
</select>