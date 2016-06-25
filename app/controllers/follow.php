<?php
    require_once "../confs/const.php";
    require_once "../models/follow.php";
    require_once "../models/user.php";
    session_start();
    $link = get_db_connect();
    $follow_information = array(); // フォローしている人の情報
    $profile_switch = intval(get_session_data('profile_switch'));
    if(isset_session('user_id')){
        if(isset_get('user_id')){
            $user_id = intval(get_get_data('user_id'));
        }else{
            $user_id = intval(get_session_data('user_id'));
        }
        // データの取得
        $link = get_db_connect();
        // プロフィール情報取得
        $user_data = user_get_data($link,$user_id);
        //　フォロー情報
        $user_information = user_information($link,$user_id);
        // フォローしている人の情報
        $follow_information = follow_information($link,$user_id);
        close_db_connect($link);
        include "../views/follow.php";
    }else{
        include "../views/twitter_top.php";
    }
