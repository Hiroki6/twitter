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
            <?php if(count($error_message) !== 0){ 
                        foreach($error_message as $error){ ?>
                <div class = "error">
                    <p><?php echo $error; ?></p>
                </div>
            <?php }}else{ ?>
            <article>
                    <section class = "timeline center">
                        <div class = "tweet clearfix">
                            <?php if(strlen($retweet_user) !== 0){ ?>
                            <p class = "retweet_user">@<?php echo $retweet_user; ?>さんをリツイート</p>
                            <?php } ?>
                            <a><img class = "timeline_img" src = "<?php echo IMAGE_URL.$tweet_information['img']; ?>"></a>
                            <a href="<?php echo CONTROL_URL; ?>profile.php?user_id=<?php echo $tweet['user_id']; ?>"><?php echo $tweet_information['user_name']; ?></a>
                            <span><?php echo $tweet_information['date']; ?></span>
                            <p><?php echo $tweet_information['tweet_content']; ?></p>
                            <?php if($tweet_information['user_id'] !== $user_id){ ?>
                            <form action = "<?php echo CONTROL_URL; ?>tweet.php" method = "post">
                                <input class = "reply_content" type = "text" name = "reply" value = "">
                                <input class = "reply" type = "submit" value = "返信">
                                <input type = "hidden" name = "sql_kind" value = "reply">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet_information['tweet_id']; ?>">
                            </form>
                            <?php if(!$retweet_check){ ?>
                            <form action = "<?php echo CONTROL_URL; ?>tweet.php" method = "post">
                                <input class = "retweet" type = "submit" value = "リツイート">
                                <input type = "hidden" name = "sql_kind" value = "retweet">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet_information['tweet_id']; ?>">
                            </form>
                            <?php }else{ ?>
                            <form action = "<?php echo CONTROL_URL; ?>tweet.php" method = "post">
                                <input class = "retweet" type = "submit" value = "リツイートを解除">
                                <input type = "hidden" name = "sql_kind" value = "retweet_remove">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $tweet_information['tweet_id']; ?>">
                            </form>
                            <?php }} ?>
                        </div>
                    <div class = "contents">
                        <?php foreach($reply_information as $reply){ ?>
                        <div class = "tweet_content clearfix">
                            <a><img class = "timeline_img" src = "<?php echo IMAGE_URL.$reply['img']; ?>"</a>
                            <a href="<?php echo CONTROL_URL; ?>profile.php?user_id=<?php echo $tweet['user_id']; ?>"><?php echo $reply['user_name']; ?></a>
                            <span><?php echo $reply['date']; ?></span>
                            <a><?php echo $reply['reply_content']; ?></a>
                            <?php if($reply['user_id'] === $user_id){ ?>
                            <form action = "<?php echo CONTROL_URL; ?>tweet.php" method = "post">
                                <input class = "reply" type = "submit" value = "削除">
                                <input type = "hidden" name = "sql_kind" value = "reply_delete">
                                <input type = "hidden" name = "reply_id" value = "<?php echo $reply['reply_id']; ?>">
                                <input type = "hidden" name = "tweet_id" value = "<?php echo $reply['tweet_id']; ?>">
                            </form>
                        </div>
                        <?php }} ?>
                    </div>
                </section>
            </article>
            <?php } ?>
        </div>
        <footer>
            <form action = "<?php echo CONTROL_URL; ?>logout.php" method = "post">
                <input class = "logout" type = "submit" value = "ログアウト">
                <input type = "hidden" name = "sql_kind" value = "logout">
            </form>
        </footer>
    </body>
</html>
