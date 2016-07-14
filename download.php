<?php

  if(isset($_GET['images'])){
    $images = json_decode($_GET['images']);
    downloadImages($images);
  }

  function downloadImages($images){

    $files = array(); /* Image array */

    //Iterate through $images and get URLs (download links)
    foreach ($images as $image) {
      $files[] = $image->url;
    }

    $tmpFile = tempnam('/tmp', '');
    $zip = new ZipArchive;
    $zip->open($tmpFile, ZipArchive::CREATE);

    foreach ($files as $file) {
        $fileContent = file_get_contents($file); // download file
        $zip->addFromString(basename($file), $fileContent);
    }
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=compressed.zip');
    header('Content-Length: ' . filesize($tmpFile));
    readfile($tmpFile);

    unlink($tmpFile);
  }
?>
