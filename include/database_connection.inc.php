<?php
// Connection to mysql
mysql_connect($mysql_server, $mysql_username, $mysql_password) or die( mysql_error() );

// Select database
mysql_select_db($mysql_db_name) or die( mysql_error() );
?>