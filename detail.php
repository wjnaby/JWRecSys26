<?PHP
session_start();
include("database.php");
?>
<?PHP
$id_user 	= (isset($_SESSION['id_user'])) ? trim($_SESSION['id_user']) : '';

$id_cafe 	= (isset($_REQUEST['id_cafe'])) ? trim($_REQUEST['id_cafe']) : '';

$act 		= (isset($_REQUEST['act'])) ? trim($_REQUEST['act']) : '';	
$name		= (isset($_POST['name'])) ? trim($_POST['name']) : '';
$rating		= (isset($_POST['rating'])) ? trim($_POST['rating']) : '';
$comment	= (isset($_POST['comment'])) ? trim($_POST['comment']) : '';

$comment	=	mysqli_real_escape_string($con, $comment);

if($act == "addReview")
{	
	$SQL_insert = " 
	INSERT INTO `review`(`id_review`, `id_cafe` ,`id_user`, `rating`, `comment`,  `date`, `photo`) 
					VALUES (NULL, $id_cafe, '$id_user','$rating','$comment', NOW(), '') ";
										
	$result = mysqli_query($con, $SQL_insert);
	
	$id_review = mysqli_insert_id($con);
	
	// -------- Photo -----------------
	if(($_FILES["photo"]["error"] == 0) && (isset($_FILES['photo']))) {
		 
		  $file_name = $_FILES['photo']['name'];
		  $file_size = $_FILES['photo']['size'];
		  $file_tmp = $_FILES['photo']['tmp_name'];
		  $file_type = $_FILES['photo']['type'];
		  
		  $fileNameCmps = explode(".", $file_name);
		  $file_ext = strtolower(end($fileNameCmps));
		  
		  if(empty($errors)==true) {
			 move_uploaded_file($file_tmp,"upload/".$file_name);
			 
			$query = "UPDATE `review` SET `photo`='$file_name' WHERE `id_review` = '$id_review'";			
			$result = mysqli_query($con, $query) or die("Error in query: ".$query."<br />".mysqli_error($con));
		  }else{
			 print_r($errors);
		  }  
	}
	// -------- End Photo -----------------
	
	//print "<script>self.location='review.php?id_agent=$id_agent';</script>";
}

$SQL_view 	= " SELECT * FROM `cafe` WHERE `id_cafe` =  $id_cafe ";
$result 	= mysqli_query($con, $SQL_view) or die("Error in query: ".$SQL_view."<br />".mysqli_error($con));
$data		= mysqli_fetch_array($result);
$photo_raw = $data["photo"];
$photos_list = $photo_raw ? array_map('trim', explode(',', $photo_raw)) : array();
$photo = !empty($photos_list) ? $photos_list[0] : "noimage.jpg";


function timeago($date) {
   $timestamp = strtotime($date);	
   
   $strTime = array("second", "minute", "hour", "day", "month", "year");
   $length = array("60","60","24","30","12","10");

   $currentTime = time();
   if($currentTime >= $timestamp) {
		$diff     = time()- $timestamp;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
		$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		return $diff . " " . $strTime[$i] . "(s) ago ";
   }
}
?>
<!DOCTYPE html>
<html>
<title>Café Recommendation System jw </title>
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
  background-color: rgba(255, 255, 255, 0.2);
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
	
	<div class="w3-padding w3-xxlarge w3-center"><b>- DETAIL -</b></div>

