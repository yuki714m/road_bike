<?php
$host     = 'localhost';
$username = 'yuki242m';   // MySQLのユーザ名（ユーザ名を入力してください）
$password = '';       // MySQLのパスワード（空でOKです）
$dbname   = 'codecamp';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$charset  = 'utf8';   // データベースの文字コード
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
$img_dir  = './img/';    // アップロードした画像ファイルの保存ディレクトリ

$user_name = '';
$passwd = '';
$email = '';
$err_msg = array();
$name = '';
$list = '';
$chara = '';
$brand = '';
$search = '';
$bike_name ='';
$price = '';
$price_min = '';
$price_max = '';
$brand_array = array(
    '台湾' => array('GIANT', 'MERIDA'),
    '北米' => array('TREK', 'Cannondale', 'Specialized', 'Cervélo'),
    'イタリア' => array('Bianchi', 'PINARELLO', 'Wilier', 'COLNAGO', 'DE ROSA', 'KUOTA', 'CARRERA'),
    'ドイツ' => array('Focus', 'FELT', 'Corratec', 'Canyon'),
    'フランス' => array('LOOK', 'TIME', 'Lapierre'),
    'スイス' => array('SCOTT', 'BMC'),
    'ベルギー' => array('RIDLEY'),
    'スペイン' => array('BH', 'ORBEA'),
    '日本' => array('ANCHOR')
    );
$chara = '';
$chara_array = array(
    '小野田坂道' => ('material/小野田.png'),
    '今泉俊輔' => ('material/今泉.png'),
    '鳴子章吉' => ('material/鳴子.png'),
    '金城真護' => ('material/金城.png'),
    '巻島裕介' => ('material/巻島.png'),
    '田所迅' => ('material/田所.png'),
    '手嶋純太' => ('material/手嶋.png'),
    '青八木一' => ('material/青八木.png'),
    '杉元照文' => ('material/杉元.png'),
    '福富寿一' => ('material/福富.png'),
    '東堂尽八' => ('material/東堂.png'),
    '荒北靖友' => ('material/荒北.png'),
    '新開隼人' => ('material/新開.png'),
    '泉田塔一郎' => ('material/泉田.png'),
    '真波山岳' => ('material/真波.png'),
    '御堂筋翔' => ('material/御堂筋.png'),
    '石垣光太郎' => ('material/石垣.png'),
    '待宮栄吉' => ('material/待宮.png')
    );


session_start();

if (isset($_SESSION['customer_id'])) {
  $customer_id = $_SESSION['customer_id'];
} else {
  header('Location: L_login.php');
  exit;
}

if (isset($_GET['chara']) === TRUE) {
    $chara = trim($_GET['chara']);
    print $chara;
}

if (isset($_GET['brand']) === TRUE) {
    $brand = trim($_GET['brand']);
    if($brand === 'brand名') {
        $barnd = '';
    }
    print $brand;
}
if (isset($_GET['search']) === TRUE) {
    $search = trim($_GET['search']);
}



if (isset($_GET['price_min']) === TRUE) {
    $price_min = trim($_GET['price_min']);
}
if($price_min !== '') {
    $pattern = "/^[1-9][0-9]*$/";
    if (preg_match($pattern, $price_min) !== 1) {
        $err_msg[] = $price_min . '下限価格が正しくありません。';
    }
}    


if (isset($_GET['price_max']) === TRUE) {
    $price_max = trim($_GET['price_max']);
}
if($price_max !== '') {
    $pattern = "/^[1-9][0-9]*$/";
    if (preg_match($pattern, $price_max) !== 1) {
        $err_msg[] = $price_max . '上限価格が正しくありません。';
    }
}

