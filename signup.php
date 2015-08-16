<?php
require_once('header.php');
echo <<<_END
<script>
    function checkUser(user) {
        if (user.value == '') {
            O('info').innerHTML = '';
            return;
        }
        params = 'user=' + user.value;
        request = ajaxRequest();
        request.open('POST', 'checkuser.php', true);
        request.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        //request.setRequestHeader('Content-length', params.length);
        //request.setRequestHeader('Connection', 'close');
        
        request.onreadystatechange = function() {
            if (this.readyState = 4) {
                if (this.state = 200) {
                    if (this.responseText != null) {
                        O('info').innerHTML = this.responseText;
                    }
                }
            }
        }
        request.send(params);
    }
    
    function ajaxRequest() {
        try { var request = new XMLHttpRequest(); } // Non IE Browser
        catch(e1) {
            try { request = new ActiveXObject("Msxml2.XMLHTTP"); } //IE 6+
            catch(e2) {
                try { request = new ActiveXObject("Microsoft.XMLHTTP"); } //IE 5
                catch(e3) {
                    request = false;
                }
            }
        }
        return request;
    }
    
    function conformPass(oPassc) {
        var pass1 = O('pass1').value;
        var pass2 = oPassc.value;
        if (pass1 != pass2) {
            O('pass_info').innerHTML = "<span class='taken'>&nbsp;&#x2718; The passwords don't match.</span>"             
        }
        else O('pass_info').innerHTML = "";
    }
</script>
    <div class="main"><h3>Please enter your details to sign up.</h3>
_END;

// check submitted information

$error = $user = $pass = $pass2 = "";

if (isset($_SESSION['user'])) destroySession();
if (isset($_POST['user'])) {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']); 
    $pass2 = sanitizeString($_POST['passc']);
    if ($user == '' || $pass=='' || $pass2 == '') $error = 'Not all fields were entered.';
    else if ( $pass != $pass2) $error = "The passwords don't match.";
    else {
        $result = queryMysql("SELECT * FROM members WHERE user='$user'");
        if ($result->num_rows) {
            $error = "That username already exists<br><br>";
        }
        else {
            $passS = secure($pass);
            queryMysql("INSERT INTO members VALUES('$user', '$passS')");
            die("<h4>Account created</h4>Please Log in.<br><br>");
        }
    }
}

// sign up form
echo <<<_END
        <form method="post" action="signup.php">$error<br>
        <span class="fieldname">Username</span>
        <input type="text" maxlength="16" name="user" value='$user' onBlur="checkUser(this)">
        <span id="info"></span><br>
        <span class="fieldname">Password</span>
        <input type="password" maxlength="16" name="pass" value="$pass" id="pass1"><br>
        <span class="fieldname">Conform Password</span>
        <input type="password" maxlength="16" name="passc" value="$pass2" onkeyup="conformPass(this)"><span id="pass_info"></span><br>
_END;
?>    
        <span class="info">&nbsp;</span>
        <input type="submit" value="Sign up">
        </form>
    </div>
    </body>
</html>