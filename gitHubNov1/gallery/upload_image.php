<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Upload an Image</title>
	<style type="text/css" title="text/css" media="all">
	.error {
		font-weight: bold;
		color: #C00;
	}
	</style>
</head>
<body>
<?php # Script 11.2 - upload_image.php
//connect to db
include('mysql.php');


// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Check for an uploaded file:
	if (isset($_FILES['upload'])) {
		
		// Validate the type. Should be JPEG or PNG.
		$allowed = array ('image/pjpeg', 'image/jpeg', 'image/JPG', 'image/X-PNG', 'image/PNG', 'image/png', 'image/x-png');
		if (in_array($_FILES['upload']['type'], $allowed)) {
		
			//get last id of image from db for new image name
			$result = mysqli_query($link, "SELECT `imageID` FROM `image` ORDER BY `imageID` DESC;") or die(mysqli_error($link));
			$id = mysqli_fetch_assoc($result);
			$new_id = $id['imageID']+1;
			// Move the file over, set new name
			if (move_uploaded_file ($_FILES['upload']['tmp_name'], "../uploads/{$new_id}.jpg")) {

				//check for description and insert description and image to db
				if(isset($_POST['description'])){
					$stmt = mysqli_prepare($link, "INSERT INTO `image`(`name`, `descID`) VALUES('".$new_id.".jpg', ?);") or die(mysqli_error($link));
					mysqli_stmt_bind_param($stmt, 'i', $_POST['description']);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
					mysqli_query($link, "INSERT INTO `votes_amount`(`amount`, `imageID`) VALUES('0', ".$new_id.");") or die(mysqli_error($link));
					mysqli_close($link);
				}

				echo '<p><em>The file has been uploaded!</em></p>';
			} // End of move... IF.
			
		} else { // Invalid type.
			echo '<p class="error">Please upload a JPEG or PNG image.</p>';
		}

	} // End of isset($_FILES['upload']) IF.
	
	// Check for an error:
	if ($_FILES['upload']['error'] > 0) {
		echo '<p class="error">The file could not be uploaded because: <strong>';
	
		// Print a message based upon the error.
		switch ($_FILES['upload']['error']) {
			case 1:
				print 'The file exceeds the upload_max_filesize setting in php.ini.';
				break;
			case 2:
				print 'The file exceeds the MAX_FILE_SIZE setting in the HTML form.';
				break;
			case 3:
				print 'The file was only partially uploaded.';
				break;
			case 4:
				print 'No file was uploaded.';
				break;
			case 6:
				print 'No temporary folder was available.';
				break;
			case 7:
				print 'Unable to write to the disk.';
				break;
			case 8:
				print 'File upload stopped.';
				break;
			default:
				print 'A system error occurred.';
				break;
		} // End of switch.
		
		print '</strong></p>';
	
	} // End of error IF.
	
	// Delete the file if it still exists:
	if (file_exists ($_FILES['upload']['tmp_name']) && is_file($_FILES['upload']['tmp_name']) ) {
		unlink ($_FILES['upload']['tmp_name']);
	}
			
} // End of the submitted conditional.
?>
	
<form enctype="multipart/form-data" action="upload_image.php" method="post">

	<input type="hidden" name="MAX_FILE_SIZE" value="524288" />
	
	<fieldset><legend>Select a JPEG or PNG image of 512KB or smaller to be uploaded:</legend>
	
	<p><b>File:</b> <input type="file" name="upload" /></p>
	
	</fieldset>
	<div align="center"><input type="submit" name="submit" value="Submit" /></div>

<select name="description">
<?php
//select options from database goes here
include('mysql.php');
$result = mysqli_query($link, "SELECT * FROM `description`;") or die(mysqli_error($link));
while($row = mysqli_fetch_assoc($result)){
	$desc[] = $row;
}
for($i=0;$i<count($desc);$i++){
	print "<option value='".$desc[$i]['descID']."'>".$desc[$i]['desc']."</option>";
}
?>
</select>

</form>
</body>
</html>