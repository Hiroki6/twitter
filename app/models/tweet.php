<?php
require_once "common.php";

// ツイート内容取得(タイムライン)
function timeline_tweet($link,$user_id,$timeline_kind){
    $tweet_information = array();
    if($timeline_kind === 0){
        $sql_tweet_get = "SELECT user_name,tweet_id,img,A.user_id AS user_id,tweet_content,date,retweet_id FROM tweet_table AS A JOIN user_table AS B ON A.user_id=B.user_id WHERE A.user_id=" .intval($user_id). " or A.user_id IN (SELECT followed_id FROM follow_table WHERE user_id=" .intval($user_id). ") or A.tweet_id IN (SELECT tweet_id FROM mention_table WHERE mention_id=" .intval($user_id). ") GROUP BY A.user_id,date ORDER BY date DESC";
    }else if($timeline_kind === 1){
        $sql_tweet_get = "SELECT user_name,tweet_id,img,A.user_id AS user_id,tweet_content,date,retweet_id FROM tweet_table AS A JOIN user_table AS B ON A.user_id=B.user_id WHERE B.user_id=" .intval($user_id). " ORDER BY date DESC";
    }else if($timeline_kind === 2){
        $sql_tweet_get = "SELECT user_name,tweet_id,img,A.user_id AS user_id,tweet_content,date,retweet_id FROM tweet_table AS A JOIN follow_table AS B ON A.user_id=B.user_id JOIN user_table AS C ON A.user_id=C.user_id WHERE A.user_id IN (SELECT followed_id FROM follow_table WHERE user_id=" .intval($user_id). ") or A.tweet_id IN (SELECT tweet_id FROM mention_table WHERE mention_id=" .intval($user_id). ") GROUP BY A.user_id,date ORDER BY date DESC";
    }else if($timeline_kind === 3){
        $sql_tweet_get = "SELECT user_name,tweet_id,img,A.user_id AS user_id,tweet_content,date,retweet_id FROM tweet_table AS A JOIN follow_table AS B ON A.user_id=B.user_id JOIN user_table AS C ON A.user_id=C.user_id WHERE A.tweet_id IN (SELECT tweet_id FROM mention_table WHERE user_id=" .intval($user_id). ") or A.tweet_id IN (SELECT tweet_id FROM mention_table WHERE mention_id=" .intval($user_id). ") GROUP BY A.tweet_id ORDER BY date DESC";
    }
    $tweet_information = db_get_data($link,$sql_tweet_get);
    return $tweet_information;
}

// ツイート内容取得(プロフィール)
function profile_tweet($link,$user_id){
    $tweet_information = array();
    $sql_tweet_get = "SELECT user_name,tweet_id,img,tweet_table.user_id AS user_id,tweet_content,date,retweet_id FROM tweet_table JOIN user_table ON user_table.user_id=tweet_table.user_id WHERE user_table.user_id=" .intval($user_id). " ORDER BY date DESC";
    $tweet_information = db_get_data($link,$sql_tweet_get);
    return $tweet_information;
}

// ツイートの取得
function get_tweet($link,$tweet_id){
    $tweet_information = array();
    $sql_tweet_get = "SELECT tweet_id,user_table.user_id AS user_id,user_name,img,tweet_content,date,retweet_id FROM tweet_table JOIN user_table ON tweet_table.user_id=user_table.user_id WHERE tweet_id=" .intval($tweet_id);
    $tweet_information = get_db_one($link,$sql_tweet_get);
    return $tweet_information;
}

