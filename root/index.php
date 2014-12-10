<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Fly</title>
 <link rel="stylesheet" href="style/bootstrap.css">

</head>
<body>


<?php include_once("php_includes/template_pageTop.php"); ?>

 <div class="jumbotron">
      <div class="container">
        <h1>Connect with Other Programmers</h1>
        <p>&lt;Share your questions, projects, and ideas&gt;</p>
        <a href="signup.php" class="btn btn-primary btn-lg">Sign Up</a>
      </div>
    </div> 
    <div class = "neighborhood-guides">
        <div class = "container">
            <h2 style="text-align:center">Hackathons</h2>
            <p style="text-align:center">Meet other programmers at monthly hackathons</p>
            <div class = "row">
                <div class ="col-md-4">
                    <div class = "thumbnail">
                        <img src ="../source_imagery/hackathon1.jpg">
                    </div>
                </div>
                <div class ="col-md-4">
                    <div class = "thumbnail">
                        <img src ="../source_imagery/hackathon2.jpg">
                    </div>
                   
                </div>
                <div class ="col-md-4">
                    <div class = "thumbnail">
                        <img src ="../source_imagery/hackathon3.jpg">
                    </div>
                
                </div>
            </div>
        </div>
    </div>

    <div class="learn-more">
    <div class="container">
    <div class="row">
        <div class = "col-md-4">
      <h3>Projects</h3>
      <p>Share your projects and get feedback</p>
      <p><a href="#">More on sharing your projects</a></p>
        </div>
      <div class = "col-md-4">
      <h3>Ideas</h3>
      <p>Share your ideas with other programmers and creaters</p>
      <p><a href="#">Learn more about sharing your ideas on Fly</a></p>
      </div>
      <div class = "col-md-4">
      <h3>Hackathons</h3>
      <p>Attend monthly hackathons, join other hackers, and win prizes</p>
      <p><a href="#">Learn about montly hackathons in your area</a></p>
      </div>
      </div>
    </div>
  </div>


<?php include_once("php_includes/template_pageBottom.php"); 

?>
</body>
</html>
<?php include_once("php_includes/check_login_status.php"); 

 if($user_ok == true) {
   header("location: user.php?u=".$_SESSION["username"]);
  } 
?>



