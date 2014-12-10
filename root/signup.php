<?php

session_start();
include_once("php_includes/check_login_status.php");

if($user_ok == true){
  header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?>
<?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
  include_once("php_includes/db_conx.php");

  $username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
  $sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
      echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
      exit();
    }
  if (is_numeric($username[0])) {
      echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
      exit();
    }
    if ($uname_check < 1) {
      echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
      exit();
    } else {
      echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
      exit();
    }
}
if(isset($_POST["emailcheck"])){
  include_once("php_includes/db_conx.php");

  $email = mysqli_real_escape_string($db_conx, $_POST['emailcheck']);
  $sql = "SELECT id FROM users WHERE username='$email' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $email_check = mysqli_num_rows($query);
   
  if ($email_check < 1) {
      echo '<strong style="color:#009900;">' . $email. ' is OK</strong>';
      exit();
    } else {
      echo '<strong style="color:#F00;">' . $email. ' is taken</strong>';
      exit();
    }
}
?><?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
  // CONNECT TO THE DATABASE
  include_once("php_includes/db_conx.php");
  // GATHER THE POSTED DATA INTO LOCAL VARIABLES
  $u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
  $e = mysqli_real_escape_string($db_conx, $_POST['e']);
  $p = $_POST['p'];

  // GET USER IP ADDRESS
  $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
  // DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
  $sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
  $u_check = mysqli_num_rows($query);
  // -------------------------------------------
  $sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
  $e_check = mysqli_num_rows($query);
  // FORM DATA ERROR HANDLING
  if($u == "" || $e == "" || $p == ""){
    echo "The form submission is missing values.";
        exit();
  } else if ($u_check > 0){ 
        echo "The username you entered is alreay taken";
        exit();
  } else if ($e_check > 0){ 
        echo "That email address is already in use in the system";
        exit();
  } else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "Username must be between 3 and 16 characters";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo 'Username cannot begin with a number';
        exit();
    } else {

    $p_hash = md5($p);
    
    $sql = "INSERT INTO users (username, email, password, ip, signup, lastlogin, notescheck)       
            VALUES('$u','$e','$p_hash','$ip',now(),now(),now())";
    $query = mysqli_query($db_conx, $sql); 
    $uid = mysqli_insert_id($db_conx);
    // Establish their row in the useroptions table
    $sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
    $query = mysqli_query($db_conx, $sql);
    // Create directory(folder) to hold each user's files(pics, MP3s, etc.)
    if (!file_exists("user/$u")) {
      mkdir("user/$u", 0755);
    }
    // Email the user their activation link

    echo "signup_success";
    exit();
  }
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>fly|Signup</title>
    
    <link rel="stylesheet" href="style/signup.css">
    <script src="js/ajax.js"></script>
    <script src="js/main.js"></script>
    <script src="js/jquery-1.11.1.js"></script>
</head>
<script>

/*function restrict(elem){
  var tf = _(elem);
  var rx = new RegExp;
  if(elem == "email"){
    rx = /[' "]/gi;
  } else if(elem == "username"){
    rx = /[^a-z0-9]/gi;
  }
  tf.value = tf.value.replace(rx, "");
}
function emptyElement(x){
  _(x).innerHTML = "";
}*/

