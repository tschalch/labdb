<div id="labnav">
    <div id="Inventory" class="labnavBox">
    <a class="labnav" href="inventory.php">Lab Inventory</a><br/>
    <a class="labnav" href="list.php?list=listItems&amp;status=1">Items to/on order</a><br/>
    <a class="labnav" href="list.php?list=listItems&amp;status=4">Finished items</a><br/>
    <a class="labnav" href="newEntry.php?form=frmItem&amp;mode=modify">.. Add Item</a><br/>
    <a class="labnav" href="list.php?list=listLocations">....Item Locations</a><br/>
    <a class="labnav" href="newEntry.php?form=frmLocations&amp;mode=modify">...... Add Location</a><br/><br/>
    <a class="labnav" href="list.php?list=listLogbook">Logbook</a><br/>
    <a class="labnav" href="newEntry.php?form=frmLog&amp;mode=modify">.. New Log Entry</a><br/>
    <a class="labnav" href="list.php?list=listItems&amp;column=1">.. Columns</a><br/>
    </div>
    
    <div id="Cloning" class="labnavBox">
    <div class ="NavTitle2" style="margin-bottom: 5px">Design Board </div>
    <a class="labnav" href="list.php?list=listGene">Plasmid Building Blocks</a><br/>
    <a class="labnav" href="newEntry.php?form=frmGene&amp;type=gene&amp;mode=modify">.. Add Fragment</a><br/>
    <a class="labnav" href="newEntry.php?form=frmGene&amp;type=PCR&amp;mode=modify">.. Add PCR</a><br/>
    <a class="labnav" href="newEntry.php?form=frmGene&amp;type=backbone&amp;mode=modify">.. Add Backbone</a><br/><br/>

    <a class="labnav" href="list.php?list=listPlasmids">Plasmids</a><br/>
    <a class="labnav" href="newEntry.php?form=frmPlasmids&amp;mode=modify">.. Add Plasmid</a><br/><br/>

    <a class="labnav" href="list.php?list=listOligo">Oligos</a><br/>
    <a class="labnav" href="newEntry.php?form=frmOligo&amp;mode=modify">.. Add Oligo</a><br/><br/>

    <a class="labnav" href="list.php?list=listStrains">Strains</a><br/>
    <a class="labnav" href="newEntry.php?form=frmStrain&amp;mode=modify">.. Add Strain</a><br/><br/>

    <a class="labnav" href="list.php?list=listProjects">Projects</a><br/>
    <a class="labnav" href="newEntry.php?form=frmProject&amp;mode=modify">.. Add Project</a><br/><br/>
    </div>

    <div id="Freezer" class="labnavBox">
    <div class ="NavTitle2" style="margin-bottom: 5px">Freezer</div>
    <a class="labnav" href="list.php?list=listBoxes">Boxes</a><br/>
    <a class="labnav" href="list.php?list=listVials">Vials</a><br/>
    <a class="labnav" href="newEntry.php?form=frmVial&amp;mode=modify">.. Add Vial</a><br/>
    <a class="labnav" href="newEntry.php?form=frmBoxes&amp;mode=modify">.. Add Box</a><br/><br/><br/>
    </div>

    <div id="RestNav">
    <a class="labnav" href="frmImport.php">Import datasets</a><br/><br/>

    <a class="labnav" href="sequence_extractor/index.php">Sequence Extractor</a><br/>
    <a class="labnav" href="../biophp_minitools/">BioPHP Minitools</a><br/>
    <a class="labnav" href="http://intranet.cshl.edu/purchasing/oligo.html">CSHL Sigma Genosis</a><br/><br/>

    <?php if(isset($userid)){
	    print "<a class=\"labnav\" href=\"logout.php\">Logout $username</a><br/>";
	    print "<a class=\"labnav\" href=\"userprofile.php\">Your profile</a><br/>";
	    } else {
	    print "<a class=\"labnav\" href=\"index.php\">Login</a><br/>";
	    print "<a class=\"labnav\" href=\"signup.php\">Register</a><br/>";
	    }
    ?>
    </div>
    
</div>

