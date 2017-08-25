<?php
function get_chat_page_JSON($page,$lista_archivos){
	$archivo=file_get_contents($lista_archivos[($page-1)]);
	$rows = explode("\n", $archivo);
	$key=-1;
	foreach ($rows as $row => $data) {
		if (preg_match('#\d+\/\d+\/\d+, \d+:\d+ - #', $data,$fecha_hora)) {
			$key++;
			$chat_posts[$key]['datetime']=$fecha_hora[0];
			$chat_posts[$key]['datetime']=str_replace(" - ", "", $chat_posts[$key]['datetime']);
			$chat_posts[$key]['date']=preg_split("#\,\s#", $chat_posts[$key]['datetime'])[0];
			$post_parts=preg_split("#\:\s#", preg_split('#\d+\/\d+\/\d+, \d+:\d+ - #', $data)[1]);
			$chat_posts[$key]['author']=$post_parts[0];
			$chat_posts[$key]['content']=$post_parts[1];
		} else {
			$chat_posts[$key]['content'].=" ".$data;
		}
	}
	// file_put_contents("./datos.txt", print_r($chat_posts));
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