<?php
    require_once "../confs/const.php";
    require_once "../models/common.php";
    require_once "../models/user.php";
    require_once "../models/follow.php";
    require_once "../models/tweet.php";
    require_once "../models/reply.php";
    session_start();
    $link = get_db_connect();
    $error_message = array();
    $user_data = array(); // ユーザー上情報
    $tweet_information = array(); // ツイート内容情報
    $method = get_request_method();
    $user_information = array(); 
    $tweet_count = array(); // ツイートの数
    $tweet_id = 0; // ツイートID
    $sql_kind = "";
    $follow_flag = FALSE; // フォローしているかどうかのフラグ
    $profile_switch = get_session_data('profile_switch'); // ログインしているユーザーのプロフィールかどうかの判定(0がログインユーザー)
    $search_user_id = 0; // 検索結果のユーザーID
    $message = '';
    $extension_flag = FALSE; // 拡張子が合ってるかどうかのフラグ
    $extensions = array('JPG','PNG','JPEG','jpg','png','jpeg'); // 拡張子配列
    if(isset_session('user_id')){
        if($method === 'POST'){
            $user_id = get_session_data('user_id');
            $sql_kind = get_post_data('sql_kind'); // postの種類
            $date = date('Y-m-d H:i:s');
            if($sql_kind === "profile_change"){ // プロフィールの変更
                $user_name = trim(get_post_data('name')); // ユーザー名
                $location = trim(get_post_data('location')); // 場所
                $introduce = trim(get_post_data('introduce')); // 紹介文
                $img = NULL; // 画像のパス
                if(!check_empty($user_name)){
                    $error_message[] = "ユーザー名は空にはできません";
                }
                //　ユーザー名のエラーチェック
                if(!check_min_max($user_name,NAME_MIN_LENGTH,MAX_LENGTH)){
                    $error_message[] = "ユーザー名は1文字以上20文字以内で入力してください";
                }
                if(check_name_update($link,$user_id,$user_name)){
                    $error_message[] = "すでにそのユーザー名は存在します";
                }
                // 画像ファイルのエラーチェック
                if (is_uploaded_file($_FILES['add_pic']['tmp_name'])){
                    $img = $_FILES['add_pic']['name'];
                    // 画像の拡張子取得
                    $extension = pathinfo($img,PATHINFO_EXTENSION);
                    // 拡張子チェック
                    $extension_flag = check_extension($extensions,$extension);
                    if($extension_flag === TRUE){
                        // ハッシュ関数によって画像名取得
                        $img = md5($img.$user_id) ."." .$extension;
                        if(!is_file(IMAGE_URL.$img)){
                            if(move_uploaded_file($_FILES["add_pic"]["tmp_name"],"images/".$img) === TRUE){
                                chmod(IMAGE_URL.$img,0755);
                            }else{
                                $error_message[] = "ファイルをアップロードできません";
                            }
                        }
                    }else{
                        $error_message[] = "画像の拡張子は.pngか.jpgにしてください";
                    }
    			}
                if(count($error_message) === 0){
                    if(update_profile($link,$user_id,$user_name,$location,$introduce,$img) !== TRUE){
                        $error_message[] = "user_table:insertエラー";
                    }
                }
                close_db_connect($link);
            }else if($sql_kind === "do_tweet"){ // ツイートした場合
                $content = get_post_data('tweet');  // ツイート内容
                $date = date('Y-m-d H:i:s'); // ツイート日時
                if(!check_empty($content)){
                    $error_message[] = "ツイート内容を記入してください";
                }
                if(!check_length($content,140)){
                    $error_message[] = "ツイート内容は140文字以内で記入してください";
                }
                if(count($error_message) === 0){
                    // ツイートテーブルに挿入
                    if(insert_tweet($link,$user_id,$content,$date) !== TRUE){
                        $error_message[] = "tweet_table:insertエラー";
                    }
                }
                close_db_connect($link);
            }else if($sql_kind === "tweet_delete"){ // ツイート削除の場合
                $tweet_id = get_post_data('tweet_id'); // 削除するツイートのID
                if(check_exist($link,$tweet_id)!==TRUE){
                    $error_message[]="そのツイートは存在しません";
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
                    transaction($link,$error_message);
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
                set_session('profile_switch',$follow_id);
                //$search_user_id = $follow_id;
            }else if($sql_kind === "follow_remove"){
                $follow_id = get_post_data('user_id');
                if(!follow_check($link,$user_id,$follow_id)){
                    $error_message[] = "すでにフォロー解除しています";
                }else{
                    // follow_tableから削除
                    if(!follow_remove($link,$user_id,$follow_id)){
                        $error_message[] = "follow_table:insertエラー";
                    }
                }
                set_session('profile_switch',$follow_id);
                //$search_user_id = $follow_id;
            }else if($sql_kind === "search"){ // 検索の時
                $address = get_post_data('search');
                $search_user_id = search_user($link,$address);
                set_session('profile_switch',$search_user_id);
                if($search_user_id === 0){
                    $error_message[] = "該当するユーザはいませんでした";
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
                    if(!insert_retweet($link,$tweet_id,$user_id,$date)){
                        $error_message[] = "insert_retweet_table:insertエラー";
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
                    if(!remove_retweet($link,$tweet_id,$user_id)){
                        $error_message[] = "insert_retweet_table:deleteエラー";
                    }
                }
                close_db_connect($link);
            }
        }
        // データの取得
        $link = get_db_connect();
        if(isset_get('user_id')){
            set_session('profile_switch',get_get_data('user_id'));
        }
        if(intval(get_session_data('profile_switch')) !== 0){
            $user_id = intval(get_session_data('profile_switch'));
            $profile_switch = intval(get_session_data('profile_switch'));
            // フォロー指定かどうかのチェック（フォローしていたらtrueを返す）
            $follow_flag = check_follow($link,get_session_data('user_id'),$user_id);
            if($user_id == get_session_data('user_id')){
                set_session('profile_switch',0);
                $follow_flag = FALSE;
                $profile_switch = intval(get_session_data('profile_switch'));
            }
        }else{
            set_session('profile_switch',0);
            $user_id = get_session_data('user_id');
            $profile_switch = intval(get_session_data('profile_switch'));
        }
        // プロフィール情報取得
        $user_data = user_get_data($link,$user_id);
        // ツイート情報取得
        $tweet_information = profile_tweet($link,$user_id);
        // リツイート相手のユーザー情報
        $my_retweet_user = my_retweet_user($link,$user_id);
        // リツイートチェック
        $retweet_check = retweet_check_profile($link,get_session_data('user_id'),$user_id);
        // フォロワー数など情報
        $user_information = user_information($link,$user_id);
        close_db_connect($link);
        include_once "../views/profile.php";
    }else{
        include_once "../views/twitter_top.php";
    }
