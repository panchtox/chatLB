<?php
function get_message()
{
	return "Hello world! It is ".date("r");
}
if ( isset( $_GET["NOW"] ) ) {
	sleep(5);
	header("Cache-Control: no-cache, must-revalidate");
	header("Content-type: text/plain");
	echo get_message();
} else { ?>
<html>
<head>
	<title>Hello world</title>
	<script type="text/javascript">
		<!--
		var httpObj=null;
		function OnLoad()
		{
			document.getElementById("action").href="javascript:Update()";
		}
		function Update()
		{
			if ( httpObj != null ) return;
			document.getElementById("data").innerHTML = "Loading";
			httpObj = NewHTTP();
			httpObj.open("GET","?NOW",true);
			httpObj.onreadystatechange = OnData;
			httpObj.send(null);
		}
		function NewHTTP()
		{
			try {
				return new XMLHttpRequest();
			} catch (e) {
				return new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		function OnData()
		{
			if ( httpObj.readyState==4 ) {
				m=document.getElementById("data");
				if (httpObj.status==200 ) {
					m.innerHTML = httpObj.responseText;
				} else {
					m.innerHTML="Error loading date and time.";
				}
				httpObj = null;
			}
		}
		-->
		</script>
</head>
<body onLoad="OnLoad();">
	<div id="data">
		<?
		if ( isset($_GET["NOW-INLINE"]) )
			echo get_message();
		else echo "Press Go";
		?>
	</div>
	<a href="?NOW-INLINE" id="action">Go</a>
</body>
</html>
<?php } ?>