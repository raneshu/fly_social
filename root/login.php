<?php
include_once("php_includes/check_login_status.php");

if($user_ok == true){
	header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?><?php


if(isset($_POST["e"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = md5($_POST['p']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// FORM DATA ERROR HANDLING
	if($e == "" || $p == ""){
		echo "login_failed";
        exit();
	} 
	else {
	
		$sql = "SELECT id, username, password FROM users WHERE email='$e' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $rowcheck = mysqli_num_rows($query);
        if($rowcheck<1){
        	echo "sign_up";
        	exit();
        }
        else{
		        $row = mysqli_fetch_row($query);
				$db_id = $row[0];
				$db_username = $row[1];
		        $db_pass_str = $row[2];
				if($p != $db_pass_str){
					echo "no_match";
		            exit();
				} else {
					
					$_SESSION['userid'] = $db_id;
					$_SESSION['username'] = $db_username;
					$_SESSION['password'] = $db_pass_str;
					setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
					setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
		    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
					// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
					$sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
		            $query = mysqli_query($db_conx, $sql);
					echo $db_username;
				    exit();
				}
			}
		}
	exit();
}
?>
<!DOCTYPE html>
<html>

<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script src="js/jquery-1.11.1.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" href="style/login.css">
<script>

function checkemail(){
  var e = _("email").value;
 if((e.match(/@/g)==null)||(e.match(/[a-z]/ig)==null)||(e.match(/./g)==null)){
    _("emailstatus").innerHTML = '<strong style="color:#F00;"> Please enter a valid email address</strong>';
  }
  else _("emailstatus").innerHTML = "";
}

function login(){
	var e = _("email").value;
	var p = _("pass").value;
	_("emailstatus").innerHTML = "";
	_("status").innerHTML = "";
	if(e == "" || p == "")
	{
		_("status").innerHTML = "Fill out all of the form data";
		
	}
	else if((e.match(/@/g)==null)||(e.match(/[a-z]/ig)==null)||(e.match(/\./g)==null)){
		    _("emailstatus").innerHTML = '<strong style="color:#F00;"> Please enter a valid email address</strong>';
		    
		  }

	else 
	{
		_("loginbtn").style.display = "none";
		_("status").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "login.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText.trim() == "login_failed"){
					_("status").innerHTML = "Login unsuccessful, please try again.";
					_("loginbtn").style.display = "block";
				} 
				else if(ajax.responseText.trim() == "sign_up"){
					_("status").innerHTML = "Login Unsuccessful. Please <a href='signup.php'><b>signup</b></a> first.";
					_("loginbtn").style.display = "block";
				} 
				else if(ajax.responseText.trim() == "no_match"){
					_("status").innerHTML = "Your username and password do not match";
					_("loginbtn").style.display = "block";
				}

				else {
					window.location = "user.php?u="+ajax.responseText.trim();
				}
	        }
        }
        ajax.send("e="+e+"&p="+p);
	}
}
</script>
</head>
<body>
 	<?php include_once("php_includes/template_pageTop.php"); ?>
	    <div class = "form-body">
	        <div class = "container">
	      	    <div class="row">
	        	    <div class=" col-md-4 col-md-offset-4 form-wrapper">
			            <form class="form-login" action = "" id="myform"  onsubmit="return false;">
			      
						    <h3 class="form-login-heading">Login in to <b>Fly</b></h3>
						    <div class="form-group">
				                <label for="Email">Email address</label>
				                <input type="email" class="form-control" id="email" placeholder="Email" required autofocus="" onblur= "checkemail();">
				                <span id="emailstatus"></span>
				            </div>

				            <div class="form-group">
				              <label for="Pass">Password</label>
				              <input type="password" class="form-control" id="pass" placeholder="Password" required autofocus="">
				            </div>

						    <input class="btn btn-lg btn-primary btn-block" type="submit" name ="submit" id="loginbtn"
					             value = "login" onclick="login();">  
					        <span id="status" style="color:red; font-weight:bold;"></span>  
					        
				        </form>
				    </div>

		   		</div>
		    </div>
		</div>

<?php include_once("php_includes/template_pageBottom.php"); ?>
</body>
</html>

  <!-- LOGIN FORM -->
  <!--<form id="loginform" onsubmit="return false;">
    <div>Email Address:</div>
    <input type="text" id="email" onfocus="emptyElement('status')" maxlength="88">
    <div>Password:</div>
    <input type="password" id="password" onfocus="emptyElement('status')" maxlength="100">
    <br /><br />
    <button id="loginbtn" onclick="login()">Log In</button> 
    <p id="status"></p>
    <a href="forgot_pass.php">Forgot Your Password?</a>
  </form>

  -->



