<?php
if($_SERVER['REQUEST_METHOD'] ==='POST'){
    if(isset($_POST['registration']) !== TRUE) {
        $err_msg[] = 'POSTされていません。';
        header('Location: L_login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ユーザー名自動入力</title>
        <style>
            .form{
                width: 300px;
                margin: auto;
            }
            .block {
                display: block;
                margin-bottom: 10px;
            }
            .small {
                font-size: 0.8em;
            }
        </style>
    </head>
    <body>
    <div class="form">
        <form method="post" action="./L_registration_go.php">
            <label for="user_name">ユーザーID</label>
            <input type="text" class="block" id="user_name" name="user_name" value="">
            
            <label for="passwd">パスワード</label>
            <input type="password" class="block" id="passwd" name="passwd" value="">
            
            <label for="email">email</label>
            <input type="text" class="block" id="email" name="email" value="">
            <input type="submit" class="block" value="新規登録">
        </form>
        </div>
    </body>
</html>