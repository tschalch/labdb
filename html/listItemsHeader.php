<?php
$listItem = "item";  #used form javascript message box.
$formaction = "${_SERVER['PHP_SELF']}?${_SERVER['QUERY_STRING']}"; #action performed when "do it" button is pressed

#SQL parameters for data retrieval:
#column names (need to be specified for each table):
$table = "inventory";
$columns = array('inventory.orderDate', 'inventory.name', 'inventory.description',
		 'inventory.orderNumber', 'inventory.casNumber', 'inventory.unitMeas',
		 'inventory.price', 'inventory.orderDate', 'inventory.received',
		 'inventory.funding', 'inventory.manufacturer', 'inventory.supplier',
		 'inventory.location', 'inventory.status','inventory.billed',
		 'tracker.trackID', 'tracker.owner','tracker.permOwner', 'inventory.quantity',
		 'user.userid'
		 );

# optional join expressions to connect to more data
//$join = "LEFT JOIN locations ON inventory.location=locations.id ";
#array of fields that is going to be searched for the term entered in the search... box
$searchfields = $columns;
$defaultOrder ="inventory.name";

# customize sql query to status-specific display of items
if (array_key_exists('status', $_GET)){
    $status = $_GET['status'];
    switch($status){
    case 1:
            $title = "Items to/on order";
            $where = " (`status`=1 OR `status`=2) ";
            $defaultOrder ="inventory.status";
            break;
    case 3:
            $title = "Lab Inventory";
            $where = " `status`=3 ";
            break;
    case 4:
            $title = "Finished Items";
            $where = " `status`=4 ";
            break;
    }
}
if (array_key_exists('column', $_GET)){
    $iscolumn = $_GET['column'];
    switch($iscolumn){
    case 1:
            $title = "Columns";
            $where = " (`type`=2) ";
            $defaultOrder ="inventory.name";
            break;
    }
}
#End SQL parameters

#array of query field => table heading
$fields = array('trackID' => 'ID',
		'name' => 'Item',
		'supplier' => 'Supplier',
		'orderNumber' => 'Cat #',
		'casNumber' => 'cas #',
		'quantity' => 'Qty',
		'unitMeas' => 'Unit Meas.',
		'price' => 'Price/Unit',
		'status' => 'Status',
		'orderDate' => 'Date ordered',
		'location' => 'Location');

#toggle Project combobox on and off
$noProjectFilter = True;
#toggle user/group filters on and off
$noUserFilter = True;

?>
