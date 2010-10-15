// Primer calculations ---  Find self-dimerization and hairpin
/* ** The self dimerization and complementarity functions **

function calcPrimer(form)
function calcDegeneratePrimers(doc)
function calculateMatrices()
function calcHairpin(seqArray, pivit)
function fillMatchMatrix(cols, rows, mat)
function makeAlignedArray(mat, minLen, maxMisMatch)
function sortAlignedArray()
function isBaseEqual(c1, c2)
function getIndexOf(seq, subSeq, startAt)

function displayHairpin()
function display3EndDimer()
function displayAllDimers() //all possible dimerization sites

**  ***/

/* GLOBALS */

var debugHairpin=0;
var debugDimers=0;
var doTiming=0;
var theAlignedArray=0;
var theHairpinArray=0;
var	minAlignLen;
var	minHairpinLen;
var bubbleSize=3; // hairpins must have this many bases between self-annealed sequences

function calcPrimer(form)
{
	var theStart;
	var theEnd;
	var	calcStart=new Date();
	if (document.OligoCalc.oligoBox.value.length < 8) {
		alert("Please enter at least 8 bases before checking for self-complementarity!");
		return false;
	}
	Calculate(form);
	if (theOligo.seqArray) delete theOligo.seqArray;
	if (theOligo.revSeqArray) delete theOligo.revSeqArray;
	if (theComplement.seqArray) delete theComplement.seqArray;
	if (theComplement.revSeqArray) delete theComplement.revSeqArray;
	
	theOligo.revSequence=reverseString(theOligo.Sequence);
	theComplement.revSequence=reverseString(theComplement.Sequence);

	theOligo.seqArray=stringToArray(theOligo.Sequence);
	theOligo.revSeqArray=stringToArray(theOligo.revSequence);
	theComplement.seqArray=stringToArray(theComplement.Sequence);
	theComplement.revSeqArray=stringToArray(theComplement.revSequence);

	minAlignLen=parseInt(form.selfComp.options[form.selfComp.selectedIndex].value); //for 3' complementarity
	minHairpinLen=parseInt(form.hairpin.options[form.hairpin.selectedIndex].value);  //for hairpin
	broadMatch=false; //true: do all degenerate comparisons
	// now create window
	primerWin=window.open( "", 'primer','width=700,toolbar=0,location=0,directories=0,status=1,menuBar=1,scrollBars=1,resizable=1' );
	primerWin.document.open("text/html");
	primerWin.document.writeln("<HTML><HEAD><TITLE>Oligo Self Complementarity Check</TITLE></HEAD>");
	primerWin.document.write('<STYLE type="text/css">');
	primerWin.document.write('<!--');
	if (isMac && browserVersion < 5.0) {
		primerWin.document.write(MacStyleSheet);
	} else {
		primerWin.document.write(PCStyleSheet);
	}
	primerWin.document.write('-->');
	primerWin.document.write('</STYLE>');
	primerWin.document.writeln("<BODY BGCOLOR=white>");
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calcPrimer - initialize window took "+(theEnd-calcStart)+" ms<br>");
	calculateMatrices(); // do this after the window is in place so we can print diagnostics if necessary
	anchorString="";
	primerWin.document.writeln(anchorString.anchor("strictMatches"));
	if(theOligo.hasIUpacBase)
	{
		primerWin.document.writeln("Your oligo contains degenerate bases.<BR>");
		primerWin.document.writeln("The strictMatch section displays only <font COLOR='RED'>perfect matches </FONT>in the case of degenerate bases.<BR>");
		primerWin.document.writeln("For Example:<BR> <PRE>   'N' matches only with 'N';<BR>   'R' matches only with 'S';<BR>   'W' matches only with 'W'; etc.</PRE><P>");
		hrefString="view all matches."
		primerWin.document.writeln("Scroll down to view <font COLOR='GREEN'><a href='#allMatches'>all Matches</a></FONT>");
	}
	if(!isCompatible)
	{	primerWin.document.writeln("<p><b>Sorry, the hairpin loop calculation is only available if you are using Netscape or IE 4.0 or higher!\n</B><br>");
	}else{
		theStart=new Date();
		primerWin.document.writeln(displayHairpin(theHairpinArray,theOligo.Sequence));
		theEnd=new Date();
		if (doTiming) primerWin.document.write("calcPrimer - displayHairpin took "+(theEnd-theStart)+" ms<br>");
	}
	theStart=new Date();
	primerWin.document.writeln(display3EndDimer(theAlignedArray));
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calcPrimer - display3EndDimer took "+(theEnd-theStart)+" ms<br>");
	theStart=new Date();
	primerWin.document.writeln(displayAllDimers(theAlignedArray,theOligo.Sequence,theOligo.revSequence));
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calcPrimer - displayAllDimers took "+(theEnd-theStart)+" ms<br>");
	theStart=new Date();
	if(theOligo.hasIUpacBase)
	{
		calcDegeneratePrimers(primerWin.document);
	} 
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calcPrimer - all calls to close took "+(theEnd-calcStart)+" ms<br>");
	primerWin.document.writeln("</BODY></HTML>"); 
	primerWin.document.close();
	primerWin.focus();
	return false; 
}

