Array.prototype.sum = function(){
	for(var i=0,sum=0;i<this.length;sum+=this[i++]);
	return sum;
}
Array.prototype.max = function(){
	return Math.max.apply({},this)
}
Array.prototype.min = function(){
	return Math.min.apply({},this)
}

var Label = new Class({
	
	attr: {
		text: '',
		range: [],
		position: null,
		isFragment: false,
		style: '',
		orbit: null
	},
	
	svg: null,
	
	bg: null,
	
	initialize: function(text){
		this.attr.text = text;	
	},
	
	translate: function(dx, dy){
		this.svg.translate(dx, dy);
		this.bg.translate(dx, dy);
	}
})

var VectorMap = new Class({

    Implements: [Options, Events],

    options: {
        width : 500, //mapBox.get('width');
        height : 500, //mapBox.get('height');
        seqBox : $('editDNA'),
        orbitSpacing : 0.05, //radial distance between the fragments as fraction of radius
	orfLength : 100
    },

    initialize: function(options, mapBox){
            this.setOptions(options);
            dnaSequence = getSequenceFromFasta(this.options.seqBox.get('html'));
            this.dnaSequence = removeNonDnaStrict(dnaSequence);
            verifyDna (this.dnaSequence);
            this.vL = this.dnaSequence.length;
            this.cx = this.options.width/2;
            this.cy = this.options.height/2;
            this.R = 0.7 * this.options.width / 2.0; // radius of vector
            this.vm = Raphael(mapBox, this.options.width, this.options.height);
            this.fragments = []; // array of fragment = [start,end,name,direction,orbitNo, width/2, styleParams]
            this.orfs = new Hash(); // array of fragment = [start,end,name,direction,orbitNo, width/2, styleParams]
            this.orbits = new Hash();
	    this.sites = []; // array of site = [name, position]
	    this.labels = []; // this is an array containing the number of labels on each base pair.
  	    for (var i = 0; i < this.vL; i++) this.labels[i] = []; //initialize array. otherwise min,max won't work
	    this.bg = null; //white rectangle for background
    }, 
    
    getBoundaries : function(fragment){
		var bndrs = fragment.slice(0,2);
		bndrs = (fragment[3]) ? [bndrs[0], bndrs[1] + this.vL] : [bndrs[1], bndrs[0] + this.vL];
		bndrs = (bndrs[1] - bndrs[0] > this.vL) ? [bndrs[0], bndrs[1] % this.vL] : bndrs;
		return bndrs;
    },

    getCollisions : function(boxes){
	var collisions = [];
	for (var i = 0; i < boxes.length; i++){
		for (var j = 0; j < boxes.length; j++){
			if (i != j){
				var collision = collisionCheck(boxes[i].box,boxes[j].box);
				if (collision) {
					collisions.push([boxes[i],boxes[j]]);
				}
			}
		}
	}
	return collisions;
    },
    
    resolveCollisions : function(labels){
	var boxes = getBoxes(labels);
	collisions = this.getCollisions(boxes);
	for (var i = 0; i < collisions.length; i++){
		//check if fragment, then label can easily be moved
		var box = collisions[i][0];
		if (box.label.attr.isFragment){
			//find label empty region in gene
			var bndrs = box.label.attr.range;
			var avSpac = [bndrs[0],bndrs[0]];
			var cnt = 0;
			var start = bndrs[0];
			for(var k = bndrs[0]; k < bndrs[1]; k ++){
				if(this.labels[k % this.vL].length > 0){
					if (k - start > avSpac[1] - avSpac[0]) avSpac = [start,k];
					start = k;
				}
			}
			if (k - start > avSpac[1] - avSpac[0]) avSpac = [start,k];
			var oldPos = box.label.attr.position;
			box.label.attr.position = Math.round((avSpac[0] + (avSpac[1]-avSpac[0]) / 2) % this.vL);
			this.placeLabel(box.label);
			for (var j = 0; j < this.labels[oldPos].length; j++){
				if (this.labels[oldPos][j] == box.label) this.labels[oldPos].splice(j, 1);
			}			
			this.labels[box.label.attr.position].push(box.label);
			collisions.splice(i,1);
			for (var j = 0; j < collisions.length; j++){
				if (collisions[j][1] == box.label) collisions.splice(j, 1);
				if (collisions[j][0] == box.label) collisions.splice(j, 1);
			}
		}
	}
	//determine clusters
	labels.sort(function(a,b){return a.attr.position - b.attr.position});	
	boxes = getBoxes(labels);
	collisions = this.getCollisions(boxes);
	var counter = 0;
	while(collisions.length && counter < 40){
		var clusters = [];
		for (var i = 0; i < collisions.length; i++){
			var found0 = false;
			var found1 = false;
			for (var j = 0; j < clusters.length; j++){
				for (var k = 0; k < clusters[j].length; k++){
					if(collisions[i][0] == clusters[j][k]) found0 = j;
					if(collisions[i][1] == clusters[j][k]) found1 = j;
				}
			}
			if (found0 !== false && found1 !== false) continue;
			if (found0 !== false && found1 === false) {
				clusters[found0].push(collisions[i][1]);
				continue;
				}
			if (found1 !== false && found0 === false) {
				clusters[found1].push(collisions[i][0]);
				continue;
				}
			if (found0 === false && found1 === false) {
				clusters.push(collisions[i]);
				continue;
				}
		}
		//resolve clusters
		for (var i = 0; i < clusters.length; i++){
			var cords = [];
			for (var j = 0; j < clusters[i].length; j++){
				var bbox = clusters[i][j].box;
				cords.push([bbox.x + (bbox.width / 2.0), bbox.y + (bbox.height / 2.0), bbox.width, bbox.height]);
			}
			// calculate center
			var center = [0,0]
			for (var j = 0; j < cords.length; j++){
				center[0] += cords[j][0];
				center[1] += cords[j][1];
			}
			center = [center[0] / j, center[1] / j];
			// calculate trajectories for each label
			// radial trajectory
			var radTr = [center[0] - this.cx, center[1] - this.cy];
			// normalize
			var radTrLen = Math.sqrt(Math.pow(radTr[0],2) + Math.pow(radTr[1],2));
			radTr = [radTr[0]/radTrLen, radTr[1]/radTrLen];
			// calculate direction of label trajectory
			var a = 0.6 * Math.PI / cords.length;
			for (var j = 0; j < cords.length; j++){
				// rotate radial vector
				var phi = a * (j - (cords.length - 1) / 2.0);
				var tra = [radTr[0] * Math.cos(phi) - radTr[1] * Math.sin(phi),
					   radTr[0] * Math.sin(phi) + radTr[1] * Math.cos(phi)];
				var f = 5 * cords.length / (j%2 +1);
				clusters[i][j].label.translate(f * tra[0], f * tra[1]);
			}
		}
		boxes = getBoxes(labels);
		collisions = this.getCollisions(boxes);
		counter++;
	}
    },
    
    initLayout : function(){
	// initialize arrays
	this.layoutMap = []; // this is an array containing the number of fragments on each bp.
	for (var i = 0; i < this.vL; i++) this.layoutMap[i] = [0,0]; //initialize array. otherwise min,max won't work
	// assign fragments to orbits, larger inside
	//sort fragments
	this.fragments.sort(function(a,b){return Math.abs((b[1]-b[0])) - Math.abs((a[1]-a[0]))});
	//sort restriction sites
	this.sites.sort(function(a,b){return a[1] - b[1]});
	// populate the frags array
	var labels = [];
        for (var i = 0; i < this.fragments.length; i++){
		var bndrs = this.getBoundaries(this.fragments[i]);
		var max_orbit = 0;
		var orbit_dir = this.fragments[i][4];
		for (var j = bndrs[0]; j < bndrs[1]; j++){
			var o = (orbit_dir > 0) ? 0 : 1;
			while(j<0) j += this.vL;
			var x = this.layoutMap[j%this.vL][o] += (1 * orbit_dir);
			max_orbit = (Math.abs(max_orbit) < Math.abs(x)) ? x : max_orbit;
		}
		this.fragments[i][4] = max_orbit;
	}
	// determine maximum outer orbit (for selection)
	var outerOrbs = this.layoutMap.map(function(x){return x[0]});
        this.maxOrb = Math.max.apply(this,outerOrbs);
	// determine minimum inner orbit (for scale)
	var innerOrbs = this.layoutMap.map(function(x){return x[1]});
        this.minOrb = Math.min.apply(this,innerOrbs);
	// collect labels
	//first from fragments
        for (var i = 0; i < this.fragments.length; i++){
		if (this.fragments[i][2] != ""){
			var site = this.fragments[i];
			var bndrs = this.getBoundaries(site);
			var pos = Math.round((bndrs[0] + (bndrs[1]-bndrs[0]) / 2) % this.vL);
			var label = this.addLabel(site[2], pos, site[7]);
			label.attr.range = bndrs;
			label.attr.isFragment = true;
			label.attr.orbit = site[4];
			this.placeLabel(label);
			labels.push(label);
		}
	}
	// then collect labels from restriction sites
	for (var i = 0; i < this.sites.length; i++){
		var site = this.sites[i];
		var label = this.addLabel(site[0], site[1], site[2]);
		this.placeLabel(label);
		labels.push(label);
	}
	// assemble same sites into groups.
	var labelGroups = {};
	for (var i = 0; i < labels.length; i++){
		var label = labels[i];
		if (labelGroups[label.attr.text]){
			labelGroups[label.attr.text].push(label);
		} else {
			labelGroups[label.attr.text] = [label];
		}
		label.attr.group = labelGroups[label.attr.text];
	}
	//color unique sites differently
	for (var g in labelGroups){
		if (labelGroups[g].length == 1){
			if (labelGroups[g][0].attr.isFragment == true) continue;
			labelGroups[g][0].attr.color = "blue";
			labelGroups[g][0].svg.attr("fill", "blue");
		}
	}
	// identify sites of high density/clashes, where lables need to fan out
	// trial and error
	this.resolveCollisions(labels);
    },

    addLabel : function(text, position, style){
	var c = 0;
	for (var i = 0; i < text.length; i++){
		if (text.charAt(i).search(/[-\s_]/) > -1 && c > 10){
			text = text.substring(0, i+1) + "\n" + text.substr(i + 1);
			c = 0;
		} else {
			c++;
		}
	}
	var label = new Label(text);
	label.attr.position = position;
	label.attr.style = style;
	label.attr.vm = this;
	label.attr.color = label.attr.style['fill'];
	this.labels[label.attr.position].push(label);	
	return label;
    },

    getCanvasSize : function(){
	var minX = 1000;
	var minY = 1000;
	var maxX = 0;
	var maxY = 0;
	this.vm.forEach(function(el){
		var box = el.getBBox();
		if (box.x < minX) minX = box.x;
		if (box.y < minY) minY = box.y;
		if (box.x + box.width > maxX) maxX = box.x + box.width;
		if (box.y + box.height > maxY) maxY = box.y + box.height;
	});
	var maxWidth = maxX - minX;
	var maxHeight = maxY - minY;
	return {minX: minX, maxX: maxX, minY: minY, maxY: maxY, maxWidth: maxWidth, maxHeight: maxHeight};
    },

    fitMap : function(){
	// determine center of complete graph
	// set center of plasmid
	//var nodes = this.vm.getChildren();
	var cs = this.getCanvasSize();
	var scaleFactor = [1.0, 1.0];
	var borders = [this.options.orbitSpacing * this.R + 4, //left
		       this.options.orbitSpacing * this.R + 4, //right
		       this.options.orbitSpacing * this.R + 4, //top
		       this.options.orbitSpacing * this.R + 4 + 20] //bottom
	scaleFactor[0] = (this.options.width - borders[0] - borders[1]) / cs.maxWidth;
	scaleFactor[1] = (this.options.height - borders[2] - borders[3]) / cs.maxHeight;
	if (scaleFactor[0] > scaleFactor[1]){
		scaleFactor[0] = scaleFactor[1];
	} else {
		scaleFactor[1] = scaleFactor[0];
	}
	//scale graph
	this.R = this.R * scaleFactor[0];
	this.vm.forEach(function (el){
		el.scale(scaleFactor[0], scaleFactor[1], this.cx, this.cy)
		if (el.nodeName == "text"){
			var text = el.raphael;
			text.attr("font-size", scaleFactor[0] * el.raphael.attrs['font-size']);
			var box = text.getBBox();
			text.translate((this.cx - box.x) * (1-scaleFactor[0]), (this.cy - box.y) * (1-scaleFactor[1]));
		}		
	});
	//center graph
	cs = this.getCanvasSize();
	var mvX = borders[0] - cs.minX + ((this.options.width - borders[0] - borders[1] - cs.maxWidth)/2.0);
	var mvY = borders[2] - cs.minY + ((this.options.height - borders[2] - borders[3] - cs.maxHeight)/2.0);
	this.vm.forEach(function (el){
		el.translate(mvX, mvY);
	})
	this.cx = this.cx + mvX;
	this.cy = this.cy + mvY;
	var r = this.vm.rect(0,0,this.options.width,this.options.height).attr({fill:"white", stroke:"white"});
	r.toBack();
	r[0].vm = this;
	r[0].onclick =  function(){
		try{
			if(this.vm.selection) this.vm.selection.remove();
		    } catch (error) {
			//alert(error);
		    }
	}
    },

    updateFragments : function(element){
        this.fragments = [];
	for (var i = 0; i < this.labels.length; i++){
		if (this.labels[i].length > 0){
			while (this.labels[i].length) {this.labels[i].pop().svg.remove();}
		}
	}
	this.findOrfs();
        var divs = element.getChildren('div');
        for (var i = 0; i < divs.length; i++) {
            var div = divs[i];
	    if (!div.get('id').contains('gene')) continue;
            var first = div.getFirst('div');
            if (!first) continue;
	    var frag = first.getFirst('input');
            if (!frag) continue;
            var name = frag.get('value'); 
	    var startel = frag.getNext('input');            
            if (!startel) continue;
	    start = startel.get('value');
            var end = startel.getNext('input').get('value');
            var direction = div.getFirst('div').getFirst('select').get('value');
            if (start != end && start != 0){
                this.fragments.push([start.toInt() - 2, end.toInt() - 1, name, direction.toInt(),
                                1, 0.02, {"fill": "#8B0000", "stroke": "black", "stroke-width": 0.5,
                                "stroke-linecap": "butt", cursor:"pointer"},
                                {fill: "#8B0000", "font-size": '10', "font-family": "Verdana", "text-anchor" : "middle"}]);
            }
        };
    },

    getFragCenter: function(fragment){
        var center = (fragment[3]) ? (fragment[0] + ((fragment[1] + this.vL - fragment[0]) % this.vL / 2)) % this.vL:
                                     (fragment[1] + ((fragment[0] + this.vL - fragment[1]) % this.vL / 2)) % this.vL;
        return center;
    },

    getOverlap: function(oldFragment, newFragment){
        var occupiedRange = (oldFragment[3]) ? [oldFragment[0],oldFragment[1]]: [oldFragment[1],oldFragment[0]];
        occupiedRange[1] += (occupiedRange[0]>occupiedRange[1]) ? this.vL : 0;
        occLength = occupiedRange[1] - occupiedRange[0];
        var newRange = (newFragment[3]) ? [newFragment[0],newFragment[1]]: [newFragment[1],newFragment[0]];
        newRange[1] += (newRange[0]>newRange[1]) ? this.vL : 0;
        newLength = newRange[1] - newRange[0];
        if ((newRange[0]%this.vL < occupiedRange[1]%this.vL && (occupiedRange[1] - newRange[0]) % this.vL < occLength)||
            (newRange[1]%this.vL > occupiedRange[0]%this.vL && (newRange[1] - occupiedRange[0]) % this.vL < occLength)||
            (newRange[0]%this.vL < occupiedRange[0]%this.vL && newRange[1]%this.vL > occupiedRange[1]%this.vL)){
            return true;
        } else {
            return false;
        }
    },

    drawFragment : function(fragment){
        var spacefound = 0;
        var orbitNo = fragment[4];
        var fragR = (1 + orbitNo * this.options.orbitSpacing) * this.R;
        var s0 = fragment[0];
        var s1 = fragment[1]; 
        var fWidth = fragment[5];
        var start1 = this.getXY(s0,fragR*(1-fWidth));
        var start2 = this.getXY(s0,fragR*(1+fWidth));
        var retract = fragment[3] ? -0.005 : 0.005;
        var dest1 = this.getXY(s1 + retract * this.vL,fragR*(1-fWidth));
        var dest2 = this.getXY(s1,fragR);
        var dest3 = this.getXY(s1 + retract * this.vL,fragR*(1+fWidth));
        var addCycle  = fragment[3] ? 360 : -360;
        var arg3 = ((Math.abs((dest2[0] + addCycle - start2[0]) % 360)) > 180 ? 1 : 0);
        var d = fragment[3] ? 0 : 1;
	//code into svg lingo
	var pathStr = 
            "M" + start2[2] + " " + start2[3] +
            "A" + fragR*(1+fWidth) + " " + fragR*(1+fWidth) + " 0 " + arg3 + " " + fragment[3] + " " + dest3[2] + " " + dest3[3] +
            "L" + dest2[2] + " " + dest2[3] +
            "L" + dest1[2] + " " + dest1[3] +
            "A" + fragR*(1-fWidth) + " " + fragR*(1-fWidth) + " 0 " + arg3 + " " + d + " " + start1[2] + " " + start1[3] +
            "Z";
	var farc = this.vm.path(pathStr).attr(fragment[6]);
        farc[0].s0 = s0;
        farc[0].s1 = s1;
        farc[0].vm = this;
        farc[0].dir = fragment[3];
        farc[0].onclick = function (){
            var dir = (this.dir) ? 'direct' : 'reverse';
            seqDirection = dir;
            showWhereInSequence(this.s0,this.s1, dir, 110);
            this.vm.setSelection([this.s0,this.s1], dir, this.vm.maxOrb + 1);
        };
    },
    
    drawLabels: function (){
	var labels = this.labels;
	labels.vm = this;
	for (var i = 0; i < this.vL; i++){
		this.labels[i].each(function (label){
		this.vm.drawLabel(label);
		})
	}
    },
    
    // returns a label based on the position array [text, bp, attributes, frag]
    placeLabel : function(label){
	var rLabel = this.R * (1 + ((this.maxOrb + 2) * this.options.orbitSpacing));
	var labelPos = this.getXY(label.attr.position, rLabel);
	//make a svg object
	var sin = Math.sin(labelPos[0] * Math.PI / 180);
	//label.attr.text += sin;
	if (sin > 0.2) label.attr.style['text-anchor'] = "start";
	if (sin < -0.2) label.attr.style['text-anchor'] = "end";
	if (label.svg) label.svg.remove();
	if (label.bg) label.bg.remove();
	var svg = this.vm.text(labelPos[2], labelPos[3], label.attr.text).attr(label.attr.style);
	svg.attr("fill", label.attr.color);
	var box = svg.getBBox();
	//svg.translate(0, - box.height / 2.0);
	svg[0].label = label;
	if (!label.attr.isFragment) svg[0].onclick = this.initSelection;
	if (!label.attr.isFragment) svg[0].onmouseover = function (event){
		var label = this.label
		var group = label.attr.group.sort(function(a,b){return b.attr.position - a.attr.position});
		var vm = label.attr.vm;
		var frags = [vm.vL];
		for (var i = 0; i < group.length; i++){
			group[i].svg.attr("fill", "red");
			group[i].svg.attr("font-weight", "bold");
			frags[i+1] = group[i].attr.position;
			frags[i] = frags[i] - group[i].attr.position
		}
		frags[0] += frags.pop();
		if (vm.selText) vm.selText.remove();
	        vm.selText = vm.vm.text(vm.cx, vm.options.height * 0.99, label.attr.text + ": " + frags.toString());
	}
	if (!label.attr.isFragment) svg[0].onmouseout = function (event){
		var label = this.label
		var group = label.attr.group
		var vm = label.attr.vm;
		for (var i = 0; i < group.length; i++){
			group[i].svg.attr("fill", group[i].attr.color);
			group[i].svg.attr("font-weight", "normal");
		}
	        vm.selText.remove();
	}
	label.svg = svg;
	var box = svg.getBBox();
	label.bg = this.vm.rect(box.x-1, box.y-1, box.width+2, box.height+2, 2).attr({fill:"white", stroke:"white"});
    },
    
    drawLabel: function(label){
	if(label.attr.isFragment){
		var fragment = label[0];
		var r1 = this.R * (1.0 + this.options.orbitSpacing * label.attr.orbit);		
	} else {
		var r1 = this.R * 1.0;
	}
	var r2 = this.R * (1.0 + this.options.orbitSpacing * (this.maxOrb + 2));
	var start = this.getXY(label.attr.position, r1);
	var kink = this.getXY(label.attr.position, r2);
	var box = label.svg.getBBox();
	var end = [box.x + box.width/2.0, box.y + box.height / 2];
	var siteParams = {stroke: label.attr.style['fill'], "stroke-width": 1, "stroke-linecap": "round"};
	var line = this.vm.path(
	    "M" + start[2] + " " + start[3] +
	    "L" + kink[2] + " " + kink[3] +
	    "L" + end[0] + " " + end[1]).attr(siteParams);
	label.bg.toFront();
	label.svg.toFront();
	label.line = line;
	line.toBack();
	},

    initSelection : function(event){
	var label = this.label;
	var vm = label.attr.vm;
	if(!label.attr.position){
		var x = event.pageX - $('map').offsetLeft;
		var y = event.pageY - $('map').offsetTop;
		this.bp = vm.getBp(x,y,vm.R);
	} else {
	    this.bp = label.attr.position;
	}
	if (event.shiftKey){
	    selEnd = this.bp;
	    selEnd = selEnd < selStart ? selEnd + vm.vL : selEnd;
	} else {
	    selStart = this.bp;
	    selEnd = null;
	}
	if (selStart && selEnd){
	    vm.setSelection([selStart,selEnd], 'direct', vm.maxOrb + 1);
	    showWhereInSequence(selStart,selEnd%vm.vL, 'direct', 110);
	}
	if (selStart && !selEnd){
	    vm.setSelection([selStart,selStart], 'direct', vm.maxOrb + 1);
	    showWhereInSequence(selStart,selStart, 'direct', 110);
	}
    },
    
    drawScale : function (interval) {
	orbitNo = this.minOrb - 1;
        var r = (1 + orbitNo * this.options.orbitSpacing) * this.R;
        var r1 = r - 0.01 * this.R;
        var r2 = r + 0.01 * this.R;

        var cw = this.width/2;
        var circle = new Label("circle");
        var c = this.vm.circle(this.cx, this.cy, this.R)
	.attr({cursor:"pointer", stroke: "grey", "stroke-width": "2"});
	circle.svg = c;
        circle.attr.vm = this;
        c[0].onclick = this.initSelection;
	c[0].label = circle;
        // print title
        var attr = {"font": '14px "Verdana"', opacity: 0.5};
        var title = this.vm.text(this.cx, this.cy, this.vL+" bp").attr(attr);
        var noTicks = Math.floor(this.vL / interval - 0.3);
        var beta = 2 * Math.PI * interval / this.vL;
        var ticks = [];
        var labels = [];
        var tickParams = {stroke: "black", "stroke-width": 1, "stroke-linecap": "round"};
        var labelParam = {fill: "black", "font-size": 8, "font-family": "Verdana"};
        for (var i = 0; i <= noTicks; i++) {
                var alpha = beta * i - Math.PI / 2,
                cos = Math.cos(alpha),
                sin = Math.sin(alpha);
                ticks[i] = this.vm.path(
                    "M" + (this.cx + r1 * cos) + " " + (this.cy + r1 * sin) +
                    "L" + (this.cx + r2 * cos) + " " + (this.cy + r2 * sin)).attr(tickParams);
                labels[i] = this.vm.text(this.cx + r1*0.85*cos,this.cy + r1*0.85 * sin,i*interval+" bp").attr(labelParam);
        }
    },

    drawVector : function() {
        this.vm.clear();
	this.initLayout();
        // add a fragments
        var fragments = this.fragments;
        fragments.vm = this;
        fragments.each( function (fragment){
            this.vm.drawFragment(fragment);
            });
        this.drawScale(500);
	this.drawLabels();
	this.fitMap();
    },

    findOrfs : function(){
        var readingFrames = [[0,'direct'],[1,'direct'],[2,'direct'],
                             [0,'reverse'],[1,'reverse'],[2,'reverse'],];
        var orfs = [];
        orfNr = 1;

        for(var rf =0; rf < readingFrames.length; rf++){
                orfs.extend(findOrfs(this.dnaSequence, readingFrames[rf][0],
                                     readingFrames[rf][1], this.options.orfLength));
        }
        for (var k =0; k < orfs.length; k++){
                //alert(orfs[k]);
                this.fragments.push(orfs[k]);
        };
    },

    getXY : function(position, r) {
        var phase = -90;
        var alpha = position * 360.0 / this.vL,
            a = (alpha + phase) * Math.PI / 180,
            x = this.cx + r * Math.cos(a) ,
            y = this.cy + r * Math.sin(a);
        return [alpha, a, x, y];
    },

    getBp : function(x, y, r) {
        x = x - this.cx;
        y = y - this.cy;
        var phase = Math.PI/2;
        if (x >= 0){
            var alpha = Math.atan(y/x);
        }
        if (x < 0){
            var alpha = Math.atan(y/x) + Math.PI;
        }
        var a = (alpha + phase);
        var l = a * this.vL / (2 * Math.PI);
        return Math.round(l);
    },
    
    setSelection : function(range, direction, orbitNo){
        try{
            if(this.selection) this.selection.remove();
        } catch (error) {
            //alert(error);
        }
        var d = 1;
        var posRange = $A(range);
        if (direction == 'reverse') posRange.sort(function(a,b){return b + a});
        var selRadius = this.R * (1+ (orbitNo*this.options.orbitSpacing));
        var startArc = this.getXY(posRange[0],selRadius);
        var endArc = this.getXY(posRange[1],selRadius);
        var selparams = {"fill": "grey", stroke : "black", "stroke-width": 1,
				"stroke-linecap": "butt", opacity : 0.3}
        var diff = (endArc[0] + 360 - startArc[0]) % 360;
        var arg3 = 0;
        if(Math.abs(diff) > 180){
            arg3 = 1;
        }
        if(diff < 0 && Math.abs(diff) < 180){
            arg3 = 1;
        }
        this.selection = this.vm.path(
                                "M" + this.cx + " " + this.cy +
                                "L" + startArc[2] + " " +  startArc[3] +
                                "A" + selRadius + " " +  selRadius + " 0 " +  arg3 + " " +  d + " " + endArc[2] + " " +  endArc[3] +
                                "Z").attr(selparams);
        //this.selection.toBack();
	try{
	        if(this.selText) this.selText.remove();
	} catch (error){
	}
        var labelparam = {fill: "black", "font-size": 10, "font-family": "Verdana"};
        this.selText = this.vm.text(this.cx, this.options.height * 0.99, (range[0]+2) + " to " + ((range[1] % this.vL) + 1) + ", "
                     + (Math.abs(range[1]-range[0])) + " bp");
        this.selText.attr(labelparam);
    }

});



