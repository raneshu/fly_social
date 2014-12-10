
<?php
$status_ui = "";
$statuslist = "";
if($isOwner == "yes"){
	$status_ui = '<textarea style="width:100%; border-radius:5px;" id="statustext" onkeyup="statusMax(this,140)" placeholder="What&#39;s new with you '.$u.'?"></textarea>';
	
	$status_ui .= '<input class="btn btn-primary pull-right" id="statusBtn" onclick="postToStatus(\'status_post\',\'a\',\''.$u.'\',\'statustext\')" value="Post"></input>';
} 
?><?php 
$sql = "SELECT * FROM status WHERE account_name='$u' AND type='a' OR account_name='$u' AND type='c' ORDER BY postdate DESC LIMIT 3";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$statusid = $row["id"];
	$account_name = $row["account_name"];//$u- owner of profile
	$author = $row["author"];
	$postdate = $row["postdate"];
	//below are validations on the data
	$data = $row["data"];
	$data = nl2br($data);
	$data = str_replace("&amp;","&",$data);
	$data = stripslashes($data);
	$statusDeleteButton = '';
	if($author == $log_username || $account_name == $log_username ){
		//$statusDeleteButton = '<span id="sdb_'.$statusid.'"><a href="#" onclick="return false;" onmousedown="deleteStatus(\''.$statusid.'\',\'status_'.$statusid.'\');" title="DELETE THIS STATUS AND ITS REPLIES">delete</a></span> ';
		$statusDeleteButton = '<span id="sdb_'.$statusid.'"><small><a href="#" onclick="return false;" onmousedown="deleteStatus(\''.$statusid.'\',\'status_'.$statusid.'\');" title="DELETE THIS STATUS">delete</a></small>&nbsp&nbsp&nbsp</span>';
	}
	// GATHER UP ANY STATUS REPLIES
	/*$status_replies = "";
	$query_replies = mysqli_query($db_conx, "SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate ASC");
	$replynumrows = mysqli_num_rows($query_replies);
    if($replynumrows > 0){
        while ($row2 = mysqli_fetch_array($query_replies, MYSQLI_ASSOC)) {
			$statusreplyid = $row2["id"];
			$replyauthor = $row2["author"];
			$replydata = $row2["data"];
			$replydata = nl2br($replydata);
			$replypostdate = $row2["postdate"];
			$replydata = str_replace("&amp;","&",$replydata);
			$replydata = stripslashes($replydata);
			$replyDeleteButton = '';
			if($replyauthor == $log_username || $account_name == $log_username ){
				//delete reply
				$replyDeleteButton = '<span id="srdb_'.$statusreplyid.'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''.$statusreplyid.'\',\'reply_'.$statusreplyid.'\');" title="DELETE THIS COMMENT">remove reply</a></span>';
			}
			$status_replies .= '<div id="reply_'.$statusreplyid.'" class="reply_boxes"><div><b>Reply by <a href="user.php?u='.$replyauthor.'">'.$replyauthor.'</a> '.$replypostdate.':</b> '.$replyDeleteButton.'<br />'.$replydata.'</div></div>';
        }
    }*/
    //previous statuses
	//$statuslist .= '<div id="status_'.$statusid.'" class="status_boxes"><div>'.$data.'</div><br/><b>Posted by <a href="user.php?u='.$author.'">'.$author.'</a> '.$postdate.':</b> '.$statusDeleteButton.' <br />'.$status_replies.'</div>';
	$statuslist .='<div id="status_'.$statusid.'" style="margin-top:20px; border:1px solid #767676; border-radius:5px; padding:5px"><div style="padding-bottom:25px;background-color:white;">'.$data.'</div><small>Posted by <a href="user.php?u='.$author.'">'.$author.'</a> '.$postdate.'&nbsp&nbsp&nbsp'.$statusDeleteButton.'</small></div>';

	/*if($isFriend == true || $log_username == $u){//privilege to reply if friend or account owner
		$statuslist.='<div>';
	    $statuslist .= '<textarea id="replytext_'.$statusid.'" class="replytext" onkeyup="statusMax(this,140)" placeholder="reply here"></textarea>';
	    $statuslist .= '<input class="btn btn-primary btn-sm pull-right" type="submit" name="submit" id="replyBtn_'.$statusid.'" onclick="replyToStatus('.$statusid.',\''.$u.'\',\'replytext_'.$statusid.'\',this)" value="Reply">';	
		$statuslist.='</div>';
	}*/
}
?>


<script>
function postToStatus(action,type,user,ta){
	var data = _(ta).value;
	if(data == ""){
		 _('statustext').setAttribute("placeholder", "write something first");
		return false;
	}
	_("statusBtn").disabled = true;
	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText.trim().split("|");
			if(datArray[0] == "post_ok"){
				var sid = datArray[1];
				data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				var currentHTML = _("statusarea").innerHTML;
				_("statusarea").innerHTML = '<div id="status_'+sid+'" style="margin-top:20px; border:1px solid blue; border-radius:5px; padding:5px"><div style="padding-bottom:25px;background-color:white;">' + data +'</div><small>Posted by you just now</small>&nbsp&nbsp&nbsp&nbsp<span id="sdb_'+sid+'"><small><a href="#" onclick="return false;" onmousedown="deleteStatus(\''+sid+'\',\'status_'+sid+'\');" title="DELETE THIS STATUS ">delete</a></small>&nbsp&nbsp&nbsp</span></div>';
				_("statusarea").innerHTML += currentHTML;
					_("statusBtn").disabled = false;
				_(ta).value = "";
			} 
			else {
				alert(ajax.responseText.trim());
			}
		}
	}
	ajax.send("action="+action+"&type="+type+"&user="+user+"&data="+data);
}

function deleteStatus(statusid,statusbox){
	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText.trim() == "delete_ok"){
				_(statusbox).style.display = 'none';
			
			} else {
				alert(ajax.responseText.trim());
			}
		}
	}
	ajax.send("action=delete_status&statusid="+statusid);
}

function statusMax(field, maxlimit) {
	if (field.value.length > maxlimit){
		alert(maxlimit+" maximum character limit reached");
		field.value = field.value.substring(0, maxlimit);
	}
}


</script>
<div id="statusui" style="margin-bottom: 50px;">
  <?php echo $status_ui; ?>
</div>
<div id="statusarea">
  <?php echo $statuslist; ?>
</div>