function calcDegeneratePrimers(doc)
{
	if(!theOligo.hasIUpacBase){ return;}
	broadMatch=true; //true: do all degenerate comparisons
	calculateMatrices();
	anchorString="<font COLOR='green'>-----------------------------<BR>-----------------------------<BR></FONT>";
	doc.write(anchorString.anchor("allMatches"));
	
	doc.write("Your oligo contains degenerated bases.<BR>");
	doc.write("This section displays <font COLOR='RED'>all potential matches </FONT>in the case of degenerated bases.<BR>");
	doc.write("For Example:<BR> <PRE>   'N' matches 'A','T','G','C', or 'N';<BR>   'R' matches 'T','C','S','N';<BR>   'W' matches 'A','T','W','N'; etc.</PRE><P>");
	hrefString="view strict matches only";
	doc.writeln("Scroll up to view <FONT COLOR='green'> <a href='#strictMatches'>strict Matches</a> </FONT>");
	
	if(!isCompatible)
	{	doc.write("<p><b>Sorry, the hairpin loop calculation is only available if you are using IE or Netscape 4.x or higher!!\n</B><br>");
	}else{
		doc.writeln(displayHairpin(theHairpinArray,theOligo.Sequence));
	}
	doc.write(display3EndDimer(theAlignedArray));
	doc.write(displayAllDimers(theAlignedArray,theOligo.Sequence,theOligo.revSequence));
}

function calculateMatrices()
{
	var theStart=new Date();
	if(theOligo.Sequence.length!=theComplement.Sequence.length) {
		primerWin.document.write("Error! Primer and its complement are different lengths!<br>");
		return;
	}
	//setup d*d matrix
	var matrix= makeMatrix(theOligo.Sequence.length);
	var theEnd=new Date();
	if (doTiming) primerWin.document.write("calculateMatrices - makeMatrix took "+(theEnd-theStart)+" ms<br>");
	theStart=new Date();
	
	//populates the matrix
	fillMatchMatrix(theOligo.seqArray, theComplement.seqArray, matrix);
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calculateMatrices - fillMatchMatrix took "+(theEnd-theStart)+" ms<br>");
	theStart=new Date();
	if (isIE)
		if (theAlignedArray) delete theAlignedArray;
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calculateMatrices - delete theAlignedArray took "+(theEnd-theStart)+" ms<br>");
	theStart=new Date();
	//exam the matrix for 3 prime complementary
	theAlignedArray = makeAlignedArray(matrix, minAlignLen, maxMismatchNum);
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calculateMatrices - makeAlignedArray took "+(theEnd-theStart)+" ms<br>");
	theStart=new Date();
	delete matrix;
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calculateMatrices - delete matrix took "+(theEnd-theStart)+" ms<br>");
	theStart=new Date();
	theAlignedArray=sortAlignedArray(theAlignedArray);
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calculateMatrices - sortAlignedArray took "+(theEnd-theStart)+" ms<br>");
	theStart=new Date();
	//exam the sequence for potential hairpins
	if(isCompatible) {
		if (isIE)
			if (theHairpinArray) delete theHairpinArray;
		theHairpinArray=calcHairpin(theOligo.Sequence, minHairpinLen);
	}
	theEnd=new Date();
	if (doTiming) primerWin.document.write("calculateMatrices - calcHairpin took "+(theEnd-theStart)+" ms<br>");
}


