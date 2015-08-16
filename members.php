<?php 
require_once('header.php');
if(!$loggedin) die();

echo "<div class='main'>";
// view profile
if (isset($_GET['view'])) {
    $view = sanitizeString($_GET['view']);
    if ( strtolower($view) ==  strtolower($user)) $name = "Your";
    else $name = "$view's";
    echo "<h3>$name Profile</h3>";
    showProfile($view);
    echo "<a class='button' href='messages.php?view=$view'>View $name messages</a><br><br>";
    die("</div></body></html>");
}
// add and remove friend
if (isset($_GET['add'])) {
    $add = sanitizeString($_GET['add']);
    $result = queryMysql("SELECT * FROM friends WHERE user='$user' AND friend='$add'");
    if (!$result->num_rows) queryMysql("INSERT INTO friends VALUES('$user', '$add')");    
}
elseif (isset($_GET['remove'])) {
    $remove = sanitizeString($_GET['remove']);
    queryMysql("DELETE FROM friends WHERE user='$user' AND friend='$remove'");
}

// display members
$result = queryMysql("SELECT user FROM members ORDER BY user");
$num = $result->num_rows;
echo "<h3>Other members</h3><ul>";
for ($i = 0; $i < $num; $i++) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if ( strtolower($row['user']) ==  strtolower($user)) continue;
    echo "<li><a href='members.php?view=" . $row['user'] . "'>" . $row['user'] . "</a>";
    // check friend status
    $follow = "follow";
    $result1 = queryMysql("SELECT * FROM friends WHERE user='$user' AND friend='" . $row['user'] . "'");
    $t1 = $result1->num_rows;
    $result1 = queryMysql("SELECT * FROM friends WHERE user='" . $row['user'] . "' AND friend='$user'");
    $t2 = $result1->num_rows;
    if ($t1 + $t2 > 1) echo "&harr; is a mutual friend.";
    elseif ($t1) echo "&larr; you are following";
    elseif ($t2) {
        echo "&rarr; is following you";
        $follow = 'recip'; 
    }

    if (!$t1) {
        echo "[<a href='members.php?add=" . $row['user'] . "'>$follow</a>]";
    }
    else echo "[<a href='members.php?remove=" . $row['user'] . "'>drop</a>]";
    echo "</li>";
}
?>
        </ul></div>
    </body>
</html>
