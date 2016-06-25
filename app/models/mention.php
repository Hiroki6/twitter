<?php
require_once "common.php";

// メンションテーブルに挿入
function insert_mention($link,$user_id,$tweet_id,$mention_id){
    $sql_mention_insert = "INSERT INTO mention_table(tweet_id,user_id,mention_id) VALUES(" .intval($tweet_id). "," .intval($user_id). "," .intval($mention_id). ")";
    if(execute_db($link,$sql_mention_insert) !== TRUE){
        return FALSE;
    }
    return TRUE;
}

// メンションテーブルのツイート削除
function delete_mention_table($link,$tweet_id){
    $sql_mention_delete = "DELETE FROM mention_table WHERE tweet_id=" .intval($tweet_id);
    if(execute_db($link,$sql_mention_delete) !== TRUE){
        return FALSE;
    }
    return TRUE;
}
