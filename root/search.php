<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $u; ?></title>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<link rel="stylesheet" href="style/bootstrap.css">

<script>
	
</script>
</head>
<body>
	<?php include_once("php_includes/check_login_status.php"); 

	 if($user_ok == true) {

	 	include_once("php_includes/template_pageTop.php"); ?>
		<div class='container'>
		    <div class='row'>
		        <form>
			        <div class='span3 input-append' style="margin-top:30%; margin-left:30%">
			            <input class="span2" style="height:26px;width:400px;" id="main_search" type="text" >
			            <input class="btn btn-large btn-primary"  type="button" value="search">
			        </div>

		        </form>
		    </div>
		</div>
	
	<?php } include_once("php_includes/template_pageBottom.php"); else{
		header("location: user.php?u=".$_SESSION["username"]);
  	} ?>

	
</body>
</html>