<?php
    require_once "../confs/const.php";
    require_once "../models/user.php";
    $link = get_db_connect();
    $name = "";
    $address = "";
    $passwd = "";
    $user_name = "";
    $error_message = array();
    $data = array();
    $message = "";
    session_start();
    $method = get_request_method();
    $user_id = 0;
    if($method === 'POST'){
        $name = get_post_data('name');
        $address = get_post_data('address');
        $passwd = get_post_data('passwd');
        $user_name = get_post_data('user_name');
        // 名前エラーチェック
        if(!check_empty($name)){
            $error_message[] = "名前を入力してください";
        }
        // アドレスのエラーチェック
        if(!check_empty($address)){
            $error_message[] = "メールアドレスを入力してください";
        }else if(!check_address($address)){
            $error_message[] = "正しいメールアドレスを入力してください";
        }
        // パスワードのエラーチェック
        if(!check_min_max($passwd,PASSWD_MIN_LENGTH,MAX_LENGTH)){
            $error_message[] = "パスワードは6文字以上20文字以内で入力してください";
        }
        //　ユーザー名のエラーチェック
        if(!check_min_max($user_name,NAME_MIN_LENGTH,MAX_LENGTH)){
            $error_message[] = "ユーザー名は1文字以上20文字以内で入力してください";
        }
        // ユーザー名の被りがあるかどうかのチェック
        if(check_name_overlap($link,$user_name)){
            $error_message[] = "すでにそのユーザー名は存在します";
        }
        // パスワードの被りがあるかどうかのチェック
        if(check_password_overlap($link,$passwd)){
            $error_message[] = "すでにそのパスワードは存在します";
        }
        // エラーがなければ
        if(count($error_message) === 0){
            // 登録済みかどうかの確認
            if(($user_id = check_register($link,$name,$address,$passwd,$user_name)) === 0){
                if(($user_id = user_register($link,$name,$address,$passwd,$user_name)) === 0){
                    $error_message[] = "user_table:insertエラー" .$sql;
                    include_once VIEW_URL."register.php";
                }else{
                    set_login_user($user_id);
                }
            }else{
                set_login_user($user_id);
            }
            close_db_connect($link);
            header ("location:timeline.php");
        }else{
            include_once "../views/register.php";
        }
    }
