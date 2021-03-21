<?PHP
session_start();

//handler incase a non-loggedin user tries to access these pages, or a user without sufficient authorisation
if ($_SESSION['loggedin'] != true)
{
    header('Location: index.php');
}
else if ($_SESSION['isAdmin'] != 1)
{
    header('Location: home.php');
}

require 'logout.php';
require 'connection.php';

if (isset($_POST['logout']))
{
    logout();
}

// dashboard query of a view to return all selections made by users regarding what data they wanted to see
$query = 'select * from user_data_choice';
$result = $connection->query($query);
while ($row = $result->fetch_assoc())
{
    $gbd = $row['gbd'];
    $tra = $row['tra'];
}

// block to handle user deletion and deletion of dependencies with user IDs
if (isset($_POST['deleteId']))
{
    $query = 'delete from user where id ="' . $_POST['deleteId'] . '"';
    $connection->query($query);
    $query = 'delete from user_settings where user_id ="' . $_POST['deleteId'] . '"';
    $connection->query($query);

    // couple lines of code which ensure a tab is open when the dashboard is launched
    $current = 'users';
}
else
{
    $current = 'dashboard';
}
?>
<!DOCTYPE html>
<html>
   <head>
	<title>Dashboard</title>
	<link rel="stylesheet" href="style.css">
	<script src="tabSwitch.js"></script>
     
         <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Setting', 'Number of Users'],
          ['Genetic Breakdown',<?PHP echo $gbd; ?>],
          ['Trait Analysis',<?PHP echo $tra; ?>]
        ]);

        var options = {
          title: 'Number of Users per Genomic Data Usage Setting'
          , 'width':900
          , 'height':700
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
     
   </head>
   <body>
      <form action= "<?php $_PHP_SELF ?>" method = "post">
         <input type = "submit" id="logout" name="logout" value="Log out"/>
      </form>
      <!-- tutorial on tabs within a HTML page sourced from https://www.w3schools.com/howto/howto_js_tabs.asp -->
      <div class="tab">
         <button class="tablinks" onclick="tabSwitch(event, 'dashboard')">Dashboard</button>
         <button class="tablinks" onclick="tabSwitch(event, 'users')">User Management</button>
      </div>
      <div id="dashboard" class="tabcontent">
             <div id="piechart"></div>
     </div>
      <div id="users" class="tabcontent">
        <?PHP
// create and execute query to get all users
$query = 'select id, username, email, isAdmin from user';
$result = $connection->query($query);
// begin iteration so there's a new row for each user
echo "<table><tr><th>Username</th><th>Email</th><th>Admin?</th><th>Delete User</th></tr>";
while ($row = $result->fetch_assoc())
{
    if ($row['isAdmin'] == 0)
    {
        $admin = 'No';
    }
    else
    {
        $admin = 'Yes';
    }
    echo '<tr><td>' . $row["username"] . '</td><td>' . $row["email"] . '</td><td>' . $admin . '</td><td><form action="dashboard.php" method = "post"><button value="' . $row["id"] . '" name="deleteId">Delete</button></form></td>';
}
echo "</table>"
?>
      </div>
     <!-- included after the tabs have rendered so they can be displayed -->
     <script>
     <?PHP
if ($current == 'users')
{
    echo "tabSwitch(event, 'users')";
}
else
{
    echo "tabSwitch(event, 'dashboard')";
}
?>
     </script>
   </body>
</html>
