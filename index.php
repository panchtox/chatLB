<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
$replace_array = array(    'ú'=>'ú', 'á'=>'á','í'=>'í','ó'=>'ó','é'=>'é','ñ'=>'ñ');
$chats_folder="./pruebas/output/";
$lista_archivos=glob($chats_folder."*.txt");
$archivo=file_get_contents($lista_archivos[max(array_keys($lista_archivos))]);
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
?>
<!doctype html>
<html>
	<head>
		<title>Bolumetricas LG</title>
		<meta charset="UTF-8">
		<link rel="shortcut icon" type="image/png" href="./images/faviconlb.png"/>
		<link href="styles/bootstrap.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="styles/lastHundred.css"/>
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<script src="//code.jquery.com/jquery-2.0.3.min.js" type="text/javascript"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
		<script src="./lib/esimakin-twbs-pagination-9b6d211/jquery.twbsPagination.js" type="text/javascript"></script>
		<script src="./lib/jqueryScrollTo/jquery.scrollTo.js"></script>
		<script type="text/javascript" src="./lib/jQuery-One-Page-Nav-master/jquery.nav.js"></script>
		<script src="./lib/d3-cloud-master/build/d3.layout.cloud.js" charset="utf-8"></script>
	</head>
	<body>
		<img src="./images/csi_logo03.png" style="width:50px;float:left">
			<h1>Chat Luigi Bosco</h1>
		<ul id="nav">
			<li class="current"><a href="#wordcloud">Nube de palabras</a></li>
			<li><a href="#chatTimeline">L&iacute;nea de tiempo</a></li>
		</ul>
		<div id="wordcloud">
			<select name="author" onchange="pick_author_words(document.getElementsByName('author')[0].selectedOptions[0]);">
			<option value="">-----------------</option>
			<?php
			$words_folder="./pruebas/words/";
			$lista_archivos_words=glob($words_folder."*.txt");
			foreach($lista_archivos_words as $key => $value){
			echo '<option value="'.$key.'">'.htmlentities(strtr(str_replace(".txt", "", basename($value)), $replace_array)).'</option>';
			}
			?>
		</select>
		<div id="cloud"></div>
		</div>
		<div id="chatTimeline">
			
			<div id="chart"></div>
		
			<div class="container">
					<nav aria-label="Page navigation">
						<ul class="pagination" id="pagination"></ul>
					</nav>
			</div>
			<div id="tooltip" class="hidden">
				<p id="little">fecha</p>
			</div>
		</div>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#nav').onePageNav();
		});
		</script>
		<script type="text/javascript">
			var chat_posts_js=<?php echo(json_encode($chat_posts));?>;
			console.log(chat_posts_js);
			var chat_chunks=<?php echo sizeof($lista_archivos); ?>;
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
			// console.log(nested_data);
			// console.log(<?php echo max(array_keys($lista_archivos)); ?>);

			var viewportWidth  = document.documentElement.clientWidth
				,viewportHeight = document.documentElement.clientHeight;
			var h=viewportHeight*0.60, w=viewportWidth*0.98, margin=30;
			var tooltipHeight=viewportHeight/1.8,tooltipWidth=viewportWidth/3;

			timeline('#chart',nested_data,w,h);

