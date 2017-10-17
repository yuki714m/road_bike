<?php
$host     = 'localhost';
$username = 'yuki242m';   // MySQLのユーザ名（ユーザ名を入力してください）
$password = '';       // MySQLのパスワード（空でOKです）
$dbname   = 'codecamp';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$charset  = 'utf8';   // データベースの文字コード
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
$user_name = '';
$passwd = '';
$data = array();
$msg = array();
$err_msg = array();



// セッション開始
session_start();
if($_SERVER['REQUEST_METHOD'] !=="POST") {
    $err_msg[] = 'POSTされていません。';
    header('Location: L_login.php');
    exit;
}

if(isset($_POST['user_name']) === TRUE) {
    $user_name = $_POST['user_name'];
} 
if($user_name === '') {
    $err_msg[] = 'ユーザーネームが入力されてません';
}


if(isset($_POST['passwd']) === TRUE) {
    $passwd = $_POST['passwd'];
} 
if($passwd === '') {
    $err_msg[] = 'パスワードが入力されていません。';
}

try {
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $sql = "SELECT 
        user_id
    FROM bike_users
    WHERE user_name = ?
    AND password = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $user_name,PDO::PARAM_STR);
    $stmt->bindValue(2, $passwd,PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch();
    
    if (isset($data['user_id'])) {
        $_SESSION['customer_id'] = $data['user_id'];
        header('Location: L_product_list.php');
        exit;
    } else {
        $_SESSION['err_msg'] = 'ユーザー名・パスワードが違います';
        header('Location: L_login.php');
        exit;
    }
} catch (PDOException $e) {
    $err_msg[] = 'データベース照会エラー';
}    
?>