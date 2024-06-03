<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');

    @include 'connect.php';

    foreach ($_SERVER as $parm => $value)  
    echo $parm .'='. $value."<br><br>";
    echo json_encode(array($parm => $value));

