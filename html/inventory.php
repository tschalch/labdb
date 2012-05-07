<?php
$title = "Lab Inventory";
include_once("accesscontrol.php");
include("header.php");
include_once("functions.php");
?>
</head>
<body>
<?php
include("listItemsHeader.php");
$_GET["list"]='listItems';
include("title_bar.php");
include("navigation.php");
?><div id="content"><?php include("listTitle.php");?>

<img src="img/inventoryFlow-0_1.png"  type='image/png' border=0 usemap='#ideamap'>
	<map name='ideamap'>

<area shape='RECT' coords='122, 67, 244, 153' href='list.php?list=listItems&amp;status=3' >
<area shape='RECT' coords='48, 394, 48, 394' href='' title = '' alt = ''>
<area shape='RECT' coords='1189, 473, 1189, 473' href='' title = '' alt = ''>
<area shape='RECT' coords='718, 269, 718, 269' href='' title = '' alt = ''>
<area shape='RECT' coords='743, 294, 798, 335'>
<area shape='RECT' coords='315, 236, 315, 236' href='' title = '' alt = ''>
<area shape='RECT' coords='340, 261, 503, 330'>
<area shape='RECT' coords='317, 137, 317, 137' href='' title = '' alt = ''>
<area shape='POLY' coords='343, 215, 369, 163, 471, 163, 497, 215, 420, 267'>
<area shape='RECT' coords='436, 393, 436, 393' href='' title = '' alt = ''>
<area shape='RECT' coords='461, 418, 621, 486'>
<area shape='RECT' coords='578, 259, 578, 259' href='' title = '' alt = ''>
<area shape='RECT' coords='603, 284, 763, 352'>
<area shape='RECT' coords='577, 179, 577, 179' href='' title = '' alt = ''>
<area shape='RECT' coords='602, 204, 765, 273'>
<area shape='RECT' coords='573, 79, 573, 79' href='' title = '' alt = ''>

<area shape='POLY' coords='599, 158, 627, 105, 736, 105, 764, 158, 682, 211'>
<area shape='RECT' coords='685, 400, 685, 400' href='' title = '' alt = ''>
<area shape='RECT' coords='423, 565, 423, 565' href='' title = '' alt = ''>
<area shape='POLY' coords='449, 673, 541, 591, 633, 673, 602, 754, 480, 754'>
<area shape='RECT' coords='91, 39, 91, 39' href='list.php?list=listItems&amp;status=3' title = '' alt = ''>
<area shape='RECT' coords='420, 471, 420, 471' href='' title = '' alt = ''>
<area shape='RECT' coords='445, 496, 637, 568'>
<?php
include("footer.php");
?>
