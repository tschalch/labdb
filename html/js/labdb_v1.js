function showMenuInTableHead(e){
    var id = $(e.currentTarget).data('record_id');
    var items = {};
    items["new"] = $("<span class=\"menu-item\"><a style=\"\" href=\"newEntry.php?id="+id+"&amp;mode=modify\"> <img style=\"padding-right:5px;\" title=\"New Entry\" title=\"new\"src=\"img/copy.png\" /> New Entry </a></span>");
    items["edit"] = $("<span class=\"menu-item\"><a style=\"\" href=\"editEntry.php?id="+id+"&amp;mode=modify\"> <img style=\"padding-right:5px;\" title=\"Edit record\" title=\"edit\"src=\"img/b_edit.png\" /> Edit </a></span>");
    items["fasta"] = $("<span class=\"menu-item\"><a style=\"\" href=\"fasta.php?id="+id+"\"> <img style=\"padding-right:5px;\" title=\"Get fasta file\" title=\"edit\"src=\"img/File.ico\" /> Fasta file </a></span>");
    items["delete"] = $("<span class=\"menu-item\"><a style=\"cursor:pointer;\" onclick=\"deleteRecord(this, "+id+");\"> <img style=\"padding-right:5px;\" src=\"img/b_drop.png\" /> Delete </a></span>");
    items["vial"] = $("<span class=\"menu-item\"><a href=\"newEntry.php?form=frmVial&amp;mode=modify&amp;template="+id+"\"> <img style=\"\" title=\"New vial based on this record.\"src=\"img/vial.png\" /> New Vial </a></span>");

    $('.lists tr').each(function(i){
	$(this).css('background-color', '');
	var color =  $(this).data('color');
	if (undefined != color) $(this).css('background-color', color);
    });
    $('.menu').html('');
    var row =  $(e.currentTarget).closest('tr');
    row.css('background-color','LightGray');
    var menu = $("#menu_"+id);
    menu.html('');
    var menu_row = $("<td colspan=\"100\"></td>");
    for (var i = 0; i < menu_items.length; i++){
	var item = items[menu_items[i]]
	if (item){
	    menu_row.append(item);
	}
    }
    menu.append(menu_row);
}
 
function AddFragmentField(type, removeOthers){
	//alert("type: "+type);
	trs=document.getElementsByTagName("div");
	for(var w=0;w<trs.length;w++){
		if (removeOthers & trs[w].id.split('.')[0] == 'cmb'){
			trs[w].style.display="none";
		}
	}
	for(var w=0;w<trs.length;w++){
		if(trs[w].style.display == "none" & trs[w].id.split('.')[1] == type){
			trs[w].style.display="block";
			break;
		}
	}
}

function RunPCR(){
	try {
	  xmlhttp = window.XMLHttpRequest?new XMLHttpRequest():
	 		new ActiveXObject("Microsoft.XMLHTTP");
	}
	catch (e) { /* do nothing */ }
	//xmlhttp.onreadystatechange = function(data){
		//if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		    //window.open("sequence_extractor/index.php");
	//	}
	oligo1 = document.getElementById("oligo1").value;
	oligo2 = document.getElementById("oligo2").value;
	template = document.getElementById("template").value;
	url = "writeOligos.php?oligo1=" + oligo1 
	       + "&oligo2=" + oligo2 
	       + "&output=extractor" 
	       + "&template="+template;
	//alert(url);
	xmlhttp.open("GET", url);
	xmlhttp.send(null);
}

function ChangeDisplayDiv(fields, disp){
	for(var v=0; v<fields.length;v++){
		name = fields[v];
		trs=document.getElementById(name);
		trs.style.display=disp;
	}
}

function RemoveHiddenDiv(fields){
	for(var v=0; v<fields.length;v++){
		trs=document.getElementById(fields[v]);
		if (trs.style.display == 'none'){
			trs.parentNode.removeChild(trs);
		}
	}
}


function ShowMaterialDrop(radio){
	var id = radio.value;
	trs=document.getElementsByTagName("div")
	trs=document.getElementsByTagName("div")
	for(var w=0;w<trs.length;w++){
		if (trs[w].id.substr(0,3) == "mat"){
			trs[w].style.display = "none";
		}
		if (trs[w].id == "mat"+id){
			trs[w].style.display = "inline";
		}
	}
}

function SetMaterialId(id){
	element = document.getElementById("materialID");
	element.value = id;
	//alert("id: "+id+" element: "+element);
	return true;
}

