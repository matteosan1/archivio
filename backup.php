<?php

/**
* @link http://gist.github.com/385876
*/
function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}



//$ch = curl_init();

//curl_setopt($ch, CURLOPT_URL, 'http://lx000000010550.sum.local:8983/solr/admin/cores?action=status&core=mpscs_docs&indexInfo=true');

//curl_setopt($ch, CURLOPT_URL, 'http://lx000000010550.sum.local:8983/solr/commodity/select?fl=last_modified&q=*:*&rows=1&sort=last_modified%20desc');
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//$data = curl_exec($ch);
//$dict = json_decode($data, true);

//curl_close($ch);

$ch = curl_init();
$last_upload = date( 'Y-m-d\T\0\0\:\0\0\:\0\0\Z', strtotime($_POST...));
curl_setopt($ch, CURLOPT_URL, 'http://lx000000010550.sum.local:8983/solr/commodity/select?fq=last_modified:['.$last_upload.'%20TO%20NOW]&q=*:*&rows=10000&wt=csv');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
$data = curl_exec($ch);
curl_close($ch);

$csv_filename = 'backup_".$last_upload.".csv";
file_put_contents($csv_filename, $data);

$entries = csv_to_array($filename='', $delimiter=',');

$zip = new ZipArchive();
$zip_filename = "copertine_".$last_upload.".zip";

if ($zip->open($zip_filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$zip_filename>\n");
}

foreach($entry in $entries) {
    $zip->addFile($entry);
}

$zip->close();

echo '<a href="'.$csv_filename.'">Catalogo  CSV</a><br>";
echo '<a href="'.$zip_filename.'">Copertine ZIP</a><br>";
?>

<!DOCTYPE html>
<html>
<body>

# AGGIUNGERE JQUERY PER INVIO DATA UPLOAD
# AGGIUNGERE JQUERY PER AGGIUNTA LINK BACKUP e COPERTINE

<h2>Date Field</h2>

<p>The <strong>input type="date"</strong> is used for input fields that should contain a date.</p>

<form action="/action_page.php">
  <label for="birthday">Data di backup:</label>
  <input type="date" id="last_upload" name="last_upload">
  <input type="submit" value="Backup">
</form>
<div id="link"></div>
</body>
</html>

