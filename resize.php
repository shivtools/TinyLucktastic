<?php

  $configs = include(getcwd() . '/configs/config.php');
  header('Content-Type: application/json');

  //If the image and size(s) have been provided, make a call to query Kraken's API
  if(isset($_GET['image']) && isset($_GET['sizes'])){
      $response = queryKraken($_GET['image'], $_GET['sizes']);
      echo($response);
  }

  /*
    @param {JSON} Image containing properties of image such as name, S3 URL, width, height, percentage saved
    @param {Array} Array of sizes as floats
    @return {Array} Array containing resized images from Kraken.io
  */
  function queryKraken($image, $sizes){

    require_once(getcwd(). '/kraken-php/lib/Kraken.php');
    global $configs; //array that stores api keys and secrets
    $today = date("m.d.y");

    //Initialize $kraken object using Kraken's SDK
    $kraken = new Kraken($configs['KRAKEN_KEY'], $configs['KRAKEN_SECRET']);

    //Array to store JSON responses from Kraken's API
    $kraken_response = array();

    //Iterate through each size and make call to Kraken to resize
    foreach ($sizes as $size) {

      //Check if size != NULL i.e is valid
      if(floatval($size)){

          //Dimensions array to store new dimensions to resize to
          $dimensions = array(
            'width' => $image['width'] * floatval($size),
            'height' => $image['height'] * floatval($size)
          );

          //Parameters to resize to and to push to Amazon S3. Sent with call to Kraken.io API
          $params = array(
            "url" => $image['url'],
            "wait" => true,
            "lossy" => true,
            'resize' => array(
              "width" => $dimensions['width'],
              "height" => $dimensions['height'],
              "strategy" => "exact"
            ),
            "convert" => array(
              "format" => "png"
            ),
            "s3_store" => array(
              "key" => $configs['S3_KEY'],
              "secret" => $configs['S3_SECRET'],
              "bucket" => $configs['S3_BUCKET'],
              "path" => "resized/" . $today . "/" . $size . "/" . $image['name'],
              "region" => $configs['S3_REGION'],
              "headers" => array(
                  "Cache-Control" => "max-age=2592000000",
                  "Expires" => 1700000000
              )
            )
          );

          $data = $kraken->url($params); //Make call to Kraken.io's API
          $data['resize_scale'] = $size; //Store the resizing scale, so it can be stored in session storage after AJAX call receives response
          $kraken_response[] = $data;    //Append response to $kraken_response array
        }
    }

    $arr = returnArr($kraken_response); //Pass associative array containing resized image data to returnArr
    return json_encode($arr); //Parse array (to JSON) containing information about one or more resized images and pass back to AJAX call
  }

  /*
    @param {JSON} JSON response from Kraken
    @return {Array} Return array containing appropriate JSON response to send to front end
  */
  function returnArr($kraken_images){

    $json_images = array();

    //Iterate through each response (for each size) from Kraken's API and extract information for each image
    foreach ($kraken_images as $data) {

      if($data['success']){

        $saved_percentage = round(($data['saved_bytes']/$data['original_size'])*100,2);

        $arr = array(
          'name' => $data['file_name'],
          'url' => $data['kraked_url'],
          'saved_percentage' => $saved_percentage,
          'width' => $data['kraked_width'],
          'height' => $data['kraked_height'],
          'resize_scale' => $data['resize_scale']
        );
      }
      else{
        $arr = array(
          'error' => $data['message']
        );
      }

      //Append array to $json_images
      $json_images[] = $arr;
    }

    return $json_images;
  }
?>
