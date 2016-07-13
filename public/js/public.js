$(document).ready(function(){
  $(function() {
    Dropzone.options.myAwesomeDropzone = {
      paramName: "file", // The name that will be used to transfer the file
      maxFilesize: 2, // MB
      accept: function(file, done) {
        console.log(file);
        done();
      }
    };
  });
});