/* A PARTIR DE AQUI, DIBUJO DE CHART DE LINEAS*/

	function timeline(reference,data,width,height){
			d3.select(reference).selectAll("svg").remove();

			var svg1=d3.select(reference)
				.style("width",width+"px")
				.append("svg")
				.attr("width",width)
				.attr("height",height)
				;

			var yscale = d3.scale.linear().domain([0, d3.max(data,function(d){return d.values.length;})]).range([height - margin,0 + margin]);
			var xscale=d3.time.scale()
			.domain([d3.min(data,function(d){return d.values[0].date;}),d3.max(data,function(d){return d.values[0].date;})])
			.range([0 + margin, width - margin]);
			//Agrego los ejes
			var xAxis=d3.svg.axis().scale(xscale).orient("bottom")
			.ticks(d3.time.day,1)
			.tickFormat(d3.time.format("%d-%m"))
			;
			var yaxis=d3.svg.axis().scale(yscale).orient("left").ticks(5);
			svg1.append("g").attr("class","xaxis")
			.attr("transform","translate(0,"+(height-margin)+")")
			.call(xAxis);
			svg1.append("g").attr("class","yaxis")
			.attr("transform","translate("+margin+",0)")
			.call(yaxis)
			.append("text")
			.attr("y",margin)
			.attr("x",margin/2)
			.attr("dy", ".71em")
			.style("text-anchor", "end");
			//Generar la funcion para la linea
			var lineFunction = d3.svg.line()
			.interpolate("cardinal")
			// .interpolate("monotone")
			.x(function(d) { return xscale(d.values[0].date);})
			.y(function(d) { return yscale(d.values.length);});
			//Y dibujarla
			var lineGraph = svg1.append("path")
			.attr("d", lineFunction(data))
			.attr("class","dataline")
			.attr("stroke", "steelblue")
			.attr("stroke-width", 2)
			.attr("fill", "none");

			//Y los circulitos
			var circles=svg1.append("g");
			circles.selectAll("circle")
				.data(data)
				.enter()
				.append("circle")
				.attr("cx",function(d){return xscale(d.values[0].date);})
				.attr("cy",function(d) { return yscale(d.values.length);})
				.attr("r",3)
				.attr("id",function(d){return 'd'+d.key})
				.attr("stroke","black")
				.attr("fill","red")
				.attr("count",function(d) { return d.values.length;})
				.on("mouseover", function() {
				d3.select(this).attr("r",5);
				d3.select("#tooltip")
					.style("left", calculateXtooltipPosition("#chart svg",parseFloat(d3.select(this).attr('cx'))) + "px")
					.style("top", parseFloat(d3.select(this).attr('cy'))-50+d3.select('#chart svg')[0][0].parentNode.offsetTop + "px")
					.style("max-height",tooltipHeight+"px")
					.style("width",tooltipWidth+"px")
					.classed("hidden", false);
				d3.select("#little").text(d3.select(this).attr('id').substr(1,15)+": "+d3.select(this).attr('count')+" posteos");
				d3.select("#tooltip").selectAll("div")
					.remove();
				d3.select("#tooltip").selectAll("div")
					.data(data[findWithAttr(data,'key',(d3.select(this).attr('id')).substr(1))].values)
					.enter()
					.append("div")
					.attr("class","tuitText")
					.html(function(d){return modifyText(d);})
					;
				})
				.on("mouseout",function(){
					d3.select(this).attr("r",3);
				})
				;
		d3.select("#chart svg").on("click",function(){d3.select("#tooltip").classed("hidden",true)});
		}
/***********************************************************************/

		function findWithAttr(array, attr, value) {
			for(var i = 0; i < array.length; i += 1) {
				if(array[i][attr] === value) {
					return i;
				}
			}
		}

		function calculateXtooltipPosition(container,xCursorPositionInContainer){
			var containerLeft=d3.select(container)[0][0].parentNode.offsetLeft,
				containerRight=containerLeft+w;
			var calculatedTooltipXposition;
			if ((xCursorPositionInContainer+tooltipWidth)>containerRight) {
				calculatedTooltipXposition=containerLeft+xCursorPositionInContainer - tooltipWidth -10;
			}else{
				calculatedTooltipXposition=containerLeft+xCursorPositionInContainer+10;
			}
			return calculatedTooltipXposition;
		}

		function modifyText(chat_post){
			return "<i>"+chat_post.author+"</i>"+": "+chat_post.content;
		}
		</script>
		<script type="text/javascript">
			$(function () {
				window.pagObj = $('#pagination').twbsPagination({
					totalPages: chat_chunks,
					visiblePages: 5,
					startPage: chat_chunks,
					initiateStartPageClick:false,
					onPageClick: function (event, page) {
						// console.info(page + ' (from options)');
						$.post("./lib/retrieve_chat_chunk.php",
							{
								PAGE:page
							},
							function(data){
								data=eval(data);
								// console.log(data);
								var formatDateTime=d3.time.format("%d/%m/%y, %H:%M").parse;
								var formatDate=d3.time.format("%d/%m/%y").parse;
								Object.keys(data).forEach(function(k){data[k].datetime=formatDateTime(
									data[k].datetime
									)});
								Object.keys(data).forEach(function(k){data[k].date=formatDate(
									data[k].date
									)});
								// console.log(data);
								data_array=[];
								Object.keys(data).forEach(function(k){data_array.push(data[k])});
								var nested_data = d3.nest().key(function(d) { return d.date; }).entries(data_array);
								console.log(nested_data);
								timeline('#chart',nested_data,w,h);
							}
							)
					}
					,first:"Primero"
					,prev:"Anterior"
					,next:"Siguiente"
					,last:"&Uacute;ltimo"
				})
				// .on('page', function (event, page) {
				// 	console.info(page + ' (from event listening)');
				// })
				;
			});
		</script>
		<script type="text/javascript">
			var fill = d3.scale.category20();
			// var dataautor;
			function pick_author_words(author){
			console.log(author.value);
			console.log(author.innerHTML);
			var filename="./pruebas/words/"+author.innerHTML+".txt";
			d3.json(filename,function(error,data){
				console.log(data);
				// dataautor=data;
				drawcloud(data);
			})
		}
		function drawcloud(dataset){
			d3.select("#cloud").selectAll("svg").remove();
		var layout = d3.layout.cloud()
			.size([1000, 500])
			.words(dataset.map(function(d) {
			  return {text: d.word, size: (d.freq * (132/d3.max(dataset,function(d){return d.freq}))), test: "haha"};
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
		<script type="text/javascript">
		function timeline_update(reference,data,width,height){

		}
		</script>
