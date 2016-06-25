<?php
    require_once "../confs/const.php";
    require_once "../models/common.php";
    require_once "../models/user.php";
    require_once "../models/follow.php";
    require_once "../models/tweet.php";
    require_once "../models/reply.php";
    session_start();
    $user_data = array(); // ユーザー情報
    $method = get_request_method();
    $tweet_content = ""; // ツイート内容
    $tweet_information = array(); // ツイートに関する情報
    $now = time();
    $error_message = array();
    $user_id = ""; // ユーザーID
    $address = ""; // 検索の時のメールアドレス
    $compare_data = array(); // 比較用データ格納
    $message = "";
    $follow_id = 0; // フォローされるユーザーのID
    $search_user_id = 0; // 検索されたユーザ
    $another_user_data = array(); // 他ユーザのデータ情報
    $user_information = array(); // ツイート数、フォロー数、フォロワー数
    $retweet_check = array(); // そのツイートがリツイートされているかどうかの連想配列
    if(isset_session('user_id')){
        if($method === 'POST'){
            $link = get_db_connect();
            $user_id = get_session_data('user_id');
            $sql_kind = get_post_data('sql_kind');
            $date = date('Y-m-d H:i:s');
            if($sql_kind === "do_tweet"){ // ツイートした時
                $tweet_content = get_post_data('tweet');
                $date = date('Y-m-d H:i:s');
                if(!check_empty($tweet_content)){
                    $error_message[] = "ツイート内容を記入してください";
                }
                if(!check_length($tweet_content,140)){
                    $error_message[] = "ツイート内容は140文字以内で記入してください";
                }
                if(count($error_message) === 0){
                    // ツイートテーブルに挿入
                    if(insert_tweet($link,$user_id,$tweet_content,$date) !== TRUE){
                        $error_message[] = "tweet_table:insertエラー";
                    }
                }
                close_db_connect($link);
            }else if($sql_kind === "follow"){
                $follow_id = get_post_data('user_id');
                if(follow_check($link,$user_id,$follow_id)){
                    $error_message[] = "すでにフォローしています";
                }else{
                    // follow_tableに挿入
                    if(!insert_follow($link,$user_id,$follow_id)){
                        $error_message[] = "follow_table:insertエラー";
                    }
                }
                close_db_connect($link);
            }else if($sql_kind === "tweet_delete"){ // ツイート削除の場合
                $tweet_id = get_post_data('tweet_id'); // 削除するツイートのID
                if(!check_exist($link,$tweet_id)){
                    $error_message[] = "そのツイートは存在しません";
                }else{
                    // 更新系の処理を行う前にトランザクション開始(オートコミットをオフ）
                    mysqli_autocommit($link, false);
                    // ツイートテーブルのツイート削除
                    if(!delete_tweet($link,$tweet_id)){
                        $error_message[] = "tweet_table:deleteエラー";
                    }
                    // 返信テーブルのツイート削除
                    if(!delete_reply_table($link,$tweet_id)){
                        $error_message[] = "reply_table:deleteエラー";
                    }
                    // メンションテーブルのツイート削除
                    if(!delete_mention_table($link,$tweet_id)){
                        $error_message[] = "mention_table:deleteエラー";
                    }
                    transaction($link,$error_message);
                }
                close_db_connect($link);
            }else if($sql_kind === "retweet"){
                $tweet_id = get_post_data('tweet_id');
                if(!check_exist($link,$tweet_id)){
                    $error_message[] = "そのツイートは存在しません";
                }else if(check_retweet($link,$tweet_id,$user_id)){
                    $error_message[] = "すでにリツイートしてあります";
                }
                if(count($error_message) === 0){
                    if(insert_retweet($link,$tweet_id,$user_id,$date) !== TRUE){
                        $error_message[] = "retweet_table:insertエラー";
                    }
                }
                close_db_connect($link);
            }else if($sql_kind === "retweet_remove"){
                $tweet_id = get_post_data('tweet_id');
                if(!check_exist($link,$tweet_id)){
                    $error_message[] = "そのツイートは存在しません";
                }else if(!check_retweet($link,$tweet_id,$user_id)){
                    $error_message[] = "すでにリツイートを解除してあります";
                }
                if(count($error_message) === 0){
                    if(remove_retweet($link,$tweet_id,$user_id) !== TRUE){
                        $error_message[] = "retweet_table:deleteエラー";
                    }
                }
                close_db_connect($link);
            }else if($sql_kind === "me"){
                set_session('timeline_kind',1);
            }else if($sql_kind === "another"){
                set_session('timeline_kind',2);
            }else if($sql_kind === "all"){
                set_session('timeline_kind',0);
            }else if($sql_kind === "mention"){
                set_session('timeline_kind',3);
            }
        }
        $user_id = get_session_data('user_id');
        $timeline_kind = intval(get_session_data('timeline_kind')); //　0はすべて,1は自分,2は他のユーザー
        set_session('profile_switch',0);
        $link = get_db_connect();
        // プロフィール情報取得
        $user_data = user_get_data($link,$user_id);
        // ツイート情報取得
        $tweet_information = timeline_tweet($link,$user_id,$timeline_kind);
        // リツイートチェック
        $retweet_check = retweet_checK_timeline($link,$user_id);
        // リツイート相手のユーザー情報
        $my_retweet_user = my_retweet_user($link,$user_id);
        // フォロワー数など情報
        $user_information = user_information($link,$user_id);
        // 他のユーザの情報取得
        $another_user_data = another_user($link,$user_id);
        close_db_connect($link);
        include_once "../views/timeline.php";
    }else{
        include_once "../views/twitter_top.php";
    }
