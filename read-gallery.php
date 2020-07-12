<?php

require_once 'image-gallery-class.php';

// instantiate
$myImage = new GalleryImage();

$galleryData = $myImage->readDataFromJson('test');


if (!empty($galleryData)) {
  $myImage->generateGalleryFromJson($galleryData);
} else {
  $html = '<span>Még nincs kép a galériában. Használd az űrlapot a feltöltéshez.</span>';
  echo json_encode(array(
    'myGallery' => $html,
    'galleryItemCount' => null,
    'errorStackJson' => $this->errorStackJson
  ));
}

?>