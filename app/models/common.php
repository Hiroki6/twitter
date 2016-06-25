<?php

// 特殊文字をHTMLエンティティに変換する
function entity_str($str) {
    return htmlspecialchars($str, ENT_QUOTES, HTML_CHARACTER_SET);
}

//特殊文字をHTMLエンティティに変換する(2次元配列の値)
function double_assoc_array($assoc_array) {
    foreach ($assoc_array as $key => $value) {
        foreach ($value as $keys => $values) {
            // 特殊文字をHTMLエンティティに変換
            $assoc_array[$key][$keys] = entity_str($values);
        }
    }
    return $assoc_array;
}

// DBハンドルを取得
function get_db_connect() {
 
    // コネクション取得
    if (!$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME)) {
        die('error: ' . mysqli_connect_error());
    }
 
    // 文字コードセット
    mysqli_set_charset($link, DB_CHARACTER_SET);
 
    return $link;
}
 

// DBとのコネクション切断
function close_db_connect($link) {
    // 接続を閉じる
    mysqli_close($link);
}
 
//クエリを実行しその結果を配列で取得する
function db_get_data($link,$sql) {
    // 返却用配列
    $data = array();
    // クエリを実行する
    if ($result = mysqli_query($link, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            // １件ずつ取り出す
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        // 結果セットを開放
        mysqli_free_result($result);
    }
    $data = double_assoc_array($data);
    return $data;
}

// 更新、挿入を行う
function execute_db($link,$sql){
	// クエリを実行する
	if(mysqli_query($link,$sql) === TRUE){
		return TRUE;
	}else{
		return FALSE;
	}
}

// リクエストメソッドを取得
function get_request_method(){
	return $_SERVER['REQUEST_METHOD'];
}

// POSTデータを取得
function get_post_data($key){
	$str = $_POST[$key];
    if(is_string($str)){
        $str = trim($str);
    }
	return $str;
}

// GETデータを取得
function get_get_data($key){
	$str = $_GET[$key];
    if(is_string($str)){
        $str = trim($str);
    }
	return $str;
}

// SESSIONデータを取得
function get_session_data($key){
    $str = $_SESSION[$key];
    return $str;
}
// SESSIONデータを設定
function set_session($key,$value){
    $_SESSION[$key] = intval($value);
}
// POST値がNULLかどうかの判定
function isset_post($key){
    return isset($_POST[$key]);
}

// GET値がNULLどうかの判定
function isset_get($key){
    return isset($_GET[$key]);
}

// SESSION値がNULLかどうかの判定
function isset_session($key){
    return isset($_SESSION[$key]);
}

// 文字が空かどうかのチェック
function check_empty($string){
    $string = trim($string);
	if(mb_strlen($string) === 0){
		return FALSE;
	}else{
		return TRUE;
	}
}

// 文字が与えた文字列より長いかどうか
function check_length($string,$length){
    $string = trim($string);
	if(mb_strlen($string) > $length){
		return FALSE;
	}else{
		return TRUE;
	}
}

// 与えられた変数が正の整数かどうかの判定関数
function judge_int($value){
	if($value < 0 || ctype_digit(strval($value)) !== TRUE){
		return FALSE;
	}else{
		return TRUE;
	}
}

// 与えられた文字列の前後の全角及び半角スペース削除する関数
function cut_space($value){
	$value = preg_replace('/^[ ]+/u',"",$value);
	$value = preg_replace('/[ ]+$/u',"",$value);
	return $value;
}

// データベースから一件取得する関数
function get_db_one($link,$sql){
	$row = array();
	if($result = mysqli_query($link,$sql)){
		// 1件取得
		$row = mysqli_fetch_assoc($result);
	}
	mysqli_free_result($result);
	return $row;
}

//  与えられた変数が0かどうかの判定関数
function judge_zero($value){
	if(mb_strlen($value) === 0){
		return FALSE;
	}else{
		return TRUE;
	}
}

// 第一の引数がnullの場合第二の引数を返す関数
function judge_null($one,$two){
    if($one == NULL){
        return $two;
    }else{
        return $one;
    }
}

// 与えられた文字の長さがmin文字以上max文字以下かどうかのチェック
function check_min_max($string,$min,$max)
{
    $string = trim($string);
    if(mb_strlen($string) < $min || mb_strlen($string) > $max){
        return FALSE;
    }
    return TRUE;
}

// トランザクション
function transaction($link,$error_message){
    if(count($error_message) === 0){
        // 処理確定
        mysqli_commit($link);
    }else{
        // 処理取消
        mysqli_rollback($link);
    }
}

// ログアウト処理
function logout(){
    $session_name = session_name();
    $_SESSION = array();
    if(isset($session_name) === TRUE){
        setcookie($session_name,'',$now-3600);
    }
    session_destroy();
}
?>