function showWhereInSequence(start, end, direction, lineLength){
    seqStart = start + 2;
    seqEnd = end + 1;
    if(direction == 'reverse'){
        var temp = start;
        start = end;
        end = temp;
    }
    var seqenceField = $("editDNA");
    if (!seqenceField) return;
    var highlightStartTag = "<span style='background-color:#F0E68C;'>";
    var highlightEndTag = "</span>";
    var seq = (sequence == "")? seqenceField.get('html') : sequence;
    sequence = seq;
    // include line breaks (<br/>)
    var iStart = start + (Math.floor(start/lineLength) * 4) + 1;
    var iEnd = end + (Math.floor(end/lineLength) * 4) + 1;
    selectedSeq = seq.substring(iStart,iEnd);
    nonSelectedSeq = seq.substring(iEnd, iStart);
    var seqlen = seq.length;
    end = end % seqlen;
    if (end < start){
        var newSeq = highlightStartTag + seq.substring(0,iEnd) + highlightEndTag + nonSelectedSeq +
                highlightStartTag + seq.substring(iStart,seqlen) + highlightEndTag;
    } else {
        var newSeq = seq.substring(0,iStart) + highlightStartTag + selectedSeq +
                highlightEndTag + seq.substring(iEnd,seqlen);
    }
    seqenceField.set('html',newSeq);

    // write translation field
    var translateField = $('editProtein');
    var offset = (start + 1) % 3;
    var preString = '';
    var postString = '';
    var dna = removeNonDnaStrict(seq);
    if (direction == 'reverse'){
	offset = ((dna.length - end - 1) % 3);
	var t1 = writeTranslation(dna, offset, direction);
	offset = ((dna.length - offset) % 3);
	preString = '  ';
	t1 = reverse(t1);
    } else {
	var t1 = writeTranslation(dna, offset, direction);
	postString = '  ';
    }
    t1 = removeNonProtein(t1);
    var empty = ['&nbsp;','&nbsp;','&nbsp;','&nbsp;','&nbsp;','&nbsp;'];
    var t2 = empty.slice(0,offset).join('');
    var c = offset;
    for(x = 0; x < t1.length; x++){
	var tadd = preString;
	tadd += t1.charAt(x);
	tadd += postString;
	for (var y = 0; y < tadd.length; y++){
		t2 += (tadd.charAt(y) != ' ') ? tadd.charAt(y) : '&nbsp;';
		c++;
		if(c >= (lineLength)){
			t2 += "<br/>";
			c = 0;
		}
	}
    }
    try{
    translateField.set('html', t2)
    } catch (error) {
	
    }
    
}

