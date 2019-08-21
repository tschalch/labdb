<?php
#writes a list of selected records to wiki table format
include_once("accesscontrol.php");
include_once("functions.php");
include("header.php");

function unigePrintEntry($entry){
    global $orderTotal;
    $unigeString = "<tr>";
    $unigeString .= "<td>${entry['quantity']} x ${entry['unitMeas']}</td>";
    $unigeString .= "<td>".$entry['name']. "</td>";
    $unigeString .= "<td>${entry['manufacturer']}</td>";
    $unigeString .= "<td>${entry['orderNumber']}</td>";
    $priceInt = floor($entry['price']);
    $priceCents = round(100 * ($entry['price']-$priceInt));
    $unigeString .= "<td>$priceInt</td>";
    $unigeString .= sprintf("<td>%02d</td>",$priceCents);
    $total = $entry['quantity'] * $entry['price'];
    $orderTotal += $total;
    $totalInt = floor($total);
    $totalCents = round(100 * ($total - $totalInt));
    $unigeString .= "<td>$totalInt</td>";
    $unigeString .= sprintf("<td>%02d</td>",$totalCents);
    $unigeString .= "</tr>";
    return $unigeString;
}

if ($_GET["output"]=='unige' and $_GET["selection"]){
    $orderTotal = 0;
    $selection = explode(",", $_GET["selection"]);
    $htmlOutput = "<table>\n";
    foreach ($selection as $entry){
	$entry = getRecord($entry, $userid);
	$htmlOutput .= unigePrintEntry($entry) . "\n";
    }
    $orderTotalInt = floor($orderTotal);
    $orderTotalCents = sprintf("%02d", round(100 * ($orderTotal - $orderTotalInt)));
    $htmlOutput .= "<tr><td/><td/><td/><td/><td/><td>$orderTotalInt</td><td>$orderTotalCents</td></tr>";
    $htmlOutput .= "</table>\n";
    echo "$htmlOutput\n";
}

?>
