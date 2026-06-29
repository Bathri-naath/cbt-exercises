<?php


     $SERVERNAME = "localhost";
     $USERNAME = "root";
     $PASSWORD = "Bathri1409_";
     $DBNAME = "deposit";


    $conn = new mysqli($SERVERNAME,$USERNAME,$PASSWORD,$DBNAME);
        
        if ($conn->connect_error){
            die("Connection failed: ".$conn->connect_error);
        }
        echo "Connected Successfully";
