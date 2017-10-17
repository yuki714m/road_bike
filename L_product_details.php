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
$detail = '';


session_start();

if (isset($_SESSION['customer_id'])) {
  $customer_id = $_SESSION['customer_id'];
} else {
  header('Location: L_login.php');
  exit;
}

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $detail = $_POST['detail'];
    if($detail === "商品詳細ページ") {
        if (isset($_POST['bike_id']) === TRUE) {
        $bike_id = trim($_POST['bike_id']);  
        } else {
            $err_msg[] = 'idが送られていません';
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
            bike_id,
            bike_name,
            price,
            img,
            brand,
            chara,
            country
        FROM bike_item
        WHERE bike_id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$bike_id,PDO::PARAM_INT);
        $stmt->execute();
        $list = $stmt->fetchAll();
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
    </head>
    <body>
    <?php foreach($err_msg as $value) { ?>
        <p><?php print h($value); ?></p>
    <?php } ?>
    <header>
        <a href="#"><img src="material/freefont_logo_cicaboldItalic.png"></a>
        <a href="#"><img  src="material/david-marcu-125458.jpg" height="450px" width="960px"></a>
        
        <div class="top">
        <h2 class="top_zero">商品詳細ページ</h2>
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
        
        <?php if (isset($_POST['bike_id']) === TRUE) { ?>
            <?php foreach($list as $value) { ?>
                <div class="one">
                    <div class="img_left">
                    <p><img src="<?php print h($img_dir . $value['img']); ?>" width="200" height="200"></p>
                    </div>
                    <div class="img_left">
                    <p>モデル名：<?php print h($value['bike_name']); ?></p>
                    <p>ブランド名：<?php print h($value['brand']); ?></p>
                    <p>金額：<?php print h($value['price']); ?></p>
                    <p>使用キャラ：<?php print h($value['chara']); ?></p>
                    <p>生産国：<?php print h($value['country']); ?></p>
                    <form method="post" action="L_carts.php">
                        <input type="hidden" name="bike_id" value="<?php print h($value['bike_id']); ?>">
                        <input type="submit" name="carts" value="カートに入れる">
                    </form>    
                    </div>
                </div>
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