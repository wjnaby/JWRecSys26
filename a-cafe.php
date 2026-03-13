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

// Ensure food_photos column exists
$check = @mysqli_query($con, "SHOW COLUMNS FROM `cafe` LIKE 'food_photos'");
if($check && mysqli_num_rows($check) == 0) {
	mysqli_query($con, "ALTER TABLE `cafe` ADD COLUMN `food_photos` TEXT DEFAULT NULL AFTER `photo`");
}

if($act == "add")
{	
	// Validate food_photos: 2 to 4 images (more than 1, less than 5)
	$food_uploaded = array();
	$allowed = array('jpeg','jpg','png','gif');
	if(isset($_FILES['food_photos']) && is_array($_FILES['food_photos']['name'])) {
		foreach($_FILES['food_photos']['name'] as $key => $file_name) {
			if(empty($file_name)) continue;
			$file_tmp = $_FILES['food_photos']['tmp_name'][$key];
			$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			if(in_array($ext, $allowed) && is_uploaded_file($file_tmp)) {
				$food_uploaded[] = array('tmp'=>$file_tmp, 'ext'=>$ext);
			}
		}
	}
	if(count($food_uploaded) < 2 || count($food_uploaded) > 4) {
		$success = "Food & Cafe Photos: please upload 2 to 4 images (more than 1, less than 5).";
	} else {
		$SQL_insert = " 
		INSERT INTO `cafe`(`id_cafe`, `cafe_name`, `location`, `cuisine`, `photo`, `food_photos`, `price_range`, `type_cafe`) 
		VALUES (NULL, '$cafe_name', '$location', '$cuisine', '', '', '$price_range', '$type_cafe')";		
		$result = mysqli_query($con, $SQL_insert);
		$id_cafe = mysqli_insert_id($con);
		
		// Existing photo (single)
		if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
			$file_name = $_FILES['photo']['name'];
			$file_tmp = $_FILES['photo']['tmp_name'];
			$fileNameCmps = explode(".", $file_name);
			$file_ext = strtolower(end($fileNameCmps));
			if(in_array($file_ext, $allowed) && is_uploaded_file($file_tmp)) {
				move_uploaded_file($file_tmp, "upload/".$file_name);
				mysqli_query($con, "UPDATE `cafe` SET `photo`='".mysqli_real_escape_string($con,$file_name)."' WHERE `id_cafe` = '$id_cafe'");
			}
		}
		
		// Food & Cafe photos (2–4)
		$ts = time();
		$food_names = array();
		foreach($food_uploaded as $i => $f) {
			$new_name = 'food_'.$id_cafe.'_'.$i.'_'.$ts.'.'.$f['ext'];
			move_uploaded_file($f['tmp'], "upload/".$new_name);
			$food_names[] = $new_name;
		}
		$food_str = implode(',', $food_names);
		mysqli_query($con, "UPDATE `cafe` SET `food_photos`='".mysqli_real_escape_string($con,$food_str)."' WHERE `id_cafe` = '$id_cafe'");
		$success = "Successfully Add";
	}
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
	if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
		$file_name = $_FILES['photo']['name'];
		$file_tmp = $_FILES['photo']['tmp_name'];
		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		$allowed = array('jpeg','jpg','png','gif');
		if(in_array($file_ext, $allowed) && is_uploaded_file($file_tmp)) {
			move_uploaded_file($file_tmp, "upload/".$file_name);
			mysqli_query($con, "UPDATE `cafe` SET `photo`='".mysqli_real_escape_string($con,$file_name)."' WHERE `id_cafe` = '$id_cafe'");
		}
	}
	// -------- Food & Cafe Photos (optional replace, 2-4 images) --------
	if(isset($_FILES['food_photos']) && is_array($_FILES['food_photos']['name'])) {
		$food_uploaded = array();
		foreach($_FILES['food_photos']['name'] as $key => $fn) {
			if(empty($fn)) continue;
			$file_tmp = $_FILES['food_photos']['tmp_name'][$key];
			$ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
			if(in_array($ext, array('jpeg','jpg','png','gif')) && is_uploaded_file($file_tmp)) {
				$food_uploaded[] = array('tmp'=>$file_tmp, 'ext'=>$ext);
			}
		}
		if(count($food_uploaded) >= 2 && count($food_uploaded) <= 4) {
			$ts = time();
			$food_names = array();
			foreach($food_uploaded as $i => $f) {
				$new_name = 'food_'.$id_cafe.'_'.$i.'_'.$ts.'.'.$f['ext'];
				move_uploaded_file($f['tmp'], "upload/".$new_name);
				$food_names[] = $new_name;
			}
			mysqli_query($con, "UPDATE `cafe` SET `food_photos`='".mysqli_real_escape_string($con, implode(',',$food_names))."' WHERE `id_cafe` = '$id_cafe'");
		}
	}
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
:root {
  --cafe-primary: #8B4513;
  --cafe-primary-light: #D2691E;
  --cafe-primary-soft: #d4a574;
  --cafe-bg: #fffbf7;
  --cafe-card: #FFFFFF;
  --cafe-text: #333;
  --cafe-text-muted: #666;
  --cafe-border: #e0e0e0;
  --cafe-radius: 12px;
  --cafe-radius-lg: 20px;
  --cafe-shadow: 0 4px 24px rgba(0,0,0,0.08);
  --cafe-shadow-hover: 0 12px 40px rgba(139,69,19,0.15);
}
a { text-decoration: none; }
body, html { height: 100%; line-height: 1.7; font-family: "Poppins", sans-serif; }
body h1, body h2, body h3, body h4, body h5, body h6 { font-family: "Poppins", sans-serif; font-weight: 600; }

