
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
$total_purchase = '';
$purchase = '';
$total = '';


session_start();

if (isset($_SESSION['customer_id'])) {
  $customer_id = $_SESSION['customer_id'];
} else {
  header('Location: L_login.php');
  exit;
}


if($_SERVER['REQUEST_METHOD'] === "POST") {
    $purchase = $_POST['purchase'];
    if($purchase === "お会計") {
        
        
        if (isset($_POST['total']) === TRUE) {
        $total = trim($_POST['total']);  
        } else {
            $err_msg[] = '合計金額が送られていません';
        }
    }
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
            bike_item.chara,
            bike_carts.amount,
            bike_item.stock,
            bike_item.status
        FROM bike_carts
        INNER JOIN bike_item
        ON bike_item.bike_id = bike_carts.bike_id
        WHERE user_id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$customer_id,PDO::PARAM_INT);
        $stmt->execute();
        $customer_bike = $stmt->fetchAll();
    } catch (PDOException $e){
        $err_msg[] = 'アイテムデータベースのid呼び出し失敗';
    }
    
    $dbh->beginTransaction();
    try{
        //カート内のアイテム一つ一つについて
        foreach ($customer_bike as $value){
            //在庫が足りているか
            if($value['stock'] < $value['amount']){
                $err_msg[] = $value['bike_name'] . '在庫が足りません。';
            }
            //公開されているのかどうか
            if($value['status'] !== '0'){
                $err_msg[] = $value['bike_name'] . '購入できません。';
            }
            if (count($err_msg) === 0) {
                try{
                    $sql = "UPDATE
                        bike_item
                    SET stock = ?
                    WHERE bike_id = ?";
                    $new_stock = $value['stock'] - $value['amount'];
                    var_dump($new_stock);
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1,$new_stock,PDO::PARAM_INT);
                    $stmt->bindValue(2,$value['bike_id'],PDO::PARAM_INT);
                    $stmt->execute();
                } catch (PDOException $e){
                    $err_msg[] = 'カートデータベースの上書き失敗';
                }
            }
        }
        //ログインユーザーのカートの削除
        try{
            $sql = "DELETE FROM
                bike_carts
            WHERE user_id = ?";
            $stmt =$dbh->prepare($sql);
            $stmt->bindvalue(1,$customer_id,PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e){
            $err_msg[] = 'カートの削除が出来ませんでした';
        }
    } catch (PDOException $e) {
        $err_msg[] = '購入処理に失敗';
    }    
        
    if(count($err_msg) === 0){
        $dbh->commit();
        $msg[] = 'データ追加完了トランザクション';
    }else{
        $dbh->rollback();
        $err_msg[] = 'データ追加失敗トランザクション';
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
    <main>
    <nav>
    <section class="left">
        <h3><div class="nav_title">キャラ別詳細</div></h3>
        <div class="school">総北高校</div>
        <ul>
            <!--<li class="#"><a class="#" href="L_product_list.php?chara=小野田坂道&bland=bmc">小野田坂道</a></li>-->
            <li class="#"><a class="icon" href="L_product_list.php?chara=小野田坂道">小野田 坂道</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=今泉俊輔">今泉 俊輔</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=鳴子章吉">鳴子 章吉</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=金城真護">金城 真護</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=巻島裕介">巻島 裕介</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=田所迅">田所 迅</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=手嶋純太">手嶋 純太</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=青八木一">青八木 一</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=杉元照文">杉元 照文</a></li>
        </ul>
        <div class="school">箱根学園</div>
        <ul>
            <li class="#"><a class="icon" href="L_product_list.php?chara=福富寿一">福富 寿一</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=東堂尽八">東堂 尽八</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=荒北靖友">荒北 靖友</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=新開隼人">新開 隼人</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=泉田塔一郎">泉田 塔一郎</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=真波山岳">真波 山岳</a></li>
        </ul>
        <div class="school">京都伏見高校</div>
        <ul>
            <li class="#"><a class="icon" href="L_product_list.php?chara=御堂筋翔">御堂筋 翔</a></li>
            <li class="#"><a class="icon" href="L_product_list.php?chara=石垣光太郎">石垣 光太郎</a></li>
        </ul>
        <div class="school">広島呉南工業高校</div>
        <ul>
            <li class="#"><a class="icon" href="L_product_list.php?chara=待宮栄吉">待宮 栄吉</a></li>
        </ul>
    </section>
    </nav>
    <article>
        <?php if(count($err_msg) === 0) { ?>
            <?php foreach($customer_bike as $value) { ?>
            <div class="one">
                <div class="img_left"><img src="<?php print h($img_dir . $value['img']); ?>" width="200" height="200"></div>
                <div class="img_right">
                <p><?php print h($value['bike_name']); ?></p>
                <p><?php print h($value['brand']); ?></p>
                <p><?php print h($value['price']); ?></p>
                <p><?php print h($value['chara']); ?></p>
                <p><?php print h($value['amount']); ?>個</p>
                </div>
            </div>
            <?php $total_purchase += ($value['price'] * $value['amount']); ?>
            <p>合計金額：<?php print $total_purchase; ?>円</P>
            <p>お買上げありがとうございました。</p>
            <p>商品は後日発送致します。</p>
            <?php } ?>
        <?php } ?>
    </article>
    </main>
    
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