function calcHairpin(theFullSequence, minHairpinLength)
{
/*  compare theCompSeq with theFullSeq starting at theFullSeq[startPos]. Successful matches must be at least minMatch long */
/* The resulting array is an array of arrays. each result should be an array of 4 integers
	result[0]: position of start of match in sequence
	result[1]: position of end of match
	result[2]: position of the start of the complement (really the end since it would be 3'-5')
	result[3]: position of the end of the complement (really the start since it would be 3'-5')
*/

	var theFullComplement=MakeComplement(theFullSequence, 1);
	var theResults = new Array();

	if (debugHairpin) primerWin.document.write("<PRE>");
	if (debugHairpin) primerWin.document.write("calcHairpin: theFullSequence  ="+theFullSequence+"; theFullSequence.length="+theFullSequence.length+"; minHairpinLen"+minHairpinLen+";<br>");
	if (debugHairpin) primerWin.document.write("calcHairpin: theFullComplement="+theFullComplement+"; theFullComplement.length="+theFullComplement.length+";<br>");
	
	var theResult;
	var count;
	var compPos;
	var seqPos;
	var maxSeqLength=Math.abs(theFullSequence.length/2)-bubbleSize; // makes sure that we do not anneal the full length of the primer - that should come out in the dimerization report
	var maxMatch=0;
	for (compPos=0; compPos<theFullComplement.length-2*minHairpinLength; compPos++) {
		maxMatch=0;
		for (seqPos=0; seqPos<theFullSequence.length-maxSeqLength; seqPos++) {
			if (debugHairpin) primerWin.document.write("calcHairpin: compPos="+compPos+"; seqPos="+seqPos+";<br>");
			theResult=getIndexOf(theFullSequence.substring(0,seqPos+maxSeqLength),theFullComplement.substring(compPos,theFullComplement.length), seqPos, minHairpinLength);
			if (theResult[0] > -1) {
				// theResult[0] is the index of the first match of theFullComplement that is of at least length minHairpinLength in theFullSequence
				// theResult[1] is the length of the match
				
				theResults=DoHairpinArrayInsert(theResult[0],theResult[0]+theResult[1]-1,theFullSequence.length-compPos-theResult[1],theFullSequence.length-compPos-1,theResults);
				if (theResult[1] > maxMatch) maxMatch=theResult[1];
				seqPos=theResult[0]+theResult[1]-minHairpinLength;  // move forward to guarantee nothing else is found that is a reasonable match
				if (seqPos+minHairpinLength>=maxSeqLength) {
					compPos+=maxMatch-minHairpinLength; // move compPos forward to stop identical checks if long match was found!
					break; // we have moved far enough on the primer to guarentee we have everything  -further would give us the reverse match
				}
			} else {
				if (maxMatch > minHairpinLength) compPos+=maxMatch-minHairpinLength; // move compPos forward to stop identical checks if long match was found!
				break;  //not found in the rest of the sequence!
			}
		}
	}
	if (debugHairpin) primerWin.document.write("</PRE>");
	return theResults;
}

