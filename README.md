# TinyLucktastic

#### What is TinyLucktastic?
TinyLucktastic is a tool to drag and drop, upload, compress, resize and push **multiple** images to a specified Amazon S3. Images are compressed using Kraken's awesome image compression API.

![alt text](https://raw.githubusercontent.com/shivtools/TinyLucktastic/master/public/markdown/tiny.png "Sneak peek!")

#### Get started

* In order to get started, include a **configs** folder in the root directory,
along with a **config.php** file.

* Your **config.json** file should look like this:

```php

//Stores config variables such as API keys and secrets

return array(
    'KRAKEN_KEY' => '',
    'KRAKEN_SECRET' => '',
    'S3_KEY' => '',
    'S3_SECRET' => '',
    'S3_REGION' => '',
    'S3_BUCKET' => ''
);

?>

```

Example of S3-region would be: us-west-2.

* You **must** already have a bucket ready in S3 and paste its filename into the input filed that asks for your S3 bucket name.

Feel free to get in touch with me regarding any questions or to add more functionality to this tool!
