<?php

$cacheDir = "archiv/";
$fileName="201604";

$fileInputCSV = $fileName."_extracted.csv";
$fileOutputCSV = $fileName."_cleaned.csv";
$fileOutputCSVPath = $cacheDir.$fileOutputCSV;
$fileInputCSVPath = $cacheDir.$fileInputCSV;

$fh_inp = fopen($fileInputCSVPath, 'r');
$fh_out = fopen($fileOutputCSVPath, 'w+');
// calculate cleaned_total, and cleaned_quota 
while($line = fgetcsv($fh_inp)) {
    $a = (($line[6]+$line[8]+$line[9])/($line[6]+$line[8]+$line[9]+$line[11]))*100;
    $a = number_format($a, 1,"," ,".");
    array_push($line, $line[6]+$line[8]+$line[9], $line[6]+$line[8]+$line[9]+$line[11], $a);
    fputcsv($fh_out, $line);
  }
fclose($fh_inp);
?>