// ツイートテーブルに挿入
function insert_tweet($link,$user_id,$content,$date){
    $sql_tweet_insert = "INSERT INTO tweet_table(user_id,tweet_content,date) VALUES(" .intval($user_id). ",'" .$content. "','" .$date. "')";
    if(execute_db($link,$sql_tweet_insert) !== TRUE){
        return FALSE;
    }
    preg_match_all('/@[\w\W]+[\s　]|@[\w\W]+$/',$content,$matches);
    // ツイートID取得
    $tweet_id = mysqli_insert_id($link);
    $user_names = array();
    // メンション@があればメンションテーブルにも挿入
    if(count($matches[0]) !== 0){
        for($i = 0;$i < count($matches[0]); $i++){
            // 空白と@を取り除く
            $matches[0][$i] = trim(mb_convert_kana($matches[0][$i], "s", 'UTF-8'));
            //$matches[0][$i] = trim($matches[0][$i]);
            $matches[0][$i] = str_replace("@","",$matches[0][$i]);
        }
        // 全てのユーザー名取得
        $sql_username = "SELECT user_name,user_id FROM user_table";
        $user_names = db_get_data($link,$sql_username);
        // 一致するユーザー名がいるかどうかのチェック
        for($i = 0;$i < count($matches[0]); $i++ ){
            foreach($user_names as $user){
                // そのユーザーがいたらメンションテーブルに挿入する
                if($matches[0][$i] === $user['user_name']){
                    if(insert_mention($link,$user_id,$tweet_id,$user['user_id'])!==TRUE){
                        return FALSE;
                    }
                }
            }
        }
    }
    return TRUE;
}

// ツイート削除
function delete_tweet($link,$tweet_id){
    $sql_tweet_delete = "DELETE FROM tweet_table WHERE tweet_id=" .intval($tweet_id);
    if(execute_db($link,$sql_tweet_delete) !== TRUE){
        return FALSE;
    }
    return TRUE;
}

// リツイート処理
function insert_retweet($link,$tweet_id,$user_id,$date){
    $content = array();
    $sql_tweet_get = "SELECT tweet_content,tweet_id AS retweet_id FROM tweet_table WHERE tweet_id=" .intval($tweet_id);
    $content = get_db_one($link,$sql_tweet_get);
    $sql_retweet_insert = "INSERT INTO tweet_table(user_id,tweet_content,date,retweet_id) VALUES(" .intval($user_id). ",'" .$content['tweet_content']. "','" .$date. "'," .$content['retweet_id']. ")";
    if(execute_db($link,$sql_retweet_insert) !== TRUE){
        return FALSE;
    }
    return TRUE;
}

// リツイート解除
function remove_retweet($link,$tweet_id,$user_id){
    $sql_retweet_remove = "DELETE FROM tweet_table WHERE retweet_id=" .$tweet_id. " AND user_id=" .$user_id;
    if(execute_db($link,$sql_retweet_remove) !== TRUE){
        return FALSE;
    }
    return TRUE;
}

// リツイートチェック関数（タイムライン）
function retweet_checK_timeline($link,$user_id){
    $retweet_check = array(); // そのツイートがリツイートされているかどうかのチェック配列
    $another_tweet = array();
    $my_tweet = array();
    // そのユーザーとフォローしているひとのツイートを取得
    $sql_tweet = "SELECT tweet_id FROM tweet_table AS A JOIN follow_table AS B ON A.user_id=B.user_id JOIN user_table AS C ON A.user_id=C.user_id WHERE A.user_id=" .intval($user_id). " or A.user_id IN (SELECT followed_id FROM follow_table WHERE user_id=" .intval($user_id). ") or tweet_id IN (SELECT tweet_id FROM mention_table WHERE mention_id=" .intval($user_id).  ") GROUP BY A.user_id,date ORDER BY date DESC";
    $another_tweet = db_get_data($link,$sql_tweet);
    // そのユーザーのツイートがリツイート情報取得
    $sql_retweet = "SELECT retweet_id FROM tweet_table WHERE user_id=" .intval($user_id). " ORDER BY date DESC";
    $my_tweet = db_get_data($link,$sql_retweet);
    foreach($another_tweet as $another){
        if(count($my_tweet) !== 0){
            foreach ($my_tweet as $my) {
                if($another['tweet_id'] === $my['retweet_id']){
                    $retweet_check[$another['tweet_id']] = TRUE;
                    break;
                }
                $retweet_check[$another['tweet_id']] = FALSE;
            }
        }else{
            $retweet_check[$another['tweet_id']] = FALSE;
        }
    }
    return $retweet_check;
}

