<?php
    require_once "../confs/const.php";
    require_once "../models/common.php";
    $link = get_db_connect(); // データベース接続
    $error_message = array();
    $name = ""; // 名前
    $address = ""; // メールアドレス
    $passwd = ""; // パスワード
    session_start();
    $method = get_request_method();
    if($method === 'POST'){
        $name = get_post_data('name');
        $address = get_post_data('mail');
        $passwd = get_post_data('new_passwd');
    }
    if(isset_session('user_id')){
        header ("location:timeline.php");
        exit;
    }else{
        include_once "../views/register.php";
    }
