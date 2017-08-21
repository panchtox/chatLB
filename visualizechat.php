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
?>
<!doctype html>
<html>
	<head>
		<title>Twitter user history</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="./styles/lastHundred.css"/>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
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

			var viewportWidth  = document.documentElement.clientWidth
				,viewportHeight = document.documentElement.clientHeight;
			var h=viewportHeight*0.35, w=viewportWidth*0.98, margin=30;
			var tooltipHeight=viewportHeight/1.5,tooltipWidth=viewportWidth/3;

			timeline('#chart',nested_data,w,h);

/* A PARTIR DE AQUI, DIBUJO DE CHART DE LINEAS*/

	function timeline(reference,data,width,height){
			d3.select(reference).selectAll("svg").remove();

			var svg=d3.select(reference)
				.style("width",width+"px")
				.append("svg")
				.attr("width",width)
				.attr("height",height);

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
			svg.append("g").attr("class","xaxis")
			.attr("transform","translate(0,"+(height-margin)+")")
			.call(xAxis);
			svg.append("g").attr("class","yaxis")
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
			var lineGraph = svg.append("path")
			.attr("d", lineFunction(nested_data))
			.attr("class","dataline")
			.attr("stroke", "steelblue")
			.attr("stroke-width", 2)
			.attr("fill", "none");

			//Y los circulitos
			var circles=svg.append("g");
			circles.selectAll("circle")
				.data(nested_data)
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
					.style("left", calculateXtooltipPosition("svg",parseFloat(d3.select(this).attr('cx'))) + "px")
					.style("top", parseFloat(d3.select(this).attr('cy'))-50+d3.select('svg')[0][0].parentNode.offsetTop + "px")
					.style("max-height",tooltipHeight+"px")
					.style("width",tooltipWidth+"px")
					.classed("hidden", false);
				d3.select("#little").text(d3.select(this).attr('id').substr(1,10)+": "+d3.select(this).attr('count')+" posteos");
				d3.select("#tooltip").selectAll("div")
					.remove();
				d3.select("#tooltip").selectAll("div")
					.data(nested_data[findWithAttr(nested_data,'key',(d3.select(this).attr('id')).substr(1))].values)
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
		d3.select("svg").on("click",function(){d3.select("#tooltip").classed("hidden",true)});
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

				<style type="text/css">
		.yaxis path,.yaxis line,.xaxis path, .xaxis line {
		    fill: none;
		    shape-rendering: crispedges;
		    stroke: #FEBD17;
		}
		.yaxis text,.xaxis text {
		    font-family: sans-serif;
		    font-size: 8px;
		    stroke:#7a7a7a;
		}
		body {
		    background: none repeat scroll 0 0 #222222;
		    color: #FFFFFF;
		    font-family: "Avenir Next",Avenir,"Segoe UI",Roboto,"Helvetica Neue",sans-serif;
		    line-height: 1.6;
		    text-align: center;
		}

		#tooltip.hidden {
			display: none;
		}

		#tooltip p {
			margin: 0;
			font-family: sans-serif;
			font-size: 12px;
			line-height: 20px;
		}

		#tooltip {
			position: absolute;
			height: auto;
			padding: 0px 0px 5px 0px;
			background-color: #f0f0f0;
			-webkit-border-radius: 10px;
			-moz-border-radius: 10px;
			/*border-radius: 10px;*/
			-webkit-box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.4);
			-moz-box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.4);
			box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.4);
			/*pointer-events: none;*/
			opacity: 0.8;
		    overflow: auto;
		}

		#little {
		    text-align: left;
		    font-size: 0.8em;
		    background-color: #8e8e8e;
		}

		.tuitText {
		    color: black;
		    font-size: 0.8em;
		    text-align: left;
		    margin: 0px 0px 1px 0px;
		    background-color: white;
		}
		</style>
