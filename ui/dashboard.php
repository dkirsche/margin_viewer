<HTML>
<HEAD>
<TITLE>Dashboard</TITLE>
<script type="text/javascript" src="/cgi-bin/includecode/jquery-1.4.4.min.js"></script>
<SCRIPT>
	$(document).ready(function(){
		loadUrl='displaymargin.php';
		$("#margin").html("loading").load(loadUrl);
	});
</SCRIPT>
</HEAD>
<BODY>
	<div id='margin'></div>


</BODY>
</HTML>


