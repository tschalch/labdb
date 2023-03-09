<?php

include_once("accesscontrol.php");
include_once 'functions.php';

//print_r($_FILES);
//print_r($_GET);

$valid_extensions = ['.pdf', '.docx', '.doc', '.xls', '.xlsx']; // valid extensions
$path = '/local/labdb/uploads/'; // upload directory

if(!empty($_GET['getFiles']) && !empty($_GET['trackID'])) {
  $trackID = $_GET['trackID'];
  printFiles($trackID);
}

if(!empty($_GET['removeFile'])) {
  $fileID = $_GET['removeFile'];
  $q = "SELECT * FROM uploads WHERE id=$fileID;";
  $result = pdo_query($q);
  if ((is_countable($result) ? count($result) : 0) === 1){
    $r = $result[0];
    $f = $r['file_name'];
    $f = $path.strtolower($f); 
    if (is_file($f)) {
      unlink($f);
      $q = "DELETE FROM `uploads` WHERE id=$fileID; ";
      pdo_query($q);
    }
  }
}

if(!empty($_GET['file_desc']) && !empty($_FILES['file'])) {
  $file = $_FILES['file']['name'];
  $tmp = $_FILES['file']['tmp_name'];

  // get uploaded file's extension
  $ext = '.'.strtolower(pathinfo($file, PATHINFO_EXTENSION));

  // can upload same image using rand function
  $final_file = $file;
  //echo "<br/>";
  //echo "file: $file; extension: $ext <br/>\n";

  header('Content-type: application/json');
  // check's valid format
  if(in_array($ext, $valid_extensions)) 
  { 
    $upload_path = "$path/$final_file"; 

    $data = ['description' => $_GET['file_desc'], 'filename' => $final_file];
    if (file_exists($upload_path)){
      echo json_encode(array_merge(["fileError" => "FileExists"], $data), JSON_THROW_ON_ERROR);
      return;
    }

    if(move_uploaded_file($tmp,$upload_path)) {
      $json = json_encode($data);
      if ($json === false) {
          // Avoid echo of empty string (which is invalid JSON), and
          // JSONify the error message instead:
          $json = json_encode(["jsonError", json_last_error_msg()], JSON_THROW_ON_ERROR);
          if ($json === false) {
              // This should not happen, but we go all the way now:
              $json = '{"jsonError": "unknown"}';
          }
          // Set HTTP response status code to: 500 - Internal Server Error
          http_response_code(500);
      }
      echo $json;
      //$trackID = $_GET['trackID'];

      //insert form data in the database
      //$query = "INSERT INTO uploads (trackID, description, file_name) VALUES ('".$trackID."','".$description."','".strtolower($final_file)."')";
      //$results = pdo_query($query);

      //printFiles($trackID);

      //echo "Success...";
      //echo print_r($results);

      //echo $results?'ok':'err';
    }
  } 
  else 
  {
      echo json_encode(["fileError" => "FileExtensionError"]);
  }
} 

function printFiles($trackID){
  $query = "SELECT * FROM uploads WHERE trackID=$trackID;";
  $files = pdo_query($query);
  foreach($files as $f){
    print "<div data-file-id=\"".$f['id']."\" data-trackid=\"".$f['trackID']."\">";
    print "<a href=\"/uploads/".$f['file_name']."\">".$f['file_name']."</a> / <span>".$f['description']."</span>";
    print "<img onClick=\"removeFile(this)\" style=\"display:inline; margin: 0.2em; cursor: pointer; vertical-align: middle; height:1.5em;\" title=\"delete\" alt=\"delete\" src=\"img/delete-item.png\" />";
    print "</div>";
  }
}


?>
