<?php
include_once("php_includes/check_login_status.php");

$u = "";
$sex = "Male";
$userlevel = "";
$profile_pic = "";
$profile_pic_btn = "";
$avatar_form = "";
$country = "";
$joindate = "";
$lastsession = "";

if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: index.php");
    exit();	
}

$sql = "SELECT * FROM users WHERE username='$u' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);

$numrows = mysqli_num_rows($user_query);
if($numrows<1){
	header("location:filenotfound.php");
}
////////////////

$isOwner = "no";
if($u == $log_username && $user_ok == true){
	$isOwner = "yes";
	$profile_pic_btn = '<a href="#" onclick="return false;" onmousedown="toggleElement(\'avatar_form\')">Toggle Avatar Form</a>';
	$avatar_form  = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$avatar_form .=   '<h4>Change your avatar</h4>';
	$avatar_form .=   '<input type="file" name="avatar" required>';
	$avatar_form .=   '<p><input type="submit" value="Upload"></p>';
	$avatar_form .= '</form>';
}

while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$gender = $row["gender"];
	$country = $row["country"];
	$userlevel = $row["userlevel"];
	$avatar = $row["avatar"];
	$signup = $row["signup"];
	$lastlogin = $row["lastlogin"];
	$joindate = strftime("%b %d, %Y", strtotime($signup));
	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
}
if($gender == "f"){
	$sex = "Female";
}
$profile_pic = '<img src="user/'.$u.'/'.$avatar.'" alt="'.$u.'"style="height:250px;" class="img-square img-thumbnail img-responsive">';
if($avatar == NULL){
	$profile_pic = '<img src="../source_imagery/avatardefault.jpg" alt="'.$user1.'" style="height:250px;" class="img-square img-thumbnail img-responsive"  >';
}
?><?php
$isFriend = false;
$ownerBlockViewer = false;
$viewerBlockOwner = false;
if($u != $log_username && $user_ok == true){
	$friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0){
        $isFriend = true;
    }
	$block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0){
        $ownerBlockViewer = true;
    }
	$block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $block_check2)) > 0){
        $viewerBlockOwner = true;
    }
    $request_check1 = "SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='0' LIMIT 1";
	$query = mysqli_query($db_conx, $request_check1);//if unaccepted request exists from you
	if(mysqli_num_rows($query) > 0){
		$reqSent = true;
	}
	$request_check2 = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND user2='$log_username' AND accepted='0' LIMIT 1";
	$query = mysqli_query($db_conx, $request_check1);//if unaccepted request exists from you
	if(mysqli_num_rows($query) > 0){
		$reqSent = true;
	}

}
?><?php 
if($u == $log_username || $user_ok!=true){
	$friend_button = ''; 

	$block_button = '';
}
else{
	$friend_button = '<input class="btn btn-success btn-block" type="submit" name="submit" id="friendbtn" value="Friend Request Sent" disabled>'; 

	$block_button = '<input class="btn btn-primary btn-block" type="submit" onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')" name="submit" id="blockBtn" value="Block User" >';

	if($isFriend == true){
		$friend_button = '<input class="btn btn-primary btn-block" onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')" type="submit" name="submit" id="friendbtn" value="Unfriend">';
	} 
 
	else if($user_ok == true && $u != $log_username && $ownerBlockViewer == false && $reqSent == false){
		$friend_button =  '<input class="btn btn-primary btn-block" onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')" type="submit" name="submit" id="friendbtn" value="Send friend Request">';
	}

	else if($viewerBlockOwner == true){
		$block_button =  '<input class="btn btn-primary btn-block" onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')" type="submit" name="submit" id="blockbtn" value="Unblock">';
		$friend_button = ''; 
	} 
	
}

?><?php
$friendsHTML = '';
$friends_view_all_link = '';
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u'";

$query = mysqli_query($db_conx, $sql);
$query_count = mysqli_fetch_row($query);
$friend_count = $query_count[0];
if($friend_count < 1){
	$friendsHTML = $u." has no friends yet";//if the profile owner has no friends yet
} else {
	$max = 18;
	$all_friends = array();
	$sql = "SELECT user1 FROM friends WHERE user2='$u' ORDER BY RAND() LIMIT $max";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user1"]);
	}
	$sql = "SELECT user2 FROM friends WHERE user1='$u' ORDER BY RAND() LIMIT $max";
	//research makiing joint queries
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user2"]);
	}
	$friendArrayCount = count($all_friends);
	if($friendArrayCount > $max){
		array_splice($all_friends, $max);
	}
	if($friend_count > $max){
		$friends_view_all_link = '<a href="view_friends.php?u='.$u.'">view all</a>';
	}
	$orLogic = '';
	foreach($all_friends as $key => $user){
			$orLogic .= "username='$user' OR ";
	}
	$orLogic = chop($orLogic, "OR ");
	$sql = "SELECT username, avatar FROM users WHERE $orLogic";
	$query = mysqli_query($db_conx, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$friend_username = $row["username"];
		$friend_avatar = $row["avatar"];
		if($friend_avatar != ""){
			$friend_pic = 'user/'.$friend_username.'/'.$friend_avatar.'';
		} else {
			$friend_pic = '../source_imagery/avatardefault.jpg';
		}
		$friendsHTML .= '<a href="user.php?u='.$friend_username.'"><img style="height: 60px" class="img-square img-thumbnail img-responsive" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'"> </a>';
	}
}
?><?php 
$coverpic = "";
$sql = "SELECT filename FROM photos WHERE user='$u' ORDER BY RAND() LIMIT 1";
$query = mysqli_query($db_conx, $sql);
if(mysqli_num_rows($query) > 0){
	$row = mysqli_fetch_row($query);
	$filename = $row[0];
	$coverpic = '<img src="user/'.$u.'/'.$filename.'" alt="pic">';
}
?>

