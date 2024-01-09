<?php

function connect(){
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "tax_calculator";
    
    $conn = mysqli_connect($server, $username, $password, $database);

        if(!$conn){
            die("Connection Failed: ".mysqli_connect_error());
        }

    return $conn;
}

?>