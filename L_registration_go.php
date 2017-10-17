<?php
$host     = 'localhost';
$username = 'yuki242m';   // MySQLのユーザ名（ユーザ名を入力してください）
$password = '';       // MySQLのパスワード（空でOKです）
$dbname   = 'codecamp';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$charset  = 'utf8';   // データベースの文字コード

$user_name = '';
$passwd = '';
$email = '';
$err_msg = array();
$create_datetime = date('Y-m-d H:i:s');
$value ='';
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

if($_SERVER['REQUEST_METHOD'] ==="POST") {
    
    if (isset($_POST['user_name']) === TRUE) {
       $user_name = trim($_POST['user_name']);    
    }
    if ($user_name === ''){
        $err_msg[] = '何か入力して下さい。';
    }
    $pattern = "/^[a-z][a-z0-9]{7,}$/";
    if (preg_match($pattern, $user_name) !== 1) {
        $err_msg[] = $user_name . 'ユーザー名は半角英数で８文字以上入力して下さい。';
    }
    
    if (isset($_POST['passwd']) === TRUE) {
       $passwd = trim($_POST['passwd']);    
    }
    $pattern = "/^[a-z][a-z0-9]{7,}$/";
    if (preg_match($pattern, $passwd) !== 1) {
        $err_msg[] = $passwd . 'パスワードは半角英数で８文字以入力して下さい。';
    }
    
    if (isset($_POST['email']) === TRUE) {
       $email = trim($_POST['email']);    
    }
    $pattern = '|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|';
    if (preg_match($pattern, $email) !== 1) {
        $err_msg[] = $email . 'emailは半角英数で入力して下さい。';
    }
    if(count($err_msg) === 0) {
        try {
            $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            try{
                $sql = "SELECT
                    user_name
                FROM bike_users
                WHERE user_name =?";
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1,$user_name,PDO::PARAM_STR);
                $stmt->execute();
                $user_data = $stmt->fetch();
            } catch (PDOException $e){
            $err_msg[] = '接続できませんでした。理由：'.$e->getMessage();
            }
            if(isset($user_data['user_name']) !== TRUE){
                try{
                    $sql = "INSERT INTO
                            bike_users
                            (user_name,
                            password,
                            email,
                            create_datetime)
                        VALUES (?,?,?,?)";
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1,$user_name,PDO::PARAM_STR);
                    $stmt->bindValue(2,$passwd,PDO::PARAM_STR);
                    $stmt->bindValue(3,$email,PDO::PARAM_STR);
                    $stmt->bindValue(4,$create_datetime, PDO::PARAM_STR);
                    $stmt->execute();
                } catch (PDOException $e){
                $err_msg[] = '接続できませんでした。理由：'.$e->getMessage();
                }
            } else {
                $err_msg[] = 'すでに同じIDをがぞ存在します。';
            }
        } catch (PDOException $e) {
            $err_msg[] = '接続できませんでした。理由：'.$e->getMessage();
        }
    }else{
        $err_msg[] = 'エラーが有るため登録追加失敗';
    }
} else {
    $err_msg[] = 'postされていません';
}
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>登録完了画面</title>
        <style>
            .form{
                width: 300px;
                margin: auto;
            }
        </style>
    </head>
    <body>
        <div class="form"></div>
            <?php foreach($err_msg as $value) { ?>
                <p><?php h(print $value); ?></p>
            <?php } ?>
            <?php if(count($err_msg) === 0) { ?>
                <h2>登録完了</h2>
            <?php } else { ?>
                <h2>登録失敗</h2>
            <?php } ?> 
            <input type="button" onclick="location.href='L_login.php'"value="ログインページへ">
        </div>
    </body>
</html>