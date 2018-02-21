<?PHP
/*--------------------------------------------

	zip_path		= path to where your zip is stored
	db_path			= path to where your sql db is stored
	download_secret	= 32 character key, can be anything you like but must be 32 characters long
	
	to download the zip file call like this
	dbup.php?f=zip&key=YOUR_KEY
	
	to download the sql file call like this
	dbup.php?f=sql&key=YOUR_KEY

---------------------------------------------*/

$c = array(
	'zip_path' 			=> '',
	'db_path' 			=> '',
	'download_secret'	=> '6304a0cb52eaf0bb57cfc6b2d67c5fef'
	);
	
if (!isset($_GET['f'], $_GET['key'])) {
	die();
}
	
$secret = $_GET['key'];
if (strlen($secret) <> 32 and $secret != $c['download_secret']) {
	echo "Invalid download key!";
	die();
}

$download_name = date("d_M_Y_-_H_i_s");

$f = $_GET['f'];
if ($f == "zip") {
	$file = $c['zip_path'] . "zipp.zip";
	$mime = "application/zip";
	$output_name = $download_name . "-website.zip";
} elseif ($f == "sql") {
	$file = $c['db_path'] . "dbb.sql";
	$mime = "text/plain";
	$output_name = $download_name . "-db.sql";
}
else
	die();
	
function sendHeaders($file, $type, $name=NULL)
{
    if (empty($name))
        $name = basename($file);
	
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename="'.$name.'";');
    header('Content-Type: ' . $type);
    header('Content-Length: ' . filesize($file));
}

if (is_file($file))
{
	sendHeaders($file, $mime, $output_name);
    $chunkSize = 1024 * 1024;
    $handle = fopen($file, 'rb');
    while (!feof($handle))
    {
        $buffer = fread($handle, $chunkSize);
        echo $buffer;
        ob_flush();
        flush();
    }
    fclose($handle);
    exit;
}
?>