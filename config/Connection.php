<?php

    class Connection
    {
        function getNewConnection()
        {
            $env = parse_ini_file(__DIR__ . '/../.env');
            $db = new mysqli($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);
            if ($db->connect_error) {
                die("Connection failed: " . $db->connect_error);
            } 
            return $db;
        }
    }

?>