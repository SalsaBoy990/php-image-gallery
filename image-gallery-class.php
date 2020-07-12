<?php

class GalleryImage implements JsonSerializable
{

  // to store the image path of uploaded image
  private $imagePath;

  // to store image title (used in image labels, and in alt attributes)
  private $imageTitle;

  // to store image upload date time in seconds from 1970-01-01
  private $timestamp;


  public static $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

  // to store error stack
  public $errorStackImage;
  public $errorStackTitle;
  public $errorStackJson;

  public static $suppressErrorMessages;



  // getters
  public function getImagePath()
  {
    return $this->imagePath;
  }
  public function getImageTitle()
  {
    return $this->imageTitle;
  }

  public function getTimestamp()
  {
    return $this->timestamp;
  }

  // setters
  public function setImagePath($path)
  {
    $this->imagePath = $path;
  }
  public function setImageTitle($title)
  {
    $this->imageTitle = $title;
  }
  public function setTimestamp()
  {
    // current datetime
    $date = new DateTime();
    // convert to seconds, store it
    $this->timestamp = $date->getTimestamp();
  }

  // constructor
  function __construct()
  {
    $this->imagePath = "";
    $this->imageTitle = "";
    $this->errorStackImage = array();
    $this->errorStackTitle = array();
    $this->errorStackJson = array();
  }
  // desctructor
  function __destruct()
  {
  }

  /**
   *  The json_encode function will not show non-public properties.
   *  A Jsonserializable interface was added in PHP 5.4 which allows you to accomplish this.
   *  @see https://www.codebyamir.com/blog/object-to-json-in-php
   * @param bool $suppressErrorMessages true if you want to export the errorstack to json, false otherwise
   */
  public function jsonSerialize()
  {
    if (self::$suppressErrorMessages === false) {
      return [
        'guid' => $this->createGuid(),
        'imagePath'   => $this->getImagePath(),
        'imageTitle' => $this->getImageTitle(),
        'errorStackImage' => $this->errorStackImage,
        'errorStackTitle' => $this->errorStackTitle,
        'errorStackJson' => $this->errorStackJson,
      ];
    } else {
      return [
        'guid' => $this->createGuid(),
        'imagePath'   => $this->getImagePath(),
        'imageTitle' => $this->getImageTitle(),
        'uploadTime' => $this->getTimestamp()
      ];
    }
  }

  public function saveDataToJSON($filename)
  {
    // add extension to filename
    $filename = $filename . '.json';

    // check if file is empty
    $emptyFile = file_get_contents($filename) ? false : true;
    // echo $emptyFile;

     // store current datetime
     $this->setTimestamp();

    // read file if empty
    if ($emptyFile) {
      try {
        if (($result = fopen($filename, 'w')) === false) {
          throw new Exception('Cannot read the file because it is currently not accessible.<br />');
        }
      } catch (Exception $e) {
        echo $e->getMessage();
      }

      // add first item to the file
      $json = "[";
      $json .= json_encode($this->jsonSerialize(true));
      $json .= "]";

      fwrite($result, $json);
      fclose($result);

      $successMsg = 'OK. Image data saved to <a href="' . $filename  . '">' . $filename . '</a><br />';
      echo json_encode(array(
        'successMessage' => $successMsg,
        'errorStackImage' => '',
        'errorStackTitle' => '',
        'errorStackJson' => ''
      ));
      //--------------------------- File closed
      // $this->testJson($json);
    } // append file if not empty
    else {
      try {
        if (($result = fopen($filename, 'r')) === false) {
          throw new Exception('Cannot read the file because the file is unaccessible.<br />');
        }
      } catch (Exception $e) {
        echo $e->getMessage();
      }


      $tmp = stream_get_contents($result);
      // echo $tmp;
      fclose($result);
      //--------------------------- File closed

      // remove ] from the end
      $tmp = substr($tmp, 0, (strlen($tmp) - 1));

      // add , after previous item
      $tmp .= ",";
      // append new item
      $tmp .= json_encode($this->jsonSerialize(true));
      // put ] back to the end
      $tmp .= "]";


      try {
        if (($result = fopen($filename, 'w')) === false) {
          throw new Exception('Error. Cannot write to the file.<br />');
        }
      } catch (Exception $e) {
        echo $e->getMessage();
      }

      fwrite($result, $tmp);
      fclose($result);
      // echo 'OK. Image data saved to <a href="' . $filename  . '">' . $filename . '</a><br />';
      $successMsg = 'OK. Image data saved to <a href="' . $filename  . '">' . $filename . '</a><br />';
      echo json_encode(array(
        'successMessage' => $successMsg,
        'errorStackImage' => '',
        'errorStackTitle' => '',
        'errorStackJson' => ''
      ));
      //--------------------------- File closed
      // $this->testJson($tmp);

    }
    return;
  }

  // read data from JSON
  public function readDataFromJson($filename)
  {
    // add extension to filename
    $filename = $filename . '.json';

    // check if file is empty
    if ($json = file_get_contents($filename)) {
      $data = json_decode($json);

      // print_r($data);
    } else {
      array_push($this->errorStackJson, 'Error reading json file.');
      return '';
    }
    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';

    return $data;
  }

