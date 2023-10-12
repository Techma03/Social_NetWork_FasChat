<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("config/connect.php");
include("includes/fetch_users_info.php");
include ("includes/time_function.php");
if(!isset($_SESSION['Username'])){
    header("location: index");
}
?>
<html dir="<?php echo lang('html_dir'); ?>">
<head>
    <title>Notifications | FasChat</title>
    <meta charset="UTF-8">
    <meta name="description" content="FasChat is a social network platform helps you meet new friends and stay connected with your family and with who you are interested anytime anywhere.">
    <meta name="keywords" content="Notifications,social network,social media,FasChat,meet,free platform">
    <meta name="author" content="Munaf Aqeel Mahdi">
    <meta name="viewport"content="width=device-width, target-densitydpi=device-dpi"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "includes/head_imports_main.php";?>
</head>
<style>
@media(max-width:1024px){
   
    .not{
       
    }
    .fetchNotifications{
        max-width:600px;
        min-width:300px;
        position:relative;
        left:-13px;
    }
}
</style>
<body onload="fetchNotifications()">
<!--=============================[ NavBar ]========================================-->
<?php include "includes/navbar_main.php"; ?>
<!--=============================[ Div_Container ]========================================-->

<div class="main_container" >

<div style="background-color:white;max-width:800px;min-width:100px;">


<?php



$what = htmlentities(htmlspecialchars($_POST['what']));
$path = htmlentities(htmlspecialchars($_POST['path']));
$load = htmlentities(htmlspecialchars($_POST['load']));
$myId = $_SESSION['id'];

$seen = "1";
$seenQ = $conn->prepare("UPDATE notifications SET seen=:seen WHERE for_id=:myId");
$seenQ->bindParam(':seen',$seen,PDO::PARAM_INT);
$seenQ->bindParam(':myId',$myId,PDO::PARAM_INT);
$seenQ->execute();

$notify_sql = "SELECT * FROM notifications WHERE for_id =:myId ORDER BY time DESC LIMIT :load,10";