<div class="w3-container w3-padding-16" id="contact">
    <div class="w3-content w3-container w3-white w3-round w3-card" style="max-width:1000px">
		<div class="w3-padding">

			<div class="w3-row">
				<div class="w3-col m5 w3-padding-16">
					<?PHP if(count($photos_list) > 1) { ?>
					<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:8px;">
						<?PHP foreach($photos_list as $p) { ?>
						<a href="upload/<?PHP echo htmlspecialchars($p);?>" target="_blank"><img src="upload/<?PHP echo htmlspecialchars($p);?>" class="w3-round w3-border" style="width:100%;height:100px;object-fit:cover;"></a>
						<?PHP } ?>
					</div>
					<?PHP } else { ?>
					<img src="upload/<?PHP echo $photo;?>" class="w3-image w3-round">
					<?PHP } ?>
				</div>
				<div class="w3-col m7 w3-padding">
					<h3><b><?PHP echo $data["cafe_name"];?></b></h3>
					<?PHP echo $data["cuisine"];?>
					
					<div class="w3-padding"></div>
					<b class="w3-margin-right">Price Range : </b> <?PHP echo $data["price_range"];?>
					
					<div class="w3-padding"></div>
					<b class="w3-margin-right">Type of Cafe : </b> <?PHP echo $data["type_cafe"];?>
					
					<div class="w3-padding"></div>
					<hr>
					
					<?PHP $eventName= $data["cafe_name"]; ?>
					<iframe width="100%" height="400" src="https://maps.google.com/maps?q=<?php echo $eventName; ?>&output=embed" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					
					
								<span class="w3-large"><b>REVIEWS</b></span>
																
								<?PHP
								$SQL_list = "SELECT * FROM `review`,`user` WHERE review.id_user = user.id_user AND `id_cafe` = $id_cafe";
								$result = mysqli_query($con, $SQL_list) ;
								while ( $data	= mysqli_fetch_array($result) )
								{
									$id_review	= $data["id_review"];
									$name		= $data["name"];
									$rating		= $data["rating"];
									$comment	= $data["comment"];
									$date		= $data["date"];
									$photo		= $data["photo"];
								?>
								<div class="w3-round w3-border" style="width:100%">
								<div class="w3-row w3-padding">
									<div class="w3-col s12">
										<b><?PHP echo $name; ?></b><span class="w3-right w3-small"><?PHP echo $date; ?></span><br>
										
										<?PHP 
										for($i = 1; $i <=5; $i++) { 
											if($rating >= $i) echo '<i class="fa fa-star w3-text-amber"></i>';
										} ?>
										
										<span class="w3-small w3-text-grey">&nbsp;<?PHP echo timeago($date); ?></span>
										<div class="w3-padding-small"></div>
										
										<div class="w3-small">
										<?PHP echo $comment; ?>
										</div>
										<div class="w3-padding-small"></div>
										<?PHP if($photo) { ?>
										<a target="_blank" href="upload/<?PHP echo $photo;?>"><img src="upload/<?PHP echo $photo;?>" class="w3-round w3-border" width="150px"></a>
										<?PHP } ?>
									</div>
								</div>
								</div>
								
								<div class="w3-padding"></div>

								<?PHP } ?>
								
								
								<div class="w3-padding-16"></div>

						<?PHP if(isset($_SESSION["email"])) { ?>
						<!-- review form -->
						<div class="w3-containerx" id="contact">
							<div class="w3-content w3-container w3-cardx w3-border w3-round w3-padding-16" style="max-width:800px">
							<b>Leave a review</b>
							
							<form action="" method="post" class="" enctype="multipart/form-data">
								<!--<input class="w3-input w3-border w3-round" type="text" name="name" value="" placeholder="Enter your name">-->
								
								<div class="w3-section" >
									Photo
									<input type="file" class="w3-input w3-border w3-round" name="photo" id="photo" accept=".jpeg, .jpg,.png,.gif">										
									<div class="w3-small w3-text-grey">  only JPEG, JPG, PNG or GIF allowed </div>
								</div>
			
								<textarea class="w3-input w3-border w3-round" rows="3" name="comment" placeholder="Add a review here.." required></textarea>
								
								<style>
								.rate {
									float: left;
									height: 12px;
									padding: 0 0px;
								}
								.rate:not(:checked) > input {
									position:absolute;
									top:-9999px;
								}
								.rate:not(:checked) > label {
									float:right;
									width:1em;
									overflow:hidden;
									white-space:nowrap;
									cursor:pointer;
									font-size:30px;
									color:#ccc;
								}
								.rate:not(:checked) > label:before {
									content: '★ ';
								}
								.rate > input:checked ~ label {
									color: #ffc700;    
								}
								.rate:not(:checked) > label:hover,
								.rate:not(:checked) > label:hover ~ label {
									color: #deb217;  
								}
								.rate > input:checked + label:hover,
								.rate > input:checked + label:hover ~ label,
								.rate > input:checked ~ label:hover,
								.rate > input:checked ~ label:hover ~ label,
								.rate > label:hover ~ input:checked ~ label {
									color: #c59b08;
								}
								</style>
								
								<div class="rate">
									<input type="radio" id="star5" name="rating" value="5" />
									<label for="star5" title="text">5 stars</label>
									<input type="radio" id="star4" name="rating" value="4"  />
									<label for="star4" title="text">4 stars</label>
									<input type="radio" id="star3" name="rating" value="3"  />
									<label for="star3" title="text">3 stars</label>
									<input type="radio" id="star2" name="rating" value="2"  />
									<label for="star2" title="text">2 stars</label>
									<input type="radio" id="star1" name="rating" value="1"  />
									<label for="star1" title="text">1 star</label>
								</div>
								
								<div class="w3-section">
									<input name="id_cafe" type="hidden" value="<?PHP echo $id_cafe;?>">
									<input name="act" type="hidden" value="addReview">
									<button type="submit" class="w3-padding-large w3-button w3-right w3-round-large w3-black"><b>SUBMIT</b></button>		
								</div>
							</form>

							</div>
						</div>
						<!-- review form -->
						<?PHP } ?>
		
				</div>
			</div>
			
			
		
		</div>
		<div class="w3-padding-16"></div>
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
