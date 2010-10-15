/* ** The following functions are defined on this page **
-- general utils
function Disallow(form)
-- cookie utils
function getCookieVal (offset) 
function GetCookie (name) 
function GetDefCookie (name, defaultval)
function GetDelimitedValue(theString, offset, delimiter)

-- dna utils
function CheckBase(theString) 
function IsBase(theBase) 
function IsIUpacBase(theBase) 
function CalcIUpair(base0, base, i, choice)
function AreThereIUpacBases(theSequence)
function MakeComplement(theSequence, isDNA)
function CountChar(theChar, theSequence) 
function CountNeighbors(theSeekSeq, theSequence) 
function FormatBaseString(theString) 
function micrograms(MolWt,Conc)

-- string utilities
function stringToArray(theString)
function reverseString(theString)
function RemoveNonPrintingChars(theString) 

**  ***/

function Disallow(form) {
	form.oligoBox.focus();
}

// cookie utilities

function getCookieVal (offset) {
  var endstr = document.cookie.indexOf (";", offset);
  if (endstr == -1) {
    endstr = document.cookie.length;
  }
  return unescape(document.cookie.substring(offset, endstr));
}

//  Function to return the value of the cookie specified by "name".
//    name - String object containing the cookie name.
//    returns - String object containing the cookie value, or null if
//      the cookie does not exist.

function GetCookie (name) {
  var arg = name + "=";
  var alen = arg.length;
  var clen = document.cookie.length;
  var i = 0;
  while (i < clen) {
    var j = i + alen;
    if (document.cookie.substring(i, j) == arg)
      return getCookieVal (j);
    i = document.cookie.indexOf(" ", i) + 1;
    if (i == 0) break;
  }
  return null;
}

// Function to get a cookie value. if it does not exist, then pass default

function GetDefCookie (name, defaultval) {
	var value = GetCookie(name);
	if (value == null) {
		return defaultval;
	}
	return value;
}

function GetDelimitedValue(theString, offset, delimiter) {
  var endstr = theString.indexOf (delimiter, offset);
  if (endstr == -1)
    endstr = theString.length;
  if (endstr-offset<1) return "";
  return theString.substring(offset, endstr);
}

//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// Checks that theString contains only bases
function CheckBase(theString) {
	var returnString = "";
	var cnt = 0;
	var rcnt = 0;
	var cha="";
	theString = theString.toUpperCase();
	theString = RemoveNonPrintingChars(theString);
	for ( var i = 0; i < theString.length; i++) {
		cha=theString.charAt(i);
		if (IsIUpacBase(cha) || IsBase(cha)) {
			returnString +=cha;
			cnt++;
		}else if(cha!=" " && cha!="\n"){
			alert("base # "+(cnt+1)+" :"+cha+" is not a valid base!");
			return -1;
		}
	}
	return returnString;
}
// allowed bases
function IsBase(theBase) {
	if ((theBase == "A") ||
		(theBase == "G") ||
		(theBase == "C") ||
		(theBase == "U") ||
		(theBase == "T")) {
			return 1;
	}
	return 0;
}
// check if base is a IUPAC base
function IsIUpacBase(theBase) {
	if ((theBase == "M") ||
		(theBase == "R") ||
		(theBase == "W") ||
		(theBase == "S") ||
		(theBase == "Y") ||
		(theBase == "K") ||
		(theBase == "V") ||
		(theBase == "H") ||
		(theBase == "D") ||
		(theBase == "B") ||
		(theBase == "N")) 
	{
			return 1;
	}
	return 0;
}