function DoHairpinArrayInsert(a,b,c,d,results)
{
	var arrayCount=results.length;
	if (a >= c || a >= b || c >= d || b>=c) {
		if (debugHairpin) primerWin.document.write("DoHairpinArrayInsert: ERROR IN VALUES PASSED! [0]="+a+"; [1]="+b+"[2]="+c+"; [3]="+d+";<br>\n");
		return results;
	}
	for (var i=0;i<arrayCount;i++) {
		if (results[i][0]<=a && results[i][1]>=b && results[i][2]<=c && results[i][3]>=d)
			return results;
		if (results[i][0]>=a && results[i][1]<=b && results[i][2]>=c && results[i][3]<=d) {
			results[i][0]=a;
			results[i][1]=b;
			results[i][2]=c;
			results[i][3]=d;
			if (debugHairpin) primerWin.document.write("DoHairpinArrayInsert: position "+i+" in results replaced with [0]="+a+"; [1]="+b+"[2]="+c+"; [3]="+d+";<br>");
			return results;
		}
	}
	results[arrayCount]=new Array(4);
	results[arrayCount][0]=a;
	results[arrayCount][1]=b;
	results[arrayCount][2]=c;
	results[arrayCount][3]=d;
	if (debugHairpin) primerWin.document.write("DoHairpinArrayInsert: arrayCount="+arrayCount+"; [0]="+a+"; [1]="+b+"[2]="+c+"; [3]="+d+";<br>");
	return results;
}

function getIndexOf(seq, subSeq, startIndex, minMatch)
{
// look for subSeq in seq
/* returns an array where
	theResult[0] is the index of the first match of subseq that is of at least length minMatch in seq
	theResult[1] is the length of the match
*/
	var theResult= new Array(2);
	theResult[0]=-1;
	theResult[1]=-1;
	if (!broadMatch) {
		for(var k=minMatch; k<=subSeq.length; k++) {
			// can replace this with seq.search for GREP capabilities
			theMatch=seq.indexOf(subSeq.substring(0,k),startIndex);
			if (theMatch < 0) {
				break;
			} else {
				theResult[0]= theMatch;
				theResult[1] = k;
				if (debugHairpin) primerWin.document.write("("+theMatch+","+k+") ");
			}
		}
	} else {
		for(var i=startIndex; i<seq.length; i++) {
			if(isBaseEqual(seq.charAt(i),subSeq.charAt(0))) {
		 		for(j=0; j<subSeq.length; j++) {
					if(!isBaseEqual(seq.charAt(i+j),subSeq.charAt(j))) {
						break;
					} else if (j >= minMatch-1) {
						theResult[0]= theMatch;
						theResult[1] = k;
					}
				}	
				if (j==subSeq.length) {
						theResult[0]= theMatch;
						theResult[1] = k;
				}
			}
		}
	}
	if (debugHairpin) primerWin.document.write("TheResult[0]="+theResult[0]+" (first match); TheResult[1]="+theResult[1]+";<br>");
	return theResult;
}

function makeMatrix(matLength) 
{
	var theMatrix = new Array(matLength);
	for(var i=0; i<matLength; i++) {
		// increment column
		theMatrix[i]=new Array(matLength);
	}
	return theMatrix;
}

function fillMatchMatrix(cols, rows, mat)
{
	var d=cols.length;
	if (d<4) return;
	if (broadMatch) {
		// Do the degenerate thing!
		for(i=0; i<d; i++) {
			// increment column
			for(var j=0; j<d; j++) {
				// increment row
				if(isBaseEqual( cols[i], rows[j]) ) {
					mat[i][j]=1;
					if(i>0 && j>0)
					{
						mat[i][j]+=mat[i-1][j-1]; //(increment diagonal values)
					}
				} else {	
					mat[i][j]=0;
					if(i>1 && j>1 ) {
						if(mat[i-1][j-1]>mat[i-2][j-2] &&mat[i-1][j-1]>1 && i<d-1 && j<d-1) {
						// allow one base mismatch only if there are at least 2 matched base on 5' and at least 1 matched base on 3'
							mat[i][j]=mat[i-1][j-1]; 
						} else if(i<d-1 && j<d-1) {
							mat[i-1][j-1]=0;
						}
					}
				}
			}
		}
	} else {
		for (i=0; i<=1; i++) {
			// increment column
			for (var j=0; j<2; j++) {
				// increment row
				if (cols[i] == rows[j]) {
					mat[i][j]=1;
					if (i && j)
						mat[i][j]+=mat[i-1][j-1]; //(increment diagonal values)
				} else {	
					mat[i][j]=0;
				}
			}
		}
		for (i=2; i<d-1; i++) {
			// increment column
			for (var j=2; j<d-1; j++) {
				// increment row
				if (cols[i] == rows[j]) {
					mat[i][j]=mat[i-1][j-1]+1; //(increment diagonal values)
				} else {	
					mat[i][j]=0;
					if (mat[i-1][j-1]>1 && cols[i+1] == rows[j+1]) {
					// allow one base mismatch only if there are at least 2 matched base on 5' and at least 1 matched base on 3'
						mat[i][j]=mat[i-1][j-1]; 
					}
				}
			}
		}
		i=d-1;
		j=i;
		// increment column
		// increment row
		if (cols[i] == rows[j]) {
			mat[i][j]=1;
			mat[i][j]+=mat[i-1][j-1]; //(increment diagonal values)
		} else {	
			mat[i][j]=0;
		}
	}
}

