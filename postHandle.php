<?php
session_start();

$dbhandle = new PDO("sqlite:posts.sqlite") or die("Failed to open DB");
if (!$dbhandle) die ($error);

$verb = $_SERVER["REQUEST_METHOD"];

if ($verb === "POST"){
    if(($_POST['postName'] != NULL) || ($_POST['postName'] != array())){
        $postName = $_POST["postName"];
        console.log($postName);
        $postText = $_POST["postText"];
        console.log($postText);
        $stmt = $dbhandle->query("SELECT * FROM posts ORDER BY number DESC LIMIT 0, 1")->fetch();
        $postNum = ($stmt['number'] + 1);
        console.log($postNum);
	
        $qry = $dbhandle->prepare("INSERT INTO posts (user, number, likes, data, commentnum) VALUES (?, ?, 0, ?, 0)");
        $qry->bindParam(1, $postName);
        $qry->bindParam(2, $postNum);
        $qry->bindParam(3, $postText);
        $qry->execute();
        console.log("Insert Attempted");
        //$qry->execute(array(strval($postName), intval($postNum), strval($postText)));
    }else if(($_POST['postId'] != NULL) || ($_POST['postId'] != array())){
        $postNum = $_POST["postId"];
        $prepper = $dbhandle->prepare("SELECT * FROM posts WHERE number = ?");
        //$prepper->bindParam(1, $postNum);
        $prepper->execute([$postNum]);
        $stmt = $prepper->fetch();
        $likeCount = ($stmt['likes'] + 1);
    	
        $qry = $dbhandle->prepare("UPDATE posts SET likes = ? WHERE number = ?");
        $qry->bindParam(1, $likeCount);
        $qry->bindParam(2, $postNum);
        $qry->execute();
        console.log("Insert Attempted");
    }else{
	$postNum = $_POST["parentPostId"];
	$userName = $_POST["usrName"];
	$prepper = $dbhandle->prepare("SELECT * FROM posts WHERE number = ?");
	$prepper->execute([$postNum]);
	$stmt = $prepper->fetch();
        $commCount = ($stmt['commentnum']);
	    
	$commCount = $commCount + 1;
	$qry = $dbhandle->prepare("UPDATE posts SET commentnum = ? WHERE number = ?");
        $qry->bindParam(1, $commCount);
        $qry->bindParam(2, $postNum);
        $qry->execute();
		
	$qry = $dbhandle->prepare("INSERT INTO comments (postnum, comnum, comtext, comlikes, username) VALUES (?, ?, ?, 0, ?)");
        $qry->bindParam(1, $postName);
	$qry->bindParam(2, $commCount);
        $qry->bindParam(3, $_POST["commentData"]);
        $qry->bindParam(4, $userName);
        $qry->execute();
    }
}
else if ($verb === "GET"){
    if(($_GET["commentedPost"]!=NULL) || ($_GET["commentedPost"]!=array())){
	$postResults=array();
	$postNumber=$_GET["commentedPost"];
	$prepper = $dbhandle->prepare("SELECT * FROM comments WHERE postnum = ?");
	$prepper->execute([$postNumber]);
	$stmt = $prepper->fetchAll();
	foreach($stmt as $row){
	    array_push($postResults,$row['username']);
	    array_push($postResults,$row['comlikes']);
	    array_push($postResults,$row['comtext']);
	}
	header('HTTP/1.1 200 OK');
    	header('Content-Type: application/json');
	echo json_encode($postResults);
    }else{
        $postResults=array();
        //$postNumber=$_GET["postNum"];
        //$postNum = json_decode(file_get_contents('php://input'), true);
        //$prepper = $dbhandle->prepare("SELECT * FROM posts ORDER BY number DESC LIMIT 0, 5");
        //$prepper->execute([intval($postNumber)]);
        //$stmt = $prepper->fetchAll();
        $stmt = $dbhandle->query("SELECT * FROM posts ORDER BY number DESC LIMIT 0, 5")->fetchAll();
        if($stmt == array()){}
        else{
	    foreach ($stmt as $row){
	        array_push($postResults,$row['number']);
	        array_push($postResults,$row['user']);
	        array_push($postResults,$row['likes']);
	        array_push($postResults,$row['data']);
	        array_push($postResults,$row['commentnum']);
	    }
	    header('HTTP/1.1 200 OK');
    	    header('Content-Type: application/json');
	    echo json_encode($postResults);
	}
    }
}
else if ($verb === "PUT"){
    $postNum = $_PUT["postId"];
    $prepper = $dbhandle->prepare("SELECT * FROM posts WHERE number = ?");
    //$prepper->bindParam(1, $postNum);
    $prepper->execute([$postNum]);
    $stmt = $prepper->fetch();
    $likeCount = ($stmt['likes'] + 1);
    	
    $qry = $dbhandle->prepare("UPDATE posts SET likes = ? WHERE number = ?");
    $qry->bindParam(1, $likeCount);
    $qry->bindParam(2, $postNum);
    $qry->execute();
    console.log("Insert Attempted");
}
?>
