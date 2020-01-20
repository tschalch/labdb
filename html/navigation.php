<?php 
include_once("functions.php");
?>
<script>
//window.addEvent('domready',function() {
    //Element.prototype.hide = function() {
        // Do nothing
    //};
//});
</script>
<nav class="navbar navbar-custom navbar-expand-md navbar-dark fixed-top">
    <button class="navbar-toggler" type="button" data-toggle="collapse" 
      data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" 
      aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="index.php">Lab Database</a>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Inventory</a>
        <div class="dropdown-menu" aria-labelledby="dropdown01">
          <a class="dropdown-item" href="list.php?list=listItems">All Items</a>
          <a class="dropdown-item" href="list.php?list=listItems&amp;status=1">Items to/on order</a>
          <a class="dropdown-item" href="list.php?list=listItems&amp;status=4">Finished items</a>
          <a class="dropdown-item" href="newEntry.php?form=frmItem&amp;mode=modify">.. Add Item</a>
          <a class="dropdown-item" href="list.php?list=listLocations">....Item Locations</a>
          <a class="dropdown-item" href="newEntry.php?form=frmLocations&amp;mode=modify">...... Add Location</a>
          <a class="dropdown-item" href="list.php?list=listLogbook">Logbook</a>
          <a class="dropdown-item" href="newEntry.php?form=frmLog&amp;mode=modify">.. New Log Entry</a>
          <a class="dropdown-item" href="list.php?list=listItems&amp;column=1">.. Columns</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown02" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Research</a>
        <div class="dropdown-menu" aria-labelledby="dropdown02">
          <a class="dropdown-item" href="list.php?list=listGene">Plasmid Building Blocks</a>
          <a class="dropdown-item" href="newEntry.php?form=frmGene&amp;type=gene&amp;mode=modify">.. Add Fragment</a>
          <a class="dropdown-item" href="newEntry.php?form=frmGene&amp;type=PCR&amp;mode=modify">.. Add PCR</a>
          <a class="dropdown-item" href="newEntry.php?form=frmGene&amp;type=backbone&amp;mode=modify">.. Add Backbone</a>

          <a class="dropdown-item" href="list.php?list=listPlasmids">Plasmids</a>
          <a class="dropdown-item" href="newEntry.php?form=frmPlasmids&amp;mode=modify">.. Add Plasmid</a>

          <a class="dropdown-item" href="list.php?list=listOligo">Oligos</a>
          <a class="dropdown-item" href="newEntry.php?form=frmOligo&amp;mode=modify">.. Add Oligo</a>

          <a class="dropdown-item" href="list.php?list=listStrains">Strains</a>
          <a class="dropdown-item" href="newEntry.php?form=frmStrain&amp;mode=modify">.. Add Strain</a>

          <!--<a class="dropdown-item" href="list.php?list=listProjects">Projects</a>
          <a class="dropdown-item" href="newEntry.php?form=frmProject&amp;mode=modify">.. Add Project</a>-->
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown03" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Freezer</a>
        <div class="dropdown-menu" aria-labelledby="dropdown03">
          <a class="dropdown-item" href="list.php?list=listBoxes">Boxes</a>
          <a class="dropdown-item" href="list.php?list=listVials">Vials</a>
          <a class="dropdown-item" href="newEntry.php?form=frmVial&amp;mode=modify">.. Add Vial</a>
          <a class="dropdown-item" href="newEntry.php?form=frmBoxes&amp;mode=modify">.. Add Box</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools</a>
        <div class="dropdown-menu" aria-labelledby="dropdown04">
          <a class="dropdown-item" href="frmImport.php">Import datasets</a>

          <a class="dropdown-item" href="sequence_extractor/index.php">Sequence Extractor</a>
          <a class="dropdown-item" href="/sms2/index.html">Sequence Maniuplation Suite</a>
          <a class="dropdown-item" href="http://www.bioinformatics.nl/cgi-bin/primer3plus/primer3plus.cgi/">Primer3Plus</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown05" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Booking</a>
        <div class="dropdown-menu" aria-labelledby="dropdown05">
<?php
$sql="SELECT resourceID, resource_name FROM resources WHERE active='1';";
$resources = pdo_query($sql);
if(isset($resources) && is_array($resources)){
  foreach ($resources as $r) {
  print "<a class=\"dropdown-item\" href=\"fullcalendar.php?resource=${r['resourceID']}\">${r['resource_name']}</a>";
}
}
?>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown05" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">User</a>
        <div class="dropdown-menu" aria-labelledby="dropdown05">
          <?php if(isset($userid)){
            print "<a class=\"dropdown-item\" href=\"logout.php\">Logout $username</a>";
            print "<a class=\"dropdown-item\" href=\"userprofile.php\">Your profile</a>";
            } else {
            print "<a class=\"dropdown-item\" href=\"index.php\">Login</a>";
            print "<a class=\"dropdown-item\" href=\"signup.php\">Register</a>";
            }
          ?>
        </div>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0" method="get" action="index.php">
      <input class="form-control mr-sm-2" type="text" name="searchTerm" placeholder="Search" aria-label="Search">
      <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>
