<?php
require_once "common.php";

// フォローしたときのfollow_tableへの挿入
function insert_follow($link,$user_id,$follow_id){
    $sql_follow_insert = "INSERT INTO follow_table(user_id,followed_id) VALUES(".intval($user_id). "," .intval($follow_id). ")";
    if(execute_db($link,$sql_follow_insert)){
        return TRUE;
    }
    return FALSE;
}
// フォロー解除したときのfollow_tableの削除
function follow_remove($link,$user_id,$follow_id){
    $sql_follow_remove = "DELETE FROM follow_table WHERE user_id=" .intval($user_id). " and followed_id=" .intval($follow_id);
    if(execute_db($link,$sql_follow_remove)){
        return TRUE;
    }
    return FALSE;
}

// フォローしている人の情報取得
function follow_information($link,$user_id){
    $follow_information = array();
    $sql_follow = "SELECT user_id ,img,user_name FROM user_table WHERE user_id IN (SELECT followed_id FROM follow_table WHERE user_id=" .intval($user_id). ")";
    $follow_information = db_get_data($link,$sql_follow);
    return $follow_information;
    
}
// フォロワーの人の情報取得
function follower_information($link,$user_id){
    $follower_information = array();
    $sql_follower = "SELECT user_id,img,user_name FROM user_table WHERE user_id IN (SELECT user_id FROM follow_table WHERE followed_id=" .intval($user_id). ")";
    $follower_information = db_get_data($link,$sql_follower);
    return $follower_information;
}

// フォローしていかどうかのチェック
// フォローしてたらtrue,フォローしていなかったらfalse
function check_follow($link,$login_id,$user_id){
    $follow_ids = array();
    $sql_follow = "SELECT followed_id FROM follow_table WHERE user_id=" .intval($login_id);
    $follow_ids = db_get_data($link,$sql_follow);
    foreach($follow_ids as $follow){
        if($user_id === intval($follow['followed_id'])){
            return TRUE;
        }
    }
    return FALSE;
}

// そのユーザーをすでにフォローしているかどうか(フォローしてたらtrue,してなかったらfalse)
function follow_check($link,$user_id,$follow_id){
    $follow_table = array();
    $sql_follow = "SELECT followed_id FROM follow_table WHERE user_id=" .intval($user_id);
    $follow_table = db_get_data($link,$sql_follow);
    foreach($follow_table as $follow){
        if($follow['followed_id'] === $follow_id){
            return TRUE;
        }
    }
    return FALSE;
}
