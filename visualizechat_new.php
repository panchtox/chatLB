<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
$chats_folder="./pruebas/output/";
$lista_archivos=glob($chats_folder."*.txt");
$archivo=file_get_contents($lista_archivos[max(array_keys($lista_archivos))]);
preg_match_all('#(\d+\/\d+\/\d+, \d+:\d+ - )(.*)#',$archivo,$match);
$chat_chunk=$match[0];

foreach ($chat_chunk as $key => $post) {
	$re="#(\d+\/\d+\/\d+, \d+:\d+ - )#";
	$chat_posts_pre[$key]=preg_split($re, $post, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
}

foreach ($chat_posts_pre as $key => $value) {
	$chat_posts[$key]['datetime']=$chat_posts_pre[$key][0];
	$chat_posts[$key]['datetime']=str_replace(" - ", "", $chat_posts[$key]['datetime']);
	$chat_posts[$key]['date']=preg_split("#\,\s#", $chat_posts[$key]['datetime'])[0];
	$post_parts=preg_split("#\:\s#", $chat_posts_pre[$key][1]);
	$chat_posts[$key]['author']=$post_parts[0];
	$chat_posts[$key]['content']=$post_parts[1];
}
// print_r($chat_posts);
?>
<!doctype html>
<html>
	<head>
		<title>Twitter user history</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="./styles/lastHundred.css"/>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
		<!-- // <script type="text/JavaScript" src="./lib/d3.v2.js"></script> -->
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
	</head>
	<body>
		<div id="userTimeline">
			<img src="./images/csi_logo03.png" style="width:50px;float:left">
			<h1>Chat Luigi Bosco</h1>
			<div id="chart"></div>
		</div>
		<div id="tooltip" class="hidden">
			<p id="little">fecha</p>
		</div>
		<script type="text/javascript">
			var chat_posts_js=<?php echo(json_encode($chat_posts));?>;
			var formatDateTime=d3.time.format("%d/%m/%y, %H:%M").parse;
			var formatDate=d3.time.format("%d/%m/%y").parse;
			Object.keys(chat_posts_js).forEach(function(k){chat_posts_js[k].datetime=formatDateTime(
				chat_posts_js[k].datetime
				)});
			Object.keys(chat_posts_js).forEach(function(k){chat_posts_js[k].date=formatDate(
				chat_posts_js[k].date
				)});
			chat_posts_array=[];
			Object.keys(chat_posts_js).forEach(function(k){chat_posts_array.push(chat_posts_js[k])});
			var nested_data = d3.nest().key(function(d) { return d.date; }).entries(chat_posts_array);
			console.log(nested_data);
		</script>
