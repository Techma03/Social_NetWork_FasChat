<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("config/connect.php");
include("includes/fetch_users_info.php");
include ("includes/time_function.php");
if(!isset($_SESSION['Username'])){
    header("location: index");
}

$msgId = trim(filter_var(htmlentities($_GET['id'])),FILTER_SANITIZE_NUMBER_INT);
?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title><? echo lang('messages'); ?> | FasChat</title>
    <meta charset="UTF-8">
    <meta name="description" content="FasChat is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="Notifications,social network,social media,FasChat,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">

	<meta name="viewport"content="width=device-width, target-densitydpi=device-dpi"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "includes/head_imports_main.php";?>
</head>
<body style="max-width:100%;max-height:20px;">
<!--=============================[ NavBar ]========================================-->

<?php include "includes/navbar_main.php"; ?>

<!--=============================[ Div_Container ]========================================-->
<div class="messages_container">

    <div style="text-align: <? echo lang('textAlign'); ?>">
    	<div class="messages" style="min-width:140px;max-width:700px;height:410px;">
    		<div class="messages_col1" style="display:none;"  >
    		<div class="mCol1_title">
    		<input type="text" class="m_contacts_search" id="mU_search" name="mU_search" placeholder="<? echo lang('search'); ?>" />
    		</div>
    		<div id="m_contacts" class="scrollbar" style="position: absolute; top: 0; right: 0; left: 0; bottom: 0; margin-top: 50px; overflow: auto;">
                <p class="m_contacts_title"><? echo lang('requests'); ?></p>
                <div id="m_contacts_requests">
                    <div style="text-align: center; padding: 15px;"><img src="<? echo $dircheckPath; ?>imgs/loading_video.gif"></div>
                </div>
                <br>
                <p class="m_contacts_title" style="border-top: 1px solid #d0d4d8;"><? echo lang('friends'); ?></p>
                <div id="m_contacts_friends">
                    <div style="text-align: center; padding: 15px;"><img src="<? echo $dircheckPath; ?>imgs/loading_video.gif"></div>
                </div>
    		</div>
    		<div id="m_contacts_search" class="scrollbar" style="position: absolute; top: 0; right: 0; left: 0; bottom: 0; margin-top: 50px; overflow: auto;"></div>
    		</div>
    		<div class="messages_col2" >
    		<div class="mCol2_title" style="padding:3px;" data-user="0">
    			<? echo lang('messages'); ?>
    		</div>
    		<div class="mCol2_msgs scrollbar">
			<?php 
				
				$uid = filter_var(htmlentities($_GET['id']),FILTER_SANITIZE_NUMBER_INT);
				// m_seen set to seen
				//$id = $_GET['id'];
				$seen = "1";
