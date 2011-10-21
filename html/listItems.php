<?php

include("listItemsHeader.php");
include("listhead.php");
foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	$edit = 0;
	$permissions = array(9,9,9);
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	if($row['status']==4 and $status != 4){
		print "<tr style=\"background-color: #DCDCDC;\">";
	} elseif ($row['status']==1) {
		print "<tr style=\"background-color: #FFF0F5;\">";
	} else {
		print "<tr>";
	}
	echo listActions($id, $edit, False);
	print "<td class=\"lists\" width=\"1%\" align=\"RIGHT\">$id</td>";
	echo "<td class=\"lists\" width=\"15%\"><a href=\"editEntry.php?id=$id&mode=display\">${row['name']}</a></td>";
	echo "<td class=\"lists\" width=\10%\">${row['supplier']}</td>";
	echo "<td class=\"lists\" width=\15%\">${row['orderNumber']}</td>";
	echo "<td class=\"lists\" width=\15%\">${row['casNumber']}</td>";
	echo "<td class=\"lists\" width=\"5%\">${row['quantity']}</td>";
	echo "<td class=\"lists\" width=\"5%\">${row['unitMeas']}</td>";
	echo "<td class=\"lists\" width=\"5%\">${row['price']}</td>";
	$stat = getStatus($row['status']);
	echo "<td class=\"lists\" width=\"10%\">$stat</td>";
	if ($row['orderDate']!='0000-00-00' and $row['orderDate']) $date = date("m/d/Y",strtotime($row['orderDate']));
	echo "<td class=\"lists\" width=\10%\">$date</td>";
	$date = '';
	$loc = getRecord($row['location'], $userid);
	echo "<td class=\"lists\" width=\5%\">";
		echo "<a href=\"editEntry.php?id=${row['location']}&amp;mode=display\"> ${loc['name']}</a>";
	echo "</td>";
	echo "</tr>";
	$i++;
}
if ($status == 1){
	listProcessor(array(3,4,5));
} else {
	listProcessor(array(6));
}
?>

</table>


