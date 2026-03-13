<?PHP
session_start();
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

a:link {
  text-decoration: none;
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

.w3-bar .w3-button {
  padding: 16px;
}
</style>

<body class="bgimg-1">

<?PHP include("menu.php"); ?>


<div  >

	<div class="w3-padding-64"></div>


<div class="w3-container w3-padding-16" id="contact">
    <div class="w3-content w3-container " style="max-width:1000px">
		<div class="w3-xxlarge w3-text-white w3-center"><b>WELCOME!!</b></div>
		<div class="w3-xlarge w3-text-white w3-center"><b>Find the best Cafes & Coffee Shops in Shah Alam and Kuala Lumpur</b></div>
	
		<div class="w3-padding-16"></div>
		
		<div class="w3-padding">
			
			<form action="search.php" method="post">
		
			
				<div class="w3-row w3-card w3-padding w3-white w3-round">
					<div class="w3-col m5 w3-padding" >
						<input class="w3-input w3-border w3-round" type="text" name="keyword" placeholder="Search..." required>
					</div>
					<div class="w3-col m3 w3-padding" >
						<select class="w3-button w3-select w3-border w3-round" type="text" name="option" required>
							<option value="location">Location</option>
							<option value="cafe_name">Cafe Name</option>
							<option value="type_cafe">Type Cafe</option>							
						</select>
					</div>
					<div class="w3-col m4 w3-padding" >
						<button type="submit" class="w3-button w3-block w3-orange w3-round"><b>SEARCH</b><i class="fa fa-fw w3-margin-left  fa-search"></i></button>
					</div>
				</div>
			
			  <input name="act" type="hidden" value="login">
			  
			</form>
			
		</div>
		

    </div>
</div>

<div class="w3-padding-64"></div>
	
</div>

<?PHP include("footer.php");?>	
 
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
