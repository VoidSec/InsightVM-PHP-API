<html>
	<head>
		<title>
			InsightVM Report Parser
		</title>
	</head>
	<body>
		<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
			<label for="report">InsightVM (XML) Report</label>
			<input type="file" name="report" id="report" accept=".xml,text/xml">
			<p>Must be generated as "Nexpose Simple XML Export"</p>
			<p>XML only, smaller than 100MB </p>
			<label for="separator">Separator</label>
			<input type="text" name="separator" id="separator" value=";" maxlength="1">
			<br><br>
			<input type="submit" value="Generate CSV">
		</form>
		<?php
		//SQL Query Export based by vuln can do pretty much the same thing
		//CONFIG
			set_time_limit(0);
			ini_set('post_max_size', '150M');
			ini_set('upload_max_filesize', '100M');
			$time_start = microtime(true);
			$data = @strtotime("now");
			$uniq_id = md5(uniqid(rand(), true));
			$DEBUG = false;
			$VERBOSE= false;
			$BACKUP = false;
		//////////////////////////////////////////////
			if(isset($_FILES["report"]["name"])){				
				$InputFile = basename($data."-".$uniq_id.$_FILES["report"]["name"]);
				$FileType = strtolower(pathinfo($InputFile,PATHINFO_EXTENSION));
				if (file_exists($InputFile)) {
					die ("Error: file already exists");
				}
				if ($_FILES["report"]["size"] > 104857600) {// Check file size (Bigger than 100MB)
					die ("Error: your file is too large");
				}
				if($FileType != "xml") {
					die ("Error: only XML files are allowed");    
				}
				if (!move_uploaded_file($_FILES["report"]["tmp_name"], $InputFile)) {
					die ("Sorry, there was an error uploading your file");
				}

				if(isset($_POST["separator"])&&$_POST["separator"]!=null){
					$separator=$_POST["separator"][0];
				}else{
					$separator=";";
				}
				echo("<h3>==========> Parsing InsightVM Report</h3>");
				echo("=> Loading XML report<br>");
				$xml = simplexml_load_file($InputFile);
				echo("==> Converting to JSON<br>");
				$json = json_encode($xml);
				if($BACKUP===true){
					echo("===> Creating Backup file<br>");
					$BackupFile = fopen("backup-$data.json","w");
					fwrite($BackupFile,$json);
					fclose($BackupFile);
				}				
				unlink($InputFile);				
				echo("====> Converting JSON to PHP data structure<br>");
				$phpvar = json_decode($json, true);
				if($DEBUG===true){
					echo("++++++> ENTERING DEBUG MODE (a lot of 'garbage' will be printed)<br><br>");
					$PHPFileVar = fopen("[DEBUG]-$data.txt","w");
					fwrite($PHPFileVar,print_r($phpvar,true));
					if($VERBOSE===true){
						var_dump($phpvar);
					}
					fclose($PHPFileVar);
					echo("<br><br>++++++> END DEBUG MODE <++++++<br><br>");
				}
				$hosts=sizeof($phpvar["devices"]["device"]);
				echo("=====> Calculating Hosts: <b>$hosts</b><br>");
				echo("======> Generating CSV File <br><br>");
				
				$CSVFileExport = fopen($data."-".$uniq_id.".csv", "w") or die("Error: Unable to write the exported file");
				fwrite($CSVFileExport,'"IP";"OS";"Vulnerability";"Status Code";'."\n");
				for($i=0;$i<$hosts;$i++){
					$VulnsxIP=sizeof($phpvar["devices"]["device"][$i]["vulnerabilities"]["vulnerability"]);
					$j=0;
					while($j<$VulnsxIP){
						fwrite($CSVFileExport,'"'.$phpvar["devices"]["device"][$i]["@attributes"]["address"].'"'.$separator);
						fwrite($CSVFileExport,'"'.$phpvar["devices"]["device"][$i]["fingerprint"]["description"].'"'.$separator);
							if(array_key_exists($j, $phpvar["devices"]["device"][$i]["vulnerabilities"]["vulnerability"])){
								fwrite($CSVFileExport,'"'.$phpvar["devices"]["device"][$i]["vulnerabilities"]["vulnerability"][$j]["@attributes"]["id"].'"'.$separator);
								fwrite($CSVFileExport,'"'.$phpvar["devices"]["device"][$i]["vulnerabilities"]["vulnerability"][$j]["@attributes"]["resultCode"].'"'.$separator."\n");								
							}
							else{
								fwrite($CSVFileExport,'"'.$phpvar["devices"]["device"][$i]["vulnerabilities"]["vulnerability"]["@attributes"]["id"].'"'.$separator);
								fwrite($CSVFileExport,'"'.$phpvar["devices"]["device"][$i]["vulnerabilities"]["vulnerability"]["@attributes"]["resultCode"].'"'.$separator."\n");
							}
						$j++;
					}
				}
				fclose($CSVFileExport);
				$time_end = microtime(true);
				$execution_time = ($time_end - $time_start);
				echo("<h2><a href='".$data."-".$uniq_id.".csv'>Download CSV Report</a></h2>");
				die("<h3>==========> STATUS [OK] ($execution_time s)</h3>");
			}else{
				die("Error: missing report file");
			}
		?>
	</body>
</html>