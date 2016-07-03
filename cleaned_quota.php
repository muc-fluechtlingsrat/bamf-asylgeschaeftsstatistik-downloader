<?php

$date = new \DateTime();
for($m = 0; $m <= 4; $m++) {
  $fileName = $date->format('Y').$date->format('m');
    $cacheDir = "archiv/";
    $fileInputCSV = $fileName."_extracted.csv";
    $fileOutputCSV = $fileName."_cleaned.csv";
    $fileOutputCSVPath = $cacheDir.$fileOutputCSV;
    $fileInputCSVPath = $cacheDir.$fileInputCSV;
    // print_r($fileInputCSVPath); 
    if (file_exists($fileInputCSVPath)) {
      $fh_inp = fopen($fileInputCSVPath, 'r');
      $fh_out = fopen($fileOutputCSVPath, 'w+');
      $extracted = fopen("archiv/header_cleaned.csv", "r");
      while (($line = fgetcsv($extracted)) !== FALSE) {
        //$line is an array of the csv elements
        fputcsv($fh_out, $line);
      } 
      fclose($extracted);
      // calculate cleaned_total, and cleaned_quota 
      while($line = fgetcsv($fh_inp)) {
          $a = (($line[6]+$line[8]+$line[9])/($line[6]+$line[8]+$line[9]+$line[11]))*100;
          $a = number_format($a, 1,"," ,".");
          $a = strval($a)."%";
          array_push($line, $line[6]+$line[8]+$line[9], $line[6]+$line[8]+$line[9]+$line[11], $a);
          fputcsv($fh_out, $line);
        }
      fclose($fh_inp);
      fclose($fh_out);
    }
  $date->sub(new \DateInterval('P1M'));
  } 
?>
