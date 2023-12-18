
<?php // Mitchell Bennis (mitch@elementengage.com)
// Version 2.1 -- Rev 12.15.23
// PHP 8 Approved

// This script will display a simple form.
// When the form is submitted it will use the inputs to copy a remote file to local.

<?php 
// Mitchell Bennis (mitch@elementengage.com)
// Version 3.0 -- Revamped for PHP 8 compatibility and best practices

// This script displays a form and copies a remote file to a local destination upon submission.

function urlExists($src) {
	$handle = curl_init($src);
	if (!$handle) {
		throw new Exception("CURL initialization failed for URL: $src");
	}
	curl_setopt($handle, CURLOPT_HEADER, false);
	curl_setopt($handle, CURLOPT_FAILONERROR, true);
	curl_setopt($handle, CURLOPT_NOBODY, true);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
	$result = curl_exec($handle);
	if (curl_errno($handle)) {
		throw new Exception("CURL error for URL: $src - " . curl_error($handle));
	}
	curl_close($handle);   
	return $result;
}

function sanitize_input($data) {
	return htmlspecialchars(stripslashes(trim($data)));
}

function process_input($field) {
	return isset($_POST[$field]) ? sanitize_input($_POST[$field]) : null;
}

$src_path = $filename = $dest_folder = $newname = "";
$error = "";

try {
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eeGo'])) {
		$src_path = process_input('src_path');
		$filename = process_input('filename');
		$dest_folder = process_input('dest_folder');
		$rename = isset($_POST['rename']);
		$newname = $rename ? process_input('newname') : $filename;

		if (!$src_path || !$filename || !$dest_folder) {
			throw new Exception("Required fields are missing.");
		}

		if ($rename && !$newname) {
			throw new Exception("New name for renaming is missing.");
		}

		$src = filter_var($src_path . '/' . $filename, FILTER_VALIDATE_URL);
		if (!$src) {
			throw new Exception("Invalid source URL.");
		}

		$dest = $_SERVER['DOCUMENT_ROOT'] . '/' . $dest_folder . '/' . $newname;

		if (!url_exists($src)) {
			throw new Exception("Source file does not exist: $src");
		}

		if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $dest_folder)) {
			throw new Exception("Destination folder does not exist: $dest_folder");
		}

		if (file_exists($dest)) {
			throw new Exception("Destination file already exists: $newname");
		}

		if (!copy($src, $dest)) {
			throw new Exception("Failed to copy file from $src to $dest");
		}

		echo "<p>Copy Complete: $newname</p>";
	}
} catch (Exception $e) {
	$error = $e->getMessage();
}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>Copy Remote File - There to Here</title>
</head>
<body>
	<h1>Copy File</h1>
	<?php if ($error): ?>
		<p style="color: red;"><?php echo $error; ?></p>
	<?php endif; ?>
	<form action="copyFile.php" method="post">
		<p><strong>Source Path:</strong><input type="text" size="50" name="src_path" value="<?php echo $src_path; ?>" /> /</p>
		<p><strong>Source File Name:</strong> <input type="text" size="45" name="filename" value="<?php echo $filename; ?>" /></p>
		<hr width="50%" size="2" align="left" />
		<p><strong>Destination Folder:</strong> <input type="text" size="50" name="dest_folder" value="<?php echo $dest_folder; ?>" /> /</p>
		<p><input type="checkbox" name="rename" <?php if ($rename) echo "checked"; ?> /> <strong>Rename at Destination to:</strong> <input type="text" size="40" name="newname" value="<?php echo $newname; ?>" /></p>
		<input type="submit" name="eeGo" value="Copy" />
	</form>
</body>
</html>

	
?>