  // read data from JSON
  public function printGalleryFromJson($data)
  {
    // echo '<h2>A képgalériám</h2>';
    echo '<div class="gallery">';
    foreach ($data as $key => $elem) {
      // first item
      if ($key === 0) {
        echo  '<div class="gallery-item w-2 h-1">';
        echo   '<div class="image">';
        echo      '<img id="first-img" src="' . $elem->imagePath . '" alt="' . $elem->imageTitle . '" class="single-image">';
        echo    '</div>';
        echo    '<div class="text">' . $elem->imageTitle . '</div>';
        echo  '</div>';
        // last item
      } else if ($key === count($data) - 1) {
        echo  '<div class="gallery-item w-2 h-1">';
        echo   '<div class="image">';
        echo      '<img id="last-img" src="' . $elem->imagePath . '" alt="' . $elem->imageTitle . '" class="single-image">';
        echo    '</div>';
        echo    '<div class="text">' . $elem->imageTitle . '</div>';
        echo  '</div>';
        // items in between
      } else {
        echo  '<div class="gallery-item w-2 h-1">';
        echo   '<div class="image">';
        echo      '<img src="' . $elem->imagePath . '" alt="' . $elem->imageTitle . '" class="single-image">';
        echo    '</div>';
        echo    '<div class="text">' . $elem->imageTitle . '</div>';
        echo  '</div>';
      }
    }
    echo  '</div>';
  }


  // generate gallery from JSON
  public function generateGalleryFromJson($data)
  {
    // newer first, older after
    function orderByDate ($a, $b) {
      return $b->uploadTime -  $a->uploadTime;
    }

    usort ($data, 'orderByDate');

    $imageCount = count($data);

    // $html = '<h2>A képgalériám</h2>';
    $html = '<div class="gallery">';
    foreach ($data as $key => $elem) {
      // first item
      if ($key === 0) {
        $html .= '<div class="gallery-item w-2 h-1">';
        $html .=  '<div class="image">';
        $html .=     '<img id="first-img" src="' . $elem->imagePath . '" alt="' . $elem->imageTitle . '" class="single-image img-fluid">';
        $html .=   '</div>';
        $html .=   '<div class="text">' . $elem->imageTitle . '</div>';
        $html .= '</div>';
        // last item
      } else if ($key === count($data) - 1) {
        $html .= '<div class="gallery-item w-2 h-1">';
        $html .=  '<div class="image">';
        $html .=     '<img id="last-img" src="' . $elem->imagePath . '" alt="' . $elem->imageTitle . '" class="single-image img-fluid">';
        $html .=   '</div>';
        $html .=   '<div class="text">' . $elem->imageTitle . '</div>';
        $html .= '</div>';
        // items in between
      } else {
        $html .= '<div class="gallery-item w-2 h-1">';
        $html .=  '<div class="image">';
        $html .=     '<img id="' . $key . '" src="' . $elem->imagePath . '" alt="' . $elem->imageTitle . '" class="single-image img-fluid">';
        $html .=   '</div>';
        $html .=   '<div class="text">' . $elem->imageTitle . '</div>';
        $html .= '</div>';
      }
    }
    $html .= '</div>';
    echo json_encode(array(
      'myGallery' => $html,
      'galleryItemCount' => $imageCount,
      'errorStackJson' => $this->errorStackJson
    )
  );
    $html;
  }


  // print for testing purposes
  public function testJson($json)
  {
    echo '<pre style="background: white;">';
    print_r($json);
    echo '</pre>';
    // decode and print for testing purposes
    $jsonDecoded = json_decode($json);
    echo '<br><br />Json decoded:<br />';
    echo '<pre style="background: white;">';
    print_r($jsonDecoded);
    echo '</pre>';
  }



