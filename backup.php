<?PHP
/*--------------------------------------------

	uname 		= mysql username
	pwd			= mysql password
	db			= mysql database or leave blank if alldb is set to true
	alldb		= if you wish to backup all databases set to true
	save_to		= path to save your backups to (must have ending /)
	web_folder	= path your web directory (must have ending /)

---------------------------------------------*/

$c = array(
	'uname' 		=> '',
	'pwd' 			=> '',
	'db' 			=> '',
	'alldb'			=> false,
	'save_to' 		=> '',
	'web_folder'	=> ''
	);

function zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source))
        return false;

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE))
        return false;

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = str_replace('\\', '/', $file);

            if(in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            else if (is_file($file) === true)
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
        }
    }
    else if (is_file($source) === true)
        $zip->addFromString(basename($source), file_get_contents($source));

    return $zip->close();
}
	
$cmd = "mysqldump -u " . $c['uname'] . " -p'" . $c['pwd'] . "' " . ($c['alldb'] ? "--all-databases" : $c['db']) . " > " . $c['save_to'] . "dbb.sql";

$arr_out = array();
unset($return);

exec($cmd, $arr_out, $return);

if($return !== 0) {
    echo "mysqldump for " . ($c['alldb'] ? "all databases" : $c['db']) . " failed with a return code of " . $return . "\n";
	echo "Database was no backed up\n";
} else
	echo "Database backed up successfully\n\n\n";

if (zip($c['web_folder'], $c['save_to'] . 'zipp.zip') == false)
	echo "Backup of the website was not successful (PHP ZIP extension not detected)\n";
else
	echo $c['web_folder'] . " Successfully archive\n";
?>