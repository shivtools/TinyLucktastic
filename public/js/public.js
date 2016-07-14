$(document).ready(function () {


    var hasSessionStorage = checkSessionStorage(); //Check if user's browser supports session storage
    var hasCustomDimensions = false; //If user clicks on custom dimensions, then set this variable to true

    var $custom = $('.customButton'); //If user decides to input custom specs
    var $customDiv = $('.customDiv');
    var $resize = $('.resize');

    //When a user clicks on the custom button, hide the button and show input fields
    $custom.click(function () {
        hasCustomDimensions = true;
        $custom.hide();
        $customDiv.fadeIn(300);
    });

    //What to do when resize ('Go!') button is clicked
    $resize.click(function () {
        if (hasSessionStorage) {
            var checkboxes = $('.checkbox');

            //If user has provided a custom percentage to rescale to
            var percentage = parseInt($('#percentage')[0].value);

            //Check if input is null. If input provided, then divide by 100, round to 4 dp
            var scaledPercentage = (percentage.length == 0) ? null : (percentage / 100).toFixed(4);

            //Pass the value of percentages as well as the checkboxes to getResizedImages
            getResizedImages(checkboxes, scaledPercentage);
        } else {
            alert('Sorry, but you cannot resize using this browser. Please try using the latest version of Chrome!')
        }
    });




});

//Given the switches (array of HTML divs, populate window with download URLs for resized images)
var getResizedImages = function (checkboxes, percentage) {

    var sizes = [];

    //If the user has provided a custom percentage, push it into array
    if (percentage) {
        sizes.push(parseFloat(percentage));
    }

    //Push image sizes for resizing to array sizes
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            sizes.push(parseFloat(checkboxes[i].name));
        }
    }

    //Get session stored images and parse it into JSON object
    var json_objects = JSON.parse(sessionStorage.images);

    //Call resize to make AJAX call to PHP
    resize(json_objects, sizes);
}

/*
  Function to make AJAX call to resize.php, and populate urlList with URLs (linking to S3) to download images
  @param {Array}  JSON objects containing information about each image
  @param {Array}  Integers containing sizes to resize images to
*/
var resize = function (images, sizes) {

    //If no images || no sizes are provided, complain and exit.
    if (images.length == 0) {
        alert('Please provide an image to resize and try again!');
        return;
    } else if (sizes.length == 0) {
        alert('Please provide one or more sizes to work with!');
        return;
    } else {

        var $urlList = $('.urlList');

        //Make parallel AJAX calls to resize.php and populate URL box as soon as response is returned
        images.forEach(function (image) {
            $.ajax({
                url: 'resize.php',
                type: 'GET',
                data: {
                    'image': image,
                    'sizes': sizes
                },
                success: function (processed_images) {

                    // $urlList.empty(); //Empty the current list of URLs

                    processed_images.forEach(function (image) {

                        //Create a li element
                        var li = document.createElement('li');
                        li.setAttribute('class', 'collection-item');

                        //Create anchor with href = url
                        var a = document.createElement('a');
                        a.setAttribute('href', image.url);
                        a.setAttribute('download', image.name);
                        a.textContent = 'Download ' + image.name + '!';

                        var p = document.createElement('p');
                        p.textContent = 'We just saved you ' + image.saved_percentage + '% for your ' + image.height + 'x' + image.width + ' image :)!';

                        //Append the anchor element as a child to the list element --> <li> <a></a> </li>
                        li.appendChild(p);
                        li.appendChild(a);

                        //Append the li element containing the download link to the list (ul)
                        $urlList.append(li);
                    });
                },
                error: function (err) {
                    alert('There was a problem in converting your images. Please close the current tab and try again! \n Error: ', err.responseText);
                }
            });
        });
    }

}


//Check if user's browser supports session storage
var checkSessionStorage = function () {
    if (typeof (Storage) !== "undefined") {
        return true;
    } else {
        alert('You will not be able to resize images using this browser. Please try using the tool with the latest version of Chrome!');
        return false;
    }
}