function postwith (to,p) {
  var myForm = document.createElement("form");
  myForm.method="post" ;
  myForm.target="newWindow";
  myForm.action = to ;
  for (var k in p) {
    var myInput = document.createElement("input") ;
    myInput.setAttribute("name", k) ;
    myInput.setAttribute("value", p[k]);
    myForm.appendChild(myInput) ;
  }
  document.body.appendChild(myForm) ;
  myForm.submit() ;
  document.body.removeChild(myForm) ;
}

function translate(formLink){
    
    var dnaSequence = removeNonDnaStrict(selectedSeq);
    if (seqDirection == 'reverse'){
        dnaSequence = reverse(complement(dnaSequence));
    }
    Cookie.write("fcounter", fcounter);
    Cookie.write("seqStart", seqStart);
    Cookie.write("seqEnd", seqEnd);
    Cookie.write("dir", seqDirection);
    postwith($(formLink).get('href'), {DNASequence:dnaSequence,
        proteinSequence:writeTranslation(dnaSequence)});
    
}

function collisionCheck(t,u){
//check the horizontal overlap   
	var x = ((t.x + t.width >= u.x) && (t.x <= u.x + u.width));
//check the vertical overlap     
	var y = ((t.y + t.height >= u.y) && (t.y <= u.y + u.height));
	if(x && y) {
		return true;
	} else {  
		return false;
	}
}

function getBoxes(labels){
	var boxes = [];
	for (var i = 0; i < labels.length; i++){
		boxes[i] = {box:labels[i].svg.getBBox(), label:labels[i]};
	}
	return boxes;
}
