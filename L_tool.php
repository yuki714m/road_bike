<?php
$host     = 'localhost';
$username = 'yuki242m';   // MySQLのユーザ名（ユーザ名を入力してください）
$password = '';       // MySQLのパスワード（空でOKです）
$dbname   = 'codecamp';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$charset  = 'utf8';   // データベースの文字コード
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
$img_dir  = './img/';    // アップロードした画像ファイルの保存ディレクトリ

$data     = array();
$msg      = array();
$err_msg  = array();     // エラーメッセージ
$new_img_filename = '';   // アップロードした新しい画像ファイル名
$bike_id = '';
$bike_name = '';
$name = '';
$log = '';
$price = 100;
$change_stock = 0;
$stock = 0;
$brand = '';
$chara = '';
$change_status = '';
$status = '';
$country = '';
$change_bike_name = '';
$change_price = '';
$brand_array = array(
    '台湾' => array('GIANT', 'MERIDA'),
    '北米' => array('TREK', 'Cannondale', 'Specialized', 'Cervélo', 'Argon18'),
    'イタリア' => array('Bianchi', 'PINARELLO', 'Wilier', 'COLNAGO', 'DE ROSA', 'KUOTA', 'CARRERA'),
    'ドイツ' => array('Focus', 'FELT', 'Corratec', 'Canyon'),
    'フランス' => array('LOOK', 'TIME', 'Lapierre'),
    'スイス' => array('SCOTT', 'BMC'),
    'ベルギー' => array('RIDLEY'),
    'スペイン' => array('BH', 'ORBEA'),
    '日本' => array('ANCHOR')
    );


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kind = $_POST['kind'];
    if($kind === '商品追加'){
        // 2
        if (isset($_POST['new_name']) === TRUE) {
           $bike_name = trim(mb_convert_kana($_POST['new_name'], "s", 'utf-8'));
        }
        if ($bike_name === ''){
            $err_msg[] = ': 商品名が空です。入力して下さい。';
        }
        // 3
        if (isset($_POST['new_price']) === TRUE) {
           $price = trim($_POST['new_price']);    
        }
        $pattern = "/^[1-9][0-9]*$/";  //正の整数のみ
        //$pattern = "/^(0|[1-9][0-9]*)$/";　０と正の整数
        if (preg_match($pattern, $price) !== 1) {
            $err_msg[] = $price . ' : 金額は半角で1円以上で入力して下さい。';
        }
        // 5
        if (isset($_POST['new_stock']) === TRUE) {
            $stock = trim($_POST['new_stock']); 
        }
        $pattern = "/^[1-9][0-9]*$/";  //正の整数のみ
        //$pattern = "/^(0|[1-9][0-9]*)$/"; 　０と正の整数
        if (preg_match($pattern, $stock) !== 1 ) {
            $err_msg[] = $stock . ' : １個以上の整数正の値を入力して下さい';
        }
        // 6
        if (isset($_POST['status']) === TRUE){
            $status = $_POST['status'];
        } else {
            $err_msg[] = '公開非公開情報の調子が悪いです。';
        }
        $pattern = "/^(0|1)$/";
        if (preg_match($pattern, $status) !== 1) {
            $err_msg[] = $status . ' : 不正なステータスが入力されました。';
        }
        // 7
        if (isset($_POST['new_brand']) === TRUE) {
            $brand = $_POST['new_brand'];
            if ($brand === "GIANT") {
                $chara = '福富寿一';
                $country = '台湾';
            } else if ($brand === "MERIDA") {
                $chara = '古賀公貴';
                $country = '台湾';
            } else if ($brand === "TREK") {
                $chara = '金城真護';
                $country = '北米';
            } else if ($brand === "Cannondale") {
                $chara = '手嶋純太';
                $country = '北米';
            } else if ($brand === "Specialized") {
                $chara = '田所迅';
                $country = '北米';
            } else if ($brand === "Cervélo") {
                $chara = '新開隼人';
                $country = '北米';
            } else if ($brand === "Argon18") {
                $chara = '無し';
                $country = '北米';
            } else if ($brand === "Bianchi") {
                $chara = '荒北靖友';
                $country = 'イタリア';
            } else if ($brand === "PINARELLO") {
                $chara = '鳴子章吉';
                $country = 'イタリア';
            } else if ($brand === "Wilier") {
                $chara = '無し';
                $country = 'イタリア';
            } else if ($brand === "COLNAGO") {
                $chara = '待宮栄吉・杉元照文';
                $country = 'イタリア';
            } else if ($brand === "DE ROSA") {
                $chara = '御堂筋翔';
                $country = 'イタリア';
            } else if ($brand === "KUOTA") {
                $chara = '無し';
                $country = 'イタリア';
            } else if ($brand === "CARRERA") {
                $chara = '無し';
                $country = 'イタリア';
            } else if ($brand === "Focus") {
                $chara = '無し';
                $country = 'ドイツ';
            } else if ($brand === "FELT") {
                $chara = '無し';
                $country = 'ドイツ';
            } else if ($brand === "Corratec") {
                $chara = '青八木一';
                $country = 'ドイツ';
            } else if ($brand === "Canyon") {
                $chara = '無し';
                $country = 'ドイツ';
            } else if ($brand === "LOOK") {
                $chara = '真波山岳';
                $country = 'フランス';
            } else if ($brand === "TIME") {
                $chara = '巻島裕介';
                $country = 'フランス';
            } else if ($brand === "Lapierre") {
                $chara = '無し';
                $country = 'フランス';
            } else if ($brand === "SCOTT") {
                $chara = '今泉俊輔';
                $country = 'スイス';
            } else if ($brand === "BMC") {
                $chara = '小野田坂道';
                $country = 'スイス';
            } else if ($brand === "RIDLEY") {
                $chara = '東堂尽八';
                $country = 'ベルギー';
            } else if ($brand === "BH") {
                $chara = '泉田塔一郎';
                $country = 'スペイン';
            } else if ($brand === "ORBEA") {
                $chara = '無し';
                $country = 'スペイン';
            } else if ($brand === "ANCHOR") {
                $chara = '石垣光太郎';
                $country = '日本';
            }
        } else {
            $err_msg[] = 'ブランドが正しく送られていません';
        }
        if($brand === ''){
            $err_msg[] = ': ブランド名が空です。入力して下さい。';
        }
        if($country === ''){
            $err_msg[] = ': 国名が空です。';
        }
       
       
        if (isset($_FILES['new_img']) === TRUE && is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {
            // 画像の拡張子を取得
            $extension = mb_strtolower(pathinfo($_FILES['new_img']['name'], PATHINFO_EXTENSION));
            // 指定の拡張子であるかどうかチェック
            if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {
              // 保存する新しいファイル名の生成（ユニークな値を設定する）
                $file_type = @exif_imagetype($_FILES['new_img']['tmp_name']);
                if($file_type === IMAGETYPE_JPEG ||$file_type === IMAGETYPE_PNG) {
                    $new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
                    // 同名ファイルが存在するかどうかチェック
                    if (is_file($img_dir . $new_img_filename) !== TRUE) {
                        // アップロードされたファイルを指定ディレクトリに移動して保存
                        if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img_filename) !== TRUE) {
                            $err_msg[] = 'ファイルアップロードに失敗しました';
                        }
                    } else {
                        $err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください。';
                    }
                } else {
                    $err_msg[] = '不正なファイル形式です。';
                }
            } else {
                $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEG・PNGのみ利用可能です。';
            }
        } else {
            $err_msg[] = 'ファイルを選択してください';
        }
        
        
    }else if($kind ==='モデル名変更'){
        if(isset($_POST['change_bike_name']) === TRUE){
            $change_bike_name =  trim(mb_convert_kana($_POST['change_bike_name'], "s", 'utf-8'));
        }
        if ($change_bike_name === ''){
            $err_msg[] = ': 商品名が空です。入力して下さい。';
        }
        if(isset($_POST['bike_id']) === TRUE){
            $bike_id = trim($_POST['bike_id']);
        }
        if($bike_id === ''){
            $err_msg[] = 'idが選択されていません。';
        }
    
    
    }else if($kind ==='価格変更'){
        if(isset($_POST['change_price']) === TRUE){
            $change_price =  trim($_POST['change_price']);
        }
        if ($change_price === ''){
            $err_msg[] = ':金額が空です。入力して下さい。';
        }
        $pattern = "/^[1-9][0-9]*$/";  //正の整数のみ
        //$pattern = "/^(0|[1-9][0-9]*)$/";　０と正の整数
        if (preg_match($pattern, $change_price) !== 1) {
            $err_msg[] = $price . ' : 金額は半角で1円以上で入力して下さい。';
        }
        if(isset($_POST['bike_id']) === TRUE){
            $bike_id = trim($_POST['bike_id']);
        }
        if($bike_id === ''){
            $err_msg[] = 'idが選択されていません。';
        }
        
        
    }else if($kind ==='在庫数変更'){
        if (isset($_POST['change_stock'])){
            $change_stock = trim($_POST['change_stock']);
        }
        $pattern = "/^[1-9][0-9]*$/"; //正の整数のみ
        //$pattern = "/^(0|[1-9][0-9]*)$/";  //0と正の整数のみ
        if (preg_match($pattern, $change_stock) ) {
            $msg[] = $change_stock . ' : 正しい在庫数です。（在庫数変更）';
        }else{
            $err_msg[] = '在庫変更：正の整数を入力して下さい';
        }
        if(isset($_POST['bike_id']) === TRUE){
            $bike_id = trim($_POST['bike_id']);
        }
        if($bike_id === ''){
            $err_msg[] = 'idが選択されていません。';
        }
        
        
    }else if($kind ==="表示変更"){
        if(isset($_POST['bike_id']) === TRUE){
            $bike_id = trim($_POST['bike_id']);
        }
        if($bike_id === ''){
            $err_msg[] = 'idが選択されていません。';
        }
        if(isset($_POST['change_status']) === TRUE){
            $change_status = trim($_POST['change_status']);
        }
        if($change_status === ''){
            $err_msg[] = '公開状態が間違っています。';
        }
    }
    
    
    else if($kind ==="削除"){
        if(isset($_POST['bike_id']) === TRUE){
            $bike_id = trim($_POST['bike_id']);
        }
        if($bike_id === ''){
            $err_msg[] = 'idが選択されていません。';
        }
    }
}    
    
    
    // アップロードした新しい画像ファイル名の登録、既存の画像ファイル名の取得
