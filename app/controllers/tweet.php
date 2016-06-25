<?php
    require_once "../confs/const.php";
    require_once "../models/tweet.php";
    require_once "../models/reply.php";
    session_start();
    $link = get_db_connect();
    $method = get_request_method();
    $reply_information = array(); // すべての返信内容
    $tweet_information = array(); // ツイート内容
    $user_id = get_session_data('user_id');
    $error_message = array();
    $retweet_check = FALSE;
    $retweet_user = array();
    if(isset_session('user_id')){
        if($method === 'POST'){
            $tweet_id = get_post_data('tweet_id'); // そのツイートのID
            $sql_kind = get_post_data('sql_kind'); 
            $date = date('Y-m-d H:i:s');
            if(!check_exist($link,$tweet_id)){
                $error_message[] = "そのツイートは存在しません";
            }else{
                if($sql_kind === "reply"){
                    $reply_content = get_post_data('reply'); // 返信内容
                    if(!check_empty($reply_content)){
                        $error_message[] = "返信内容を記入してください";
                    }
                    if(!check_length($reply_content,140)){
                        $error_message[] = "返信内容は140文字以内で記入してください";
                    }
                    if(count($error_message) === 0){
                        if(!insert_reply($link,$tweet_id,$user_id,$reply_content,$date)){
                            $error_message[] = "reply_table:insertエラー";
                        }
                    }
                    close_db_connect($link);
                }else if($sql_kind === "reply_delete"){
                    $reply_id = get_post_data('reply_id');
                    if(!delete_reply($link,$reply_id)){
                        $error_message[] = "reply_table:deleteエラー";
                    }
                    close_db_connect($link);
                }else if($sql_kind === "retweet"){
                    if(check_retweet($link,$tweet_id,$user_id)){
                        $error_message[] = "すでにリツイートしてあります";
                    }else{
                        if(!insert_retweet($link,$tweet_id,$user_id,$date)){
                            $error_message[] = "insert_retweet_table:insertエラー";
                        }
                    }
                    close_db_connect($link);
                }else if($sql_kind === "retweet_remove"){
                    if(!check_retweet($link,$tweet_id,$user_id)){
                        $error_message[] = "すでにリツイートを解除してあります";
                    }else{
                        if(!remove_retweet($link,$tweet_id,$user_id)){
                            $error_message[] = "insert_retweet_table:deleteエラー";
                        }
                    }
                    close_db_connect($link);
                }
            }
        }
        if($method === 'GET'){
            $tweet_id = get_get_data('tweet_id');
        }
        $link = get_db_connect();
        if(check_exist($link,$tweet_id)){
            // ツイート内容取得
            $tweet_information = get_tweet($link,$tweet_id);
            // リツイートチェック
            $retweet_check = one_retweet_check($link,$tweet_id,$user_id);
            // リツイートされたユーザー情報
            $retweet_user = one_retweet_user($link,$tweet_id,$user_id);
            // そのツイートの返信内容取得
            $reply_information = get_reply($link,$tweet_id);
            close_db_connect($link);
        }else{
            $error_message[] = "そのツイートは存在しません。";
        }
        include_once "../views/tweet.php";
    }else{
        include_once "../views/twitter_top.php";
    }
