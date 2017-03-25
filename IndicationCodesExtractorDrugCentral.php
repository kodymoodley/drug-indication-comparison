<html>
<head>
    <title>DrugCentral: indication codes lookup</title>
    <link href="css/style.css" type="text/css" rel="stylesheet" />
</head>
<body>
    <!-- start header div --> 
    <div id="header">
        <h3>DrugCentral: indication codes lookup</h3>
    </div>
    <!-- end header div -->   
     
    <!-- start wrap div -->   
    <div id="wrap">
        <!-- start PHP code -->
        <?php
            //error_reporting(E_ALL);
            //ini_set('display_errors', 1);
            include("conn_dev.php");
            header("Access-Control-Allow-Origin: *");

            $query_result = mysqli_query($conn, "SELECT code FROM atc WHERE chemical_substance ='cisplatin'") or die(mysqli_error());
            $object = mysqli_fetch_object($query_result);
            $message = $object->code;
            echo $message;
            //header("Content-Type: application/json; charset=UTF-8");

            /*if (isset($_POST['password']) && isset($_POST['retype'])) {
                if ($_POST['password'] == $_POST['retype']){
                    $pwd = $_POST['password'];
                    if (strlen($pwd) < 5){
                        echo '<form method="post">
                            Password: <input type="password" name="password"><br>
                            Re-type Password: <input type="password" name="retype"><br>
                            <input type="submit" value="Reset">
                            </form>';
                        echo '<div class="errormsg">Password too short.</div>';
                    }
                    else{
                        // Make sure its a new pwd
                        $oldPwdFetch = mysqli_query($conn, "SELECT password FROM customers WHERE email='".$_GET['email']."' AND pwdhash='".$_GET['hash']."' AND verified='1'") or die(mysqli_error()); 
                        $value = mysqli_fetch_object($oldPwdFetch);
                        $oldPwd = $value->password;
                        if ($pwd == $oldPwd){
                            echo '<form method="post">
                                Password: <input type="password" name="password"><br>
                                Re-type Password: <input type="password" name="retype"><br>
                                <input type="submit" value="Reset">
                                </form>';
                            echo '<div class="errormsg">Choose a NEW password.</div>';
                        }
                        else{
                            $newhash = md5( rand(0,1000) );
                            $newPwdSet = mysqli_query($conn, "UPDATE customers SET password = \"".$pwd."\", pwdhash = \"".$newhash."\" WHERE email='".$_GET['email']."' AND pwdhash='".$_GET['hash']."' AND verified='1'") or die(mysqli_error());

                            if ($newPwdSet){
                                echo '<div class="errormsg">Password successfully reset.</div>';
                            }
                            else{
                                echo '<form method="post">
                                Password: <input type="password" name="password"><br>
                                Re-type Password: <input type="password" name="retype"><br>
                                <input type="submit" value="Reset">
                                </form>';
                                echo '<div class="errormsg">Password reset tried but failed.</div>';
                            }
                        }
                    }
                }
                else{
                    echo '<form method="post">
                        Password: <input type="password" name="password"><br>
                        Re-type Password: <input type="password" name="retype"><br>
                        <input type="submit" value="Reset">
                        </form>';
                    echo '<div class="errormsg">Passwords do not match.</div>';
                }
            }
            else if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
                // Verify data
                $email = mysqli_real_escape_string($conn, $_GET['email']); // Set email variable
                $hash = mysqli_real_escape_string($conn, $_GET['hash']); // Set hash variable
                $search = mysqli_query($conn, "SELECT email, pwdhash, verified FROM customers WHERE email='".$email."' AND pwdhash='".$hash."' AND verified='1'") or die(mysqli_error()); 
                $match  = mysqli_num_rows($search);
                if($match > 0){
                    // We have a match, activate the account
                    echo '<form method="post">
                        Password: <input type="password" name="password"><br>
                        Re-type Password: <input type="password" name="retype"><br>
                        <input type="submit" value="Reset">
                    </form>';
                    //mysqli_query($conn, "UPDATE customers SET verified='1' WHERE email='".$email."' AND hash='".$hash."' AND verified='0'") or die(mysql_error());
                    //echo '<div class="statusmsg">Your account has been activated, you can now login</div>';
                }else{
                    // No match -> invalid url or account has already been activated.
                    echo '<div class="statusmsg">The url is either invalid or you have already reset your password.</div>';
                }
                 
            } 
            else{
                // Invalid approach
                echo '<div class="statusmsg">Invalid approach, please use the link that has been sent to your email.</div>';
            }*/
             
        ?>
        <!-- stop PHP Code -->
 
         
    </div>
    <!-- end wrap div --> 
</body>
</html>