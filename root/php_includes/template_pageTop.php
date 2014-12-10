<?php
// It is important for any file that includes this file, to have
// check_login_status.php included at its very top.

    $envelope = "";
    $loginLink = "<a href='login.php'>Login</a>";
    $signupLink ="<a href='signup.php'>Signup</a>";
    $findFriends ="";
    
    if($user_ok == true) {
      $sql = "SELECT notescheck FROM users WHERE username='$log_username' LIMIT 1";
      $query = mysqli_query($db_conx, $sql);
      $row = mysqli_fetch_row($query);
      $notescheck = $row[0];
      $sql = "SELECT id FROM notifications WHERE username='$log_username' AND date_time > '$notescheck' LIMIT 1";
      $query = mysqli_query($db_conx, $sql);
      $numrows = mysqli_num_rows($query);
        if ($numrows == 0) {
          $envelope = '<a href="notifications.php" title="Your notifications and friend requests"><img src="../source_imagery/notification_default.jpg" width="22" height="12" alt="Notes"></a>';
        } 
        else {
          $envelope = '<a href="notifications.php" title="You have new notifications"><img src="../source_imagery/note_flash.gif" width="22" height="12" alt="Notes"></a>';
        }
        $findFriends ="<a href='search.php' style='background-color:#cde7f0;border-radius:5px;'>Find Friends</a>"; 
        $loginLink = "<a href='user.php?u=$log_username'>".$log_username."</a>";
        $signupLink ="<a href='logout.php'>Logout</a>";
    }
?>
<!--
<header>
<div id="pageTop">
  <div id="pageTopWrap">

    <div id="pageTopLogo">
      <a href="index.php">
        <img src="../source_imagery/logo.jpg" alt="logo" title="This is the logo">
      </a>
    </div>

    <div id="pageTopRest">
      <div id="menu1">
        <div>
          <?php //echo $envelope; ?> &nbsp; &nbsp; <?php //echo $loginLink; ?>
        </div>
      </div>
    </div>

  </div>
</div>
</header> -->
<!DOCTYPE html>
<html>

  <head>
    <!--<link href="http://s3.amazonaws.com/codecademy-content/courses/ltp/css/shift.css" rel="stylesheet">-->
  <link rel="stylesheet" href="style/bootstrap.css">
  <script src="js/jquery-1.11.1.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="style/style.css">
    
  </head>

  <body>
    
    <nav role="navigation" class="navbar navbar-default">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
          
            <a href="index.php"><img src = "../source_imagery/logo_bottom1.jpg"></a>
            
            
        </div>
        <!-- Collection of nav links and other content for toggling -->
        <div id="navbarCollapse" class="collapse navbar-collapse">
            
            <ul class="nav navbar-nav navbar-right">
                <li><?php echo $findFriends ?></li>
                <li><?php echo $envelope ?></li>
                <li><?php echo $loginLink ?></li>
                <li><?php echo $signupLink ?></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </div>
    </nav>

   
  </body>
</html>

