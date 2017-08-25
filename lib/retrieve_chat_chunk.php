<?php
function get_chat_page_JSON($page,$lista_archivos){
	$archivo=file_get_contents($lista_archivos[($page-1)]);
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

	return json_encode($chat_posts);
}

if ( isset( $_POST["PAGE"] ) ) {
	$page=$_POST["PAGE"];
	$chats_folder="./../pruebas/output/";
	$lista_archivos=glob($chats_folder."*.txt");
	header("Cache-Control: no-cache, must-revalidate");
	header("Content-type: application/javascript");
	echo get_chat_page_JSON($page,$lista_archivos);
}
?>