.bgimg-1 {
  background-position: top;
  background-size: cover;
  background-attachment: fixed;
  background-image: url(images/banner.jpg);
  min-height: 100%;
  background-color: rgba(0, 0, 0, 0.2);
  background-blend-mode: overlay;
}
.w3-bar .w3-button { padding: 16px; }

/* Modern card layout */
.cafe-page-container {
  background: var(--cafe-card);
  border-radius: var(--cafe-radius-lg);
  box-shadow: var(--cafe-shadow);
  overflow: hidden;
  border: 1px solid var(--cafe-border);
}
.cafe-header {
  background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-light) 100%);
  color: #fff;
  padding: 18px 24px;
  position: relative;
  overflow: hidden;
}
.cafe-header::after {
  content: '';
  position: absolute;
  bottom: -15px; right: -15px;
  width: 80px; height: 80px;
  background: rgba(255,255,255,0.08);
  border-radius: 50%;
}
.cafe-header h1 {
  margin: 0;
  font-size: 1.35rem;
  font-weight: 600;
  letter-spacing: -0.02em;
  display: flex;
  align-items: center;
  gap: 8px;
}
.cafe-header h1 i { opacity: 0.95; font-size: 1.2rem; }
.cafe-header p {
  margin: 6px 0 0;
  opacity: 0.92;
  font-size: 0.85rem;
  max-width: 420px;
  line-height: 1.5;
}
.btn-add-cafe {
  background: #fff !important;
  color: var(--cafe-primary) !important;
  border: none;
  padding: 12px 24px;
  font-weight: 600;
  font-size: 0.9rem;
  border-radius: 50px;
  transition: all 0.25s ease;
  box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}
