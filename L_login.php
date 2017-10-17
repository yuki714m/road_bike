<?php
$err_msg = array();


session_start();
if(isset($_SESSION['err_msg']) ===TRUE){
    $err_msg[] = $_SESSION['err_msg'];
    unset($_SESSION['err_msg']);
}

if (isset($_SESSION['customer_id'])) {
  header('Location: L_product_list.php');
  exit;
}
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
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
            .bottom {
                margin:5px;
                float: left;
            }
        </style>
    </head>
    <body>
        <div class="form">
            <?php foreach($err_msg as $value){ ?>
                <p><?php print h($value); ?></p>
            <?php } ?>    
            <form action="./L_login_process.php" method="post">
                <label for="user_name">ユーザーネーム</label>
                <input type="text" class="block" id="user_name" name="user_name" value="">
                <label for="passwd">パスワード</label>
                <input type="password" class="block" id="passwd" name="passwd" value="">
                <span class="bottom"><input type="submit" value="ログイン"></span>
            </form>
            
            <form action="./L_registration.php" method="post">
            <span class="bottom"><input type="submit" name="registration" value="会員登録ページへ"></span>
            </form>
        </div>
    </body>
</html>