<?php 

error_reporting(E_ALL ^ E_NOTICE);
session_start();
include("../config/connect.php");
include("../includes/fetch_users_info.php");
include ("../includes/time_function.php");
if(!isset($_SESSION['Username'])){
    header("location: ../index");
}

$msgId = trim(filter_var(htmlentities($_GET['id'])),FILTER_SANITIZE_NUMBER_INT);




    
    $uid = filter_var(htmlentities($_GET['yid']),FILTER_SANITIZE_NUMBER_INT);
    $myid = filter_var(htmlentities($_GET['myid']),FILTER_SANITIZE_NUMBER_INT);
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
    $executer = $insertM->execute();
    // if message sent successfully do nothing, If not give me an error
    
    if ($executer) {
    //echo '<script>alert(boujour);</script>';
        header("location:../chatbox.php?id=".$_GET['yid']."");
    }else{
        echo "error";
    }
    
    
  
    ?>
