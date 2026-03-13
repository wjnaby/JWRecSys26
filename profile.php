<?PHP
session_start();
include("database.php");

$id_user = $_SESSION["id_user"];
?>
<?PHP	
$act 	= (isset($_POST['act'])) ? trim($_POST['act']) : '';	

$name 		= (isset($_POST['name'])) ? trim($_POST['name']) : '';
$email 		= (isset($_POST['email'])) ? trim($_POST['email']) : '';
$phone		= (isset($_POST['phone'])) ? trim($_POST['phone']) : '';
$password 	= (isset($_POST['password'])) ? trim($_POST['password']) : '';
$repassword = (isset($_POST['repassword'])) ? trim($_POST['repassword']) : '';
$preferences= (isset($_POST['preferences'])) ? trim($_POST['preferences']) : '';

$name		=	mysqli_real_escape_string($con, $name);
$preferences=	mysqli_real_escape_string($con, $preferences);


$success = "";

if($act == "edit")
{	
	$SQL_update = " 
	UPDATE `user` SET 
		`name` = '$name',
		`email` = '$email',
		`phone` = '$phone',
		`password` = '$password'
	WHERE `email` =  '{$_SESSION['email']}'";	
										
	$result = mysqli_query($con, $SQL_update) or die("Error in query: ".$SQL_update."<br />".mysqli_error($con));
	
	$success = "Successfully Updated";
	//print "<script>alert('Successfully Updated');</script>";
}


$SQL_view 	= " SELECT * FROM `user` WHERE `email` =  '{$_SESSION['email']}' ";
$result 	= mysqli_query($con, $SQL_view) or die("Error in query: ".$SQL_view."<br />".mysqli_error($con));
$data		= mysqli_fetch_array($result);
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
a {
  text-decoration: none;
}

body,h1,h2,h3,h4,h5,h6 {font-family: "Poppins", sans-serif}

body, html {
  height: 100%;
  line-height: 1.8;
}

/* Full height image header */
.bgimg-1 {
  background-position: top;
  background-attachment: fixed;
  background-size: cover;
  background-image: url("images/banner.jpg");
  min-height: 100%;
}

.w3-bar .w3-button {
  padding: 16px;
}
</style>

<body class="bgimg-1">

<?PHP include("menu.php"); ?>

<!--- Toast Notification -->
<?PHP 
if($success) { Notify("success", $success, "profile.php"); }
?>	


<div class="" >

	<div class="w3-padding-48"></div>
		
	
<div class="w3-container w3-padding" id="contact">
    <div class="w3-content w3-container w3-white w3-round w3-card" style="max-width:500px">
		<div class="w3-padding">
		
			<form method="post" action="" >
				<b class="w3-xlarge">My Profile</b>
				<hr style="margin: 0 0 0 0">
				
				<div class="w3-section" >
					<label>Full Name *</label>
					<input class="w3-input w3-border w3-round" type="text" name="name" value="<?PHP echo $data["name"];?>" required>
				</div>
			  
			  
				<div class="w3-section">
					<label>Contact *</label>
					<input class="w3-input w3-border w3-round" type="text" name="phone" value="<?PHP echo $data["phone"];?>" required>
				</div>
			  
				<div class="w3-section" >
					<label>Email *</label>
					<input class="w3-input w3-border w3-round" type="email" name="email"  value="<?PHP echo $data["email"];?>" required>
				</div>
			  
				<div class="w3-section">
					<label>Password *</label>
					<input class="w3-input w3-border w3-round" type="password" name="password" value="<?PHP echo $data["password"];?>" required>
				</div>
				
				<hr class="w3-clear">
				<input type="hidden" name="act" value="edit" >
				<button type="submit" class="w3-button w3-block w3-padding-large w3-amber w3-margin-bottom w3-round"><b>UPDATE</b></button>

			</form>
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