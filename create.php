<?php
require_once 'image-gallery-class.php';


GalleryImage::$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

// instantiate
$myImage = new GalleryImage();

// $galleryData = $myImage->readDataFromJson('test');
// $myImage->printGalleryFromJson($galleryData);


// validate form submission
if ($myImage->validateInputForm() === true) {

  // include errorStack for the server response
  $myImage::$suppressErrorMessages = false;

  // echo '<pre style="background: white;">';
  // print_r($myImage->jsonSerialize());
  // echo '</pre>';

  // do not store error message to json
  $myImage::$suppressErrorMessages = true;
  $myImage->saveDataToJSON('test');

} else {
  echo json_encode(array(
    'successMessage' => '',
    'errorStackImage' => $myImage->errorStackImage,
    'errorStackTitle' => $myImage->errorStackTitle,
    'errorStackJson' => $myImage->errorStackJson
  ));
  // echo '<pre style="background: white;">';
  // print_r(!empty($myImage->errorStack) ? $myImage->errorStack : '');
  // echo '</pre>';
}
