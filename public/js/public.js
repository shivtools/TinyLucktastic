$(document).ready(function () {

    var $custom = $('.customButton'); //If user decides to input custom specs
    var $customDiv = $('.customDiv');
    var $resize = $('.resize');
    var $newTab = $('.newTab');

    //When a user clicks on the custom button, hide the button and show input fields
    $custom.click(function () {
        $custom.hide();
        $customDiv.fadeIn(300);
    });

    //If user wants to process a new batch, clear sessionStorage
    $newTab.click(function () {
        sessionStorage.clear();
        alert('You are good to go to process another batch of images :D!');
    });

    //What to do when resize ('Go!') button is clicked
    $resize.click(function () {
        if (checkSessionStorage()) {
            var checkboxes = $('.checkbox');

            //If user has provided a custom percentage to rescale to, parse it as an integer
            var percentage = parseInt($('#percentage')[0].value);

            //Check if input is null. If input provided, then divide by 100, round to 4 dp
            var scaledPercentage = (percentage.length == 0) ? null : (percentage / 100).toFixed(4);

            //Pass the value of percentages as well as the checkboxes to getResizedImages
            getResizedImages(checkboxes, scaledPercentage);
        } else {
            alert('Sorry, but you cannot resize using this browser. Please try using the latest version of Chrome!');
            return;
        }
    });

});

/*
    Populates window with download URLs and information for resized images

    @param {Object} HTML checkboxes
    @param {Float} Percentage value provided by user in custom field
*/
var getResizedImages = function (checkboxes, percentage) {

    var sizes = [];

    //If the user has provided a custom percentage, push it into array
    if (percentage) {
        sizes.push(parseFloat(percentage));
    }

    var atLeastOneChecked = false;

    //Push image sizes for resizing to array sizes
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            sizes.push(parseFloat(checkboxes[i].name));
            atLeastOneChecked = true;
        }
    }

    //Provide checks to see if user has checked at least one box or provided a percentage value
    if (!atLeastOneChecked && percentage == null) {
        alert('You must provide at least one of the resizing options! sigh...');
        return;
    } else if (percentage && percentage <= 0) {
        alert('You must be joking.. please provide a proper percentage value... smh...');
        return;
    }
    //Check if there are no image objects present in local storage. If not, prompt user
    else if (sessionStorage.length == 0) {
        alert('Whaaaaa... No images to resize... sigh....');
        return;
    }
    //If all checks have passed, then send AJAX call to PHP script to resize image
    else {
        //Get session stored images and parse it into JSON object
        var json_objects = JSON.parse(sessionStorage.images);

        //Call resize to make AJAX call to PHP
        resize(json_objects, sizes);
    }
}

/*
    Function to make AJAX call to resize.php, and populate urlList with URLs (linking to S3) to download images

    @param {Object}  Array containing JSON objects containing information about each image
    @param {Object}  Array containing integers containing sizes to resize images to
*/
var resize = function (images, sizes) {

    //If no images || no sizes are provided, complain and exit.
    if (images.length == 0) {
        alert('Please provide an image to resize and try again!');
        return;
    } else {

        var $urlList = $('.urlList'); //HTML unordered list that contains URLs

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

                        saveToSessionStorage(image); //Save image to session storage

                        //Check if image has no error from backend (Kraken processing)
                        if (!image.error) {
                            var li = generateListElement(image, $urlList); //Generates list element to be appended to urlList
                            //Append the li element containing the download link to the list (ul)
                            $urlList.append(li);
                        } else {
                            alert('There was an error processing one of your images: ', image.error);
                        }

                    });
                },
                error: function (err) {
                    alert('There was a problem in converting your images. Please close the current tab and try again! \n Error: ', err.responseText);
                }
            });
        });
    }

}

/*
    Returns HTML list element after extracting information from image object

    @param {Object} JSON object containing image and its properties
*/
var generateListElement = function (image) {

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

    return li;
}

/*
    Stores resized image to browser's session storage. Resized images from session storage will be used to zip and download all images

    @param {Object} JSON image object
*/
var saveToSessionStorage = function (image) {

    var resizedImages;

    //Store object in browser's session storage
    if (!sessionStorage.resized) {
        //If there are no images currently stored in sessionStorage, then create an array and add first JSON object to it
        resizedImages = [];
        resizedImages.push(image);
    } else {
        //If there are currently images, get string, parse it to JSON, add another JSON object and encode it again as a string.
        resizedImages = JSON.parse(sessionStorage.resized);
        resizedImages.push(image);
    }
    sessionStorage.resized = JSON.stringify(resizedImages);
}


/*
    Check if user's browser supports session storage
*/
var checkSessionStorage = function () {
    if (typeof (Storage) !== "undefined") {
        return true;
    } else {
        alert('You will not be able to resize images using this browser. Please try using the tool with the latest version of Chrome!');
        return false;
    }
}
