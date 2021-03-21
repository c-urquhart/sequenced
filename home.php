<?PHP
session_start();

//handler incase a non-loggedin user tries to access these pages, or a user without sufficient authorisation
if ($_SESSION['loggedin'] != true)
{
    header('Location: index.php');
}
else if ($_SESSION['isAdmin'] == 1)
{
    header('Location: dashboard.php');
}

require 'logout.php';
require 'connection.php';

if (isset($_POST['logout']))
{
    logout();
}

// functionality to change feature selection
if (isset($_POST['toggleToTraits'])){
  // update the settings table
  $query = 'UPDATE user_settings set status = "1" where user_id = "'.$_SESSION['userId'].'" and rule_id = "1"';
  $connection->query($query);
  
  // log the change
  $query = 'INSERT INTO settingchange (user_id, rule_id, previous_value, new_value) VALUES ("';
  $query = $query.$_SESSION['userId'].'","1","0","1")';
  $connection->query($query);

}

if (isset($_POST['toggleToBreakdown'])){
  // update the settings table
  $query = 'UPDATE user_settings set status = "0" where user_id = "'.$_SESSION['userId'].'" and rule_id = "1"';
  $connection->query($query);
  
  // log the change
  $query = 'INSERT INTO settingchange (user_id, rule_id, previous_value, new_value) VALUES ("';
  $query = $query.$_SESSION['userId'].'","1","1","0")';
  $connection->query($query);
  
}


// get user's current setting
$query = 'select status from user_settings where rule_id = 1 and user_id ='.$_SESSION['userId'];
$result = $connection->query($query);
while($row = $result->fetch_assoc()) {
  $status = $row['status'];
}

if($status==0){
  // genetic breakdown selected
  $title = 'Genetic Breakdown';
  $heading = 'Your personal genetic breakdown';
}else{
  // trait analysis selected
  $title = 'Trait Analysis';
  $heading = 'Your personal trait analysis';
}

function breakdownTable(){
  // Arrays holding the data to be displayed in the table
  // As we currently don't hold the genetic data of our users, this serves as a placeholder of what could be possible
  $backgroundArray = array("Western European", "North European", "South European", "Western Sub-Saharan African", "Berber",
                          "Arabic", "Indo-Aryan", "Han Chinese", "Polynesian", "Mesoamerican");
  $compositionArray = array("40%", "20%", "12.5%", "2.5%", "5%", "15%", "7%", "3%", "0%", "0%");
  
  // Here the arrays are randomised in order to give each user a random pairing
  shuffle($backgroundArray);
  shuffle($compositionArray);
  
  echo '<table><tr><th>Background</th><th>Percentage Makeup</th></tr>';
  for($i=0; $i < 10; $i++){
    echo '<tr><td>'.$backgroundArray[$i].'</td><td>'.$compositionArray[$i].'</td></tr>';
  }
  echo '</table>';
}

function traitTable(){
  // similar to the breakdown table, as there is no actual genetic data, a randomised table will be displayed of potential traits
  // for the purposes of demonstration
  $traitArray = array("You are 20% more likely to develop male pattern baldness",
                     "You have a 5% higher chance to exceed the age of 100 years than the general population",
                     "There is a 75% chance you have blonde hair",
                     "There is a 0.02% chance you have with heterochromia",
                     "There is a 62% chance you have detached earlobes",
                     "There is a 13% chance you have Protanopia colourblindness",
                     "You have approximately a 1% higher chance of developing heart disease than the general population",
                     "There is a 16% chance you are short-sighted",
                     "You have a 14% higher chance than the general population to be in a car crash",
                     "You are 16 times less likely than the general population to enjoy the taste of liqourice"
                     );
  shuffle($traitArray);
  echo '<table><tr><th>Your Traits</th></tr>';
  for($i=0; $i < 5; $i++){
    echo '<tr><td>'.$traitArray[$i].'</td></tr>';
  }
  echo '</table>';
}

?>
<!DOCTYPE html>
<html>
   <head>
	<title><?PHP echo $title;?></title>
	<link rel="stylesheet" href="style.css">
     </head>
  <body>
    <div id='logoutBtn'>
    <form action= "<?php $_PHP_SELF ?>" method = "post">
         <input type = "submit" id="logout" name="logout" value="Log out" class="logoutBtn"/>
      </form>
    </div>
    <?PHP 
    	if($status==0){
          echo '<form action= "home.php" method = "post">';
          echo '<input type = "submit" id="toggleToTraits" name="toggleToTraits" value="Change to Trait Analysis"/>';
          echo '</form>';
        }else{
          echo '<form action= "home.php" method = "post">';
          echo '<input type = "submit" id="toggleToBreakdown" name="toggleToBreakdown" value="Change to Genetic Breakdown"/>';
          echo '</form>';
        }
    ?>
    <br />
    <div id="homecontent">
      <h1 id="homeheading"><?PHP echo $heading;?></h1><br />
      <!-- insert tables dependent on user's settings -->
      <?PHP if($status == 0){breakdownTable();} else{traitTable();}?>
    </div>
  </body>
</html>