var addcmbx = function (event, recid){
	//prevent the page from changing
	event.stop();  
	//make the ajax call, replace text
	var el = new Element('div');
	var type = $(event.target).get('id');
	addXcmbx(el, table, type, recid, '','');
}

function addXcmbx(el, table, type, recid, start, end, dir) {  
	dir = (dir=='reverse') ? 0 : 1;
	var req = new Request.HTML({  
		method: 'get',  
		url: "getCrosscombo.php",
		data: { 'id' : recid, 'table': table, 'type' : type,'fcounter':fcounter,
		'mode': mode, 'start': start, 'end':end, 'dir':dir },
		update: el,
		onComplete: function () {
		    new Autocompleter.labdb(el.getFirst('div').getFirst('div').getFirst('input'),'autocomplete.php',{
			'postData': {
			'field': 'name', // send additional POST data, check the PHP code
			'table': table,
			'extended': '1',
			}
		    });
		    el.getFirst('div').inject($('xcmbxFrags'));
		    if(typeof(vm)!="undefined"){
			    vm.updateFragments($('xcmbxFrags'));
			    vm.drawVector();
			    vm.updateFragments($('xcmbxFrags'));
			    vm.drawVector();
		    };
		}
		}).send();
	fcounter++;
};

function destroyFrag(el){
    $(el).getParent().getParent().destroy(); 
    if(typeof(vm)!="undefined"){
	    vm.updateFragments($('xcmbxFrags'));
	    vm.drawVector();
    };
}

function CloseAddFragment(){
	var el = window.opener.document.createElement('div');
	window.opener.addXcmbx(el, 'fragments', 'gene', $('id').get('html'),
	    Cookie.read("seqStart"),
	    Cookie.read("seqEnd"),
	    Cookie.read("dir")
	);
	window.close();
	return;
}

function FilterSequence(field, type){
	var sequence = field.value;
	if (type == 'DNA' | type == 'oligo') var cleanSeq = sequence.replace(/[^atgcun]/ig,'').toUpperCase();
	if (type == 'protein') var cleanSeq = sequence.replace(/[^wchrmyqfdpankegtvislx*]/ig,'').toUpperCase();
	if (type == 'oligo'){
		if (/[^atgcun]/i.test(sequence)){
			if (confirm("Your sequence contains non-nulceic acid characters. Do you want to remove them?")) { 
				field.value = cleanSeq;
				return;
			}
			return;
		}
		return;
	}
	var splitSeq = new Array(parseInt((cleanSeq.length + 10) / 10));
	i = 0;
	j = 0;
	while(splitSeq[j] = cleanSeq.slice(i, i+10)){
		i+=10;
		j++;
	} 
	var finalSeq = '';
	blockPerLine = 6;
	for (var x = 0; x < splitSeq.length; x+=blockPerLine){
		finalSeq += splitSeq.slice(x,x+blockPerLine).join("")
		finalSeq += "\n";
	}
	//var finalSeq = splitSeq.join(" ");
	field.value = finalSeq;
}

function GetSequenceLength(field){
	var sequence = field.value;
	var cleanSeq = sequence.replace(/ /ig,'');
	return cleanSeq.length;
}

function FilterOligo(field){
	var sequence = field.value;
	cleanSeq = sequence.replace(/[^atgcu]/ig,'').toUpperCase();
	field.value = cleanSeq;
	document.mainform.none_0_len.value=cleanSeq.length;
}

function selectAll(){
  for (var i = 0; i < document.mainform.elements.length; i++) {
    var e = document.mainform.elements[i];
    if ((e.name != 'allbox') && (e.type == 'checkbox')) {
	e.checked = document.mainform.allbox.checked;
    }
  }
}

function GeneAssembler(){
	trs=document.getElementsByTagName("div")
	var geneIDs = ''
	for(var w=0;w<trs.length;w++){
		if(trs[w].style.display=="inline"){
			var value = document.getElementById("gene"+trs[w].id).value;
			if (value != 'NA'){
				geneIDs += value + ":";
			}
		}
	}
	geneIDs = geneIDs.substring(0,geneIDs.length-1)
	return geneIDs
}

function FilterFields(){
	var cmbs = $$('select');
	cmbs.each(function(element){
		if (element.value == 'NA') element.getParent("div").destroy();	
	});
}


function ProcessMaterial(){
	materialField = document.getElementById("materialID");
	plasmid = document.getElementById("plasmid").value;
	glystock = document.getElementById("glycerolstock").value;
	if(plasmid != 'NA') materialField.value = "plasmid:" + plasmid;
	if(glystock != 'NA') materialField.value = "glycerolstock:" + glystock;
}

