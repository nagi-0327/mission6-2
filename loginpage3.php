<?php
//my SQLと連携
$dsn = 'mysql:dbname=tb210391db;host=localhost';
$user = 'tb-210391';
$pass = 'CP3dxw8n66';
$pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブル作成（テーブルが存在しないエラーを避けるため念のため。一度作成してあれば要らない）
$sql = "CREATE TABLE IF NOT EXISTS loginfo"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "mailadress TEXT,"
    . "pass TEXT,"
    . "date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
    .");";
$stmt = $pdo->query($sql);

// 1. ユーザIDの入力チェック
if(!empty($_POST["notfirst"])){
    if (empty($_POST["useradress"])) {  // 値が空のとき
        echo'メールアドレスが未入力です。<br>';
    }
    if (empty($_POST["password"])) {
        echo'パスワードが未入力です。<br>';
    }
}
$notfirst = "yes";

if(!empty($_POST["useradress"]) && !empty($_POST["password"])){
    //まず登録済みのアドレスすべてと入力されたアドレスを比較する
    $sql = 'SELECT * FROM loginfo';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        if($_POST["useradress"] == $row['mailadress']){
            $registered = "yes";
            $password = $row['pass'];
        }
    }

    if(empty($registered)){ //未登録だった場合の処理
        echo "このメールアドレスは未登録です。<br>新規登録はページ下部から。";
    }else{
        if(password_verify($_POST["password"], $password)){
           session_regenerate_id(true);
           header("Location:https://tb-210391.tech-base.net/main.php");  // メイン画面へ遷移
           exit();  // 処理終了
       } else {
          // 認証失敗（パスワードが一致していない場合）
          echo'パスワードが違います。';
          echo "<br>";
       }
    }
}
?>

<html>
<head>
<title>ログイン画面</title>
<meta charset="utf-8">
</head>
<body>
<h1>ログイン画面</h1>
 
<form action="" method="POST">
 <input type ="text" name="useradress" value=""placeholder="メールアドレス" ><br>
 <input type ="text" name="password" value=""placeholder="パスワード" ><br>
 <input type="submit" name="login" value="ログイン"><br>
 <input type ="hidden" name="notfirst" value="<?php if(!empty($notfirst)){echo $notfirst;}?>" >
 </form>
 <br>
   <form action="newpage3.php">
        <input type="submit" value="新規登録はこちら">
 </form>
 
</body>
</html>
