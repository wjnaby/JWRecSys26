<?PHP
session_start();
include("database.php");

$keyword 	= (isset($_POST['keyword'])) ? trim($_POST['keyword']) : '';
$option 	= (isset($_POST['option'])) ? trim($_POST['option']) : '';

if($option == "cafe_name")
	$SQL_search = "WHERE (`cafe_name` LIKE '%" .$keyword . "%' )";
elseif($option == "type_cafe")
	$SQL_search = "WHERE (`type_cafe` LIKE '%" .$keyword . "%' )";
elseif($option == "location")
	$SQL_search = "WHERE (`location` LIKE '%" .$keyword . "%' )";
else
	$SQL_search = "";
?>
<!DOCTYPE html>
<html>
<title>Café Recommendation System</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="w3.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
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

/* Full height bg */
.bgimg-1 {
  min-height: 100%;
}

.w3-bar .w3-button {
  padding: 16px;
}
</style>

<body>

<?PHP include("menu.php"); ?>


<div class="bgimg-1" >

	<div class="w3-padding-32"></div>
	
	<div class="w3-padding w3-xxlarge w3-center w3-text-white"><b>Top Cafe In Selangor</b></div>
	
	<div class="w3-container w3-padding-16" id="contact">
		<div class="w3-content w3-container w3-center " style="max-width:1200px">
			
			
			<div class="w3-row w3-center">
				
				<?PHP
				$SQL_list = "SELECT * FROM `cafe` ". $SQL_search;
				$result = mysqli_query($con, $SQL_list) ;
				while ( $data	= mysqli_fetch_array($result) )
				{
					$photo	= $data["photo"];
					if(!$photo) $photo = "avatar.png";
					$id_cafe	= $data["id_cafe"];
				?>
				<div class="w3-col m4 w3-padding w3-padding-16 w3-center" style="height:500px; margin-bottom:4%;">
					<div class="w3-padding w3-padding-16 w3-card w3-white w3-round-large">
						<img src="upload/<?PHP echo $photo;?>" style="height:300px; width:300px;" class="w3-image w3-round">
						<div class="w3-padding">
							<h4><b><?PHP echo $data["cafe_name"]; ?></b></h4>
							<?PHP echo substrwords($data["location"], 70); ?>
						</div>
												
						<a href="detail.php?id_cafe=<?PHP echo $id_cafe;?>" class="w3-button w3-block w3-amber w3-round-large"><b> DETAIL <i class="fa fa-fw fa-chevron-right"></i></b></a>
					</div>
				</div>
				
				<?PHP 
				} 
				?>
				
				
			</div>
			
		  
		</div>
	</div>
	

	
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
