<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=f32ee',
   'f32ee', 'f32ee');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($pdo -> connect_error){
  echo "Database is not online";
  exit;
}