// リツイートチェック関数(プロフィール)
function retweet_check_profile($link,$login_id,$user_id){
    $retweet_check = array();
    if($login_id !== $user_id){
        $user_tweet = array();
        $login_tweet = array();
        // そのユーザーのツイートを取得
        $sql_tweet = "SELECT tweet_id FROM tweet_table WHERE user_id=" .intval($user_id);
        $user_tweet = db_get_data($link,$sql_tweet);
        // そのユーザーのツイートのリツイート情報取得
        $sql_retweet = "SELECT retweet_id FROM tweet_table WHERE user_id=" .intval($login_id). " ORDER BY date DESC";
        $login_tweet = db_get_data($link,$sql_retweet);
        foreach($user_tweet as $user){
            if(count($login_tweet) !== 0){
                foreach($login_tweet as $login){
                    if($user['tweet_id'] === $login['retweet_id']){
                        $retweet_check[$user['tweet_id']] = TRUE;
                        break;
                    }
                    $retweet_check[$user['tweet_id']] = FALSE;
                }
            }else{
                $retweet_check[$user['tweet_id']] = FALSE;
            }
        }
    }
    return $retweet_check;
}
// そのツイートがすでにリツイートしているかどうかのチェック関数
function check_retweet($link,$tweet_id,$user_id){
    $my_tweets = array();
    $sql_tweet = "SELECT retweet_id FROM tweet_table WHERE user_id=" .intval($user_id). " AND retweet_id IS NOT NULL";
    $my_tweets = db_get_data($link,$sql_tweet);
    foreach($my_tweets as $my_tweet){
        if($my_tweet['retweet_id'] === $tweet_id){
            return TRUE;
        }
    }
    return FALSE;
}
// リツイートした相手のユーザー情報
function my_retweet_user($link,$user_id){
    $my_tweets = array();
    // そのユーザーのツイートに関する情報取得
    $sql_tweet = "SELECT tweet_id,retweet_id FROM tweet_table WHERE user_id=" .intval($user_id). " AND retweet_id IS NOT NULL";
    $my_tweets = db_get_data($link,$sql_tweet);
    $my_retweet_user = array();
    // リツイートしたツイート相手の情報取得
    $sql_tweet = "SELECT tweet_id,user_name,B.user_id AS user_id FROM tweet_table AS A JOIN user_table AS B ON A.user_id = B.user_id WHERE tweet_id IN (SELECT retweet_id FROM tweet_table WHERE user_id=" .intval($user_id). " AND retweet_id IS NOT NULL)";
    if($result = mysqli_query($link,$sql_tweet)){
        while($row = mysqli_fetch_assoc($result)){
            foreach($my_tweets as $my_tweet){
                if($my_tweet['retweet_id'] === $row['tweet_id']){
                    $my_retweet_user[$my_tweet['tweet_id']] = array(
                        'user_id' => $row['user_id'],
                        'user_name' => $row['user_name']
                    );
                }
            }
        }
    }
    return $my_retweet_user;
}

// 個別ツイートページにおけるリツイートした相手のユーザー情報
function one_retweet_user($link,$tweet_id,$user_id){
    $retweet_id = array();
    $sql_retweet = "SELECT retweet_id FROM tweet_table WHERE tweet_id=" .intval($tweet_id);
    $retweet_id = get_db_one($link,$sql_retweet);
    $one_retweet_user = '';
    if($retweet_id['retweet_id'] !== NULL){
        $sql_username = "SELECT user_name FROM tweet_table AS A JOIN user_table AS B ON A.user_id = B.user_id WHERE tweet_id=" .$retweet_id['retweet_id'];
        if($result = mysqli_query($link,$sql_username)){
            while($row = mysqli_fetch_assoc($result)){
                $one_retweet_user = $row['user_name'];
            }
        }
    }
    return $one_retweet_user;
}

// 個別ツイートページにおけるリツイートチェック関数
function one_retweet_check($link,$tweet_id,$user_id){
    $my_tweet = array();
    $sql_retweet = "SELECT retweet_id FROM tweet_table WHERE user_id=" .intval($user_id). " ORDER BY date DESC";
    $my_tweet = db_get_data($link,$sql_retweet);
    foreach($my_tweet as $my){
        if($tweet_id === $my['retweet_id']){
            return TRUE;
        }
    }
    return FALSE;
}

// そのツイートが存在するかどうかのチェック
function check_exist($link,$tweet_id){
    $content = array();
    $sql_tweet = "SELECT tweet_id FROM tweet_table WHERE tweet_id=" .intval($tweet_id);
    $content = get_db_one($link,$sql_tweet);
    if(count($content) === 0){
        return FALSE;
    }
    return TRUE;
}