function makeAlignedArray(mat, minLen, maxMisMatch)
{
// assumes an orthogonal matrix
/* theAlignedArray is a bit strange in the second dimension. Assume it is a length 5 array called 'theResults'
	theResults[0] == start index
	theResults[1] == start matching index in reverse complement seq
	theResults[2] == end index of aligned bases (inclusive)
	theResults[3] == end matching index in reverse complement Seq
	theResults[4] == number of mismatches
*/
	var matLength=mat.length;
	var count=0;
	var theResults = new Array();
	var i;
	var	j;
	var	k;
	var mismatches;
	for(i=0; i<matLength; i++) {
		for(j=0; j<matLength; j++) {
			if(mat[i][j]==1)  { //potential start of an alignment
				mismatches=0;
				hasMatch=1;
				lastMatch=1;
				maxInc = matLength-(i<=j ? j : i);
				for (k=1; k<maxInc; k++) {
					hasMatch=mat[i+k][j+k];
					if(!hasMatch) break;
					if(hasMatch<=lastMatch) {
						if(mismatches>=maxMisMatch)
							break;
						mismatches++;
					}
					lastMatch=hasMatch;
				}
				if(k-mismatches>=minLen) {
					theResults[count]=new Array(5);
					theResults[count][0]=i;	//start index
					theResults[count][1]=j;	//start matching index in reverse complement seq
					theResults[count][2]=i+k-1; //end index of aligned bases (inclusive)
					theResults[count][3]=j+k-1; //end matching index in reverse complement Seq
					theResults[count][4]=mismatches;  //mismatch counts
					count++;
				}
			}
		}
	}
	return theResults;
}

function sortAlignedArray(alignedArray)
{
// assumes an orthogonal matrix
/* theAlignedArray is a bit strange in the second dimension. Assume it is a length 5 array called 'theResults'
	theResults[0] == start index
	theResults[1] == start matching index in reverse complement seq
	theResults[2] == end index of aligned bases (inclusive)
	theResults[3] == end matching index in reverse complement Seq
	theResults[4] == number of mismatches
*/
	if(alignedArray.length>2) {
		if (1==2) {
			var tempArray=new Array(5);
			var swapped=0;
			//bubble sort
			do {
				swapped=0;
				for (var n=0; n<alignedArray.length-2; n++) {
					if (alignedArray[n][2]-alignedArray[n][0]<alignedArray[n+1][2]-alignedArray[n+1][0]) {
						for (var i=0; i<5; i++) {
							tempArray[i]=alignedArray[n][i];
							alignedArray[n][i]=alignedArray[n+1][i];
							alignedArray[n+1][i]=tempArray[i];
						}
						swapped=1;
					}
				}
			} while (swapped==1);
		} else {
			alignedArray=alignedArray.sort(arrayOrder);
		}
	}
	return alignedArray;
}

function arrayOrder(a,b) { return ( ((a[2]-a[0]) < (b[2]-b[0]))? 1 :( ((a[2]-a[0]) > (b[2]-b[0]))? -1 : (a[0]-a[1]) - (b[0]-b[1]) )); } //size plus position

