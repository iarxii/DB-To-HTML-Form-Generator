<?php
// administrative mysql db connection declarations

define('DB_SERVER','localhost');
define('DB_USERNAME','e_licensing_db_client');
define('DB_PASSWORD','fNZN%#lALa010zH3v:J4:pn5u%_:Vw(k');
define('DB_DATABASE','gdoh_e-licensing_db');

// define('DB_SERVER', $_SESSION['DB_SERVER']);
// define('DB_USERNAME', $_SESSION['DB_USERNAME']);
// define('DB_PASSWORD', $_SESSION['DB_PASSWORD']);
// define('DB_DATABASE', $_SESSION['DB_DATABASE']);

$dbconn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);