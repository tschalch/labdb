<div id="cssmenu">
<ul>
    <li> <a href="index.php"><i class="fa fa-fw fa-home"></i>Lab Database</a></li>
    <li id="xInventory" class="xxlabnavBox"><a class="xlabnav" href="list.php?list=listItems">Inventory</a>
     <ul>
    <li><a class="xlabnav" href="list.php?list=listItems&amp;status=1">Items to/on order</a></li>
    <li><a class="xlabnav" href="list.php?list=listItems&amp;status=4">Finished items</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmItem&amp;mode=modify">.. Add Item</a></li>
    <li><a class="xlabnav" href="list.php?list=listLocations">....Item Locations</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmLocations&amp;mode=modify">...... Add Location</a></li>
    <li><a class="xlabnav" href="list.php?list=listLogbook">Logbook</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmLog&amp;mode=modify">.. New Log Entry</a></li>
    <li><a class="xlabnav" href="list.php?list=listItems&amp;column=1">.. Columns</a></li>
    </ul>
    </li>
 
    <li id="xCloning" class="xxlabnavBox"><a class="xlabnav" href="list.php?list=listGene">Research</a>
     <ul>
    <li><a class="xlabnav" href="list.php?list=listGene">Plasmid Building Blocks</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmGene&amp;type=gene&amp;mode=modify">.. Add Fragment</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmGene&amp;type=PCR&amp;mode=modify">.. Add PCR</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmGene&amp;type=backbone&amp;mode=modify">.. Add Backbone</a></li>

    <li><a class="xlabnav" href="list.php?list=listPlasmids">Plasmids</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmPlasmids&amp;mode=modify">.. Add Plasmid</a></li>

    <li><a class="xlabnav" href="list.php?list=listOligo">Oligos</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmOligo&amp;mode=modify">.. Add Oligo</a></li>

    <li><a class="xlabnav" href="list.php?list=listStrains">Strains</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmStrain&amp;mode=modify">.. Add Strain</a></li>

    <!--<li><a class="xlabnav" href="list.php?list=listProjects">Projects</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmProject&amp;mode=modify">.. Add Project</a></li>-->
    </ul>
    </li>
    
    <li id="xFreezer" class="xxlabnavBox"><a class="xlabnav" href="list.php?list=listBoxes">Freezer</a>
    <ul>
    <li><a class="xlabnav" href="list.php?list=listBoxes">Boxes</a></li>
    <li><a class="xlabnav" href="list.php?list=listVials">Vials</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmVial&amp;mode=modify">.. Add Vial</a></li>
    <li><a class="xlabnav" href="newEntry.php?form=frmBoxes&amp;mode=modify">.. Add Box</a></li>
    </ul>
    </li>

    <li id="xRestNav"><a>Tools</a>
    <ul>
    <li><a class="xlabnav" href="frmImport.php">Import datasets</a></li>

    <li><a class="xlabnav" href="sequence_extractor/index.php">Sequence Extractor</a></li>
    <li><a class="xlabnav" href="/sms2/index.html">Sequence Maniuplation Suite</a></li>
    <li><a class="xlabnav" href="http://www.bioinformatics.nl/cgi-bin/primer3plus/primer3plus.cgi/">Primer3Plus</a></li>
    </ul>
    <li><a>User Profile</a>
    <ul>
    <?php if(isset($userid)){
	    print "<li><a class=\"xlabnav\" href=\"logout.php\">Logout $username</a></li>";
	    print "<li><a class=\"xlabnav\" href=\"userprofile.php\">Your profile</a></li>";
	    } else {
	    print "<li><a class=\"xlabnav\" href=\"index.php\">Login</a></li>";
	    print "<li><a class=\"xlabnav\" href=\"signup.php\">Register</a></li>";
	    }
    ?>
    </ul>
<li>
       <form method="get" action="index.php">
    <input class="searchField" type="text" value="" name="serachTerm"/>
    <input type="submit" class="search" value="&#xf002;" />
        </form>
</li>
   </ul> 
</div>