try {
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // エラーがなければ、アップロードした新しい画像ファイル名を保存            
    if(count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST'){
        if($kind ==='商品追加'){
            try {
                // SQL文を作成
                $sql = 'INSERT INTO
                    bike_item
                    (bike_name,
                    price,
                    img,
                    stock,
                    status,
                    brand,
                    country,
                    chara)
                VALUES (?,?,?,?,?,?,?,?)';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $bike_name,PDO::PARAM_STR);
                $stmt->bindValue(2, $price,PDO::PARAM_INT);
                $stmt->bindValue(3, $new_img_filename,PDO::PARAM_STR);
                $stmt->bindValue(4, $stock,PDO::PARAM_INT);
                $stmt->bindValue(5, $status,PDO::PARAM_INT);
                $stmt->bindValue(6, $brand,PDO::PARAM_STR);
                $stmt->bindValue(7, $country,PDO::PARAM_STR);
                $stmt->bindValue(8, $chara,PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                $err_msg[] = '在庫情報追加でERROR!?';
                throw $e;
            }
            if(count($err_msg) === 0){
                $msg[] = 'データ追加完了';
            }else{
                $err_msg[] = 'データ追加失敗';
            }
            
            
        }else if($kind === 'モデル名変更'){
            $sql = 'UPDATE
                bike_item
            SET bike_name = ?
            WHERE bike_id = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $change_bike_name,PDO::PARAM_STR);
            $stmt->bindValue(2, $bike_id,PDO::PARAM_INT);
            $stmt->execute();
            $msg[] = 'モデル名更新完了';


        }else if($kind === '価格変更'){
            $sql = 'UPDATE
                bike_item
            SET price = ?
            WHERE bike_id = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $change_price,PDO::PARAM_INT);
            $stmt->bindValue(2, $bike_id,PDO::PARAM_INT);
            $stmt->execute();
            $msg[] = '金額更新完了';            
            
            
        }else if($kind === '在庫数変更'){
            $sql = 'UPDATE
                bike_item
            SET stock = ?
            WHERE bike_id = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $change_stock,PDO::PARAM_INT);
            $stmt->bindValue(2, $bike_id,PDO::PARAM_INT);
            $stmt->execute();
            $msg[] = '在庫数更新完了';
            
            
        }else if($kind === '表示変更') {
            $sql = 'UPDATE
                bike_item
            SET status = ?
            WHERE bike_id = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $change_status,PDO::PARAM_INT);
            $stmt->bindValue(2, $bike_id,PDO::PARAM_INT);
            $stmt->execute();
            $msg[] = 'ステータス変更';
            
            
        }else if($kind === '削除') {
            $sql = 'DELETE FROM
                bike_item
            WHERE bike_id = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $bike_id,PDO::PARAM_INT);
            $stmt->execute();
            $msg[] = '削除完了';
        }    
    }        
    
    try {
        $sql = 'SELECT
            bike_id,
            bike_name,
            price,
            img,
            stock,
            status,
            brand,
            country,
            chara
        FROM bike_item';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
    } catch (PDOException $e) {
        $err_msg[] = 'データの情報の取得に失敗';
        throw $e;
    }            
} catch (PDOException $e) {
  $err_msg[] = 'DBエラー：'.$e->getMessage();
}
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>画像アップロード</title>
    <style>
        h2 {
            padding-top: 20px;
            border-top: solid 1px;
        }
        table {
            width:auto;
            border-collapse:collapse;
        }
        table,tr,th,td{
            border:solid 1px;
            padding: 10px;
            text-align: center;
        }
        .white {
            background-color:white;
        }
        .gray {
            background-color:#A9A9A9;
        }
    </style>