.btn-add-cafe:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(210,105,30,0.4);
  background: #fffbf7 !important;
}
.form-section { margin-bottom: 22px; }
.form-section label {
  display: block;
  font-weight: 500;
  color: var(--cafe-text);
  margin-bottom: 8px;
  font-size: 0.9rem;
}
.form-section label i { margin-right: 6px; opacity: 0.8; }
.form-section input[type="text"],
.form-section input[type="file"],
.form-section textarea,
.form-section .w3-input {
  border-radius: var(--cafe-radius);
  border: 1px solid var(--cafe-border);
  padding: 12px 16px;
  transition: all 0.2s ease;
  font-size: 0.95rem;
}
.form-section input:focus,
.form-section textarea:focus,
.form-section .w3-input:focus {
  border-color: var(--cafe-primary-light);
  outline: none;
  box-shadow: 0 0 0 3px rgba(210, 105, 30, 0.2);
}
.form-hint { font-size: 0.82rem; color: var(--cafe-text-muted); margin-top: 6px; }
.modal-form-header {
  background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-light) 100%);
  color: #fff;
  padding: 22px 28px;
  font-weight: 600;
  font-size: 1.15rem;
  border-radius: var(--cafe-radius-lg) var(--cafe-radius-lg) 0 0;
  display: flex;
  align-items: center;
  gap: 10px;
}
.modal-close { color: #fff !important; opacity: 0.9; transition: opacity 0.2s; }
.modal-close:hover { opacity: 1; }
.btn-submit {
  background: linear-gradient(135deg, var(--cafe-primary), var(--cafe-primary-light)) !important;
  border: none;
  padding: 12px 28px;
  font-weight: 600;
  font-size: 0.95rem;
  border-radius: 50px;
  transition: all 0.25s ease;
}
.btn-submit:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(210, 105, 30, 0.4);
}
.photo-upload-zone {
  border: 2px dashed var(--cafe-primary-soft);
  background: #fffbf7;
  border-radius: var(--cafe-radius);
  padding: 28px;
  text-align: center;
  transition: all 0.25s ease;
  cursor: pointer;
}
.photo-upload-zone:hover,
.photo-upload-zone.dragover {
  border-color: var(--cafe-primary-light);
  background: #fff5eb;
}
.photo-upload-zone i {
  font-size: 2.25rem;
  color: var(--cafe-primary-light);
  margin-bottom: 10px;
  display: block;
}
.photo-preview-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
.photo-preview-item {
  width: 72px; height: 72px;
  border-radius: 10px;
  overflow: hidden;
  border: 2px solid var(--cafe-border);
  object-fit: cover;
}
.table-cafe td, .table-cafe th {
  vertical-align: middle !important;
  padding: 16px 14px !important;
  font-size: 0.9rem;
}
.table-cafe thead th {
  background: #fffbf7 !important;
  color: var(--cafe-text);
  font-weight: 600;
  border-color: var(--cafe-border) !important;
}
.table-cafe tbody tr {
  transition: background 0.2s ease;
}
.table-cafe tbody tr:hover { background: #fffbf7 !important; }
.table-cafe tbody td { border-color: #f5f0eb !important; }
.action-btn { padding: 8px 12px; border-radius: 10px; transition: all 0.2s; }
.action-btn:hover { background: #fff5eb; color: var(--cafe-primary); }
.w3-modal-content { border-radius: var(--cafe-radius-lg); overflow: hidden; box-shadow: var(--cafe-shadow-hover); }
/* DataTables modern styling */
.dataTables_wrapper .dataTables_filter input {
  border: 1px solid var(--cafe-border);
  border-radius: 10px;
  padding: 8px 14px;
  font-size: 0.9rem;
}
.dataTables_wrapper .dataTables_filter input:focus {
  border-color: var(--cafe-primary-light);
  outline: none;
  box-shadow: 0 0 0 2px rgba(210, 105, 30, 0.2);
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
  border-radius: 8px !important;
  margin: 0 2px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: var(--cafe-primary) !important;
  border-color: var(--cafe-primary) !important;
  color: #fff !important;
}
.dataTables_wrapper .dataTables_length select {
  border-radius: 8px;
  border: 1px solid var(--cafe-border);
}
.cafe-thumb { width:100%; max-width:56px; height:56px; object-fit:cover; border-radius:10px; border:1px solid var(--cafe-border); }
</style>

<body class="bgimg-1">

<?PHP include("menu-admin.php"); ?>

<!--- Toast Notification -->
<?PHP 
if($success) { 
	$status = (strpos($success, 'Food & Cafe') === 0) ? "error" : "success";
	Notify($status, $success, "a-cafe.php"); 
}
?>	

<div class="cafe-page-wrapper" style="padding-top:100px;padding-bottom:48px;display:flex;flex-direction:column;align-items:center;">
	<div class="w3-container w3-content" style="max-width:960px;margin:0 auto;width:100%;">    
	  <div class="cafe-page-container w3-white">
		<div class="cafe-header w3-display-container">
			<h1><i class="fas fa-mug-hot"></i> Cafe Management</h1>
			<p>Add and manage your cafe listings with photos and details. Help visitors discover amazing spots to enjoy.</p>
			<a onclick="document.getElementById('add01').style.display='block';" class="w3-button btn-add-cafe w3-display-topright" style="position:absolute!important;top:16px;right:20px;padding:8px 18px!important;font-size:0.85rem!important;"><i class="fa fa-fw fa-plus"></i> Add New Cafe</a>
		</div>
	  
	  <div class="w3-padding" style="padding:20px 24px !important;">
		
		<div class="table-responsive" style="border-radius:var(--cafe-radius);overflow:hidden;border:1px solid var(--cafe-border);">
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
				<td><img src="upload/<?PHP echo $main_photo; ?>" class="cafe-thumb" alt="Cafe"></td>
				<td><?PHP echo $data["cafe_name"] ;?></td>
				<td><?PHP echo $data["location"] ;?></td>
				<td><?PHP echo substrwords($data["cuisine"],100) ;?></td>
				<td><?PHP echo $data["price_range"] ;?></td>
				<td><?PHP echo $data["type_cafe"] ;?></td>
				<td>
				<a href="#" onclick="document.getElementById('idEdit<?PHP echo $bil;?>').style.display='block'" class="action-btn w3-text-grey" title="Edit"><i class="fa fa-fw fa-edit"></i></a>
				<a title="Delete" onclick="document.getElementById('idDelete<?PHP echo $bil;?>').style.display='block'" class="action-btn w3-text-grey" style="color:#c44536!important"><i class="fa fa-fw fa-trash-alt"></i></a>
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
				<?PHP 
				$edit_food = isset($data['food_photos']) ? trim($data['food_photos']) : '';
				$edit_food_arr = $edit_food ? array_map('trim', explode(',', $edit_food)) : array();
				?>
				<div class="form-section">
					<label>Food & Cafe Photos (optional replace: 2–4 images)</label>
					<?PHP if(!empty($edit_food_arr)) { ?>
					<div class="w3-margin-bottom">
						<a class="w3-tag w3-green w3-round" href="upload/<?PHP echo htmlspecialchars($edit_food_arr[0]);?>" target="_blank"><small>View</small></a>
						<small class="w3-text-grey"><?PHP echo count($edit_food_arr);?> images</small>
					</div>
					<?PHP } ?>
					<input type="file" class="w3-input w3-border" name="food_photos[]" accept=".jpeg,.jpg,.png,.gif" multiple>
					<p class="form-hint">Upload 2–4 new images to replace (optional)</p>
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
			<button type="submit" class="w3-button btn-submit w3-text-white"><i class="fas fa-check" style="margin-right:6px"></i>Save Changes</button>

		</form>
		</div>
	</div>
<div class="w3-padding-24"></div>
</div>

<div id="idDelete<?PHP echo $bil; ?>" class="w3-modal" style="z-index:10;">
	<div class="w3-modal-content w3-round-large w3-card-4 w3-animate-zoom" style="max-width:460px">
      <header class="w3-container" style="background:linear-gradient(135deg,#c44536,#e07a5f);color:#fff;padding:20px 24px;">
        <span onclick="document.getElementById('idDelete<?PHP echo $bil; ?>').style.display='none'" class="w3-button w3-display-topright w3-text-white" style="opacity:0.9"><i class="fa fa-fw fa-times"></i></span>
        <span style="font-weight:600;font-size:1.05rem;"><i class="fas fa-trash-alt" style="margin-right:8px"></i>Delete this cafe?</span>
      </header>

		<div class="w3-container w3-padding-24">
		<form action="" method="post">
			<p style="color:#495057;margin:0 0 20px;line-height:1.6;">This will permanently remove the cafe from your list. This action cannot be undone.</p>
			
			<input type="hidden" name="id_cafe" value="<?PHP echo $data["id_cafe"];?>" >
			<input type="hidden" name="act" value="del" >
			<button type="button" onclick="document.getElementById('idDelete<?PHP echo $bil; ?>').style.display='none'" class="w3-button w3-margin-right" style="background:#e9ecef;color:#495057;border-radius:10px;padding:10px 20px;">Cancel</button>
			<button type="submit" class="w3-button w3-margin-bottom" style="background:#c44536;color:#fff;border:none;border-radius:10px;padding:10px 24px;font-weight:500;">Yes, delete</button>
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
					<label><i class="fas fa-image" style="color:var(--cafe-primary-light)"></i> Photo *</label>
					<input class="w3-input w3-border" type="file" name="photo" accept=".jpeg,.jpg,.png,.gif" required>
					<p class="form-hint">JPEG, JPG, PNG or GIF allowed</p>
				</div>
				
				<div class="form-section">
					<label><i class="fas fa-utensils" style="color:var(--cafe-primary-light)"></i> Food & Cafe Photos * (2 to 4 images)</label>
					<div class="photo-upload-zone" id="foodPhotoZone" onclick="document.getElementById('food_photos').click()">
						<i class="fas fa-cloud-upload-alt"></i>
						<p style="margin:0;color:var(--cafe-text-muted);">Click or drag to upload 2–4 photos of food & ambiance</p>
						<p class="form-hint">More than 1, less than 5 • JPEG, JPG, PNG or GIF</p>
						<input type="file" name="food_photos[]" id="food_photos" accept=".jpeg,.jpg,.png,.gif" multiple style="display:none;">
					</div>
					<div class="photo-preview-grid" id="foodPhotoPreview"></div>
					<p class="form-hint" id="foodPhotoCountMsg" style="color:#c62828;display:none;"></p>
				</div>
				
				<div class="form-section">
					<label><i class="fas fa-store" style="color:var(--cafe-primary-light)"></i> Cafe Name *</label>
					<input class="w3-input w3-border" type="text" name="cafe_name" placeholder="e.g. The Coffee House" required>
				</div>
				
				<div class="form-section">
					<label><i class="fas fa-map-marker-alt" style="color:var(--cafe-primary-light)"></i> Location *</label>
					<input class="w3-input w3-border" type="text" name="location" placeholder="Address or area" required>
				</div>
				
				<div class="form-section">
					<label><i class="fas fa-utensils" style="color:var(--cafe-primary-light)"></i> Description / Cuisine *</label>
					<textarea class="w3-input w3-border" name="cuisine" id="makeMeSummernote2" rows="4" placeholder="Describe the cafe, menu, ambiance..." required></textarea>
				</div>
				
				<div class="w3-row">
					<div class="w3-half form-section">
						<label><i class="fas fa-tag" style="color:var(--cafe-primary-light)"></i> Price Range *</label>
						<input class="w3-input w3-border" type="text" name="price_range" placeholder="e.g. RM 10 - 30" required>
					</div>
					<div class="w3-half form-section">
						<label><i class="fas fa-mug-hot" style="color:var(--cafe-primary-light)"></i> Type of Cafe *</label>
						<input class="w3-input w3-border" type="text" name="type_cafe" placeholder="e.g. Book Cafe, Cozy" required>
					</div>
				</div>
			  
			  <hr class="w3-clear">
			  
			  <div class="w3-section">
				<input name="act" type="hidden" value="add">
				<button type="submit" class="w3-button btn-submit w3-text-white" id="btnAddSubmit"><i class="fas fa-check"></i> Add Cafe</button>
				<button type="button" onclick="document.getElementById('add01').style.display='none'" class="w3-button" style="background:#e9ecef;color:#495057;border-radius:50px;padding:12px 24px;">Cancel</button>
			  </div>
		</form> 
      </div>
</div>

<!-- Script -->
<script type="text/javascript">
	$('#makeMeSummernote2,.summernote-edit').summernote({ height:200 });
</script>
<script>
(function(){
	var zone=document.getElementById('foodPhotoZone'), inp=document.getElementById('food_photos'), prev=document.getElementById('foodPhotoPreview'), msg=document.getElementById('foodPhotoCountMsg'), form=document.getElementById('addCafeForm');
	if(!form) return;
	function update(){
		var files=inp.files; prev.innerHTML=''; msg.style.display='none';
		if(files.length) {
			for(var i=0;i<files.length&&i<5;i++){
				var f=files[i];
				if(!f.type.match(/^image\/(jpeg|jpg|png|gif)$/)) continue;
				var r=new FileReader(); r.onload=(function(idx){ return function(e){ var img=document.createElement('img'); img.src=e.target.result; img.className='photo-preview-item'; prev.appendChild(img); }; })(i);
				r.readAsDataURL(f);
			}
			if(files.length<2||files.length>4){ msg.textContent='Select 2 to 4 images (currently: '+files.length+').'; msg.style.display='block'; }
		}
	}
	inp.addEventListener('change',update);
	form.addEventListener('submit',function(e){
		var n=inp.files.length;
		if(n<2||n>4){ e.preventDefault(); msg.textContent='Food & Cafe Photos: select 2 to 4 images (currently: '+n+').'; msg.style.display='block'; zone.scrollIntoView({behavior:'smooth'}); return false; }
	});
	zone.addEventListener('dragover',function(e){ e.preventDefault(); zone.classList.add('dragover'); });
	zone.addEventListener('dragleave',function(){ zone.classList.remove('dragover'); });
	zone.addEventListener('drop',function(e){ e.preventDefault(); zone.classList.remove('dragover'); if(e.dataTransfer.files.length){ inp.files=e.dataTransfer.files; update(); } });
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
