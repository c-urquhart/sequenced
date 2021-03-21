<?PHP

function logout(){

  // convert to function later
  session_start();

  // empty out all session data
  session_destroy();

  // redirect to landing page
  header('Location: index.php');
}

?>
