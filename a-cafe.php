<?PHP
session_start();

include("database.php");
if( !verifyAdmin($con) ) 
{
	header( "Location: index.php" );
	return false;
}
?>
<?PHP
$id_cafe	= (isset($_REQUEST['id_cafe'])) ? trim($_REQUEST['id_cafe']) : '0';
$act 		= (isset($_REQUEST['act'])) ? trim($_REQUEST['act']) : '';	

$cafe_name	= (isset($_POST['cafe_name'])) ? trim($_POST['cafe_name']) : '';
$location	= (isset($_POST['location'])) ? trim($_POST['location']) : '';
$cuisine	= (isset($_POST['cuisine'])) ? trim($_POST['cuisine']) : '';
$price_range= (isset($_POST['price_range'])) ? trim($_POST['price_range']) : '';
$type_cafe	= (isset($_POST['type_cafe'])) ? trim($_POST['type_cafe']) : '';

$cafe_name	=	mysqli_real_escape_string($con, $cafe_name);
$cuisine	=	mysqli_real_escape_string($con, $cuisine);

$success = "";

if($act == "add")
{	
	$uploaded_photos = array();
	$allowed = array('jpeg','jpg','png','gif');
	
	if(isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
		foreach($_FILES['photos']['name'] as $key => $file_name) {
			if(empty($file_name)) continue;
			$file_tmp = $_FILES['photos']['tmp_name'][$key];
			$fileNameCmps = explode(".", $file_name);
			$file_ext = strtolower(end($fileNameCmps));
			if(in_array($file_ext, $allowed) && is_uploaded_file($file_tmp)) {
				$uploaded_photos[] = array('tmp'=>$file_tmp, 'ext'=>$file_ext);
			}
		}
	}
	
	if(count($uploaded_photos) < 2 || count($uploaded_photos) > 5) {
		$success = "Please upload between 2 and 5 images (JPEG, JPG, PNG or GIF).";
	} else {
		$SQL_insert = " 
		INSERT INTO `cafe`(`id_cafe`, `cafe_name`, `location`, `cuisine`, `photo`, `price_range`, `type_cafe`) 
		VALUES (NULL, '$cafe_name', '$location', '$cuisine', '', '$price_range', '$type_cafe')";		
		$result = mysqli_query($con, $SQL_insert);
		$id_cafe = mysqli_insert_id($con);
		
		$photo_names = array();
		$ts = time();
		foreach($uploaded_photos as $i => $f) {
			$new_name = 'cafe_'.$id_cafe.'_'.$i.'_'.$ts.'.'.$f['ext'];
			move_uploaded_file($f['tmp'], "upload/".$new_name);
			$photo_names[] = $new_name;
		}
		$photo_str = implode(',', $photo_names);
		mysqli_query($con, "UPDATE `cafe` SET `photo`='".mysqli_real_escape_string($con,$photo_str)."' WHERE `id_cafe` = '$id_cafe'");
		$success = "Successfully Add";
	}
	
	//print "<script>self.location='a-cafe.php';</script>";
}

if($act == "edit")
{	
	$SQL_update = " UPDATE
						`cafe`
					SET
						`cafe_name` = '$cafe_name',
						`location` = '$location',
						`cuisine` = '$cuisine',
						`price_range` = '$price_range',
						`type_cafe` = '$type_cafe'
					WHERE `id_cafe` =  '$id_cafe'";	
										
	$result = mysqli_query($con, $SQL_update) or die("Error in query: ".$SQL_update."<br />".mysqli_error($con));
	
	// -------- Photo -----------------
	if(isset($_FILES['photo'])){
		 
		  $file_name = $_FILES['photo']['name'];
		  $file_size = $_FILES['photo']['size'];
		  $file_tmp = $_FILES['photo']['tmp_name'];
		  $file_type = $_FILES['photo']['type'];
		  
		  $fileNameCmps = explode(".", $file_name);
		  $file_ext = strtolower(end($fileNameCmps));
		  
		  if(empty($errors)==true) {
			 move_uploaded_file($file_tmp,"upload/".$file_name);
			
			$query = "UPDATE `cafe` SET `photo`='$file_name' WHERE `id_cafe` = '$id_cafe'";		
			$result = mysqli_query($con, $query) or die("Error in query: ".$query."<br />".mysqli_error($con));
		  }else{
			 print_r($errors);
		  }  
	}
	// -------- End Photo -----------------
	
	$success = "Successfully Update";
	//print "<script>alert('Successfully Update'); self.location='a-cafe.php';</script>";
}

