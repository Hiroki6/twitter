<?php
require_once "common.php";

// 返信したときの返信テーブルの更新
function insert_reply($link,$tweet_id,$user_id,$reply_content,$date){
    $sql_reply_insert = "INSERT INTO reply_table(tweet_id,user_id,reply_content,date) VALUES(" .intval($tweet_id). "," .intval($user_id). ",'" .$reply_content. "','" .$date. "')";
    if(execute_db($link,$sql_reply_insert) !== TRUE){
        return FALSE;
    }
    return TRUE;
}

// そのツイートの返信内容取得
function get_reply($link,$tweet_id){
    $reply_information = array();
    $sql_reply_get = "SELECT tweet_id,reply_id,reply_table.user_id as user_id,user_name,img,reply_content,date FROM reply_table JOIN user_table ON reply_table.user_id=user_table.user_id WHERE tweet_id=" .intval($tweet_id). " ORDER BY date DESC";
    $reply_information = db_get_data($link,$sql_reply_get);
    return $reply_information;
}

// 返信テーブルのツイート削除
function delete_reply_table($link,$tweet_id){
    $sql_reply_delete = "DELETE FROM reply_table WHERE tweet_id=" .intval($tweet_id);
    if(execute_db($link,$sql_reply_delete) !== TRUE){
        return FALSE;
    }
    return TRUE;
}

// 返信の削除
function delete_reply($link,$reply_id){
    $sql_reply_delete = "DELETE FROM reply_table WHERE reply_id=" .intval($reply_id);
    if(execute_db($link,$sql_reply_delete) !== TRUE){
        return FALSE;
    }
    return TRUE;
}