$seenUpdate = $conn->prepare("UPDATE messages SET m_seen = :seen WHERE m_from=:uid AND m_to=:myid");
$seenUpdate->bindParam(':seen',$seen,PDO::PARAM_INT);
$seenUpdate->bindParam(':myid',$myid,PDO::PARAM_INT);
$seenUpdate->bindParam(':uid',$uid,PDO::PARAM_INT);
$seenUpdate->execute();
// select messages and fetch
$getMsgs = $conn->prepare("SELECT * FROM messages WHERE (m_from = :myid AND m_to = :uid) OR (m_from = :uid AND m_to = :myid)");
$getMsgs->bindParam(':myid',$myid,PDO::PARAM_INT);
$getMsgs->bindParam(':uid',$uid,PDO::PARAM_INT);
$getMsgs->execute();
$getMsgsCout = $getMsgs->rowCount();
// if selected messages more than zero [0] do this, If not >> do [else] code
if ($getMsgsCout > 0) {
while ($msgs_row = $getMsgs->fetch(PDO::FETCH_ASSOC)) {
	$toUserQuery = $conn->prepare("SELECT Userphoto FROM signup WHERE id = :uid");
	$toUserQuery->bindParam(':uid',$uid,PDO::PARAM_INT);
	$toUserQuery->execute();
	// get msg user into
	while ($toUser = $toUserQuery->fetch(PDO::FETCH_ASSOC)) {
		$userPhoto = $toUser['Userphoto'];
	}
// set message style for me and the other user that I chatting with him
if ($msgs_row['m_from'] == $myid) {
	$m_msgU = "m_msgU2";
	$userDir = "rtl";
	$msgUserPhoto = "";
}else{
	$m_msgU = "m_msgU1";
	$userDir = "ltr";
	$msgUserPhoto = "<td style='position: relative; width: 30px;'><div class='m_msgUserImg'><img src='".$path."imgs/user_imgs/".$userPhoto."'></div></td>";
}
// set time variable
$mTime = time_ago($msgs_row['m_time']);
//setting up message 
$em_img_path = $path."imgs/emoticons/";
include ("includes/emoticons.php");
$message_body = str_replace($em_char,$em_img,$msgs_row['message']);
$hashtag_path = $path."hashtag/";
$hashtags_url = '/(\#)([x00-\xFF]+[a-zA-Z0-9x00-\xFF_\w]+)/';
$message_body = preg_replace($hashtags_url, '<a href="'.$hashtag_path.'$2" title="#$2">#$2</a>', $message_body);
$message_body = nl2br($message_body);
// send result
echo "
<table class='m_msgTable' data-count='".$getMsgsCout."' style='direction:".$userDir.";'>
	<tr>
		".$msgUserPhoto."
		<td>
			<div class='".$m_msgU."' style='direction:".lang('dir').";' title='".date("d M, Y",$msgs_row['m_time'])."'>
			<p style='margin:0;display: inline;'>".$message_body."</p><sub style='font-size: 11px; margin:0px 8px; font-weight: bold;display: inline-block;'>".$mTime."</sub>
			</div>
		</td>
	</tr>
</table>
";
}
}else{
// [else] code .. this mean that there are not messages to show
echo  "
<p class='selectToChat'>
	".lang('emptyChat')."
</p>
";
}


			?>
			</div>
			
			<div id="m_userSeen" style="display:none;padding: 0px 8px; color: #545454; font-size: 12px;text-align: right;"><span class="fa fa-check"></span> seen</div>
			<div id="m_userTyping" class="m_msgU1" style="display:none;margin: 8px;margin-bottom: 15px;"><img src="<? echo $dircheckPath; ?>/imgs/typing.gif" style=" width: 30px; "></div>
			<div id="m_messages_loading" style='display:none;text-align: center; padding: 15px;'><img src='<? echo $dircheckPath; ?>imgs/loading_video.gif'></div>
    		</div>
		
    		<div class="m_SendField_box">
    			<div class="m_SendField">
    				<textarea dir="auto" maxlength="1538" name="msg" id="mSendField" placeholder="<? echo lang('write_a_message'); ?>"></textarea>
					<span style="margin-top:-42px;<? echo lang('float2'); ?>:0px;<? echo lang('float'); ?>:auto;" class="fa fa-smile-o m_SendField_span" onclick="mEmojiBtn()">
					<button  name="envoi" id="mSendField" type="submit">Send</button></span>
					
					<div id="emBox" data-emtog="0" style="<? echo lang('float2'); ?>:0px;bottom: 50px;top: auto;" class="emoticonsBox"></div>
					
					
					
				</div><span>
    			</div>
    		</div>
			<?php 

		if(isset($_POST['envoi'])){
			if(!empty($_POST['msg']) ){
				$uid = filter_var(htmlentities($_GET['id']),FILTER_SANITIZE_NUMBER_INT);
			$msg = trim(filter_var(htmlentities($_POST['msg']),FILTER_SANITIZE_STRING));
			$mid = rand(0,999999999)+time();
			$mTime = time();
			$mSeen="0";
			$insertM = $conn->prepare("INSERT INTO messages (m_id,message,m_from,m_to,m_time,m_seen) VALUES (:mid,:msg,:myid,:uid,:mTime,:mSeen)");
			$insertM->bindParam(':mid',$mid,PDO::PARAM_INT);
			$insertM->bindParam(':msg',$msg,PDO::PARAM_STR);
			$insertM->bindParam(':myid',$myid,PDO::PARAM_INT);
			$insertM->bindParam(':uid',$uid,PDO::PARAM_INT);
			$insertM->bindParam(':mTime',$mTime,PDO::PARAM_INT);
			$insertM->bindParam(':mSeen',$mSeen,PDO::PARAM_INT);
			$insertM->execute();
			// if message sent successfully do nothing, If not give me an error
			
			if ($insertM) {
			//echo '<script>alert(boujour);</script>';
			echo "error";
			}else{
				echo "error";
			}
			}
			
		}
			
			?>
    		<div class="messages_col3" style="display:none" >
    		<div class="mCol3_title">
    			<? echo lang('user_profile'); ?>
    		</div>
		
    		<div class="mCol3_userInfo">
    			<div style="position: relative; ">
    			<div class="mCol3_userInfo_avatar">
    			</div>
    			<div class="mCol3_userActive" style="background: #ccc;<? echo lang('float2'); ?>:55%;"></div>
    			</div>
    			<h4 style="text-align: center;"><div style="width: 60%; height: 10px; background: rgba(217, 221, 224, 0.55); margin: auto;"></div></h4>
    			<p style="text-align:center;margin: 0px;color: gray"><div style="width: 40%; height: 10px; background: rgba(217, 221, 224, 0.55); margin: auto;"></div></p>
    		</div>
    		<div class="mCol3_bio" style="text-align:<? echo lang('textAlign'); ?>;">
    			<div style="width: 80%; height: 10px; background: rgba(217, 221, 224, 0.55);"></div>
    			<div style="width: 60%; height: 10px; background: rgba(217, 221, 224, 0.55);margin-top: 8px;"></div>
    		</div>
    		</div>
		
			
    	</div>
		
	
