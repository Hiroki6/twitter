<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "utf-8">
        <title>Twitter</title>
        <link rel = "stylesheet" href = "<?php echo STYLE_URL; ?>design.css">
    </head>
    <body>
        <header class = "clearfix">
            <a class = "home" href = "timeline.php"><img src = "<?php echo IMAGE_URL; ?>home.jpg"></a>
            <a><img class = "logo" src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
            <form action = "<?php echo CONTROL_URL; ?>profile.php" method = "post">
                <input class = "search" type = "text" size = "40" name = "search" value = "" placeholder = "twitterを検索">
                <input type = "submit" value = "検索">
                <input type = "hidden" name = "sql_kind" value = "search">
            </form> 
        </header>
        <div class = "main"><!--背景-->
            <article class = "clearfix">
                <?php if(count($error_message) !== 0){ 
                        foreach($error_message as $error){ ?>
                <div class = "error">
                    <p><?php echo $error; ?></p>
                </div>
                <?php }} ?>
                <section class = "profile"><!--プロフィール-->
                    <div class = "top">
                        <?php if(mb_strlen($user_data['img']) !== 0){ ?>
                        <a><img class = "profile_img" src = "<?php echo IMAGE_URL.$user_data['img']; ?>"></a>
                        <?php }else{ ?>
                        <a><img class = "profile_img" src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
                        <?php } ?>
                        <a class = "name" href = "<?php echo CONTROL_URL; ?>profile.php"><?php echo $user_data['user_name']; ?></a>
                    </div>
                    <div class = "detail">
                        <ul class = "subject clearfix">
                            <li>ツイート</li>
                            <li>フォロー</li>
                            <li>フォロワー</li>
                        </ul>
                        <ul class = "number clearfix">
                            <a href = "<?php echo CONTROL_URL; ?>profile.php"><li><?php echo $user_information['tweet_count']; ?></li></a>
                            <a href = "follow.php"><li><?php echo $user_information['follow']; ?></li></a>
                            <a href = "<?php echo CONTROL_URL; ?>follower.php"><li><?php echo $user_information['follower']; ?></li></a>
                        </ul>
                    </div>
                </section>
                <section class = "timeline left"><!--タイムライン-->
                    <div class = "tweet clearfix">
                        <?php if(mb_strlen($user_data['img']) !== 0){ ?>
                        <a><img class = "timeline_img" src = "<?php echo IMAGE_URL.$user_data['img']; ?>"></a>
                        <?php }else{ ?>
                        <a><img class = "timeline_img" src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
                        <?php } ?>
                        <form action = "<?php echo CONTROL_URL; ?>timeline.php" method = "post">
                            <input class = "content" type = "text" name = "tweet" value = "" placeholder = "いまどうしてる？">
                            <input class = "submit" type = "submit" value = "ツイート">
                            <input type = "hidden" name = "sql_kind" value = "do_tweet">
                        </form>
                    </div>
                    <div class = "select_timeline clearfix">
                        <form action = "<?php echo CONTROL_URL; ?>timeline.php" method = "post">
                            <input type = "submit" value = "自分のみ">
                            <input type = "hidden" name = "sql_kind" value = "me">
                        </form>
                        <form action = "<?php echo CONTROL_URL; ?>timeline.php" method = "post">
                            <input type = "submit" value = "フォローのみ">
                            <input type = "hidden" name = "sql_kind" value = "another">
                        </form>
                        <form action = "<?php echo CONTROL_URL; ?>timeline.php" method = "post">
                            <input type = "submit" value = "すべて">
                            <input type = "hidden" name = "sql_kind" value = "all">
                        </form>
                        <form action = "<?php echo CONTROL_URL; ?>timeline.php" method = "post">
                            <input type = "submit" value = "@ツイート">
                            <input type = "hidden" name = "sql_kind" value = "mention">
                        </form>
                    </div>
                    <div class = "contents">
                        <?php foreach($tweet_information as $tweet){ ?>
                        <div class = "tweet_content clearfix">
                            <?php if(isset($my_retweet_user[$tweet['tweet_id']])){ ?>
                            <p>@<?php echo $my_retweet_user[$tweet['tweet_id']]['user_name']; ?>さんをリツイート</p>
                            <?php }if(mb_strlen($tweet['img']) !== 0){ ?>
                            <a><img class = "timeline_img" src = "<?php echo IMAGE_URL.$tweet['img']; ?>"></a>
                            <?php }else{ ?>
                            <a><img class = "timeline_img" src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
                            <?php } ?>
                            <a href="<?php echo CONTROL_URL; ?>profile.php?user_id=<?php echo $tweet['user_id']; ?>"><?php echo $tweet['user_name']; ?></a>
                            <span><?php echo $tweet['date']; ?></span>
                            <a href = "<?php echo CONTROL_URL; ?>tweet.php?tweet_id=<?php echo $tweet['tweet_id']; ?>"><?php echo $tweet['tweet_content']; ?></a>
                        <?php if($user_id === intval($tweet['user_id'])){ ?>
                            <form action = "<?php echo CONTROL_URL; ?>timeline.php" method = "post">
                                <input class = "reply" type = "submit" value = "削除">
                                <input type = "hidden" name = "sql_kind" value = "tweet_delete">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet['tweet_id']; ?>">
                            </form>
                        <?php }else{ 
                                if(!$retweet_check[$tweet['tweet_id']]){?>
                            <form action = "<?php echo CONTROL_URL; ?>timeline.php" method = "post">
                                <input class = "retweet" type = "submit" value = "リツイート">
                                <input type = "hidden" name = "sql_kind" value = "retweet">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet['tweet_id']; ?>">
                            </form>
                        <?php   }else{ ?>
                            <form action = "<?php echo CONTROL_URL; ?>timeline.php" method = "post">
                                <input class = "retweet" type = "submit" value = "リツイートを解除">
                                <input type = "hidden" name = "sql_kind" value = "retweet_remove">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet['tweet_id']; ?>">
                            </form>
                        <?php }} ?>
                        </div>
                        <?php }?>
                    </div>
                </section>
                <section class = "another">
                    <?php if(mb_strlen($message) !== 0){ ?>
                        <section class = "search_result">
                            <p><?php echo $message; ?></p>
                        </section>
                    <?php }if(count($another_user_data) !== 0){ ?>
                    <p>おすすめユーザ</p>
                    <?php foreach($another_user_data as $user){?>
                    <section class = "another_user clearfix">
                        <?php if(mb_strlen($user['img']) !== 0){ ?>
                        <a><img class = "another_img" src = "<?php echo IMAGE_URL.$user['img']; ?>"></a>
                        <?php }else{ ?>
                        <a><img class = "another_img" src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
                        <?php } ?>
                        <a class = "another_name" href = "<?php echo CONTROL_URL; ?>profile.php?user_id=<?php echo $user['user_id']; ?>"><?php echo $user['user_name']; ?></a>
                        <form action = "timeline.php" method = "post">
                            <input class = "follow" type = "submit" value = "フォロー">
                            <input type = "hidden" name = "sql_kind" value = "follow">
                            <input type = "hidden" name = "user_id" value = "<?php echo $user['user_id']; ?>">
                        </form>
                    </section>
                    <?php }} ?>
                </section>
            </article>
            <footer>
            <form action = "<?php echo CONTROL_URL; ?>logout.php" method = "post">
                <input class = "logout" type = "submit" value = "ログアウト">
                <input type = "hidden" name = "sql_kind" value = "logout">
            </form>
            </footer>
        </div>
    </body>
</html>
