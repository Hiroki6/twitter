<!DOCTYPE html>
<html lang = "ja">
    <head>
        <title>フォロー</title>
        <meta charset = "utf-8">
        <link rel = "stylesheet" href = "<?php echo STYLE_URL; ?>follow.css">
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
        <div class = "main red">
            <article>
                <section class = "profile light_red">
                    <?php if(mb_strlen($user_data['img']) !== 0){?>
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
                        <ul class = "hover_red">
                            <?php if($profile_switch !== 0){ ?>
                            <a href = "<?php echo CONTROL_URL; ?>profile.php?user_id=<?php echo $user_id; ?>">
                            <?php }else{?>
                            <a href = "<?php echo CONTROL_URL; ?>profile.php">
                            <?php } ?>
                                <li>ツイート</li>
                                <li><?php echo $user_information['tweet_count']; ?></li>
                            </a>
                            <a class = "select_red">
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
                    <div class = "follow_people">
                        <?php foreach($follow_information as $follow){ ?>
                        <div class = "follow_person">
                            <?php if(mb_strlen($follow['img']) !== 0){ ?>
                            <a><img src = "<?php echo IMAGE_URL.$follow['img']; ?>"></a>
                            <?php }else{ ?>
                            <a><img src = "<?php echo IMAGE_URL; ?>Twitter_logo_blue.png"></a>
                            <?php } ?>
                            <a class = "another_name" href = "<?php echo CONTROL_URL; ?>profile.php?user_id=<?php echo $follow['user_id']; ?>"><?php echo $follow['user_name']; ?></a>
                        </div>
                        <?php } ?>
                    </div>
                </section>
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
