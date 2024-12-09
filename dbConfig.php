<?php

    $db_connection = new mysqli("localhost","root","","video_platformdb");

    if($db_connection->connect_error)
    {
        die ("connetion failed".$db_connection->connect_error);
    }
?>