function checkusername(){
  var u = _("username").value;
  if(u != ""){
    _("unamestatus").innerHTML = 'checking ...';
    var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
          if(ajaxReturn(ajax) == true) {
              _("unamestatus").innerHTML = ajax.responseText;
          }
        }
        ajax.send("usernamecheck="+u);
  }
}
function checkemail(){
  var e = _("email").value.toString();
  if((e.match(/[@]/)==null)||(e.match(/[a-z]/)==null)||(e.match(/[.]/)==null)){
    _("emailstatus").innerHTML = '<strong style="color:#F00;"> Email is invalid</strong>';
  }
  else if (e != ""){
    _("emailstatus").innerHTML = 'checking ...';
    var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
          if(ajaxReturn(ajax) == true) {
              _("emailstatus").innerHTML = ajax.responseText;
          }
        }
        ajax.send("emailcheck="+e);
  }
}
function signup(){
  var u = _("username").value;
  var e = _("email").value;
  var p = _("pass").value;
  
  var status = _("status");
  if(u == "" || e == "" || p == ""){
    status.innerHTML = "Fill out all of the form data";
  } else if( $("#termsNote").data('clicked') != true){
    status.innerHTML = "Please view the terms of use";
  } 
  else {

      _("signupbtn").style.display = "none";
      status.innerHTML = 'please wait ...';
      var ajax = ajaxObj("POST", "signup.php");
          ajax.onreadystatechange = function() {
            if(ajaxReturn(ajax) == true) {
              if(ajax.responseText.trim() != "signup_success"){
                status.innerHTML = ajax.responseText;
                _("signupbtn").style.display = "block";
              } 
              else {
                    var errMsg="Hi "+u+", Thanks for Signing up. Please <a href='login.php'>Click here</a> to login in. ";
                    var ajax1 = ajaxObj("POST", "login.php");
                    ajax1.onreadystatechange = function() {
                        if(ajaxReturn(ajax1) == true) {
                          if(ajax1.responseText.trim() == "login_failed"){
                            window.scrollTo(0,0);
                            _("myform").innerHTML = errMsg;
                          } 
                          else if(ajax1.responseText.trim() == "sign_up"){
                            _("myform").innerHTML = errMsg;
                          } 
                          else if(ajax1.responseText.trim() == "no_match"){
                            _("myform").innerHTML = errMsg;
                          }

                          else {
                            window.location = "user.php?u="+ajax.responseText.trim();
                          }
                        }
                    }

                  ajax1.send("e="+e+"&p="+p);
                }
                //end of ajax call to login.php
              }
            }
          ajax.send("u="+u+"&e="+e+"&p="+p);
    }
}

$(document).ready(function(){
  $('#termsNote').click(function(){
    $(this).data('clicked', true);
    $('#terms').toggle();
  });
});

/* function addEvents(){
  _("elemID").addEventListener("click", func, false);
}
window.onload = addEvents; */
</script>

<body>
  <?php include_once("php_includes/template_pageTop.php"); ?>
  <div class = "form-body">
    <div class = "container">
      <div class="row">
        <div class=" col-md-4 col-md-offset-4 form-wrapper">
          <form class="form-signin" action = "" id="myform"  onsubmit="return false;">
            <h3 class="form-signin-heading">Signup to use <b>Fly</b></h3>

            <div class="form-group">
              <label for="Username">Username</label>
              <input type="text" class="form-control" id="username" placeholder="Username" required autofocus="" onblur= "checkusername();" >
              <span id="unamestatus"></span>
            </div>
            
            <div class="form-group">
              <label for="Email">Email address</label>
              <input type="email" class="form-control" id="email" placeholder="Email" required autofocus="" onblur= "checkemail();">
               <span id="emailstatus"></span>
            </div>

            <div class="form-group">
              <label for="Pass">Password</label>
              <input type="password" class="form-control" id="pass" placeholder="Password" required autofocus="">
            </div>

           
            <div  id="termsNote" style="margin-bottom: 10px; cursor:pointer;"><a>View the Terms Of Use</a></div>
            <div id="terms" style="display:none;">
              <h3>Terms Of Use</h3>
              <p>a. Must be a programmer or learning programming.</p>
              <p>b. Must be nice to other users.</p>
              <p>c. Must be respectful of others.</p>
            </div>
          
            <div id="status"></div>      
            
            <input class="btn btn-lg btn-primary btn-block" type="submit" name ="submit" id="signupbtn"
             value = "Signup" onclick="signup();">  
             <span id="status"></span>
        </form>
      </div>
      <div class="col-md-4"></div>
    </div>
  </div>
</div>
  

<?php include_once("php_includes/template_pageBottom.php"); ?>



  </body>
</html>









