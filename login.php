
<!DOCTYPE HTML>
<html><head><title>Login Page</title> </head>
 <body>

 <?php
//this line is for debugging purposes so that we can see the actual POST data
echo "<pre>"; var_dump($_POST); echo "</pre>";
 
include "checksession.php";
loginStatus(); //show the current login status
echo "<pre>"; var_dump($_SESSION); echo "</pre>";
 
//simple logout
if (isset($_POST['logout'])) logout();
 
if (isset($_POST['login']) and !empty($_POST['login']) and ($_POST['login'] == 'Login')) {
    include "config.php"; //load in any variables
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE, DBPORT) or die();
 
//validate incoming data - only the first field is done for you in this example - rest is up to you to do
//firstname
    $error = 0; //clear our error flag
    $msg = 'Error: ';
    if (isset($_POST['email']) and !empty($_POST['email']) and is_string($_POST['email'])) {
       $un = htmlspecialchars(stripslashes(trim($_POST['email'])));  
       $email = (strlen($un)>50)?substr($un,1,50):$un; //check length and clip if too big       
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid username '; //append error message
       $email = '';  
    } 
                    
//password  - normally we avoid altering a password apart from whitespace on the ends   
       $password = trim($_POST['password']);        
       
//This should be done with prepared statements!!
    if ($error == 0) {
        $query = "SELECT customerID,password FROM customer WHERE email = '$email'";
        $result = mysqli_query($DBC,$query);     
        if (mysqli_num_rows($result) == 1) { //found the user
            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_close($DBC); //close the connection once done
  //this line would be added to the registermember.php to make a password hash before storing it
  //$hash = password_hash($password); 
  //this line would be used if our user password was stored as a hashed password
           //if (password_verify($password, $row['password'])) {           
            if ($password === $row['password']) //using plaintext for demonstration only!            
              login($row['customerID'],$email);
        } echo "<h2>Login fail</h2>".PHP_EOL;   
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}
?>
<h1>Login</h1>
<form method="POST" action="login.php">
  <p>
    <label for="email">User Email: </label>
    <input type="text" id="email" name="email" maxlength="50"> 
  </p> 
  <p>
    <label for="password">Password: </label>
    <input type="password" id="password" name="password" maxlength="32"> 
  </p> 
  
   <input type="submit" name="login" value="Login">
   <input type="submit" name="logout" value="Logout">   
 </form>
</body>
</html>