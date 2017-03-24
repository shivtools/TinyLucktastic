<html>

  <head>
    <link href="./public/css/dropzone.css" type="text/css" rel="stylesheet" />
    <link href="./public/css/main.css" type="text/css" rel="stylesheet" />
    <link href="./public/css/pace.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css">
    <script src="./public/js/dropzone.js"></script>
    <script src="./public/js/pace.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.0.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>
    <script src="./public/js/public.js"></script>

  </head>

  <body>
      <div class="navbar-fixed green accent-4">
        <nav class = "green accent-4">
          <div class="nav-wrapper">
            <a href="#!" class="brand-logo center">TinyLucktastic</a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
              <li><a class='newBatch btn tooltipped' href="#" data-position="bottom" data-delay="50" data-tooltip="Use this option to compress a fresh batch of images.">New batch</a></li>
           </ul>
          </div>
        </nav>
     </div>

     <br>
     <blockquote>
      <br>
      <h3 class="flow-text center">Welcome to s3-imagur, an incredibly simple and lightweight image compression tool.</h3>
      <br>
    </blockquote>

    <div class="container">
      <form action='upload.php' id="uploader" class="col 12 dropzone"></form>
    </div>

    <div class="container imageURLs">
      <div class = "card-panel">
        <ul class="col 12 collection urlList">
        </ul>
     </div>
    </div>

    <br>
    <br>

    <div class = "container">
      <h3 class="flow-text"> Select an aspect to downsize image to! </h3>
      <br>
      <div class = "row">
        <p class = "col s3">
          <input class='checkbox' type="checkbox" id="0.25x" name="0.25" />
          <label for="0.25x">0.25x</label>
        </p>

        <p class = "col s3">
          <input class='checkbox' type="checkbox" id="0.5x" name="0.5"/>
          <label for="0.5x">0.5x</label>
        </p>

        <p class = "col s3">
          <input class='checkbox' type="checkbox" id="2x" name="2"/>
          <label for="2x">2x</label>
        </p>

        <a class="customButton col s1 waves-effect green accent-4 btn tooltipped" href='#' data-position="top" data-delay="50" data-tooltip="Use this option to resize to a custom dimension">Custom</a>

        <div class = "customDiv" style="display: none;">
          <div class="input-field col s1">
            <input placeholder="% (0-100)" id="percentage" name="percentage" type="text" class="validate">
            <label for="percentage">Percentage</label>
          </div>
        </div>


      </div>

      <div class = "row center">
        <br>
        <a class="resize waves-effect green accent-4 btn tooltipped" href='#' data-position="top" data-delay="50" data-tooltip="Let's resize this!">Go!</a>
      </div>

    </div>

  </body>

  <footer class="page-footer green accent-4">
       <div class="footer-copyright center">
           Made by Shiv
       </div>
    </footer>

</html>
