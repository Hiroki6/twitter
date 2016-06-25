<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "utf-8">
        <title>twitterトップページ</title>
        <link rel = "stylesheet" href = "<?php echo STYLE_URL; ?>twitter_top.css">
    </head>
    <body>
        <header>
            <a><img class = "header_img" src = "<?php echo IMAGE_URL; ?>twitter_logo.png"></a>
        </header>
        <div class = "main"> <!--背景画像-->
            <article class = wrap><!--中心部分-->
                <section class = "catch">
                    <h1>Twitterへようこそ</h1>
                    <p>友達や魅力的な人々をつながって、興味のある最新情報と見つけましょう。そして、いま起きているできごとを様々な角度から見てみましょう。</p>
                </section>
                <div class = "login">
                    <section>
                        <form action = "<?php echo CONTROL_URL; ?>twitter_top.php" method = "post">
                            <input class = "content" type = "text" name = "id" value = "" placeholder = "メールアドレスまたはユーザー名">
                            <input class = "content" type = "password" name = "passwd" value = "" placeholder = "パスワード">
                            <input class = "submit" type = "submit" value = "ログイン">
                        </form>
                    </section>
                    <section>
                        <p>twitter始めませんか？登録する</p>
                        <form action = "<?php echo CONTROL_URL; ?>register.php" method = "post">
                            <input class = "content" type = "text" name = "name" value = "" placeholder = "名前">
                            <input class = "content" type = "text" name = "mail" value = "" placeholder = "メールアドレス">
                            <input class = "content" type = "text" name = "new_passwd" value = "" placeholder = "パスワード">
                            <input class = "register" type = "submit" value = "Twitterに登録する">
                            <input type = "hidden" name = "sql_kind" value = "register">
                        </form>
                    </section>
                </div>
            </article>
            <div class = "error">
                <?php if(count($error_message) !== 0){
                        foreach($error_message as $error){ ?>
                <p><?php echo $error; ?></p>
                <?php }} ?>
            </div>
        </div>
    </body>
</html>