function CalcIUpair(base0, base, i, theSequence, choice)
{
	var IUpacBase="";
	var pair1="";
	var pair2="";
	var temp1=new Array(0,0,0);
	var temp2=new Array(0,0,0);
	var reValue=new Array(0,0,0);
	var base2=theSequence.charAt(i+1);
	if(IsIUpacBase(base0))	//if previous base is IUpacBase, do nothing
	{	return reValue;	}
	
	if(IsIUpacBase(base) )
	{
		if (debug) alert("base0 "+base0+"base"+base+" base2 "+base2);
		if(base=="M"){IUpacBase="AC";}
		else if(base=="R"){IUpacBase="AG";}
		else if(base=="W"){IUpacBase="AT";}
		else if(base=="S"){IUpacBase="CG";}
		else if(base=="Y"){IUpacBase="CT";}
		else if(base=="K"){IUpacBase="GT";}
		else if(base=="V"){IUpacBase="ACG";}
		else if(base=="H"){IUpacBase="ACT";}
		else if(base=="D"){IUpacBase="AGT";}
		else if(base=="B"){IUpacBase="CGT";}
		else if(base=="N"){IUpacBase="ACGT";}
		
		var j=0;
		while(IUpacBase.charAt(j)!="")
		{
			base=IUpacBase.charAt(j);
			//alert("base choose "+base);
			pair1=base0+base;
		//	alert("pair1 "+pair1);
			if(pair1=="AA"){temp1[0]= 1.2 ;temp1[1]=8.0; temp1[2]=21.9 ;}
			else if(pair1=="AT"){temp1[0]= 0.9 ;temp1[1]=5.6; temp1[2]=15.2  ;}
			else if(pair1=="TA"){temp1[0]=0.9  ;temp1[1]=6.6; temp1[2]= 18.4 ;}
			else if(pair1=="CA"){temp1[0]=1.7  ;temp1[1]=8.2; temp1[2]=21.0  ;}
			else if(pair1=="GT"){temp1[0]= 1.5 ;temp1[1]=9.4; temp1[2]=25.5  ;}
			else if(pair1=="CT"){temp1[0]= 1.5 ;temp1[1]=6.6; temp1[2]=16.4  ;}
			else if(pair1=="GA"){temp1[0]=1.5  ;temp1[1]=8.8; temp1[2]=23.5  ;}
			else if(pair1=="CG"){temp1[0]= 2.8 ;temp1[1]=11.8; temp1[2]=29.0  ;}
			else if(pair1=="GC"){temp1[0]=2.3  ;temp1[1]=10.5; temp1[2]=26.4  ;}
			else if(pair1=="GG"){temp1[0]=2.1  ;temp1[1]=10.9; temp1[2]=28.4  ;}
			
			if(base2==""){
				for(k=0; k<2; k++)
				{	temp2[k]=0.0;	}
			
			}else if(!IsIUpacBase(base2)){
				pair2=base+base2;
				//alert("pair2 "+pair2);
				if(pair2=="AA"){temp2[0]= 1.2 ;temp2[1]=8.0; temp2[2]=21.9 ;}
				else if(pair2=="AT"){temp2[0]= 0.9 ;temp2[1]=5.6; temp2[2]=15.2  ;}
				else if(pair2=="TA"){temp2[0]=0.9  ;temp2[1]=6.6; temp2[2]= 18.4 ;}
				else if(pair2=="CA"){temp2[0]=1.7  ;temp2[1]=8.2; temp2[2]=21.0  ;}
				else if(pair2=="GT"){temp2[0]= 1.5 ;temp2[1]=9.4; temp2[2]=25.5  ;}
				else if(pair2=="CT"){temp2[0]= 1.5 ;temp2[1]=6.6; temp2[2]=16.4  ;}
				else if(pair2=="GA"){temp2[0]=1.5  ;temp2[1]=8.8; temp2[2]=23.5  ;}
				else if(pair2=="CG"){temp2[0]= 2.8 ;temp2[1]=11.8; temp2[2]=29.0  ;}
				else if(pair2=="GC"){temp2[0]=2.3  ;temp2[1]=10.5; temp2[2]=26.4  ;}
				else if(pair2=="GG"){temp2[0]=2.1  ;temp2[1]=10.9; temp2[2]=28.4  ;}
			}else if(IsIUpacBase(base2)){
				base0=base; base=base2; i++; 
				temp2=CalcIUpair(base0,base,i,theSequence,choice);
				i--;
			}
			
			for(k=0;k<3;k++)
			{
				if(j==0){
					reValue[k]=temp1[k]+temp2[k];
				}else{
					if ((choice=="max")&&(reValue[k]<temp1[k]+temp2[k]))
					{	reValue[k]=temp1[k]+temp2[k];	
					}else if((choice=="min")&&(reValue[k]>temp1[k]+temp2[k]))
					{	reValue[k]=temp1[k]+temp2[k]; 	
					}
				}
			}
			j++;
		}
	}
	return reValue;
}

