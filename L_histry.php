
<?php
$host     = 'localhost';
$username = 'yuki242m';   // MySQLのユーザ名（ユーザ名を入力してください）
$password = '';       // MySQLのパスワード（空でOKです）
$dbname   = 'codecamp';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$charset  = 'utf8';   // データベースの文字コード
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
$img_dir  = './img/';    // アップロードした画像ファイルの保存ディレクトリ

$bike_id = '';
$err_msg = array();
$name = '';
$list = '';
$customer_bike = '';
$customer_id = '';


session_start();

if (isset($_SESSION['customer_id'])) {
  $customer_id = $_SESSION['customer_id'];
} else {
  header('Location: L_login.php');
  exit;
}

try {
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    try{
        $sql = "SELECT
            user_name
        FROM bike_users
        WHERE user_id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$customer_id,PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch();
        $name = $data['user_name'];
    } catch (PDOException $e){
        $err_msg[] = 'ユーザーデータベースのid呼び出し失敗';
    }


    try{
        $sql = "SELECT
            bike_item.bike_id, 
            bike_item.bike_name,
            bike_item.price,
            bike_item.img,
            bike_item.brand,
            bike_histry.amount,
            bike_histry.create_datetime
        FROM bike_histry
        INNER JOIN bike_item
        ON bike_item.bike_id = bike_histry.bike_id
        WHERE user_id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$customer_id,PDO::PARAM_INT);
        $stmt->execute();
        $customer_bike = $stmt->fetchAll();
    } catch (PDOException $e){
        $err_msg[] = 'アイテムデータベースのid呼び出し失敗';
    }
    
} catch (PDOException $e) {
    $err_msg[] = '接続できませんでした。理由：'.$e->getMessage();
}
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>ロードバイクの通販サイト</title>
        <link rel="stylesheet" href="html5reset-1.6.1.css">
    	<link rel="stylesheet" href="L_product_list.css">
    	<style>
    	table {
            width:960px;
            border-collapse:collapse;
            table-layout:fixed;
        }
        
        table,tr,th,td{
            border:solid 1px;
            padding: 10px;
            text-align: center;
        }
    	</style>
    </head>
    <body>
    <?php foreach($err_msg as $value) { ?>
        <p><?php print $value; ?></p>
    <?php } ?>
    <header>
        <a href="#"><img src="material/freefont_logo_cicaboldItalic.png"></a>
        <a href="#"><img  src="material/david-marcu-125458.jpg" height="450px" width="960px"></a>
        
        <div class="top">
        <h2 class="top_zero">お支払ページ</h2>
        <h2 class="top_one"><?php print h($name); ?>さん　ようこそ</h2>
        
        <input class="top_two" type="button" onclick="location.href='L_logout.php'" value="ログアウト">
        <input class="top_three" type="button" onclick="location.href='L_carts.php'" value="カートページ">
        <input class="top_four" type="button" onclick="location.href='L_product_list.php'" value="トップページ">
        
        </div>
        <div class="clear"></div>
    </header>


        <?php if(count($err_msg) === 0) { ?>
            <table>
            <tr>
                <th>お買上げ日</th><th>商品画像</th><th>モデル名</th><th>ブランド名</th><th>価格</th><th>数量</th>
            </tr>
            </table>
            <?php foreach($customer_bike as $value) { ?>                
            <table>
            <tr>
                <td><?php print h($value['create_datetime']); ?></td>
                <td><img src="<?php print h($img_dir . $value['img']); ?>" width="100" height="100"></td>
                <td><?php print h($value['bike_name']); ?></td>
                <td><?php print h($value['brand']); ?></td>
                <td><?php print h($value['price']); ?>円</td>
                <td><?php print h($value['amount']); ?>個</td>
                <?php
                $total_price += $value['price'];
                $total_amount += $value['amount'];
                ?>
            </tr>
            </table>    
            <?php } ?>
            <p>今までのお買上げ金額：<?php print h($total_price); ?>でした。毎度ありがとうございます。</p>
            <p>合計お買上げ点数：<?php print h($total_amount); ?>個でした。毎度ありがとうございます。</p>
        <?php } ?>


    
    <footer>
    	<div class="footer">
		<ul  class="footerzero">
			<li class="foot"><a href="#">サイトマップ</a></li>
			<li class="foot"><a href="#">プライバシーポリシー</a></li>
			<li class="foot"><a href="#">問い合わせ</a></li>
			<li class="foot"><a href="#">ご利用ガイド</a></li>
		</ul>
		 <p><small>Copyright &copy; CodeCamp All Rights Reserved.</small></p>
	</div>
        
        
    </footer>
    </body>
</html>