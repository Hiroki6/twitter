<?php
    require_once "../confs/const.php";
    require_once "../models/common.php";
    require_once "../models/user.php";
    session_start();
    $method = get_request_method();
    $user_info = ""; // ユーザ名あるいはアドレス
    $passwd = ""; // パスワード
    $user_id = 0; // ユーザーID
    $user_data = array(); // ユーザ情報格納
    $error_message = array(); // エラーメッセージ
    $sql_kind = "";
    if($method === 'POST'){
        $user_name = get_post_data('id'); // ユーザ名あるいはアドレス
        $passwd = get_post_data('passwd'); // パスワード
        if(!check_empty($user_name)){
            $error_message[] = "ユーザ名またはアドレスを入力してください";
        }
        if(!check_empty($passwd)){
            $error_message[] = "パスワードを入力してください";
        }
        if(count($error_message) === 0){
            $link = get_db_connect();
            $user_id = register_check($link,$user_name,$passwd);
            if($user_id === 0){
                $error_message[] = "登録されていません";
            }else{
                set_session('user_id',$user_id);
                set_session('timeline_kind',0);
                set_session('profile_switch',0);
            }
            close_db_connect($link);
        }
    }
    if(isset_session('user_id')){
        header ("location:timeline.php");
    }else{
        include_once "../views/twitter_top.php";
    }
