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

<body>

<?PHP include("menu.php"); ?>


<div class="bgimg-1" >

	<div class="w3-padding-32"></div>
	
	<div class="w3-padding w3-text-white w3-xxlarge w3-center "><b>- ABOUT US -</b></div>

<div class="w3-container w3-padding-16" id="contact">
    <div class="w3-content w3-container w3-white w3-round w3-card" style="max-width:800px">
		<div class="w3-padding">
			
			<h4><b>Welcome to JW Café Recommendation System</b></h4>
			<p>Finding the perfect café has never been easier! Our Web-Based Café Recommendation System, powered by a rule-based approach, is here to help you make informed decisions. Whether you’re searching for a quiet place to study, a lively spot to catch up with friends, or simply the best cup of coffee in town, we’ve got you covered.
			
			Designed with students around UiTM Shah Alam in mind, this system simplifies café selection by considering your preferences and providing personalized recommendations. With a user-friendly interface and accurate information, our goal is to enhance your café-hopping experience. Start exploring now and let us guide you to your ideal café destination!</p>
	
		</div>
		<div class="w3-padding-24"></div>
    </div>
</div>


<div class="w3-padding-32"></div>

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