  /**
   * The phunction PHP framework (http://sourceforge.net/projects/phunction/) uses
   * the following function to generate valid version 4 UUIDs:
   * by Alix Axel
   * @see https://www.php.net/manual/en/function.com-create-guid
   * modified by András Gulácsi mt_rand() replaced with random_int()
   */
  public function createGuid()
  {
    if (function_exists('com_create_guid') === true) {
      return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', random_int(0, 65535), random_int(0, 65535), random_int(0, 65535), random_int(16384, 20479), random_int(32768, 49151), random_int(0, 65535), random_int(0, 65535), random_int(0, 65535));
  }


  /**
   * Check $_FILES[][name]
   *
   * @param (string) $filename - Uploaded file name.
   * @author Yousef Ismaeil Cliprz
   */
  private function isFileNameValid($filename)
  {
    return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i", $filename)) ? true : false);
  }

  /**
   * @param (string) $filename - Uploaded file name.
   * @author Yousef Ismaeil Cliprz.
   */
  function isFileNameTooLong($filename)
  {
    return (bool) ((mb_strlen($filename, "UTF-8") > 225) ? true : false);
  }


  // validate user input
  public function validateInputForm()
  {
    // validate in case of a post method
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // echo '<pre style="background: white;">';
      // print_r($_POST);
      // echo '</pre>';

      // echo '<pre style="background: white;">';
      // print_r($_FILES['image']);
      // echo '</pre>';
      $noError = true;


      // check file input
      if (isset($_FILES['image']) && !empty($_FILES['image'])) {
        // echo 'Kép url: ' . $_FILES['image']['name'] . '<br />';

        $galleryFolderPath = 'images/gallery/';

        // POST image error
        if ($_FILES['image']['error'] > 0) {
          $noError = false;
          $errorCode = $_FILES['image']['error'];

          /**
           * Error code explanations
           * @see https://www.php.net/manual/en/features.file-upload.errors.php
           */
          switch ($errorCode) {
            case 1:
              array_push($this->errorStackImage, 'The uploaded file exceeds the upload_max_filesize directive in php.ini.');
              break;
            case 2:
              array_push($this->errorStackImage, 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
              break;
            case 3:
              array_push($this->errorStackImage, 'The uploaded file was only partially uploaded.');
              break;
            case 4:
              array_push($this->errorStackImage, 'No file was uploaded.');
              break;
            case 6:
              array_push($this->errorStackImage, 'Missing a temporary folder.');
              break;
            case 7:
              array_push($this->errorStackImage, 'Failed to write file to disk.');
              break;
            case 8:
              array_push($this->errorStackImage, 'A PHP extension stopped the file upload.');
              break;
            default:
              array_push($this->errorStackImage, 'An unspecified PHP error occured.');
              break;
          }

          array_push($this->errorStackImage, 'Hiba a fájl feltöltésekor. Próbáld újra.');
        }

        // Check upload content to filter out some malicious code
        // read the header information of the image and will fail on an invalid image.
        if (!@getimagesize($_FILES['image']['tmp_name'])) {
          $noError = false;
          array_push($this->errorStackImage, 'A feltöltött kép érvénytelen.');
        }

        // check if filename contains illegal chars
        if ($this->isFileNameValid($_FILES['image']['name'] === false)) {
          $noError = false;
          array_push($this->errorStackImage, 'Fájlnév nem tartalmazhat ékezetes betűket, speciális karaktereket (pl. $, [, { stb.)');
        }

        // check if filename is not too long
        if ($this->isFileNameTooLong($_FILES['image']['name'] === true)) {
          $noError = false;
          array_push($this->errorStackImage, 'A fájlnév túl hosszú. Max. 250 karakter a megengedett hossz.');
        }

        // max 500 KB
        if ($_FILES['image']['size'] > 512000) {
          $noError = false;
          array_push($this->errorStackImage, 'A fájl mérete nem lehet nagyobb, mint 500 KB.');
        }

        // get image extension, note it will not stop malicious code embedded in the image
        $type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        // echo $type  . '</br>';

        if (!in_array($type, self::$allowedExtensions)) {
          $noError = false;
          array_push($this->errorStackImage, 'A fájlnév kiterjesztése csak JPG, JPEG, PNG vagy GIF lehet.');
        }


        // split string to get filename without extension
        $explodedFilename = explode('.', $_FILES['image']['name']);

        // change uploaded filename datetime + random numbers + type
        $_FILES['image']['name'] = $explodedFilename[0] . '-' . date('Ymdhis') . random_int(6, 20) . '.' . $type;
        // echo $_FILES['image']['name'] . '</br>';


        // if file exists, stop moving file from temp to destination folder
        // there is a minimal chance of filename duplication, but unlikely after randomization
        if (file_exists($galleryFolderPath . $_FILES['image']['name'])) {
          $noError = false;
          array_push($this->errorStackImage, 'Ilyen nevű fájl már létezik. Próbáld újra feltölteni.');
        }
      } else {
        $noError = false;
        array_push($this->errorStackImage, 'Nem adtál meg képet a feltöltéshez.');
      }


      // check title input
      if (isset($_POST['title']) && !empty($_POST['title'])) {
        // echo 'Képcím: ' . $_POST['title'] . '<br />';
        // remove whitespace, strip tags, convert html entities, rewmove slashes
        $title = $this->sanitizeInputText($_POST['title']);
      } else {
        $noError = false;
        array_push($this->errorStackTitle, 'Nem adtál címet a képnek.');
      }


      if ($noError === true) {
        // temp file path
        $tmpPath = $_FILES['image']['tmp_name'];
        // copy file to here
        $movedPath = $galleryFolderPath . $_FILES['image']['name'];
        // perform file moving
        move_uploaded_file($tmpPath, $movedPath);

        $this->setImagePath($movedPath);
        $this->setImageTitle($title);
      }

      return $noError;
    }
  }

  // some basic sanitizing: remove trailing whitespace, convert html entities, strip slashes and tags
  private function sanitizeInputText($str)
  {
    $str = trim($str);
    $str = strip_tags($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str);
    $str = htmlentities($str);
    return $str;
  }
}