function ProcessGenes(){
	geneIDs = GeneAssembler()
	document.getElementById("orfs_0_genes").value = geneIDs
}

function GetOrfSequence(type){
	geneIDs = GeneAssembler();
	id = type;
	loadurl("getGene.php?geneIDs=" + geneIDs +"&amp;type="+type);
}

<!-- AJAX stuff http://aleembawany.com/weblog/webdev/000051_ajax_instant_tutorial.html --!>
function loadurl(dest, id) {
 elID = id
 try {
   xmlhttp = window.XMLHttpRequest?new XMLHttpRequest():
  		new ActiveXObject("Microsoft.XMLHTTP");
 }
 catch (e) { /* do nothing */ }
 if(elID){
 	xmlhttp.onreadystatechange = triggered;
 } else {
  xmlhttp.onreadystatechange=function() {
  if (xmlhttp.readyState==4) {
   location.reload();
  }
 }
 }
 xmlhttp.open("GET", dest);
 xmlhttp.send(null);
}

function triggered() {
  if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
    var text = xmlhttp.responseText
    document.getElementById(elID).innerHTML = text;
}

/**
 * Displays an confirmation box before to submit a "DROP/DELETE/ALTER" query.
 * This function is called while clicking links
 *
 * @param   object   the link
 * @param   object   the sql query to submit
 *
 * @return  boolean  whether to run the query or not
 */
function confirmLink(theLink, query)
{
    // Confirmation is not required in the configuration file
    // or browser is Opera (crappy js implementation)
    if (confirmMsg == '' || typeof(window.opera) != 'undefined') {
        return true;
    }

    var is_confirmed = confirm(confirmMsg + query);
    if (is_confirmed) {
		loadurl('sqlProcessor.php?query='+query);
    }
    return is_confirmed;
} // end of the 'confirmLink()' function

function deleteRecords()
{
    // Confirmation is not required in the configuration file
    // or browser is Opera (crappy js implementation)
    if (confirmMsg == '' || typeof(window.opera) != 'undefined') {
        return true;
    }
    var is_confirmed = confirm(confirmMsg);
    return is_confirmed;
} // end of the 'deleteRecord()' function

function deleteRecord(element, record_id)
{
    var is_confirmed = confirm("Do you really want to delete this record?");
    if (is_confirmed==true){
	loadurl("list_doit_get.php?action=3&selection[]=" + record_id);		
    };
    return false;
} // end of the 'deleteRecord()' function


function changePermission(id){
	loadurl('changePermission.php?id='+id);
}

var newwindow;
function poptastic(url)
{
	newwindow=window.open(url,'name','height=600,width=800,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function clearcombo(element){
  for (var i=element.options.length-1; i>=0; i--){
    element.options[i] = null;
  }
  element.selectedIndex = -1;
}

function fillcombo(element, opts){
  for (opt in opts){
    	element.options.add(opts[opt]);
  }
}

function projectselected(element, target, submit){
	if (submit){
		target.value = "*";
		element.form.submit();
	}
	clearcombo(target);
	fillcombo(target, projects[element.value]);
}

function checkDate(field){
	ok = true;
	month = document.getElementById(field+"_m").value;
	day = document.getElementById(field+"_d").value;
	year = document.getElementById(field+"_y").value;
	if (isNaN(month) | month > 12 | month < 0){
		document.getElementById(field+"_m").style.border  = "1px solid #FF6633";
		ok = false;
	}
	if (isNaN(day) | day > 31 | day < 0){
		document.getElementById(field+"_d").style.border  = "1px solid #FF6633";
		ok = false;
	}
	if (isNaN(year)){
		document.getElementById(field+"_y").style.border  = "1px solid #FF6633";
		ok = false;
	}
	newdate = year + "-" + month + "-" + day;
	if (ok) document.getElementById(field).value = newdate;
	return ok;
}

function deleteField(field, parent){
	f = $(field);
	if (f) f.destroy();
	return;
}

// function on frmPlasmids that hides the sequence editor view and displays
// the sequence text area for editing.
function switchToSeqField(){
	var seqEditor = $("SeqEditor");
	var seqField = $("SeqField");
	seqEditor.set("styles", {'display': 'none'});
	seqField.set("styles", {'display': 'block'})
}

function formatEnzymes(event, recid){
	var enzymeList = $(event.target).get("value");
	cleanList = enzymeList.replace(/, /ig,',');
	$(event.target).set("value", cleanList);
}
