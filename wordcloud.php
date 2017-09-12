<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
$chats_folder="./pruebas/words/";
$lista_archivos=glob($chats_folder."*.txt");

$replace_array = array(    'ú'=>'ú', 'á'=>'á','í'=>'í','ó'=>'ó','é'=>'é','ñ'=>'ñ');
?>
<!doctype html>
<html>
	<head>
		<title>word cloud</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="styles/lastHundred.css"/>
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<script src="./lib/d3-cloud-master/build/d3.layout.cloud.js" charset="utf-8"></script>
	</head>
	<body>
		<p>ejemplo word cloud</p>
		<select name="author" onchange="pick_author_words(document.getElementsByName('author')[0].selectedOptions[0]);">
			<option value="">-----------------</option>
			<?php
			foreach($lista_archivos as $key => $value){
			echo '<option value="'.$key.'">'.htmlentities(strtr(str_replace(".txt", "", basename($value)), $replace_array)).'</option>';
			}
			?>
		</select>
		<div id="cloud"></div>
		<script type="text/javascript">
			var fill = d3.scale.category20();
			function pick_author_words(author){
			console.log(author.value);
			console.log(author.innerHTML);
			var filename="./pruebas/words/"+author.innerHTML+".txt";
			d3.json(filename,function(error,data){
				console.log(data);
				drawcloud(data);
			})
		}
		function drawcloud(dataset){
			d3.select("#cloud").selectAll("svg").remove();
		var layout = d3.layout.cloud()
			.size([1000, 500])
			.words(dataset.map(function(d) {
			  return {text: d.word, size: (d.freq), test: "haha"};
			}))
			.padding(5)
			.font("Impact")
			.fontSize(function(d) { return d.size; })
			.on("end", draw);

		layout.start();

		function draw(words) {
		  d3.select("#cloud").append("svg")
			  .attr("width", layout.size()[0])
			  .attr("height", layout.size()[1])
			.append("g")
			  .attr("transform", "translate(" + layout.size()[0] / 2 + "," + layout.size()[1] / 2 + ")")
			.selectAll("text")
			  .data(words)
			.enter().append("text")
			  .style("font-size", function(d) { return d.size + "px"; })
			  .style("font-family", "Impact")
			  .style("fill", function(d, i) { return fill(i); })
			  .attr("text-anchor", "middle")
			  .attr("transform", function(d) {
				return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
			  })
			  .text(function(d) { return d.text; });
		}
	}
		</script>
	</body>
</html>