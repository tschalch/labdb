<?php
include_once("functions.php");
include_once("accesscontrol.php");
include_once("seq.inc.php");

if(array_key_exists('selection', $_POST)){
	$selection = $_POST['selection'];
	#print_r($selection);
	$action = $_POST['action'];
	switch ($action) {
	# write records to files
	case "0":
		include('writeOligos.php');
		break;
	case 1:
		echo "<b>Written records to files</b>";
		echo "<p>Use \"Save Target As ...\" to download files to your computer.<br/><br/>";
		foreach($selection as $id){
			$data = getRecord($id, $userid, $groups);
			$file = "output/${data['name']}.fasta";   
			if (!$file_handle = fopen($file,"w")) { echo "Cannot open file"; }  
			if (!fwrite($file_handle, ">${data['name']}\n")) { echo "Cannot write to file"; }
			($data['DNASequence'])? $seq = $data['DNASequence'] : $seq = $data['sequence'];
			$sequence = fastaseq($seq, "\n");
			fwrite($file_handle, $sequence);  
			if  ($data['proteinSequence']){
				fwrite($file_handle, "\n\n>${data['name']} protein sequence\n");  
				fwrite($file_handle, fastaseq($data['proteinSequence'], "\n"));
			}
			echo "You have successfully written data to <a href=\"$file\">$file</a>.<br/>";   
			fclose($file_handle);  
		}
    $redirect = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}";
    header("Location:$redirect");
		break;
	# change permissions
	case 2:
		$permission = $_POST['perm'];
		$newuser = $_POST['users_0_user'];
		foreach($selection as $id){
			changePermission($id, $newuser, $permission, $userid);
		}
		echo "Permissions changed<br/><br/>";
		break;
	# delete records
	case 3:
		foreach($selection as $id){
			deleteRecord($id, $userid, $groups);
		}
		echo "Records deleted<br/><br/>";
    $redirect = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}";
    header("Location:$redirect");
		break;
	# set order placed
	case 4:
        $poNumber = escape_quotes($_POST['poNumber']);
        $setpo = "";
        if ($poNumber != ''){
            $setpo = ", inventory.poNumber='$poNumber'";
        }
        foreach($selection as $id){
            $q = "UPDATE tracker,inventory SET inventory.status=2, inventory.orderDate=NOW(),
                tracker.changed=NOW() $setpo
                WHERE tracker.trackID=$id AND tracker.sampleID=inventory.id
                AND tracker.sampleType=(SELECT id FROM sampletypes WHERE st_name='item');";
            pdo_query($q);
        }
        #print $q;
		echo "Status changed to 'order placed'<br/><br/>";
    $redirect = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}";
    header("Location:$redirect");
		break;
	# set order received
	case 5:
		foreach($selection as $id){
			$q = "UPDATE tracker,inventory SET inventory.status=3, inventory.received=NOW(),
				tracker.changed=NOW() 
				WHERE tracker.trackID=$id AND tracker.sampleID=inventory.id
				AND tracker.sampleType=(SELECT id FROM sampletypes WHERE st_name='item');";
			pdo_query($q);
		}
		#print $q;
		echo "Status changed to 'received'<br/><br/>";
    $redirect = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}";
    header("Location:$redirect");
		break;
	case 6:
		foreach($selection as $id){
			$q = "UPDATE tracker,inventory SET inventory.status=4, tracker.changed=NOW() 
				WHERE tracker.trackID=$id AND tracker.sampleID=inventory.id
				AND tracker.sampleType=(SELECT id FROM sampletypes WHERE st_name='item');";
			pdo_query($q);
		}
		#print $q;
		echo "Status changed to 'finished'<br/><br/>";
    $redirect = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}";
    header("Location:$redirect");
		break;
	case 7:
	    $selString = join(",", $selection);
	    header( "Location: writeWiki.php?output=wiki&selection=$selString" ) ;
	    break;
	case 8:
	    $selString = join(",", $selection);
	    header( "Location: writeUnigeOrder.php?output=unige&selection=$selString" ) ;
	    break;
	# set billing date
	case 9:
	    foreach($selection as $id){
		    $q = "UPDATE tracker,inventory SET inventory.billed=NOW(), tracker.changed=NOW() 
			    WHERE tracker.trackID=$id AND tracker.sampleID=inventory.id
			    AND tracker.sampleType=(SELECT id FROM sampletypes WHERE st_name='item');";
		    pdo_query($q);
	    }
	    echo "Changed to 'billed'<br/><br/>";
	    $redirect = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}";
	    header("Location:$redirect");
	    break;
	}
}


#include("footer.php");


?> 