</head>
<body>
<?php foreach ($msg as $value) { ?>
    <p><?php print h($value); ?></p>
<?php } ?>
<?php foreach ($err_msg as $value) { ?>
    <p><?php print h($value); ?></p>
<?php } ?>
    <h1>ロードバイク販売サイト管理画面</h1>
    <a href="L_user.php">ユーザー管理ページ</a>
    <h2>新規商品追加</h2>
    <form method="post" enctype="multipart/form-data">
        <p>名前: <input type="text" name="new_name" value=""></p>
        <p>値段: <input type="text" name="new_price" value=""></p>
        <p>在庫: <input type="text" name="new_stock" value=""></p>
        
        <select name="new_brand">
            <?php foreach ($brand_array as $country_key => $brands) { ?>
                <optgroup label="<?php print h($country_key); ?>">
                <?php foreach ($brands as $brand) { ?>
                    <option label="<?php print h($brand); ?>" value="<?php print h($brand); ?>">
                        <?php print h($brand); ?>
                    </option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
        
        <p><input type="file" name="new_img"></p>
        <p><select name="status">
            <option value="0">公開</option>
            <option value="1">非公開</option>
        </select></p>
        <p>※キャラクター・生産国は自動追加されます</p>
        <input type="hidden" name="kind" value="商品追加">
        <p><input type="submit" value="■□■□■商品追加■□■□■"></p>
    </form>
    
    <h2>商品情報変更</h2>
    <h4>商品一覧</h4>
    <table>
    <tr>
    <th>画像</th><th>商品名</th><th>値段</th><th>在庫数</th><th>ステータス</th><th>ブランド名</th><th>生産国</th><th>キャラクター</th><th>操作</th>
    </tr>
    <?php foreach ($data as $value)  { ?>
    <?php if($value['status'] === 0){ ?>
    <tr class="white">
    <?php }else { ?>
    <tr class="gray">
    <?php } ?>
     
        <td><img src="<?php print h($img_dir . $value['img']); ?>" width="200" height="200"></td>
        <td>
            <form method="post">
            <input type="text" name="change_bike_name" value="<?php print h($value['bike_name']); ?>">
            <input type="hidden" name="bike_id" value="<?php print h($value['bike_id']); ?>">
            <input type="submit" name="kind" value="モデル名変更">
           </form>
        </td>
        
        
        <td>
            <form method="post">
            <input type="text" name="change_price" value="<?php print h($value['price']); ?>" size="8px">
            <input type="hidden" name="bike_id" value="<?php print h($value['bike_id']); ?>">
            <input type="submit" name="kind" value="価格変更">
           </form>
        </td>
        
        <td>
            <form method="post">
                <input type="text" name="change_stock" value="<?php print h($value['stock']); ?>" size="1">
                <input type="hidden" name="bike_id" value="<?php print h($value['bike_id']); ?>">
                <input type="submit" name="kind" value="在庫数変更">
            </form>    
        </td>
        <td>
            <form method="post">
                <input type="hidden" name="bike_id" value="<?php print h($value['bike_id']); ?>">
                <input type="hidden" name="kind" value="表示変更">
                <?php if($value['status'] === 0){ ?>
                    <input type='hidden' name='change_status' value="1">
                    <input type='submit' value="公開→非公開">
                <?php }else { ?>
                    <input type='hidden' name='change_status' value="0">
                    <input type='submit' value="非公開→公開">
                <?php } ?>
            </form>
        </td>
        <td><?php print h($value['brand']); ?></td>
        <td><?php print h($value['country']); ?></td>
        <td><?php print h($value['chara']); ?></td>
        <td>
            <form method="post">
                <input type="hidden" name="bike_id" value="<?php print h($value['bike_id']); ?>">
                <input type="submit" name="kind" value="削除">
            </form>
        </td>
    </tr>
    <?php } ?>
    </table>
    </body>
</html>