function AreThereIUpacBases(theSequence)
{
	for(var i=0; i<theSequence.length; i++)
	{
		if(IsIUpacBase(theSequence.charAt(i)))
		{	return 1;	}
	}
	return 0;
}

function MakeComplement(theSequence, isDNA) {
	var returnString="";
	var i;
	var temp;
	for( i=theSequence.length-1; i>=0; i--) {
		temp=theSequence.charAt(i);
		switch (temp) {
			case "A" :
				if (isDNA) {
					temp="T";
				} else {
					temp="U";
				}
				break;
			case "T" :
				temp="A";
				break;
			case "U" :
				temp="A";
				break;
			case "G" :
				temp="C";
				break;
			case "C" :
				temp="G";
				break;
			case "M" :
				temp="K";
				break;
			case "K" :
				temp="M";
				break;
			case "R" :
				temp="Y";
				break;
			case "Y" :
				temp="R";
				break;
			case "W" :
				temp="W";
				break;
			case "S" :
				temp="S";
				break;
			case "V" :
				temp="B";
				break;
			case "B" :
				temp="V";
				break;
			case "H" :
				temp="D";
				break;
			case "D" :
				temp="H";
				break;
			default : break;
		}
		returnString=returnString+temp;
	}
	return returnString;
}

function CountChar(theChar, theSequence) 
{
	var returnValue = 0
	for ( var i = 0; i < theSequence.length; i++) {
		if (theSequence.charAt(i) == theChar) {
			returnValue ++;
		}
	}
	return returnValue;
}

function CountNeighbors(theSeekSeq, theSequence) 
{
	var returnValue = 0;
	var i = 0;
	while (i>=0 && i<theSequence.length) {
		i=theSequence.indexOf(theSeekSeq,i);
		if (i>=0) {
			returnValue++;
			i++;
		}
	}
	return returnValue
}

function FormatBaseString(theString) 
{
	var returnString = "";
	var cnt = 0;
	var rcnt = 0;
	for ( var i = 0; i < theString.length; i++) {
		if (cnt>2) {
			returnString += " ";
			cnt=0;
		}
		cnt++;
		returnString += theString.charAt(i);
	}
	return returnString
}

function micrograms(MolWt,Conc)
{
/* MolWt is gms/mol; Conc is micromoles/L; assume volume is 1 milliliter */
	if (MolWt> 0 && Conc> 0) {
		return (Math.round(MolWt*Conc/100)/10);
	}
	return "";
}

function stringToArray(theString)
{	
	theArray=new Array(theString.length);
	for( var i=0; i<theString.length; i++) 
		theArray[i]=theString.charAt(i); 
	return theArray;
}

function reverseString(theString)
{	
	var reversedString="";
	for( var i=theString.length-1; i>=0; i--) 
		reversedString+=theString.charAt(i);
	return reversedString;
}

function RemoveNonPrintingChars(theString) 
{
	var returnString = ""
	for ( var i = 0; i < theString.length; i++) {
		if ( theString.charAt(i) > " ") {
			returnString += theString.charAt(i);
		}
	}
	return returnString
}