if($act == "del")
{
	$SQL_delete = " DELETE FROM `cafe` WHERE `id_cafe` =  '$id_cafe' ";
	$result = mysqli_query($con, $SQL_delete);
	
	$success = "Successfully Delete";
	//print "<script>self.location='a-cafe.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<title>Café Recommendation System</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<link href="css/table.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />

<!-- include summernote css-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- include summernote js-->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<style>
a { text-decoration : none ;}
body,h1,h2,h3,h4,h5,h6 {font-family: "Poppins", sans-serif}
body, html { height: 100%; line-height: 1.8; }

.bgimg-1 {
  background-position: top;
  background-size: cover;
  background-attachment:fixed;
  background-image: url(images/banner.jpg);
  min-height:100%;
  background-color: rgba(0, 0, 0, 0.2);
  background-blend-mode: overlay; 
}
.w3-bar .w3-button { padding: 16px; }

/* Friendly redesign */
.cafe-page-container { background: rgba(255,255,255,0.98); border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.08); overflow: hidden; }
.cafe-header { background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); color: #fff; padding: 28px 32px; }
.cafe-header h1 { margin: 0; font-size: 1.6rem; font-weight: 600; }
.cafe-header p { margin: 8px 0 0; opacity: 0.9; font-size: 0.95rem; }
.btn-add-cafe { background: linear-gradient(135deg, #D2691E, #8B4513) !important; border: none; padding: 12px 24px; font-weight: 600; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; }
.btn-add-cafe:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(210,105,30,0.4); }
.form-section { margin-bottom: 24px; }
.form-section label { display: block; font-weight: 500; color: #333; margin-bottom: 8px; font-size: 0.95rem; }
.form-section input[type="text"], .form-section textarea, .form-section input[type="file"] { border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px 14px; transition: border-color 0.2s; }
.form-section input:focus, .form-section textarea:focus { border-color: #D2691E; outline: none; }
.form-hint { font-size: 0.85rem; color: #666; margin-top: 6px; }
.modal-form-header { background: linear-gradient(135deg, #8B4513, #D2691E); color: #fff; padding: 20px 24px; font-weight: 600; font-size: 1.2rem; border-radius: 12px 12px 0 0; }
.modal-close { color: #fff !important; opacity: 0.9; }
.modal-close:hover { opacity: 1; }
.btn-submit { background: linear-gradient(135deg, #D2691E, #8B4513) !important; border: none; padding: 12px 28px; font-weight: 600; border-radius: 10px; }
.btn-submit:hover { opacity: 0.95; }
.photo-upload-zone { border: 2px dashed #d4a574; background: #fffbf7; border-radius: 12px; padding: 24px; text-align: center; transition: all 0.2s; }
.photo-upload-zone:hover, .photo-upload-zone.dragover { border-color: #D2691E; background: #fff5eb; }
.photo-upload-zone i { font-size: 2.5rem; color: #D2691E; margin-bottom: 12px; }
.photo-preview-grid { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
.photo-preview-item { width: 80px; height: 80px; border-radius: 10px; overflow: hidden; border: 2px solid #e8e8e8; object-fit: cover; }
.table-cafe td, .table-cafe th { vertical-align: middle !important; padding: 14px 12px !important; }
.table-cafe tbody tr:hover { background: #fffbf7; }
.action-btn { padding: 8px 12px; border-radius: 8px; transition: background 0.2s; }
.action-btn:hover { background: #fff5eb; }
</style>

<body class="bgimg-1">

<?PHP include("menu-admin.php"); ?>

<!--- Toast Notification -->
<?PHP 
if($success) { 
	$status = (strpos($success, 'Please') === 0) ? "error" : "success";
	Notify($status, $success, "a-cafe.php"); 
}
?>	

<div class="" >
	<div class="w3-padding-32"></div>

	<div class="w3-container w3-content" style="max-width:1200px;">    
	  <div class="cafe-page-container w3-white">
		<div class="cafe-header w3-display-container">
			<h1><i class="fas fa-coffee"></i> Cafe Management</h1>
			<p>Manage your cafe listings. Add new cafes with photos to help users discover great places.</p>
			<a onclick="document.getElementById('add01').style.display='block';" class="w3-button btn-add-cafe w3-display-topright" style="position:absolute!important;top:24px;right:24px;"><i class="fa fa-fw fa-plus"></i> Add New Cafe</a>
		</div>
	  
	  <div class="w3-padding-24">
		
		<div class="table-responsive">
		<table class="table table-bordered table-cafe" id="dataTable" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th>#</th>
					<th>Photo</th>
					<th>Cafe Name</th>
					<th>Location</th>
					<th>Description / Cuisine</th>
					<th>Price Range</th>
					<th>Type of Cafe</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?PHP
			$bil = 0;
			$SQL_list = "SELECT * FROM `cafe` ";
			$result = mysqli_query($con, $SQL_list) ;
			while ( $data	= mysqli_fetch_array($result) )
			{
				$bil++;
				$photo	= $data["photo"];
				$photos_arr = $photo ? explode(',', trim($photo)) : array();
				$main_photo = (!empty($photos_arr)) ? trim($photos_arr[0]) : "noimage.jpg";
				$id_cafe= $data["id_cafe"];
			?>			
			<tr>
				<td><?PHP echo $bil ;?></td>
				<td><img src="upload/<?PHP echo $main_photo; ?>" class="w3-round-large w3-image" alt="image" style="width:100%;max-width:60px"></td>
				<td><?PHP echo $data["cafe_name"] ;?></td>
				<td><?PHP echo $data["location"] ;?></td>
				<td><?PHP echo substrwords($data["cuisine"],100) ;?></td>
				<td><?PHP echo $data["price_range"] ;?></td>
				<td><?PHP echo $data["type_cafe"] ;?></td>
				<td>
				<a href="#" onclick="document.getElementById('idEdit<?PHP echo $bil;?>').style.display='block'" class=""><i class="fa fa-fw fa-edit fa-lg"></i></a>
				
				<a title="Delete" onclick="document.getElementById('idDelete<?PHP echo $bil;?>').style.display='block'" class="w3-text-red"><i class="fa fa-fw fa-trash-alt fa-lg"></i></a>
				</td>
			</tr>
			
<div id="idEdit<?PHP echo $bil; ?>" class="w3-modal" style="z-index:10;">
	<div class="w3-modal-content w3-round-large w3-card-4 w3-animate-zoom" style="max-width:720px">
      <header class="modal-form-header w3-display-container"> 
        <span onclick="document.getElementById('idEdit<?PHP echo $bil; ?>').style.display='none'" class="w3-button modal-close w3-large w3-display-topright"><i class="fa fa-fw fa-times"></i></span>
        <i class="fas fa-edit"></i> Update Cafe
      </header>

		<div class="w3-container w3-padding-24">
		
		<form action="" method="post" enctype="multipart/form-data">

				<div class="form-section">
					<label>Photo</label>
					<?PHP 
					$edit_photos = ($data["photo"]) ? explode(',', trim($data["photo"])) : array(); 
					$edit_first_photo = !empty($edit_photos) ? trim($edit_photos[0]) : ''; 
					if(empty($edit_first_photo)) { ?>
					<div class="custom-file">
						<input type="file" class="w3-input w3-border w3-round" name="photo" id="photo" accept=".jpeg,.jpg,.png,.gif">
					</div>
					<p></p>
					<?PHP } ?>
					
					<?PHP if(!empty($edit_first_photo)) { ?>
					<div class="w3-input w3-border w3-round">
					<a class="w3-tag w3-green w3-round" target="_BLANK" href="upload/<?PHP echo htmlspecialchars($edit_first_photo); ?>"><small>View</small></a>
					<?PHP if(count($edit_photos)>1) echo '<small class="w3-text-grey">('.count($edit_photos).' images)</small> '; ?>
					<a class="w3-tag w3-red w3-round" href="photo-del.php?id_cafe=<?PHP echo $data["id_cafe"];?>"><small>Remove All</small></a>
					</div>
					<?PHP } ?>
					<small class="form-hint">JPEG, JPG, PNG or GIF allowed</small>
				</div>
			  
				<div class="form-section">
					<label>Cafe Name *</label>
					<input class="w3-input w3-border" type="text" name="cafe_name" value="<?PHP echo htmlspecialchars($data["cafe_name"]); ?>" required>
				</div>
				
				<div class="form-section">
					<label>Location *</label>
					<input class="w3-input w3-border" type="text" name="location" value="<?PHP echo htmlspecialchars($data["location"]); ?>" required>
				</div>
				
				<div class="form-section">
					<label>Description / Cuisine *</label>
					<textarea class="w3-input w3-border summernote-edit" name="cuisine" rows="4" required><?PHP echo htmlspecialchars($data["cuisine"]); ?></textarea>
				</div>
				
				<div class="form-section">
					<label>Price Range *</label>
					<input class="w3-input w3-border" type="text" name="price_range" value="<?PHP echo htmlspecialchars($data["price_range"]); ?>" required>
				</div>
				
				<div class="form-section">
					<label>Type Of Cafe *</label>
					<input class="w3-input w3-border" type="text" name="type_cafe" value="<?PHP echo htmlspecialchars($data["type_cafe"]); ?>" required>
				</div>
			  
			<hr class="w3-clear">
			<input type="hidden" name="id_cafe" value="<?PHP echo $data["id_cafe"];?>">
			<input type="hidden" name="act" value="edit">
			<button type="submit" class="w3-button btn-submit w3-text-white">Save Changes</button>

		</form>
		</div>
	</div>
<div class="w3-padding-24"></div>
</div>

<div id="idDelete<?PHP echo $bil; ?>" class="w3-modal" style="z-index:10;">
	<div class="w3-modal-content w3-round-large w3-card-4 w3-animate-zoom" style="max-width:460px">
      <header class="w3-container w3-red w3-padding-16">
        <span onclick="document.getElementById('idDelete<?PHP echo $bil; ?>').style.display='none'" class="w3-button w3-display-topright w3-text-white"><i class="fa fa-fw fa-times"></i></span>
        <b class="w3-large"><i class="fas fa-exclamation-triangle"></i> Delete Confirmation</b>
      </header>

		<div class="w3-container w3-padding-24">
		
		<form action="" method="post">
			  
			<hr class="w3-clear">			
			Are you sure to delete this record ?
			<div class="w3-padding-16"></div>
			
			<input type="hidden" name="id_cafe" value="<?PHP echo $data["id_cafe"];?>" >
			<input type="hidden" name="act" value="del" >
			<button type="button" onclick="document.getElementById('idDelete<?PHP echo $bil; ?>').style.display='none'"  class="w3-button w3-gray w3-text-white w3-margin-bottom w3-round">CANCEL</button>
			
			<button type="submit" class="w3-right w3-button w3-red w3-text-white w3-margin-bottom w3-round">YES, CONFIRM</button>
		</form>
		</div>
	</div>
</div>				
			<?PHP } ?>
			</tbody>
		</table>
		</div>

		
	  </div>
	  </div>
	  
	<!-- End Page Container -->
	</div>
	
	<div class="w3-padding-24"></div>
	
</div>



<div id="add01" class="w3-modal" >
    <div class="w3-modal-content w3-round-large w3-card-4 w3-animate-zoom" style="max-width:720px">
      <header class="modal-form-header w3-display-container"> 
        <span onclick="document.getElementById('add01').style.display='none'" class="w3-button modal-close w3-large w3-display-topright"><i class="fa fa-fw fa-times"></i></span>
        <i class="fas fa-plus-circle"></i> Add New Cafe
      </header>
	  
      <div class="w3-container w3-padding-24">
		<form action="" method="post" enctype="multipart/form-data" id="addCafeForm">
				
				<div class="form-section">
					<label><i class="fas fa-images w3-text-amber"></i> Photos * (2 to 5 images required)</label>
					<div class="photo-upload-zone" id="photoZone" onclick="document.getElementById('photos').click()">
						<i class="fas fa-cloud-upload-alt"></i>
						<p style="margin:0;color:#666;">Click or drag to select 2–5 images</p>
						<p class="form-hint">JPEG, JPG, PNG or GIF • Min 2, Max 5 images</p>
						<input type="file" name="photos[]" id="photos" accept=".jpeg,.jpg,.png,.gif" multiple style="display:none;">
					</div>
					<div class="photo-preview-grid" id="photoPreview"></div>
					<p class="form-hint" id="photoCountMsg" style="color:#c62828;display:none;"></p>
				</div>
				
				<div class="form-section">
					<label><i class="fas fa-store w3-text-amber"></i> Cafe Name *</label>
					<input class="w3-input w3-border" type="text" name="cafe_name" placeholder="e.g. The Coffee House" required>
				</div>
				
				<div class="form-section">
					<label><i class="fas fa-map-marker-alt w3-text-amber"></i> Location *</label>
					<input class="w3-input w3-border" type="text" name="location" placeholder="Address or area" required>
				</div>
				
				<div class="form-section">
					<label><i class="fas fa-utensils w3-text-amber"></i> Description / Cuisine *</label>
					<textarea class="w3-input w3-border" name="cuisine" id="makeMeSummernote2" rows="4" placeholder="Describe the cafe, menu, ambiance..." required></textarea>
				</div>
				
				<div class="w3-row">
					<div class="w3-half form-section">
						<label><i class="fas fa-tag w3-text-amber"></i> Price Range *</label>
						<input class="w3-input w3-border" type="text" name="price_range" placeholder="e.g. RM 10 - 30" required>
					</div>
					<div class="w3-half form-section">
						<label><i class="fas fa-mug-hot w3-text-amber"></i> Type of Cafe *</label>
						<input class="w3-input w3-border" type="text" name="type_cafe" placeholder="e.g. Book Cafe, Cozy" required>
					</div>
				</div>
			  
			  <hr class="w3-clear">
			  
			  <div class="w3-section">
				<input name="act" type="hidden" value="add">
				<button type="submit" class="w3-button btn-submit w3-text-white" id="btnAddSubmit"><i class="fas fa-check"></i> Add Cafe</button>
				<button type="button" onclick="document.getElementById('add01').style.display='none'" class="w3-button w3-grey w3-round">Cancel</button>
			  </div>
		</form> 
      </div>
</div>

<!-- Script -->
<script type="text/javascript">
	$('#makeMeSummernote2,.summernote-edit').summernote({ height:200 });
</script>

<script>
(function() {
	var photoZone = document.getElementById('photoZone');
	var photosInput = document.getElementById('photos');
	var photoPreview = document.getElementById('photoPreview');
	var photoCountMsg = document.getElementById('photoCountMsg');
	var addForm = document.getElementById('addCafeForm');
	if (!addForm) return;

	function updatePreview() {
		var files = photosInput.files;
		photoPreview.innerHTML = '';
		photoCountMsg.style.display = 'none';
		if (files.length === 0) return;
		for (var i = 0; i < files.length && i < 5; i++) {
			var f = files[i];
			if (!f.type.match(/^image\/(jpeg|jpg|png|gif)$/)) continue;
			var reader = new FileReader();
			reader.onload = (function(idx) {
				return function(e) {
					var img = document.createElement('img');
					img.src = e.target.result;
					img.className = 'photo-preview-item';
					img.alt = 'Preview ' + (idx+1);
					photoPreview.appendChild(img);
				};
			})(i);
			reader.readAsDataURL(f);
		}
		if (files.length < 2 || files.length > 5) {
			photoCountMsg.textContent = 'Please select between 2 and 5 images (currently: ' + files.length + ').';
			photoCountMsg.style.display = 'block';
		}
	}

	photosInput.addEventListener('change', updatePreview);

	addForm.addEventListener('submit', function(e) {
		var files = photosInput.files;
		if (files.length < 2 || files.length > 5) {
			e.preventDefault();
			photoCountMsg.textContent = 'Please select between 2 and 5 images (currently: ' + files.length + ').';
			photoCountMsg.style.display = 'block';
			photoZone.scrollIntoView({ behavior: 'smooth' });
			return false;
		}
	});

	photoZone.addEventListener('dragover', function(e) { e.preventDefault(); photoZone.classList.add('dragover'); });
	photoZone.addEventListener('dragleave', function() { photoZone.classList.remove('dragover'); });
	photoZone.addEventListener('drop', function(e) {
		e.preventDefault();
		photoZone.classList.remove('dragover');
		if (e.dataTransfer.files.length) {
			photosInput.files = e.dataTransfer.files;
			updatePreview();
		}
	});
})();
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<!--<script src="assets/demo/datatables-demo.js"></script>-->


<script>
$(document).ready(function() {

  
	$('#dataTable').DataTable( {
		paging: true,
		
		searching: true
	} );
		
	
});
</script>

 
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
