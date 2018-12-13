<?php
//Script to connect to database
//Enter the server configuration in the second parameter of the define function.
  define('SERVERNAME','localhost');
  define('DB_USER','your user');
  define('DB_PASSWORD','your password');
  define('DB','your database name');

  $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);

  if ($connection->connect_error){
    die('Connection failed: '.mysqli_connect_error());
  }
  $connection->close();

?>