function isBaseEqual( c1, c2)
{
	if(c1==c2 ){ return true;}
	if(broadMatch){
		if(c1=='N' || c2=='N'){return true;}
		var equA="ARWVHD";
		var equT="TWYKHDB";
		var equG="GRSKVDB";
		var equC="CSYVHB";
	
		if(c1 == 'A' ) { if(equA.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'T' ) { if(equT.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'G' ) { if(equG.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'C' ) { if(equC.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'R' ) { if(equA.indexOf(c2)>=0 || equG.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'W' ) { if(equA.indexOf(c2)>=0 || equT.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'S' ) { if(equG.indexOf(c2)>=0 || equC.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'Y' ) { if(equT.indexOf(c2)>=0 || equC.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'K' ) { if(equT.indexOf(c2)>=0 || equG.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'V' ) { if(equA.indexOf(c2)>=0 || equG.indexOf(c2)>0 || equC.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'H' ) { if(equA.indexOf(c2)>=0 || equT.indexOf(c2)>0 || equC.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'D' ) { if(equA.indexOf(c2)>=0 || equT.indexOf(c2)>0 || equG.indexOf(c2)>=0) {return true;} return false;}
		if(c1 == 'B' ) { if(equT.indexOf(c2)>=0 || equG.indexOf(c2)>0 || equC.indexOf(c2)>=0) {return true;} return false;}
	}
	return false;
}	

//return string containing all hairpins		
function displayHairpin(theHairpinArray,theSequence)
{
	var returnString="";
	var s1="";
	var d=theSequence.length;
	var i,j;
	var theHairpinArrayLength=theHairpinArray.length;
	returnString=returnString+"<h2><B>Potential hairpin formation :</B></h2><PRE>"
	if(theHairpinArrayLength>0)
	{
		if(theHairpinArrayLength>1){
			if(theHairpinArray[theHairpinArrayLength-1][1]==theHairpinArray[theHairpinArrayLength-2][1] && (theHairpinArray[theHairpinArrayLength-1][2]==theHairpinArray[theHairpinArrayLength-2][2]))
			{
				theHairpinArrayLength--; //get rid of the last one
			}
		}
		for(i=0;i<theHairpinArrayLength; i++) {
					//add a bar between 2 legs of the hairpin if bases in the 2nd leg is contiguous to the 1st leg
					// substring wants a value from the start location to 1+the end location
			s1=theSequence.substring(0,theHairpinArray[i][0])+
				theSequence.substring(theHairpinArray[i][0],theHairpinArray[i][1]+1).fontcolor("red")+
				((theHairpinArray[i][1]+1>=theHairpinArray[i][2])?"-":"")+
				theSequence.substring(theHairpinArray[i][1]+1,theHairpinArray[i][2])+
				theSequence.substring(theHairpinArray[i][2],theHairpinArray[i][3]+1).fontcolor("red")+
				theSequence.substring(theHairpinArray[i][3]+1,d)
			returnString=returnString+"5' "+s1+" 3'<BR>";
		}
		returnString=returnString+"</PRE>";
	}else{
		returnString+=" None !<BR>";
	}
	
	return returnString+="\n";
}

function display3EndDimer(theAlignedArray)
{
	var d=theOligo.Sequence.length;
	var returnString="";
	var s1="";
	var s2="";
	//3' complementarity
	returnString+="<BR><B>3' Complementarity: </B><BR>"
	var is3End=false;
	for(var n=0; n<theAlignedArray.length-1; n++) //the last element in array is junck
	{
		s1="";
		s2="";
		if(theAlignedArray[n][2]==d-1)		//end position of match in original seq
		{
			returnString+="<pre><BR>5' ";
			for(i=0; i<d; i++)
			{
				if(i>=theAlignedArray[n][0]&&i<=theAlignedArray[n][2])
				{
					s1+="<FONT COLOR='red'>"+theOligo.seqArray[i]+"</FONT>";
					
				}else{
					s1+=theOligo.seqArray[i];
				}
			}
			for(i=d; i<2*d; i++)	//fill up the rest of the strings with space
			{
				s1+=" ";
			}
			returnString=returnString+s1+" 3' <BR>";
			
			//the reverse stand
			returnString+="3' ";
			j=0;
			if(theAlignedArray[n][0]>theAlignedArray[n][1])
			{
				for(j=0; j<theAlignedArray[n][0]-theAlignedArray[n][1]; j++) //fill up the difference with space, to align the matched bases in sequences
				{
					s2+=" ";
				}
			}
			for(k=0; k<d; k++)
			{
				if(k>=theAlignedArray[n][1]&&k<=theAlignedArray[n][3])
				{
					s2+="<FONT COLOR='red'>"+theOligo.revSeqArray[k]+"</FONT>";
					
				}else{
					s2+=theOligo.revSeqArray[k];
				}
			}
			for(j=j+k; j<2*d; j++)
			{
				s2+=" ";
			}
			returnString=returnString+s2+" 5'<BR></pre>";
			is3End=true;
		}
	}
	if(!is3End)
	{
		returnString+=" None !<BR> "
	}
	return returnString+="\n";
}

function displayAllDimers(theAlignedArray,theSequence,reversedSeq) //all possible dimerization sites
{
/* theAlignedArray is a bit strange in the second dimension. Assume it is a length 5 array called 'theResults'
	theResults[0] == start index
	theResults[1] == start matching index in reverse complement seq
	theResults[2] == end index of aligned bases (inclusive)
	theResults[3] == end matching index in reverse complement Seq
	theResults[4] == number of mismatches
*/
	var d=theSequence.length;
	var returnString="";
	var s1="";
	var s2="";
	var maxoffset=0;
	var offset,j,n;
	var	offsetStr,maxoffsetStr;
	// all other possible alignment sites
	returnString+="<BR><B>All potential self-annealing sites are marked in red (allowing 1 mis-match): </B><BR><PRE>";
	if(theAlignedArray.length>1)
	{
		for(n=0; n<theAlignedArray.length; n++) {
			offset=Math.abs(theAlignedArray[n][0]-theAlignedArray[n][1]);
			if (offset > maxoffset) maxoffset=offset;
		}
		for(n=0; n<theAlignedArray.length; n++)
		{
			s1="";
			s2="";
			offsetStr="";
			maxoffsetStr="";
			// pad the string with blanks as necessary
			offset=theAlignedArray[n][0]-theAlignedArray[n][1]; 
			for(j=0; j<Math.abs(offset); j++)
				offsetStr+=" ";
			for(j=Math.abs(offset); j<maxoffset; j++)
				maxoffsetStr+=" ";
			if(offset>0)
				s2+=offsetStr;
			if(offset<0) 
				s1+=offsetStr;
			if(debugDimers)  returnString+="offset="+offset+"; maxoffset="+maxoffset+"; [0]="+theAlignedArray[n][0]+"; [1]="+theAlignedArray[n][1]+";<br>";
				// substring wants a value from the start location to 1+the end location
			s1+=theSequence.substring(0,theAlignedArray[n][0])+
				theSequence.substring(theAlignedArray[n][0],theAlignedArray[n][2]+1).fontcolor("red")+
				theSequence.substring(theAlignedArray[n][2]+1,d);
			returnString+="5' "+s1+((offset>0)?offsetStr+maxoffsetStr:maxoffsetStr)+" 3'<BR>";
			s2+=reversedSeq.substring(0,theAlignedArray[n][1])+
				reversedSeq.substring(theAlignedArray[n][1],theAlignedArray[n][3]+1).fontcolor("red")+
				reversedSeq.substring(theAlignedArray[n][3]+1,d);
			returnString+="3' "+s2+((offset<0)?offsetStr+maxoffsetStr:maxoffsetStr)+" 5'<BR>&nbsp;<BR>";
		}
	}else{
		returnString+=" None !<BR>";
	}
	
	returnString+="</PRE>\n";
	return returnString;	
}

