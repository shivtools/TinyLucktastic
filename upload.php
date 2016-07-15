<?php

  $configs = include(getcwd() . '/configs/config.php');
  header('Content-Type: application/json');

  if(!empty($_FILES)){

      $image_path = $_FILES['file']['tmp_name'];
      $image_name = $_FILES['file']['name'];

      $response = queryKraken($image_path, $image_name);
      echo($response);
  }

  /*
    @param {String} Path to image
    @return {String} URL to Kraked (compressed) image if compressed, else NULL
  */
  function queryKraken($path, $image_name){

    global $configs;
    require_once(getcwd(). '/kraken-php/lib/Kraken.php');
    $today = date("m.d.y");

    $kraken = new Kraken($configs['KRAKEN_KEY'], $configs['KRAKEN_SECRET']);

    $image_name = convertToPNG($image_name); //Convert current image extension to '.png'

    //Compress image and push to Amazon S3 bucket
    $params = array(
      "file" => $path,
      "wait" => true,
      "lossy" => true,
      "convert" => array(
        "format" => "png"
      ),
      "s3_store" => array(
        "key" => $configs['S3_KEY'],
        "secret" => $configs['S3_SECRET'],
        "bucket" => $configs['S3_BUCKET'],
        "path" => "compressed/" . $today . "/" . $image_name,
        "region" => $configs['S3_REGION'],
        "headers" => array(
            "Cache-Control" => "max-age=2592000000",
            "Expires" => 1700000000
        )
      )
    );

    $data = $kraken->upload($params);
    $arr = returnArr($data, $image_name);

    return json_encode($arr);

  }

  /*
    @param {JSON} JSON response from Kraken
    @return {Array} Return array containing appropriate JSON response to send to front end
  */
  function returnArr($data, $image_name){
    if($data['success']){

      $saved_percentage = round(($data['saved_bytes']/$data['original_size'])*100,2);

      $arr = array(
        'name' => $image_name,
        'url' => $data['kraked_url'],
        'saved_percentage' => $saved_percentage,
        'width' => $data['original_width'],
        'height' => $data['original_height']
      );
    }
    else{
      $arr = array(
        'error' => $data['message']
      );
    }

    return $arr;
  }

  /*
      Converts a filename with any extension to one with a .png extension
      @params {String} Filename with any extension
  */
  function convertToPNG($file_name){

    $temp = explode('.', $file_name);     //Separate string into array, delimited by '.'
    $extension = $temp[count($temp)-1];  //Get the extension i.e last element in exploded array

    //If extension is already png, return the filename as it is.
    if($extension == "png"){
      return $file_name;
    }

    //Otherwise remove the current extension and add a '.png' extension
    $removed_extension = array_pop($temp);
    $file_with_png_extension = implode(".", $temp) . ".png";

    return $file_with_png_extension;
  }
?>
