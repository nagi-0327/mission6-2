<?php
//my SQLと連携
$dsn = 'mysql:dbname=tb210391db;host=localhost';
$user = 'tb-210391';
$passward = 'CP3dxw8n66';
$pdo = new PDO($dsn, $user, $passward, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


//4－2 テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS loginfo"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "mailadress TEXT,"
    . "pass TEXT,"
    . "date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
    .");";
$stmt = $pdo->query($sql);

//テーブル表示？削除

// エラーメッセージ、登録完了メッセージの初期化←要らなくない？


// 1. ユーザIDの入力チェック
if(!empty($_POST["notfirstnewpage"])){
    if (empty($_POST["useradress"])) {  // 値が空のとき
        echo'メールアドレスが未入力です。<br>';
    }
    if (empty($_POST["password1"])) {
        echo'パスワードが未入力です。<br>';
    }
    if (empty($_POST["password2"])) {
        echo'パスワード(確認用)が未入力です。<br>';
    }
}
$notfirst = "yes";

if (!empty($_POST["useradress"]) && !empty($_POST["password1"]) && !empty($_POST["password2"])){
    if(($_POST["password1"]) == ($_POST["password2"])){ //データ型が異なることある？
        //まず登録済みのアドレスすべてと入力されたアドレスを比較する
        $sql = 'SELECT * FROM loginfo';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($_POST["useradress"] == $row['mailadress']){
                $registered = "yes";
            }
        }

        if(!empty($registered)){ // 登録済みだった場合の処理
            echo "このメールアドレスは既に登録済みです。<br>ログインはページ下部から。";
        }else{                    // ここからアドレスの新規登録作業
            // 入力したメールアドレスとパスワードをデータベースに登録
            $useradress = $_POST["useradress"];
            $password = $_POST["password1"];
            $hash_pass = password_hash($password, PASSWORD_DEFAULT);
        
            $sql = $pdo->prepare("INSERT into loginfo(mailadress, pass) VALUES (:mailadress, :pass)"); //なんかnagi4とnagi3混ざってね？
            $sql->bindParam(':mailadress',$useradress,PDO::PARAM_STR);
            $sql->bindParam(':pass',$hash_pass,PDO::PARAM_STR);
            $sql->execute();  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）


//メール機能
            require 'Exception.php';
            require 'PHPMailer.php';
            require 'SMTP.php';
            require 'setting.php';
// PHPMailerのインスタンス生成
            $mail = new PHPMailer\PHPMailer\PHPMailer();

            $mail->isSMTP(); // SMTPを使うようにメーラーを設定する
            $mail->SMTPAuth = true;
            $mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
            $mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
            $mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
            $mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
            $mail->Port = SMTP_PORT; // 接続するTCPポート

            // メール内容設定
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";
            $mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
            $useradress = $_POST["useradress"];
            $mail->addAddress($useradress); //受信者（送信先）を追加する
            $mail->Subject = MAIL_SUBJECT; // メールタイトル
            $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
            $body = '会員登録が完了しました。';

            $mail->Body  = $body; // メール本文
            // メール送信の実行
            if(!$mail->send()) {
                echo 'メッセージは送られませんでした。';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                echo '送信完了！';
            }
        }
    }
    if($_POST["password1"] != $_POST["password2"]) {
        echo 'パスワードに誤りがあります。';
    }
}

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>新規登録</title>
</head>
<body>
<h1>新規登録画面</h1>
<form id="loginForm" name="loginForm" action="" method="POST">
    <fieldset>
        <legend>新規登録フォーム</legend>

        <label for="useradress">メールアドレス　　　</label><input type="text" name="useradress" placeholder="メールアドレスを入力" value="<?php if (!empty($_POST["useradress"])) {echo htmlspecialchars($_POST["useradress"], ENT_QUOTES);} ?>">
        <br>
        <label for="password1"> パスワード　　　　　</label><input type="password"  name="password1" value="" placeholder="パスワードを入力">
        <br>
        <label for="password2"> パスワード(確認用)　</label><input type="password" name="password2" value="" placeholder="再度パスワードを入力">
        <br>
        <input type="submit" id="signUp" name="signUp" value="新規登録">
        <input type ="hidden" name="notfirstnewpage" value="<?php if(!empty($notfirst)){echo $notfirst;}?>" >

    </fieldset>
</form>
<br>
<form action="loginpage3.php">
    <input type="submit" value="ログインはこちら">
</form>
</body>