<!-- -------------------------------------------------------------------------------------------------------------- -->


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $u; ?></title>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<!--<link rel="stylesheet" href="status_system.css">-->

<script type="text/javascript">
	function friendToggle(type,user,elem){
	
	_(elem).innerHTML = 'please wait ...';
	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText.trim() == "friend_request_sent"){
				_(elem).innerHTML = '<input class="btn btn-success btn-block" type="submit" name="submit" id="friendbtn" value="Friend Request Sent" disabled>';
			} else if(ajax.responseText.trim() == "unfriend_ok"){
				_(elem).innerHTML = '<input class="btn btn-primary btn-block" onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')" type="submit" name="submit" id="friendbtn" value="Request as friend">';
																	 
			} else {
				_(elem).innerHTML = '<h3>'+ ajax.responseText.trim() + '<h3>';
			}
		}
	}
	ajax.send("type="+type+"&user="+user);
}
function blockToggle(type,blockee,elem){
	
	var elem = document.getElementById(elem);
	elem.innerHTML = 'please wait ...';
	var ajax = ajaxObj("POST", "php_parsers/block_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText.trim() == "blocked_ok"){
				elem.innerHTML = '<input class="btn btn-primary btn-block" onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\')" type="submit" name="submit" id="blockbtn" value="Unblock">';
			} else if(ajax.responseText.trim() == "unblocked_ok"){
				elem.innerHTML = '<input class="btn btn-primary btn-block" onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\')" type="submit" name="submit" id="blockbtn" value="block">';
			} else {
				elem.innerHTML = '<h3>'+ ajax.responseText.trim() + '<h3>';
			}
		}
		
	}
	ajax.send("type="+type+"&blockee="+blockee);
}
</script>
</head>
<body>
	<?php include_once("php_includes/template_pageTop.php"); ?>
	<!--<link rel="stylesheet" href="style/user.css">-->
	
		  <!-- <div id="profile_pic_box" ><?php //echo $profile_pic_btn; ?><?php// echo $avatar_form; ?><?php //echo $profile_pic; ?></div>-->
		 
		<div class = "profile" style="overflow:hidden;">
			<div class="container col-xs-12 col-sm-12" style="padding:0px; margin:0px;">
			  <div class="row">

			    <div class="col-md-12 col-xs-12">
			      	<div class="well panel panel-default" style="min-height:500px;margin-bottom:0px;border-radius: 0 !important;">
			        	<div class="panel-body">
			            	<div class="row">

					            <div class="col-xs-12 col-sm-4 col-md-4">
					            	<?php echo $profile_pic ?>

						            <div id="friendBtn" style="margin-top:10px; margin-bottom:10px;"><?php echo $friend_button; ?></div>
									<div id="blockBtn"><?php echo $block_button; ?></div>
									 <h3><b>
							            <?php
							             if($friend_count> 0){ 
								             echo "<p>Friends</p>";
								             echo $friendsHTML; 
								             echo $friends_view_all_link;
							                };
						              ?></b></h3>

					            </div>
					            <!--/col--> 
					            <div class="col-xs-12 col-sm-8 col-md-8">
					            	<h2><?php echo $u; ?></h2>
					            	
					            	
							        <p><strong>About: </strong> Web Designer / UI Expert. </p>
							        <p><strong>Last Visit: </strong><?php echo $lastsession; ?></p>
							        <p><strong>Skills: </strong>
							        <span class="label label-info tags">html5</span> 
							        <span class="label label-info tags">css3</span>
							        <span class="label label-info tags">jquery</span>
							        <span class="label label-info tags">bootstrap3</span>
							        </p>

							       	<?php include_once("template_status.php"); ?>
									

								    
								
									
								</div>
							</div>
							<!--<div class="row">
								<div id="photo_showcase" onclick="window.location = 'photos.php?u=<?php echo $u; ?>';" title="view <?php echo $u; ?>&#39;s photo galleries">
							    <?php //echo $coverpic; ?> 
							  </div>
							</div> -->
				    	</div>
				    </div>
				</div>
				
	            

		    </div><!--/row-->
			
		</div><!--/container-->
	</div>
		<?php include_once("php_includes/template_pageBottom.php"); ?>
	
</body>
</html>