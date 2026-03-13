<?PHP

include("database.php");
$act 		= (isset($_POST['act'])) ? trim($_POST['act']) : '';
$name 		= (isset($_POST['name'])) ? trim($_POST['name']) : '';
$email 		= (isset($_POST['email'])) ? trim($_POST['email']) : '';
$phone		= (isset($_POST['phone'])) ? trim($_POST['phone']) : '';
$password 	= (isset($_POST['password'])) ? trim($_POST['password']) : '';
$repassword = (isset($_POST['repassword'])) ? trim($_POST['repassword']) : '';
$preferences= (isset($_POST['preferences'])) ? trim($_POST['preferences']) : '';

$name		=	mysqli_real_escape_string($con, $name);
$preferences=	mysqli_real_escape_string($con, $preferences);

$found = 0;
$error = "";
$success = false;

if($act == "register")
{
	$found 	= numRows($con, "SELECT * FROM `user` WHERE `email` = '$email' ");
	if($found) $error ="Email already registered";
	
	if($password <> $repassword) {
		$error ="Confirm password not matched";
	}
}

if(($act == "register") && (!$error))
{	
	$SQL_insert = " 
	INSERT INTO `user`(`id_user`, `name`, `email`,  `phone`, `password`) 
		VALUES (NULL, '$name', '$email', '$phone', '$password')	
		";	

	$result = mysqli_query($con, $SQL_insert) or die("Error in query: ".$SQL_insert."<br />".mysqli_error($con));
	$success = true;
}
?>
<!DOCTYPE html>
<html>
<title>Café Recommendation System</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Poppins", sans-serif}

body, html {
  height: 100%;
  line-height: 1.8;
}

/* Full height image header */
.bgimg-1 {
  background-position: top;
  background-size: cover;
  background-attachment:fixed;
  background-image: url(images/banner.jpg);
  min-height:100%;
  background-color: rgba(0, 0, 0, 0.2);
  background-blend-mode: overlay; 
}

a:link {
  text-decoration: none;
}

.w3-bar .w3-button {
  padding: 16px;
}
</style>

<body class="bgimg-1">

<?PHP include("menu.php"); ?>


<div  >

	<div class="w3-padding-32"></div>
		
	<div class=" w3-center w3-text-white w3-padding">
		<span class="w3-large"></span><br>
	</div>


<div class="w3-container w3-padding-16" id="contact">
    <div class="w3-content w3-container w3-white w3-round w3-card" style="max-width:500px">
		<div class="w3-padding">
			
<?PHP if($success) { ?>
<div class="w3-panel w3-green w3-display-container w3-animate-zoom">
  <span onclick="this.parentElement.style.display='none'"
  class="w3-button w3-large w3-display-topright">&times;</span>
  <h3>Success!</h3>
  <p>Your registration was successful! You may now <a href="login.php" class="w3-xlarge">Login.</a> </p>
</div>
<?PHP  } ?>

<?PHP if($error) { ?>
<div class="w3-panel w3-red w3-display-container w3-animate-zoom">
  <span onclick="this.parentElement.style.display='none'"
  class="w3-button w3-large w3-display-topright">&times;</span>
  <h3>Error!</h3>
  <p><?PHP echo $error;?></p>
</div>
<?PHP  } ?>

<?PHP if(!$success) { ?>	
		
			<form action="" method="post">

			<h3><b>Registration</b></h3>
			
				<div class="w3-section" >
					<label>Full Name *</label>
					<input class="w3-input w3-border w3-round" type="text" name="name"  required>
				</div>
			  
			  
				<div class="w3-section">
					<label>Contact *</label>
					<input class="w3-input w3-border w3-round" type="text" name="phone" required>
				</div>
			  
				<div class="w3-section" >
					<label>Email *</label>
					<input class="w3-input w3-border w3-round" type="email" name="email"  required>
				</div>
			  
				<div class="w3-section">
					<label>Password *</label>
					<input class="w3-input w3-border w3-round" type="password" name="password" required>
				</div>
				
				<div class="w3-section">
					<label>Confirm Password *</label>
					<input class="w3-input w3-border w3-round" type="password" name="repassword" required>
				</div>
			  
				<input type="hidden" name="act" value="register">
				<button type="submit" class="w3-button w3-block w3-padding-large w3-amber w3-margin-bottom w3-round"><b>SUBMIT</b></button>
			</form>
			
<?PHP } ?>
			<div class="w3-center">Already registered? <a href="login.php" class="w3-text-brown">Login here</a></div>
			
		</div>
		


    </div>
</div>

<div class="w3-padding-16"></div>

<?PHP include("footer.php");?>

	
</div>


 
<script>

// Toggle between showing and hiding the sidebar when clicking the menu icon
var mySidebar = document.getElementById("mySidebar");

function w3_open() {
  if (mySidebar.style.display === 'block') {
    mySidebar.style.display = 'none';
  } else {
    mySidebar.style.display = 'block';
  }
}

// Close the sidebar with the close button
function w3_close() {
    mySidebar.style.display = "none";
}
</script>

</body>
</html>
