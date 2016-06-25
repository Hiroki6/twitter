<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "utf-8">
        <title>Twitterに登録する</title>
        <link rel = "stylesheet" href = "<?php echo STYLE_URL; ?>design.css">
    </head>
    <body>
        <header>
            <a href = "twitter_top.php"><img src = "<?php echo IMAGE_URL; ?>twitter_logo.png" width = "150" height = auto></a>
        </header>
        <div class = "register_main"><!--背景画像-->
            <article class = "register_article"><!--中心部分-->
                <h1>Twitterをはじめましょう</h1>
                <?php if(count($error_message) !== 0){
                        foreach($error_message as $error){ ?>
                <h2><?php echo $error; ?></h2>
                <?php }} ?>
                <form action = "<?php echo CONTROL_URL; ?>check_register.php" method = "post">
                    <p><input class = "input_form" type = "text" name = "name" value = "<?php echo $name;?>" placeholder = "名前"></p>
                    <p><input class = "input_form" type = "text" name = "address" value = "<?php echo $address;?>" placeholder = "メールアドレス"></p>
                    <p><input class = "input_form" type = "text" name = "passwd" value = "<?php echo $passwd;?>" placeholder = "パスワード"></p>
                    <p><input class = "input_form" type = "text" name = "user_name" value = "" placeholder = "ユーザー名"></p>
                    <p><input class = "register" type = "submit" value = "新規登録">
                </form>
            </article>
        </div>
    </body>
</html>
