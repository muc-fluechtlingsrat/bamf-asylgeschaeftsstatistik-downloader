<?php
$rootDir = __DIR__;

$tabulaJar = "tabula.jar";
$tabulaUrl = "https://github.com/tabulapdf/tabula-java/releases/download/tabula-0.9.0/tabula-0.9.0-SNAPSHOT-jar-with-dependencies.jar";
//Check if tabola is already downloaded
if (!file_exists($rootDir."/".$tabulaJar)) {
    $tabula = file_get_contents($tabulaUrl);
    file_put_contents($rootDir."/".$tabulaJar, $tabula);
}

$cacheDir = "archiv/";
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

$today = new \DateTime();

$url = "http://www.bamf.de/SharedDocs/Anlagen/DE/Downloads/Infothek/Statistik/Asyl/__YEAR____MONTH__-statistik-anlage-asyl-geschaeftsbericht.pdf?__blob=publicationFile";

$date = new \DateTime();
for($m = 0; $m <= 4; $m++) {
    $new_url = $url;
    $new_url = str_replace(array("__YEAR__", "__MONTH__"), array($date->format('Y'), $date->format('m')), $url);

    if ($fileData = @file_get_contents($new_url)) {
        $fileName = $date->format('Y').$date->format('m');
        $filePDF = $fileName.".pdf";
        $fileCSV = $fileName.".csv";
        $fileOutputCSV = $fileName."_extracted.csv";
        $filePDFPath = $cacheDir.$filePDF;
        $fileCSVPath = $cacheDir.$fileCSV;
        $fileOutputCSVPath = $cacheDir.$fileOutputCSV;

        $fileChanged = false;
        if (file_exists($filePDFPath)) {
            if (md5($fileData) != md5_file($filePDFPath)) {
                file_put_contents($filePDFPath, $fileData);
                $fileChanged = true;
            }
        } else {
            file_put_contents($filePDFPath, $fileData);
            $fileChanged = true;
        }
        $fileChanged = true;
        if ($fileChanged == true) {
            //File has changed regenerate files
            //Generate csv from java file
            exec("java -jar ./".$tabulaJar." -f CSV -o ".$fileCSVPath." -p 2 -n ".$filePDFPath);
            $fh = fopen($fileCSVPath, 'r');
            $fh_csv = fopen($fileOutputCSVPath, 'w+');
            while($line = fgetcsv($fh)) {
		// sanitize, some line end with blank then quotes
                $lineParts = explode(" ", $line[0]);

                if (is_numeric($lineParts[0]) && number_format($lineParts[0], 0, 0, "") < 30 || $lineParts[0] === "Summe" || strpos($lineParts[0], "Herkunft") === 0) {
                    $newLine = array();
                    $colCount = 0;
                    $prevPart = null;

                    for($l = 0; $l < count($lineParts); $l++) {

                        if ($prevPart != null) {
                            if ($prevPart === "Top" && $lineParts[$l] === "10") {
            	                $lineParts[$l] = "Ten";
              	            }
                            if (preg_match('/^[0-9]{1,3}.[0-9]{3}/', $lineParts[$l]) === 1) {
                                $lineParts[$l] = str_replace(".", "", $lineParts[$l]);
                            }
                            if (preg_match('/^[0-9\-\.%]*$/', $prevPart) == false) {
                                if (preg_match('/^[0-9\-\.%]*$/', $lineParts[$l]) == false) {
                                    $newLine[$colCount] = $newLine[$colCount]." ".$lineParts[$l];
                                    $prevPart = $lineParts[$l];
                                } else {
                                    $newLine[++$colCount] = $lineParts[$l];
                                    $prevPart = $lineParts[$l];
                                }
                            } else {
                                $newLine[++$colCount] = $lineParts[$l];
                                $prevPart = $lineParts[$l];
                            }
                        } else {
                            $newLine[$colCount] = $lineParts[$l];
                            $prevPart = $lineParts[$l];
                        }

                    }
                    if (strpos($newLine[0],"Summe") === 0 || strpos($newLine[0],"Herkunft") === 0) {
		        array_unshift($newLine, "x");
		    }
		    // sanitize: Sometimes extra comma after last field
		    $newLine = array_slice($newLine,0,13);
                    fputcsv($fh_csv, $newLine);
                }
            }
            fclose($fh);
            fclose($fh_csv);

        }
    }

    $date->sub(new \DateInterval('P1M'));
}





