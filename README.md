#bamf-asylgeschäftsstatistik-downloader
English version below

## Herunterladen der PDFs vom BAMF und Verarbeitung via ´Tabula´ zu CSV und mit Bereinigung und Berechnung der bereinigten Schutzquote

Die Funktion wird folgendermaßen aufgerufen:
 
    php manual.php
    
Dies verarbeitet die Daten der letzten 4 Monaten, und erzeugt kommaseparierte Dateien (CSV) im ´archiv´-Verzeichnis.
    * YYYYMM.pdf ist das Orginaldokument vom BAMF
    * YYYYMM.csv ist das CSV, das von ´Tabula´ erzeugt wird 
    * YYYYMM_extracted.csv ist eine bereinigte Version, ohne inhaltliche Veränderung oder Ergänzung

Anschließend kann man aufrufen

    php cleaned.php

Dieses Skript fügt die bereinigte Schutzquote zu jeder Zeile hinzu.

##Download of the PDFs of the BAMF (German Federal Office for Migration and Refugees) and porcessing to CSV

Call the script like this
 
    php manual.php
    
It will process the data of four months ago until the present month.

Your new csv files will be in the ´archiv´ folder. 
    * YYYYMM.pdf is the source document from BAMF
    * YYYYMM.csv is the csv that ´Tabula´ creates
    * YYYYMM_extracted.csv is a cleaned version, but no content changed or added.

Then run 

    php cleaned.php

This will add the cleaned quota to each country.


