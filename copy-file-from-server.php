<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>Copy File - Server to Server</title>
</head>

<body>
<?php

ini_set('max_execution_time', '3600');

function url_exists($src) { // Function to check source file.
    $handle   = curl_init($src);
    if (false === $handle) {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);
    curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox    
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);   
    return $connectable;
}

if (isset($_POST['flag'])) {

	if (isset($_POST['src_path'])) { // Check for source folder.
		$src_path = $_POST['src_path'];
	} else {
		echo "<p>Forgot the Destination Folder!</p>";
		exit();
	}
	
	if (isset($_POST['filename'])) { // Check for file name.
		$filename = trim($_POST['filename']);
	} else {
		echo "<p>Forgot the File Name!</p>";
		exit();
	}
	
	if (isset($_POST['dest_folder'])) { // Check for destination folder.
		$dest_folder = $_POST['dest_folder'];
		$src = $src_path . '/' . $filename;
	} else {
		echo "<p>Forgot the Destination Folder!</p>";
		exit();
	}
	
	if ($_POST['rename']) { // Is rename selected?
		if (isset($_POST['newname'])) { // Check for new name.
			$newname = trim($_POST['newname']);
		} else {
			echo "<p>Forgot the New Name!</p>";
			exit();
		}
	} else { // Keep same name.
		$newname = $filename;
	}
	
	
	
	$dest = $_SERVER['DOCUMENT_ROOT'] . '/' . $dest_folder . '/' . $newname;
	
	if (!url_exists($src)) { // Check for existing source file
		echo '<p>ERROR: The source file does not exists!</p>';
		echo '<p>' . $src . '</p>';
		echo '<p>Try a different name.</p>';
		exit();
	}
	
	if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $dest_folder)) { // Check for a destination folder.
		echo '<p>ERROR: The destination location does not exists!</p>';
		echo '<p>' . $_SERVER['DOCUMENT_ROOT'] . '/' . $dest_folder . '/' . '</p>';
		echo '<p>Try a different path.</p>';
		exit();
	}
	
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $dest_folder . '/' . $newname)) { // Check for existing destination file.
		echo '<p>ERROR: This destination file already exists!</p>';
		echo '<p>Use a different name.</p>';
		exit();
	}
	
	if (copy ($src, $dest)) {
		echo "<p>Copy Complete: $newname</p>";
		$filename = ''; // Clear the source file name. 
		echo '<p>Copy Another File...</p>';
		
	} else {
		echo "<p>COPY FAILED: File Not Found!</p>";
		echo '<p>Please try again.</p>';
		echo "<p>Source: $src</p>";
		echo "<p>Destination: $dest</p>";
	}
}
?>
<h1>Copy File</h1>
<form action="copyFile.php" method="post">
<input type="hidden" name="flag" value="TRUE" />
<p><strong>Source Path:</strong><input type="text" size="50" name="src_path" value="<?php echo $_POST['src_path']; ?>" /> /</p>
<p><strong>Source File Name:</strong> <input type="text" size="45" name="filename" value="<?php echo $_POST['filename']; ?>" /></p>
<hr width="50%" size="2" align="left" />
<p><strong>Destination Folder:</strong> <input type="text" size="50" name="dest_folder" value="<?php if (isset($_POST['dest_folder'])) { echo $_POST['dest_folder']; } ?>" /> /</p>
<p><input type="checkbox" name="rename" /> <strong>Rename at Destination to:</strong> <input type="text" size="40" name="newname" value="<?php if (isset($_POST['rename'])) { echo $newname; } ?>" /></p>
<input type="submit" value="Copy" />
</form>
</body>
</html>
