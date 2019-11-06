<?php
ini_set('mbstring.internal_encoding' , 'UTF-8');
//my SQLと連携
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


//4－2 テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS nagi10"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
        . "pass TEXT," 
        . "date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP" 
	.");";
$stmt = $pdo->query($sql);

//selectで表示
$sql = 'SELECT * FROM nagi10';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();

if(!empty ($_POST["namae"])){
if(!empty ($_POST["naiyou"])){
    if(empty ($_POST["hensyu2"])){

//ループ一回目の時の処理        
      if(empty($array)){
         $sql = $pdo -> prepare("INSERT INTO nagi10 (name, comment, id, pass, date) VALUES (:name, :comment, :id, :pass, :date)");
         $sql -> bindParam(':name', $name, PDO::PARAM_STR);
         $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
         $sql  -> bindParam(':id', $id, PDO::PARAM_STR);
         $sql  -> bindParam(':pass', $pass, PDO::PARAM_STR);
         $sql  -> bindParam(':date', $date, PDO::PARAM_STR);

         $name = $_POST["namae"];
         $comment =$_POST["naiyou"] ;      
         $pass=$_POST["pass"];
         $date=date("Y/m/d H:i:s");
         $sql -> execute();
         
      }
    }else {
//編集番号が入っている時に編集内容を更新
         $id = $_POST["hensyu2"];     
         $name = $_POST["namae"];
         $comment =$_POST["naiyou"] ;
         $pass=$_POST["pass"];
         $date=date("Y/m/d H:i:s");

         $sql = 'update nagi10 set name=:name, comment=:comment, date=:date where id=:id';
         $stmt = $pdo->prepare($sql);
         $stmt->bindParam(':name', $name, PDO::PARAM_STR);
         $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
         $stmt->bindParam('date', $date, PDO::PARAM_STR);
         $stmt->bindParam(':id', $id, PDO::PARAM_INT);
         $stmt->execute();
    }         
}
}
//削除するときの動き
if(!empty($_POST["sakujo"])){
  foreach ($results as $row){
    if($row['id']==$_POST["sakujo"]){
      $chanu = $row['pass'];
    }
  }
  if($_POST["pass1"]==$chanu){
    $id =$_POST["sakujo"];
    $sql = 'delete from nagi10 where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
  }
}
//編集するときの動き
if(!empty($_POST["hensyu"])){
       foreach($results as $row){
               if($row['pass']== $_POST["pass2"]){
            if($_POST["hensyu"]== $row['id']){
               $hensyunumber  = $row['id'];
               $hensyuname    = $row['name'];
               $hensyucomment = $row['comment'];
            }
               }
        }
}

?>


<html>
<head>
<meta http-equiv="content-type" charset="utf-8">
<body>
<form method="post" action="">

<input type ="text" name="namae" value="<?php if(!empty($hensyuname)){echo $hensyuname;}?>"placeholder="お名前" ><br>
<textarea name="naiyou" rows="20" cols="1000" value="" placeholder ="自由に意見を記述してください。"> </textarea>
<br>
<input type ="text" name="pass" value="" placeholder="パスワード" >
<input type="submit" value="送信"><br>
<br>


<input type ="text" name="sakujo" value=""placeholder="削除対象番号" ><br>
<input type ="text" name="pass1" value="" placeholder="パスワード" >
<input type="submit" value="削除"><br><br>


<input type ="text" name="hensyu" value=""placeholder="編集対象番号" ><br>
<input type ="text" name="pass2" value="" placeholder="パスワード" >
<input type="submit" value="編集"><br><br>

<input type ="hidden" name="hensyu2" value="<?php if(!empty($hensyunumber)){echo $hensyunumber;}?>"placeholder"編集対象番号2" >


</head>
</body>
</form>
</html>

<?php
$sql = 'SELECT * FROM nagi10';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();

foreach ($results as $row){
 echo $row['id'].',';
 echo $row['name'].',';
 echo $row['comment'].',';
 echo $row['date'].'<br>'; 
 echo "<hr>";
}

?>