$notify = $conn->prepare($notify_sql);
$notify->bindParam(':myId',$myId,PDO::PARAM_INT);
$notify->bindValue(':load', (int)trim($load), PDO::PARAM_INT);
$notify->execute();
$notifyCount = $notify->rowCount();
if ($notifyCount > 0) {
while ($n_row = $notify->fetch(PDO::FETCH_ASSOC)) {
	$notify_id = $n_row['n_id'];
	$notify_from_id = $n_row['from_id'];
	$notify_for_id = $n_row['for_id'];
	$notifyType_id= $n_row['notifyType_id'];
	$notifyType = $n_row['notifyType'];
	$notify_seen = $n_row['seen'];
	$notify_time = time_ago($n_row['time']);

$notify_from = $conn->prepare("SELECT Fullname,Username,Userphoto FROM signup WHERE id=:notify_from_id");
$notify_from->bindParam(':notify_from_id',$notify_from_id,PDO::PARAM_INT);
$notify_from->execute();
while ($from_id_row = $notify_from->fetch(PDO::FETCH_ASSOC)) {
	$fullname = $from_id_row['Fullname'];
	$userphoto = $from_id_row['Userphoto'];
	$username = $from_id_row['Username'];
}

$postBody = $conn->prepare("SELECT post_content FROM wpost WHERE post_id=:notifyType_id");
$postBody->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
$postBody->execute();
while ($row = $postBody->fetch(PDO::FETCH_ASSOC)) {
	$postContent = $row['post_content'];
}
if (strlen($postContent) > 70) {
	$getPCon = " : ".substr($postContent, 0,70)." ...";
}elseif (empty($postContent)) {
	$getPCon = "";
}else{
	$getPCon = " : ".$postContent;
}
echo "
<div id='sqresultItem'>
<a style=text-decoration:none; href='".$path."posts/post?pid=".$notifyType_id."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img  src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'><b>$fullname</b> ".lang('likeNotify_str')." <span style='color: #999;'>$getPCon</span>
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/1f49f.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";


echo "
<div id='sqresultItem'>
<a style=text-decoration:none; href='".$path."posts/post?pid=".$notifyType_id."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'><b>$fullname</b> ".lang('commmentNotify_str').".
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/1f5e8.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";

$postBody = $conn->prepare("SELECT post_content FROM wpost WHERE post_id=:notifyType_id");
$postBody->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
$postBody->execute();
while ($row = $postBody->fetch(PDO::FETCH_ASSOC)) {
	$postContent = $row['post_content'];
}
if (strlen($postContent) > 70) {
	$getPCon = " : ".substr($postContent, 0,70)." ...";
}elseif (empty($postContent)) {
	$getPCon = "";
}else{
	$getPCon = " : ".$postContent;
}
echo "
<div id='sqresultItem'>
<a style=text-decoration:none; href='".$path."posts/post?pid=".$notifyType_id."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'><b>$fullname</b> ".lang('shareNotify_str')." <span style='color: #999;'>$getPCon</span>
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/1f504.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";

$getUsername = $conn->prepare("SELECT Username FROM signup WHERE id=:notifyType_id");
$getUsername->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
$getUsername->execute();
while ($row = $getUsername->fetch(PDO::FETCH_ASSOC)) {
	$pUsername = $row['Username'];
}
echo "
<div id='sqresultItem'>
<a style=text-decoration:none; href='".$path."u/".$pUsername."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'>".lang('starNotify_str')." <b>$fullname</b>
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/2b50.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";

$getUsername = $conn->prepare("SELECT Username FROM signup WHERE id=:notifyType_id");
$getUsername->bindParam(':notifyType_id',$notifyType_id,PDO::PARAM_INT);
$getUsername->execute();
while ($row = $getUsername->fetch(PDO::FETCH_ASSOC)) {
	$pUsername = $row['Username'];
}
echo "
<div id='sqresultItem'>
<a style=text-decoration:none; href='".$path."u/".$pUsername."'>
<div style='display: inline-flex;width: 100%;'>
<div class='navbar_fetchBoxUser' style='border-radius:2px;'>
<img src='".$path."imgs/user_imgs/$userphoto' />
</div>
<p style='font-size:13px;'><b>$fullname</b> ".lang('followNotify_str')."
<span style='font-size: small;'></span><br>
<img src='".$path."imgs/main_icons/1f465.png' style='width:14px;height:14px;' /> <span style='font-size:11px;'>$notify_time</span></span> 
</p>
</div>
</a>
</div>";


}
}else{
	echo "Vous n'avez aucune Notification";
}

// =============================================================

$seen ="0";
$notifyCheck = $conn->prepare("SELECT seen FROM notifications WHERE for_id=:myId AND seen =:seen");
$notifyCheck->bindParam(':seen',$seen,PDO::PARAM_INT);
$notifyCheck->bindParam(':myId',$myId,PDO::PARAM_INT);
$notifyCheck->execute();
$notifyCheckCount = $notifyCheck->rowCount();
//echo $notifyCheckCount;


?>
    </div>
    <br><br>
    <div style="display: inline-flex;postion:relative;left:-10px;width:10px;" align="center" >
        <div style="position:relative;left:-15px;text-align: <?php echo lang('textAlign'); ?>">
            <div class="fetchNotifications" style="position:relative;left:2px; ">
                <div id="notificationsP_data" data-load="0"></div>
                <p style='width: 100%;border:none;display: none' id="notificationsP_loading" align='center'><img src='<?php echo $dircheckPath; ?>imgs/loading_video.gif' style='width:20px;box-shadow: none;height: 20px;'></p>
                <p id="notificationsP_noMore" style='display:none;color:#9a9a9a;font-size:14px;text-align:center;'><?php echo lang('no_notifications'); ?></p>
                <input type="hidden" id="notificationsP_load" value="0"> 
                <p id="notifi_loadmoreBtn" class="not" style="text-align: center;display: none;"><button style="width: 50%" class="blue_flat_btn"><?php echo lang('loadmore'); ?></button></p>
            </div>
        </div>
    </div>

</div>
<!--===============================[ End ]==========================================-->
<?php include("includes/footer.php");?>
<?php include "includes/endJScodes.php"; ?>
<script type="text/javascript">
    getNotifications('notificationsP');
$('#notifi_loadmoreBtn').click(function(){
    getNotifications2('notificationsP');
});
</script>
</body>
</html>