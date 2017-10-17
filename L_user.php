<?php
$host     = 'localhost';
$username = 'yuki242m';   // MySQLのユーザ名（ユーザ名を入力してください）
$password = '';       // MySQLのパスワード（空でOKです）
$dbname   = 'codecamp';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$dbh = ''; 
$charset = 'utf8';
$err_msg = array();
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;


try{
    $dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    $sql = 'SELECT 
        user_name,
        password, 
        email,
        create_datetime, 
        update_datetime
    FROM bike_users';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll();

} catch (PDOException $e) {
    $err_msg[] = '接続できませんでした。理由：' . $e->getMessage();
}
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>ロードバイクお客様管理ページ</title>
	<style>
	    table {
            width:760px;
            border-collapse:collapse;
            table-layout:fixed;
        }
        
        table,tr,th,td{
            border:solid 1px;
            padding: 10px;
            text-align: center;
        }
        .email{
            width:45%;
        }
	</style>
</head>
<body>

<?php foreach ($err_msg as $value) { ?>
    <p><?php print h($value); ?></p>
<?php } ?>
	<h1>ロードバイクお客様管理ページ</h1>
	<a href="L_tool.php">商品管理ページへ</a>
	<h2>ユーザー情報一覧</h2>
	<?php
	foreach($data as $value) { ?>
	<table>
	<tr>
	    <th>ユーザー名</th><th class="email">メールアドレス</th><th>登録日時</th>
	</tr>
	<tr>
	    <td><?php print h($value['user_name']); ?></td>
	    <td><?php print h($value['email']); ?></td>
	    <td><?php print h($value['create_datetime']); ?></td>
	</tr>
    </table>
	<?php } ?>
</body>
</html>