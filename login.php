<?PHP
	session_start();

	// handler to make sure logged in users don't see the landing pages
	if($_SESSION['loggedin'] == true){
      if($_SESSION['isAdmin'] == 1){
        header('Location: dashboard.php');
      }else{
        header('Location: home.php');
      }
    };
	
	require 'connection.php';

	// login validation
	if(isset($_POST['email']) and isset($_POST['pass'])){
      
        // create query string
        $query = 'select * from user where lower(email) = lower("'.$_POST['email'].'") and password = "'.$_POST['pass'].'"';
        
        // checking login
      	$result = mysqli_query($connection, $query);
        
        if (mysqli_num_rows($result) > 0) {
          // login valid
          
          // assign session variable for loggedin status
          $_SESSION['loggedin'] = true;
          
          // retrieve query result to store id and admin status as a variable
          while($row = mysqli_fetch_assoc($result)) {
    		$_SESSION['isAdmin'] = $row['isAdmin'];
            $_SESSION['userId'] = $row['id'];
  			}
          // admin vs normal user handling
          if($_SESSION['isAdmin'] == 1){
            header('Location: dashboard.php');
          }else{
            header('Location: home.php');
          }
        }else{
          // login invalid, return this message
          $err = 'The credentials you entered are not valid';
        }
      
      // if valid, move to home page and set session to logged in
      
      // if invalid, say why (invalid credentials, bad password, etc)
      }

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Sequenced</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class = "intro">
    <h1 class="header">Sequenced Login</h1>
      <?PHP
      // code to display an error message to inform the user if the credentials are invalid
      	if(isset($err)){
          echo '<p id="errorMsg">'.$err.'</p></br>';
        }
      ?>
	<form class="credentialForm" action="<?php $_PHP_SELF ?>" method="post">
      <label>Email: </label>
      <input type="email" name="email"/><br />
      <label>Password: </label>
      <input type="password" name="pass"/><br />
      <input type="submit" value="Login" />
    </form>
      <p class="content">Don't have an account? Register here: </p> 
      <a class=landingPageButton href="register.php">Register</a>
    </div>
  </body>
</html>