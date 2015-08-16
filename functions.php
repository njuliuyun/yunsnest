<?php 
$dbhost = 'localhost';
$dbname = 'yunsnest';
$dbuser = 'yun';
$dbpass = '709309';
$appname = "Yun's Nest";

$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($connection->connect_error) die($connection->connect_error);

function createTable($name, $query) {
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br>";
}

function queryMysql($query) {
    global $connection;
    $result = $connection->query($query);
    if (!$result) die($connection->error);
    return $result;
}

function destroySession() {
    $_SESSION = array();
    if (session_id() != "" || isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-259200, '/');
    }
    session_destroy();
}

function sanitizeString($var) {
    global $connection;
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return $connection->real_escape_string($var);
}

function showProfile($user) {
    if (file_exists("$user.jpg")) {
        echo "<img src='$user.jpg' style='float:left;'>";
    }
    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
    
    if ($result->num_rows) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        echo stripslashes($row['text']) . "<br style='clear:both;'><br>";
    }    
}

function secure($pass) {
    $salt1 = "qm&h*";
    $salt2 = "pg!@";
    return hash('ripemd128', "$salt1$pass$salt2");
}