<?php

    $dbhandle = new PDO("sqlite:scrabble.sqlite") or die("Failed to open DB");
    if (!$dbhandle) die ($error);
 
    $count = 1;
    $binaryVar = decbin($count);
    $receiver = $_GET['receiver'];
    $rack = $receiver;
    $sizeOfRack = strlen($rack);
    $finalRackArray= array();

    while(strlen(decbin($count)) < $sizeOfRack + 1){
        $binaryVar = decbin($count);
        $binaryVar = strrev($binaryVar);
        $count ++;
        $tempArray = "";
        for ($x = 0; $x < strlen($binaryVar); $x++) {
            if($binaryVar[$x] == "1" ){
                $tempArray = $tempArray.$rack[$x];
            }
        }
        $query = "SELECT words FROM racks WHERE rack = \"$tempArray\"";
        $statement = $dbhandle->prepare($query);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        if($results == array()){
        }
        else{
            $finalResults = array();
            foreach ($results[0] as $values){
                $finalResults = $values;
            }
            $explodedArray = explode("@@", $finalResults);
            for($i = 0; $i < count($explodedArray); $i++){
                array_push($finalRackArray,$explodedArray[$i]);
            }
        }
    }

    $receiver = $_GET['receiver'];
    
    //this part is perhaps overkill but I wanted to set the HTTP headers and status code
    //making to this line means everything was great with this request
    header('HTTP/1.1 200 OK');
    //this lets the browser know to expect json
    header('Content-Type: application/json');
    //this creates json and gives it back to the browser
    echo json_encode($finalRackArray);
?>
