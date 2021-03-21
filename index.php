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
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Sequenced</title>
    <link rel="stylesheet" href="style.css">
    
    
  </head>
  <body>
    <div class = "intro">
    <h1 class="header">Welcome to Sequenced!</h1>
      <p class = "content">Congratulations on your invite to the closed beta for Sequenced!</p>
      <p class = "content">Below you can login with your existing account, or create a new one!</p>
      <a class=landingPageButton href="login.php">Login</a>
      <a class=landingPageButton href="register.php">Register</a>
    </div>
  </body>
</html>