<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_top_ridge_db_connection = "localhost";
$database_top_ridge_db_connection = "sms";
$username_top_ridge_db_connection = "root";
$password_top_ridge_db_connection = "";
$top_ridge_db_connection = new mysqli($hostname_top_ridge_db_connection, $username_top_ridge_db_connection, $password_top_ridge_db_connection,$database_top_ridge_db_connection) or die ("cannot connect");


//$hostname_top_ridge_db_connection = "mysql5021.site4now.net";
//$database_top_ridge_db_connection = "db_a3328c_kwasios";
//$username_top_ridge_db_connection = "a3328c_kwasios";
//$password_top_ridge_db_connection = "kwasiosei2";
//$top_ridge_db_connection =mysqli_connect($hostname_top_ridge_db_connection, $username_top_ridge_db_connection, $password_top_ridge_db_connection,$database_top_ridge_db_connection) or die ("cannot connect");
