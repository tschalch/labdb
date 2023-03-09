<?php

include("listItemsHeader.php");
#Set Menu items
?>

<script type="text/javascript" >
    var menu_items = ["new","edit", "delete"];
</script>

<?php
$defaultOrder = "name";
include("listhead.php");
#color rows

print "</table>";
print "<table>";
foreach ($rows as $row) {
  $files = json_decode(html_entity_decode($row['files']), true, 512, JSON_THROW_ON_ERROR);
  #$files = json_decode(' {"pointer":1,"files":{"1":{"description":"SDS","filename":"MPD_68340_SDS_sigma.pdf"}}}', true);
  #print json_last_error ( );
  #print_r($files['files']);
  foreach($files['files'] as $file){
    if(str_contains($file["description"], 'COSHH')) 
      $coshhFile = $file["filename"];
  #$coshhFile = $files['files']['COSHH'];
  #$coshhFile = print_r($files, true);
  #$coshhFile = $row['files'];
#printUploadField('Files (COSSH, MSDS, Quotes)', 'files', $formParams);
  }
#print "<tr><td>\"${row['hexID']}\"</td><td>${row['name']}</td><td> \"${coshhFile}\"</td></tr>\n";
print "<tr><td> \"${coshhFile}\" \\</td></tr>\n";
}
print "</table>";
