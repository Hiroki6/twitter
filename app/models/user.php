<?php
require_once "common.php";

// ログインチェック関数
function register_check($link,$user_name,$passwd){
    $user_id = 0;
    $sql_login_check = "SELECT user_name,user_passwd,user_address,user_id from user_table";
    $user_data = db_get_data($link,$sql_login_check); 
    foreach($user_data as $data){
        if(($user_name === $data['user_name'] || $user_name === $data['user_address']) && $passwd === $data['user_passwd']){
            $user_id = $data['user_id'];
        }
    }
    return $user_id;
}

// imgがアップされなかったら今のimgを返し、アップされたら、date付きを返す
function judge_img($img,$now_img,$update_img){
    if($img == NULL){
        return $now_img;
    }else{
        return $update_img;
    }
}

// メールアドレスチェック関数
function check_address($address){
    if(preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/',$address)!== 1){
        return FALSE;
    }
    return TRUE;
}

// 拡張子チェック関数
function check_extension($extensions,$extension){
    for($i = 0;$i < count($extensions);$i++){
        if($extensions[$i] === $extension){
            return TRUE;
        }
    }
    return FALSE;
}

// ユーザー登録チェック
function check_register($link,$name,$address,$passwd,$user_name){
    $sql_register_check = "SELECT user_id FROM user_table WHERE name='" .$name. "' AND user_address='" .$address. "' AND user_passwd='" .$passwd. "' AND user_name='" .$user_name. "'";
    $data = get_db_one($link,$sql_register_check);
    if(count($data) !== 0){
        return $data['user_id'];
    }
    return 0;
}

// ユーザー登録
function user_register($link,$name,$address,$passwd,$user_name){
    $sql_register = "INSERT INTO user_table(name,user_address,user_passwd,user_name) VALUES('" .$name. "','" .$address. "','" .$passwd. "','" .$user_name. "')";
    // データベースに挿入
    if(!execute_db($link,$sql_register)){
        return 0;
    }else{
        // A_Iを取得
        $user_id = mysqli_insert_id($link);
        return $user_id;
    }
}
// ユーザー名の被りチェック関数
function check_name_overlap($link,$user_name){
    $sql_check_name = "SELECT user_id FROM user_table WHERE user_name='" .$user_name. "'";
    $data = get_db_one($link,$sql_check_name);
    if(count($data) !== 0){
        return TRUE;
    }
    return FALSE;
}

// パスワードの被りチェック関数
function check_password_overlap($link,$passwd){
    $sql_check_password = "SELECT user_id FROM user_table WHERE user_passwd='" .$passwd. "'";
    $data = get_db_one($link,$sql_check_password);
    if(count($data) !== 0){
        return TRUE;
    }
    return FALSE;
}

// プロフィール変更時のユーザー名チェック関数
function check_name_update($link,$user_id,$user_name){
    $sql_check_name = "SELECT user_id FROM user_table WHERE user_name='" .$user_name. "' AND NOT (user_id=" .$user_id. ")";
    $data = db_get_data($link,$sql_check_name);
    if(count($data) !== 0){
        return TRUE;
    }
    return FALSE;
}
// ログイン時のセッション登録関数
function set_login_user($user_id){
    set_session('user_id',$user_id);
    set_session('timeline_kind',0);
    set_session('profile_switch',0);
}

// ユーザープロフィール取得
function user_get_data($link,$user_id){
	$user_data = array();
    $sql_profile_get = "SELECT user_name,introduce,location,img FROM user_table WHERE user_id= " .intval($user_id);
	$user_data = get_db_one($link,$sql_profile_get);
	return $user_data;
}

// ツイート数、フォロワー数、フォロー数を取得する関数
function user_information($link,$user_id){
	// フォロー数取得
	$sql_follow = "SELECT count(followed_id) as follow FROM follow_table WHERE user_id=" .intval($user_id);
	$result = mysqli_query($link,$sql_follow);
	$row = mysqli_fetch_assoc($result);
	$follow = $row['follow'];
	// フォロワー数取得
	$sql_follower = "SELECT count(user_id) as follower FROM follow_table WHERE followed_id=" .intval($user_id);
	$result = mysqli_query($link,$sql_follower);
	$row = mysqli_fetch_assoc($result);
	$follower = $row['follower'];
	// ツイート数取得
	$sql_tweet = "SELECT count(tweet_id) as tweet_count FROM tweet_table WHERE user_id=" .intval($user_id);
	$result = mysqli_query($link,$sql_tweet);
	$row = mysqli_fetch_assoc($result);
	$tweet_count = $row['tweet_count'];
	$user_information = array('follow' => $follow,
							'follower' => $follower,
							'tweet_count' => $tweet_count);
	mysqli_free_result($result);
	return $user_information;
}

// 画像のファイルのエラーチェック
function image_check($file){
	if(exif_imagetype($file) === FALSE){
        return FALSE;
    }
    return TRUE;
}

// 画像の拡張子のエラーチェック
function check_filename($file_name){
	if(preg_match('/\.png$|\.jpg$|\.jpeg$/i',$file_name) !== 1){
		return FALSE;
	}
	return TRUE;
}

// 他ユーザの情報３件取得
function another_user($link,$user_id){
    $another_user = array();
    $sql_another = "SELECT user_id,user_name,img FROM user_table WHERE NOT user_id IN (SELECT followed_id FROM follow_table WHERE user_id=" .intval($user_id). ") AND NOT user_id=" .intval($user_id). " LIMIT 3"; 
    $another_user = db_get_data($link,$sql_another);
    return $another_user;
}

// 検索時の処理
function search_user($link,$address){
    $search_user_id = 0;
    $sql_user_id = "SELECT user_id,user_address FROM user_table";
    $compare_data = db_get_data($link,$sql_user_id);
    foreach($compare_data as $data){
        if($data['user_address'] === $address){
            $search_user_id = $data['user_id'];
        }
    }
    return $search_user_id;
}

// プロフィール更新
function update_profile($link,$user_id,$user_name,$location,$introduce,$img){
    $user_data = user_get_data($link,$user_id);
    $img = judge_null($img,$user_data['img']);
    $sql_user_update = "UPDATE user_table SET user_name='" .$user_name. "',location='" .$location. "',introduce='" .$introduce. "',img='" .$img. "' WHERE user_id=" .intval($user_id);
    if(execute_db($link,$sql_user_update) !== TRUE){
        return FALSE;
    }
    return TRUE;
}
