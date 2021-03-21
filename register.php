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

	// conditional block to execute registration
	if(isset($_POST['email'])){
      // improper sizing handling
      if(strlen($_POST['username'])<4){
        $err = 'Username must be at least 4 characters long';
      }else if(strlen($_POST['pass'])<6){
        $err= 'Password must be at least 6 characters long';
      }else{
        // block to check if credentials already in use
        $query = 'select * from user where lower(email) = lower("'.$_POST['email'].'")';
        $result = mysqli_query($connection, $query);
        if (mysqli_num_rows($result) != 0) {
          $err = 'Email already in use';
        }else{
          $query = 'select * from user where lower(username) = lower("'.$_POST['username'].'")';
          $result = mysqli_query($connection, $query);
        	if (mysqli_num_rows($result) != 0) {
          		$err = 'Username already in use';
        	}else{
              // all conditions met, registration is approved
              $query = 'insert into user (username, email, password, isAdmin) VALUES ("'.$_POST['username'].'","'.$_POST['email'].'","';
              $query = $query.$_POST['pass'].'","0")';
              mysqli_query($connection, $query);
              
              // the new user ID now needs to be retrieved to store the preference they selected for the service
              $query = 'select id from user where lower(email) = lower("'.$_POST['email'].'")';
              $result = mysqli_query($connection, $query);
              if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()){
                  $id = $row['id'];
                }
              }
              // transfer the data from the form into a format compatible with the database
              if($_POST['servicePreference'] == 'breakdown'){
                $setting = 0;
              }else{
                $setting = 1;
              }
              // insert their platform preference
              $query = 'INSERT INTO user_settings (user_id, rule_id, status) VALUES ("'.$id.'","1","'.$setting.'")';
              mysqli_query($connection, $query);
              
              // use the error handler to deliver a message confirming registration was successful
              $err = 'Account created successfully! You can now login <a href="login.php">here</a>.';
            }
        }
        
      }
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
    <h1 class="header">Sequenced Registration</h1>
      <?PHP
      // code to display an error message to inform the user if the credentials are invalid
      	if(isset($err)){
          echo '<p id="errorMsg">'.$err.'</p></br>';
        }
      ?>
	<form class="credentialForm" action="<?php $_PHP_SELF ?>" method="post">
      <label>Email: </label>
      <input type="email" name="email" required/><br />
      <label>Username: </label>
      <input type="text" name="username" required/><br />
      <label>Password: </label>
      <input type="password" name="pass" required/><br />
      <label>Due to bugetary constraints during this beta period, you will only be able to utilise half of our intended gene sequencing features.</label><br />
      <label>One option is the genomic breakdown, which breaks down the geographic origins of your genome.</label><br />
      <label>The other option is to view traits that can be assertained from your genome, such as medical predispositions and interesting facts.</label><br />
      <label>Which would you like to utilise?</label><br />
      <input type="radio" id="breakdown" name="servicePreference" value="breakdown"  required/>
      <label>Genomic Breakdown</label>
      <input type="radio" id="traits" name="servicePreference" value="traits"  required/>
      <label>Traits Analysis</label><br />
      <input type="submit" value="Register" />
    </form>
      <p class="content">Already have an account? Sign in here:</p> 
      <a class=landingPageButton href="login.php">Login</a>
    </div>
  </body>
</html>