if($_SERVER['REQUEST_METHOD'] ==="POST") {
    
    if (isset($_POST['user_name']) === TRUE) {
       $user_name = trim($_POST['user_name']);    
    }
    if ($user_name === ''){
        $err_msg[] = '何か入力して下さい。';
    }
    $pattern = "/^[a-z][a-z0-9]*$/";
    if (preg_match($pattern, $user_name) !== 1) {
        $err_msg[] = $user_name . 'ユーザー名は半角英数で入力して下さい。';
    }
    
    if (isset($_POST['passwd']) === TRUE) {
       $passwd = trim($_POST['passwd']);    
    }
    $pattern = "/^[a-z][a-z0-9]*$/";
    if (preg_match($pattern, $passwd) !== 1) {
        $err_msg[] = $passwd . 'パスワードは半角英数で入力して下さい。';
    }
    
    if (isset($_POST['email']) === TRUE) {
       $email = trim($_POST['email']);    
    }
    $pattern = '|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|';
    if (preg_match($pattern, $email) !== 1) {
        $err_msg[] = $email . 'emailは半角英数で入力して下さい。';
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
        $err_msg[] = 'id呼び出し失敗';
    }
    
    try{
        $sql = "SELECT
            bike_id,
            bike_name,
            price,
            img,
            stock,
            status,
            brand,
            chara
        FROM bike_item
        WHERE status = 0";
        if($chara !== '') {
            $sql .= ' AND chara = :chara';
            // $stmt = $dbh->prepare($sql);
            // $stmt->bindValue(1,$chara,PDO::PARAM_STR);
        }
        if($brand !== '') {
            $sql .= ' AND brand = :brand';
            // $stmt = $dbh->prepare($sql);
            // $stmt->bindValue(1,$brand,PDO::PARAM_STR);
        }
        if($price_min !== ''){
            $sql .= ' AND price >= :price_min';
        }
        if($price_max !== '') {
            $sql .= ' AND price <= :price_max';
            // $stmt = $dbh->prepare($sql);
            // $stmt->bindValue(1,$price_min,PDO::PARAM_INT);
            // $stmt->bindValue(2,$price_max,PDO::PARAM_INT);
        }
        if($search !== '') {
            $sql .= ' AND bike_name LIKE :search';
            // $stmt = $dbh->prepare($sql);
            // $stmt->bindValue(1,'%' . $search .'%',PDO::PARAM_STR);
        }
        $stmt = $dbh->prepare($sql);
        if($chara !== '') {
            $stmt->bindValue(':chara',$chara,PDO::PARAM_STR);
        }
        if($brand !== '') {
            $stmt->bindValue(':brand',$brand,PDO::PARAM_STR);
        }
        if($price_min !== ''){
            $stmt->bindValue(':price_min',$price_min,PDO::PARAM_INT);
        }
        if($price_max !== '') {
            $stmt->bindValue(':price_max',$price_max,PDO::PARAM_INT);
        }
        if($search !== '') {
            $stmt->bindValue(':search','%' . $search .'%',PDO::PARAM_STR);
        }
        $stmt->execute();
        $list = $stmt->fetchAll();

    } catch (PDOException $e){
        $err_msg[] = 'データ呼び出し失敗';
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
    	<!--ジャバスクリプトデザイン-->
    	<link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
        
        <script>
            $(document).ready(function(){
                var options = {
                    auto: true,
                    pause: 4000
                };
                $('.slider').bxSlider(options);
            });
        </script>
        <!--ここまでジャバスクリプトデザイン-->
        
        
    </head>
    <body>
    <?php foreach($err_msg as $value) { ?>
        <p><?php print h($value); ?></p>
    <?php } ?>
    <header>
        <a href="#"><img src="material/freefont_logo_cicaboldItalic.png"></a>
        <!--ジャバスクリプト素材-->
        <div class="slider">
        <div><img src="material/david-marcu-125458.jpg" height="450px" width="960px"></div>
        <div><img src="material/road-bike-1714194_1280.jpg" height="450px" width="960px"></div>
        <div><img src="material/path-1700535_1280.jpg" height="450px" width="960px"></div>
        <div><img src="material/cyclists-422139_1280.jpg" height="450px" width="960px"></div>
        </div>
        <!--ここまでジャバスクリプト-->
        <div class="top">
        <h2 class="top_zero">トップページ</h2>
        <h2 class="top_one">ようこそ　<?php print h($name); ?>さん　</h2>
        
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
        <form method="get">
            <h3>ブランド別：</h3>
            <ul>
                <select name='brand'>
                    <option value="">選択して下さい</option>
                <?php foreach ($brand_array as $country_key => $brands) { ?>
                    <optgroup label="<?php print h($country_key); ?>">
                        <?php foreach($brands as $brand) { ?>
                        <option label="<?php print h($brand); ?>" value="<?php print h($brand); ?>">
                            <?php print h($brand); ?>
                        </option>
                        <?php } ?>
                    </optgroup>    
                <?php } ?>
                </select>
            </ul>
            <h3>価格別：</h3>
            <ul>
                <input type="text" name="price_min" value="" size="8px" placeholder="下限">～
                <input type="text" name="price_max" value="" size="8px" placeholder="上限">
            </ul>
            <h3>モデル名検索：</h3>
            <ul>
                <input type="text" name="search" value="" size="10px" placeholder="ロード名">
                <input type="submit" value="検索">
            </ul>
        </form>
    </section>
    </nav>
    <article>
    <section class="info">
        <h2>　　最新情報</h2>
            <ul>
                <li class="new"><a href="#">最新モデル追加について（2018年モデルの各種メーカー）</a></li>
                <li class="new"><a href="#">今後のパーツの取り揃えについて（2018年モデルの各種メーカー）</a></li>
                <li class="new"><a href="#">10月15日開催の市民ロードレースについて（参加要項含む）</a></li>
                <li class="more"><a href="#">more   </a></li>
    </section>
        <?php if(isset($chara_array[$chara]) === TRUE ) { ?>
            <img src="<?php print $chara_array[$chara]; ?>">
        <?php } ?>
        <?php foreach($list as $value) { ?>
        <div class="one">
            <div class="img_left">
            <img src="<?php print h($img_dir . $value['img']); ?>" width="250" height="250">
            
            </div>
            <div class="img_right">
            <p>モデル名：<?php print h($value['bike_name']); ?></p>
            <p>ブランド：<?php print h($value['brand']); ?></p>
            <p>金額：<?php print h($value['price']); ?></p>
            <p>使用キャラ：<?php print h($value['chara']); ?></p>
            <form method="post" action="L_product_details.php">
                <input type="hidden" name="bike_id" value="<?php print h($value['bike_id']); ?>">
                <input type="submit" name="detail" value="商品詳細ページ">
            </form>
            </div>
        </div>
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