function GetFivePrimeMod_MW(theModName) {
	var mod_mw=0;
	switch (theModName) {
		case "Amino dT (C2)":
			mod_mw+=402;
			break;
		case "Amino dT (C6)":
			mod_mw+=458;
			break;
		case "BHQ-1":
			mod_mw+=554;
			break;
		case "Bromo-dC":
			mod_mw+=368;
			break;
		case "Bromo-dU":
			mod_mw+=369;
			break;
		case "C12-Aminolink":
			mod_mw+=263;
			break;
		case "C6-Aminolink":
			mod_mw+=179;
			break;
		case "Chol":
			mod_mw+=756;
			break;
		case "CY 3.5":
			mod_mw+=607;
			break;
		case "CY 5.5":
			mod_mw+=634;
			break;
		case "Cy3":
			mod_mw+=508;
			break;
		case "CY3 NHS Ester":
			mod_mw+=766;
			break;
		case "Cy5":
			mod_mw+=534;
			break;
		case "Cy5 Ester":
			mod_mw+=820;
			break;
		case "Dig":
			mod_mw+=724;
			break;
		case "dspacer":
			mod_mw+=180;
			break;
		case "dU":
			mod_mw+=290;
			break;
		case "Fam":
			mod_mw+=538;
			break;
		case "Fluo":
			mod_mw+=538;
			break;
		case "Hex":
			mod_mw+=744.1;
			break;
		case "Hyd-1":
			mod_mw+=210;
			break;
		case "Hyd-2":
			mod_mw+=288;
			break;
		case "Iodo-dC":
			mod_mw+=415;
			break;
		case "Iodo-dU":
			mod_mw+=416;
			break;
		case "IRD700":
			mod_mw+=753;
			break;
		case "IRD800":
			mod_mw+=861;
			break;
		case "Joe":
			mod_mw+=667;
			break;
		case "LCRed-610":
			mod_mw+=825;
			break;
		case "LCRed-640":
			mod_mw+=904;
			break;
		case "LCRed-670":
			mod_mw+=534;
			break;
		case "LCRed-705":
			mod_mw+=634;
			break;
		case "Methylcytosin":
			mod_mw+=303.21;
			break;
		case "Oregon Green 488 (HPLC)":
			mod_mw+=574;
			break;
		case "P32":
			mod_mw+=81;
			break;
		case "P33":
			mod_mw+=82;
			break;
		case "Pho":
			mod_mw+=80;
			break;
		case "Psoralen":
			mod_mw+=420;
			break;
		case "Psoralen":
			mod_mw+=453;
			break;
		case "Rho":
			mod_mw+=727;
			break;
		case "Rho-Green":
			mod_mw+=536;
			break;
		case "Rox":
			mod_mw+=698;
			break;
		case "Spacer-C12":
			mod_mw+=264;
			break;
		case "Spacer-C3":
			mod_mw+=138;
			break;
		case "Spacer-C6":
			mod_mw+=180;
			break;
		case "Tamra":
			mod_mw+=576;
			break;
		case "Tet":
			mod_mw+=675;
			break;
		case "Texas Red":
			mod_mw+=882;
			break;
		case "Thiol":
			mod_mw+=328;
			break;
		case "Uni-Link":
			mod_mw+=154;
			break;
		default:
		;
	}
	return mod_mw;
}

function GetThreePrimeMod_MW(theModName) {
	var mod_mw=0;
	switch (theModName) {
		case "BHQ-1":
			mod_mw+=554;
			break;
		case "Pho":
			mod_mw+=80;
			break;
		case "BHQ-2":
			mod_mw+=554;
			break;
		case "Biotin":
			mod_mw+=380;
			break;
		case "Biotin TEG":
			mod_mw+=570;
			break;
		case "C7-Aminolink":
			mod_mw+=209;
			break;
		case "CarboxydT":
			mod_mw+=440;
			break;
		case "Chol":
			mod_mw+=855;
			break;
		case "Cy3":
			mod_mw+=587;
			break;
		case "Cy5":
			mod_mw+=613;
			break;
		case "Dabcyl":
			mod_mw+=756;
			break;
		case "ddA":
			mod_mw+=297;
			break;
		case "ddC":
			mod_mw+=273;
			break;
		case "ddT":
			mod_mw+=288;
			break;
		case "Dig with C7 Spacer":
			mod_mw+=754;
			break;
		case "Fam":
			mod_mw+=598;
			break;
		case "Fluo":
			mod_mw+=598;
			break;
		case "Hex":
			mod_mw+=1216;
			break;
		case "Inosin":
			mod_mw+=314;
			break;
		case "Joe":
			mod_mw+=697;
			break;
		case "LCRed-640":
			mod_mw+=934;
			break;
		case "Rho ":
			mod_mw+=757;
			break;
		case "Rox":
			mod_mw+=1104;
			break;
		case "Rox ":
			mod_mw+=715;
			break;
		case "Tamra":
			mod_mw+=999;
			break;
		case "Tet":
			mod_mw+=1147;
			break;
		case "Thiol (C3)":
			mod_mw+=243;
			break;
		default:
			;
	}
	return mod_mw;
}

