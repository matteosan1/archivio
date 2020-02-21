<?php

//scegliere la form per csv or per zip

$fileName = $_FILES["file"]["tmp_name"];
   
if ($_FILES["file"]["size"] > 0) {
   $csv_file = file_get_contents($fileName)
   
   $ch = curl_init();

   curl 'http://localhost:8983/solr/my_collection/update?commit=true' --data-binary @example/exampledocs/books.csv '

   $last_upload = date( 'Y-m-d\T\0\0\:\0\0\:\0\0\Z', strtotime($_POST...));
   curl_setopt($ch, CURLOPT_URL, 'http://lx000000010550.sum.local:8983/solr/commodity/select?fq=last_modified:['.$last_upload.'%20TO%20NOW]&q=*:*&rows=10000&wt=csv');
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POST,           true);
   curl_setopt($ch, CURLOPT_POSTFIELDS,     $csv_file); 
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/csv'));

   $result=curl_exec ($ch);
   curl_close($ch);
}


$fileName = $_FILES["file"]["tmp_name"];
   
if ($_FILES["file"]["size"] > 0) {
   $zip = new ZipArchive;
   $res = $zip->open($fileName);
   if ($res === TRUE) {
      $zip->extractTo('/myzips/extract_path/');
   $zip->close();
   echo 'woot!';
} else {
   echo 'doh!';
}
?>

<!DOCTYPE html>
<html>
<script type="text/javascript">
	$(document).ready(
	function() {
		$("#frmCSVImport").on(
		"submit",
		function() {

			$("#response").attr("class", "");
			$("#response").html("");
			var fileType = ".csv";
			var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+("
					+ fileType + ")$");
			if (!regex.test($("#file").val().toLowerCase())) {
				$("#response").addClass("error");
				$("#response").addClass("display-block");
				$("#response").html(
						"Invalid File. Upload : <b>" + fileType
								+ "</b> Files.");
				return false;
			}
			return true;
		});
	});
</script>

<body>

<h2>Date Field</h2>
<form class="form-horizontal" action="" method="post" name="uploadCSV"
    enctype="multipart/form-data">
    <div class="input-row">
        <label class="col-md-4 control-label">Choose CSV File</label> <input
            type="file" name="file" id="file" accept=".csv">
        <button type="submit" id="submit" name="import"
            class="btn-submit">Import</button>
	<label class="col-md-4 control-label">Choose CSV File</label> <input
            type="file" name="file" id="file" accept=".csv">
        <button type="submit" id="submit" name="import"
            class="btn-submit">Import</button>
        <br />

    </div>
    <div id="labelError"></div>
</form>
<div id="link"></div>
</body>
</html>

