<?php

include("listItemsHeader.php");
#Set Menu items
?>

<script type="text/javascript" >
    var menu_items = ["new","edit", "delete"];
</script>

<?php
include("listhead.php");

#color rows
?>
<script type="text/javascript" >
window.addEvent('domready', function() {
    $('.lists tr').each(function(i){
    $(this).css('background-color', '');
    var color =  $(this).data('color');
    if (undefined != color) $(this).css('background-color', color);
    });
});
</script>
<?php
$zindex = 11;
$totalCost = 0;
//print_r($rows);
foreach ($rows as $row) {
	#print "row: ";print_r($row); print "<br/>";
	$id = $row['trackID'];
	$edit = 0;
	$permissions = array(9,9,9);
	if (($row['owner']==$userid and $row['permOwner']>1) or getPermissions($id, $userid)>1) $edit = 1;
	if($row['status']==4 and $status != 4){
		print "<tr class=\"lists data-row\" data-record_id=\"$id\" data-color=\"#DCDCDC\">";
	} elseif ($row['status']==1) {
		print "<tr class=\"lists data-row\" data-record_id=\"$id\" data-color=\"#F8D3FF\">";
	} elseif ($row['billed']==0) {
		print "<tr class=\"lists data-row\" data-record_id=\"$id\" data-color=\"#FFFAD2\">";
	} else {
		print "<tr class=\"lists data-row\" data-record_id=\"$id\">";
	}
	echo listActions($id);
	print "<td class=\"lists\" width=\"1%\" align=\"RIGHT\">$id</td>";
	echo "<td class=\"lists\" width=\"15%\"><a href=\"editEntry.php?id=$id&mode=display\">${row['name']}</a></td>";
	echo "<td class=\"lists\" width=\10%\">${row['supplier']}</td>";
	echo "<td class=\"lists\" width=\15%\">${row['orderNumber']}</td>";
	echo "<td class=\"lists\" width=\15%\">${row['casNumber']}</td>";
	echo "<td class=\"lists\" width=\"5%\">${row['quantity']}</td>";
	echo "<td class=\"lists\" width=\"5%\">${row['unitMeas']}</td>";
	echo "<td class=\"lists\" width=\"5%\">${row['price']}</td>";
	$cost = $row['quantity'] * $row['price'];
	$totalCost += $cost;
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
	echo "<tr class=\"menu\" id=\"menu_$id\"></tr>";
	$i++;
}
    echo "<tr><td colspan = \"100\">";
    $noItems = count($rows);
    echo "Number of Items: $noItems; ";
    echo "Total Cost = \$ $totalCost"; 
    echo "</td></tr>";

if (isset($status) and $status == 1){
	listProcessor(array(3,4,5,8));
} else {
	listProcessor(array(6,7,8,9));
}
?>

</table>


