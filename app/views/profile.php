<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "utf-8">
        <title>プロフィール</title>
        <link rel = "stylesheet" href = "<?php echo STYLE_URL; ?>design_profile.css">
    </head>
    <body>
        <header>
            <a class = "home" href = "timeline.php"><img src = "<?php echo IMAGE_URL; ?>home.jpg"></a>
            <a><img class = "logo" src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
            <form action = "<?php echo CONTROL_URL; ?>profile.php" method = "post">
                <input class = "search" type = "text" size = "40" name = "search" value = "" placeholder = "twitterを検索">
                <input type = "submit" value = "検索">
                <input type = "hidden" name = "sql_kind" value = "search">
            </form> 
        </header>
        <div class = "main">
            <?php if(count($error_message) !== 0){ 
                        foreach($error_message as $error){ ?>
                <div class = "error">
                    <p><?php echo $error; ?></p>
                </div>
            <?php }}
                if($profile_switch !== 0){
                    if($follow_flag){
            ?>
             <form class = "follow_action" action = "<?php echo CONTROL_URL; ?>profile.php" method = "post">
                <input class = "follow" type = "submit" value = "フォローを解除">
                <input type = "hidden" name = "sql_kind" value = "follow_remove">
                <input type = "hidden" name = "user_id" value = "<?php echo $user_id; ?>">
            </form>
            <?php }else{ ?>
            <form class = "follow_action" action = "<?php echo CONTROL_URL; ?>profile.php" method = "post">
                <input class = "follow" type = "submit" value = "フォロー">
                <input type = "hidden" name = "sql_kind" value = "follow">
                <input type = "hidden" name = "user_id" value = "<?php echo $user_id; ?>">
            </form>
            <?php } } ?>
            <article>
                <section class = "profile">
                    <?php if(mb_strlen($user_data['img']) !== 0){ ?>
                    <a><img class = "profile_img" src = "<?php echo IMAGE_URL.$user_data['img']; ?>"></a>
                    <?php }else{ ?>
                    <a><img class = "profile_img" src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
                    <?php } ?>
                    <p><?php echo $user_data['user_name']; ?></p>
                    <p><?php echo $user_data['introduce']; ?></p>
                    <p><img class = "location" src = "<?php echo IMAGE_URL; ?>location.jpg"><?php echo $user_data['location']; ?></p>
                </section>
                <section class = "timeline">
                    <div class = "detail">
                        <ul>
                            <a>
                                <li>ツイート</li>
                                <li><?php echo $user_information['tweet_count']; ?></li>
                            </a>
                            <?php if($profile_switch !== 0){ ?>
                            <a href = "<?php echo CONTROL_URL; ?>follow.php?user_id=<?php echo $user_id; ?>">
                            <?php }else{ ?>
                            <a href = "<?php echo CONTROL_URL; ?>follow.php">
                            <?php } ?>
                                <li>フォロー</li>
                                <li><?php echo $user_information['follow']; ?></li>
                            </a>
                            <?php if($profile_switch !== 0){ ?>
                            <a href = "<?php echo CONTROL_URL; ?>follower.php?user_id=<?php echo $user_id; ?>">
                            <?php }else{ ?>
                            <a href = "<?php echo CONTROL_URL; ?>follower.php">
                            <?php } ?>
                                <li>フォロワー</li>
                                <li><?php echo $user_information['follower']; ?></li>
                            </a>
                        </ul>
                    </div>
                    <?php if($profile_switch === 0){ ?>
                    <div class = "tweet">
                        <form action = "<?php echo CONTROL_URL; ?>profile.php" method = "post">
                            <input class = "content" type = "text" name = "tweet" value = "" placeholder = "ツイートする">
                            <input class = "submit" type = "submit" value = "ツイート">
                            <input type = "hidden" name = "sql_kind" value = "do_tweet">
                        </form>
                    </div>
                    <?php } ?>
                    <div class = "contents">
                        <?php foreach($tweet_information as $tweet){ ?>
                        <div class = "tweet_content">
                            <?php if(isset($my_retweet_user[$tweet['tweet_id']])){ ?>
                            <p>@<?php echo $my_retweet_user[$tweet['tweet_id']]['user_name']; ?>さんをリツイート</p>
                            <?php }if(mb_strlen($tweet['img']) !== 0){ ?>
                            <a><img class = "timeline_img" src = "<?php echo IMAGE_URL.$tweet['img']; ?>"></a>
                            <?php }else{ ?>
                            <a><img class = "timeline_img" src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
                            <?php } ?>
                            <span><?php echo $tweet['user_name']; ?></span>
                            <span><?php echo $tweet['date']; ?></span>
                            <a href = "<?php echo CONTROL_URL; ?>tweet.php?tweet_id=<?php echo $tweet['tweet_id']; ?>"><?php echo $tweet['tweet_content']; ?></a>
                        <?php if($profile_switch === 0){ ?>
                            <form action = "<?php echo CONTROL_URL; ?>profile.php" method = "post">
                                <input class = "reply" type = "submit" value = "削除">
                                <input type = "hidden" name = "sql_kind" value = "tweet_delete">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet['tweet_id']; ?>">
                            </form>
                        </div>
                        <?php }else{
                                    if(!$retweet_check[$tweet['tweet_id']]){ ?>
                            <form action = "<?php echo CONTROL_URL; ?>profile.php" method = "post">
                                <input class = "retweet" type = "submit" value = "リツイート">
                                <input type = "hidden" name = "sql_kind" value = "retweet">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet['tweet_id']; ?>">
                            </form>
                        <?php }else{ ?>
                            <form action = "<?php echo CONTROL_URL; ?>profile.php" method = "post">
                                <input class = "retweet" type = "submit" value = "リツイートを解除">
                                <input type = "hidden" name = "sql_kind" value = "retweet_remove">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet['tweet_id']; ?>">
                            </form>
                        <?php } ?>
                        </div>
                        <?php }} ?>
                    </div>
                </section>
                <?php if($profile_switch === 0){ ?>
                <section class = "edition">
                    <p>プロフィールを編集</p>
                    <form action = "<?php echo CONTROL_URL; ?>profile.php" method = "post" enctype = "multipart/form-data">
                        <div class = "element">名前</div>
                        <input type = "text" name = "name" value = "<?php echo $user_data['user_name']; ?>">
                        <div class = "element">場所</div>
                        <input type = "text" name = "location" value = "<?php echo $user_data['location']; ?>">
                        <div class = "element">自己紹介</div>
                        <input type = "text" name = "introduce" value = "<?php echo $user_data['introduce']; ?>">
                        <div class = "element">ヘッダー画像を追加する</div>
                        <input type = "file" name = "add_pic">
                        <input type = "hidden" name = "sql_kind" value = "profile_change">
                        <input type = "submit" value = "変更を保存">
                    </form>
                </section>
                <?php } ?>
            </article>
        </div>
        <footer>
            <form action = "<?php echo CONTROL_URL; ?>logout.php" method = "post">
                <input class = "logout" type = "submit" value = "ログアウト">
                <input type = "hidden" name = "sql_kind" value = "logout">
            </form>
        </footer>
    </body>
</html>