<!--===============================[ End ]==========================================-->
<?php include "../includes/endJScodes.php"; ?>
<script type="text/javascript">
// on click on any user in contacts do this
$('#m_contacts').on("click",".mC_userLink",function(){
	$('.mCol2_title').attr("data-user",$(this).attr('data-muid'));
	mUserProfile($(this).attr('data-muid'),"click");
	mFetchMsgs($(this).attr('data-muid'),"click");
});
// on click on any user in searched contacts do this
$('#m_contacts_search').on("click",".mC_userLink",function(){
	$('.mCol2_title').attr("data-user",$(this).attr('data-muid'));
	mUserProfile($(this).attr('data-muid'),"click");
	mFetchMsgs($(this).attr('data-muid'),"click");
});
// on send text field (textarea) keypress do this
$('#mSendField').keypress(function (e) {
    if (e.keyCode == 13) {
    	// on [shift + enter] pressed do this
        if (e.shiftKey) {
            return true;
        }
        // on enter button pressed do this
        mSendField($('.mCol2_title').attr('data-user'));
        mRemoveTyping($('.mCol2_title').attr('data-user'));
        this.style.height = '40px';
        $('.mCol2_msgs').css({'bottom':this.style.height});
        return false;
    }
});
// auto hight for send text filed (textarea) code
$('#mSendField').each(function () {
  this.setAttribute('style', 'padding-<? echo lang('float2'); ?>:38px;padding-<? echo lang('float'); ?>:8px;height:40px;overflow-y:hidden;text-align:'+"<?php echo lang('textAlign'); ?>"+';');
}).on('input', function () {
  this.style.height = '40px';
  this.style.height = (this.scrollHeight) + 'px';
  $('.mCol2_msgs').css({'bottom':this.style.height});
});
// on search contacts field [key up] do this
$('#mU_search').keyup(function(){
    mSearchUser();
});
// load contacts on page load
mLoadUsers();
// refresh contacts details every 5 sec
setInterval(mLoadUsers, 5000);
// check if user selected do code in [else] or not do code in first of [if] statement
function getIn2Sec(){
if ($('.mCol2_title').attr('data-user') == "0") {	
}else{
	mUserProfile($('.mCol2_title').attr('data-user'),"timer");
	mFetchMsgs($('.mCol2_title').attr('data-user'),"timer");
}
}
// refresh [getIn2Sec] function ^^^
setInterval(getIn2Sec, 2000);
// typing a message from a user [typing codes]
var lastTypedTime = new Date(0);
function mCheckTyping() {
    if (!$('#mSendField').is(':focus') || $('#mSendField').val() == '' || new Date().getTime() - lastTypedTime.getTime() > 5000) {
        mRemoveTyping($('.mCol2_title').attr('data-user'));
    } else {
        mSetTyping($('.mCol2_title').attr('data-user'));
    }
}
setInterval(mCheckTyping, 100);
$('#mSendField').keypress(function(){lastTypedTime = new Date();});
$('#mSendField').blur(mCheckTyping);
</script>
</body>
</html>