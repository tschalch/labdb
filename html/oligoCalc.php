<!---
	Version 1.0 04/15/97 wakibbe  Oligo Calc with Thermodynamic and two empirical Tm calculations released 
	Version 1.1 ???????? wakibbe  Added BLAST2 submission feature
	Version 1.2 ???????? wakibbe  Added fluorescent tag calculations, 'Swap Strands' feature.
	Version 1.3 ???????? wakibbe  Added hairpin formation feature
	Version 2.0 10/25/99 modified by Qing Cao change Blast2, link to the new nih site.
		(Old site is http://www.ncbi.nlm.nih.gov/cgi-bin/BLAST/nph-newblast, new site is:
			http://www.ncbi.nlm.nih.gov/blast/blast.cgi
	Version 2.01 05/2000 qing cao	modified by Qing Cao, Add self-complementarity calculation
	Version 2.01 08/2000 Qing Cao, disable hairpin calculation for IE. hairpin calc only for Netscape.
	Version 3.00 12/15/2000 wakibbe separated javascript objects into distinct files 
		'purified' objects and isolated form/object interactions for use in the classroom
	Version 3.01 12/19/2000 wakibbe bug fixes for IE
	Version 3.02 02/23/2002 wakibbe complement calculation bug described by
		Alexey Merz alexey@dartmouth.edu resolved 
	Version 3.03 02/09/2004 wakibbe Added compatibility features for Safari and Mozilla browsers  
	Version 3.04 02/12/2004 wakibbe Changed BLAST configuration yet again 
	Version 3.05 02/13/2004 wakibbe Changed MW to add a monophosphate vs subtract a pyrophosphate 
	Version 3.06 02/14/2004 wakibbe Added ssDNA/dsDNA/ssRNA/dsRNA options 
	Version 3.07 03/26/2004 wakibbe Moved MW calculation back to the old method, and added notes 
		in the MW formula area. Also changed links to the paper abstracts to new 
		NCBI urls, added RNA thermodynamics paper to references. 
	Version 3.08 07/01/2004 wakibbe Changed the fluorescent tags to accept 5' and 3' tags. Added many new fluorescent tags to the lists. Changed the way the fluorescent tags MW were calculated. Massive changes to the look of the calculator. I wonder if anyone will comment? Moved the concentrations of primer and salt up into the area for user input.<br>
		Version 3.09 07/18/2006 wakibbe Clarified discussion of when the various equations are actually used. <br>
-->

<html>
<Head>
<Title>Oligonucleotide Properties Calculator</Title>
<META NAME="KEYWORDS" CONTENT="Oligonucleotide  Oligo thermodynamic annealing temperature melting temperature Tm hybridization temperature calculator dsDNA ssDNA dsRNA ssRNA melting temperature oligo properties">
<META NAME="DESCRIPTION" CONTENT="Oligonucleotide  Oligo thermodynamic annealing temperature melting temperature Tm hybridization temperature calculator OligoCalc OligoCalculator dsDNA ssDNA dsRNA ssRNA oligo properties">
<META NAME="AUTHOR" CONTENT="Warren Kibbe, Qing Cao">

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
 <!--
// GLOBALS
var NS4plus        = (document.layers);
var IE4plus        = (document.all);
// added Safari check, WAKibbe 02/09/2004
var isSafari        = (navigator.appVersion.indexOf("Safari") != -1);
var browserVersion = parseFloat(navigator.appVersion);
var browserAppName=navigator.appName;
var supportLayers  = (NS4plus || IE4plus || isSafari);   
var isMac          = (navigator.appVersion.indexOf("Mac") != -1);
var isNetscape     = (browserAppName == "Netscape");
var isIE           = (navigator.appVersion.indexOf("MSIE") != -1);
var browserVersion = (navigator.appVersion.indexOf("MSIE 5" ) != -1)?5:browserVersion;
	supportLayers  = (supportLayers || isNetscape && browserVersion > 4);   
var isCompatible   = supportLayers;



var debug = 0;  // set to 1 for debugging output, 0 to turn it off
var primerWin;
var broadMatch=false;
var theOligo=0; //holder for theOligo object
var theComplement=0; //holder for theComplement object
var theVersion="3.09 (last modified by WAKibbe 07/18/2006)";
var MacStyleSheet="BODY,P,BR,TD,DIV,A {font-family: Arial,Helvetica,serif; font-size: 10pt;} \
	 PRE {font-family: Courier New,Courier,nonproportional,fixed;} \
	 H1 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 18pt;} \
	 H2 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 14pt;} \
	 H3 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 12pt;} \
	 TH {font-family: Arial,Helvetica,sans-serif; font-size: 11pt;} \
	 .bigger {font-size: 11pt; font-weight: bold;} \
	 .darkerblue { color: white; background-color: #333399; } \
	 .standardblue { color: black; background-color: #CCCCFF; } \
	 .lighterblue { color: black; background-color: #7da7d9; }";
var PCStyleSheet="BODY,P,BR,TD,DIV,A {font-family: Arial,Helvetica,serif; font-size: 9pt;} \
	PRE {font-family: Courier New,Courier,nonproportional,fixed;} \
	H1 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 14pt;} \
	H2 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 12pt;} \
	H3 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 10pt;} \
	TH {font-family: Arial,Helvetica,sans-serif; font-size: 9pt;} \
	.H1 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 14pt;} \
	.H2 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 12pt;} \
	.H3 {font-family: Comic Sans MS,Arial,Helvetica,sans-serif; font-size: 10pt;} \
	.bigger {font-size: 9pt; font-weight: bold;} \
	.darkerblue { color: white; background-color: #333399; } \
	.darksalmon { color: black; background-color: wheat; } \
	.lightsalmon { color: b; background-color: whitesmoke; } \
	 .standardblue { color: black; background-color: aliceblue; } \
	.lighterblue { color: black; background-color: lavender; }";
 
if (debug > 0) {
	alert('browserVersion='+browserVersion);
	alert('browserAppName='+browserAppName);
	alert('supportLayers='+supportLayers);
	alert('navigator.appVersion='+navigator.appVersion);
}
 /* provide blank defs for all browsers with javascript */
	if (!supportLayers) event = null;
	function Calculate(){}
	function ReCalculate(){}
	function DoNewFocus(){}
	function GetOligoValueFromCookie(){} 
	function RecalcMWConcAndOD(){} 
	function Blast2(){} 
	function SwapStrands(){}
	function calcPrimer(){} 

	if (browserVersion < 3) {
		alert('Sorry - your browser is too old or does not support javascript 1.1! Please upgrade!');
	} else if (browserVersion < 4) {
		document.write('<META HTTP-EQUIV=REFRESH CONTENT="0;URL=../biotools/OligoCalc1.2.html">');
	} else {
		document.write("<SCRIPT LANGUAGE='JavaScript1.1' SRC='OligoCalcUtils.js'><\/SCRIPT>");
		document.write("<SCRIPT LANGUAGE='JavaScript1.1' SRC='OligoCalc.js'><\/SCRIPT>");
		document.write("<SCRIPT LANGUAGE='JavaScript1.1' SRC='OligoCalcBlast.js'><\/SCRIPT>");
		document.write("<SCRIPT LANGUAGE='JavaScript1.1' SRC='OligoCalcObj.js'><\/SCRIPT>");
		if (browserVersion >= 4)
			document.write("<SCRIPT LANGUAGE='JavaScript1.2' SRC='OligoCalcCompare.js'><\/SCRIPT>");
	}
//-->
</SCRIPT>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
	document.write('<STYLE type="text/css">');
	document.write('<!--');
	if (isMac && browserVersion < 5.0) {
		document.write(MacStyleSheet);
	} else {
		document.write(PCStyleSheet);
	}
	document.write('-->');
	document.write('</STYLE>');
//-->
</SCRIPT>

</Head>
<BODY BGCOLOR='white' OnFocus="DoNewFocus(document.OligoCalc);">

<Center>
<H1>Oligonucleotide Properties Calculator</H1>
<Form name="OligoCalc" >

<table border="0" align=center cellpadding=1 cellspacing=1 class="darkerblue">
<tr><td>
<table border="0" align=center cellpadding=2 cellspacing=0 class="standardblue">
<tr class='darkerblue'>
<td colspan=2 align='Center' class='darkerblue'><div class=bigger>Enter Oligonucleotide
Sequence Below</div>
<i>OD and Molecular Weight calculations are for single-stranded
DNA or RNA</i></td>
</tr>

<tr>

<td colspan=2>
<TABLE border=0 align=center cellpadding=0 cellspacing=0>
<TR><TD colspan=2>
<TEXTAREA name="oligoBox" rows="5" cols="80" wrap=virtual
align=left onChange="ReCalculate(this.form)"><?php echo $_GET['sequence']?></TEXTAREA><br>
</td></TR>
<a href="#helpIUPAC">Nucleotide base codes</a>
<TR><td colspan=2 bgcolor="pink">
<font color="maroon">
Reverse Complement Strand(5' to 3') is:<BR>
<TEXTAREA name="complementBox" rows="2" cols="80" wrap=virtual
align=left onFocus="" readonly="readonly"></TEXTAREA><br>
</font>
</td></TR>
<TR><TD >

	<TABLE CELLPADDING="2" CELLSPACING="0">
	<TR><TD>5' modification (if any)</TD>
		<TD>3' modification (if any)</TD>
		<TD>select ss/ds and DNA or RNA</TD>
	</TR>
	<TR>
	<TD><select name="FivePrime" onChange="RecalcMWConcAndOD(this.form)">

		<option value="0" selected> 
		<option value="402" display="Amino dT (C2)">Amino dT (C2)</option>
		<option value="458" display="Amino dT (C6)">Amino dT (C6)</option>
		<option value="554" display="BHQ-1">BHQ-1</option>
		<option value="368" display="Bromo-dC">Bromo-dC</option>
		<option value="369" display="Bromo-dU">Bromo-dU</option>
		<option value="263" display="C12-Aminolink">C12-Aminolink</option>

		<option value="179" display="C6-Aminolink">C6-Aminolink </option>
		<option value="756" display="Chol">Chol</option>
		<option value="607" display="CY 3.5">CY 3.5</option>
		<option value="634" display="CY 5.5">CY 5.5</option>
		<option value="508" display="Cy3">Cy3</option>
		<option value="766" display="CY3 NHS Ester">CY3 NHS Ester</option>

		<option value="534" display="Cy5">Cy5</option>
		<option value="820" display="Cy5 Ester">Cy5 Ester</option>
		<option value="724" display="Dig">Dig</option>
		<option value="180" display="dspacer">dspacer</option>
		<option value="290" display="dU">dU</option>
		<option value="538" display="Fam">Fam </option>

		<option value="538" display="Fluo">Fluo</option>
		<option value="744.1" display="Hex">Hex</option>
		<option value="210" display="Hyd-1">Hyd-1</option>
		<option value="288" display="Hyd-2">Hyd-2</option>
		<option value="415" display="Iodo-dC">Iodo-dC</option>
		<option value="416" display="Iodo-dU">Iodo-dU</option>

		<option value="753" display="IRD700">IRD700</option>
		<option value="861" display="IRD800">IRD800</option>
		<option value="667" display="Joe">Joe</option>
		<option value="825" display="LCRed-610">LCRed-610 </option>
		<option value="904" display="LCRed-640">LCRed-640</option>
		<option value="534" display="LCRed-670">LCRed-670</option>

		<option value="634" display="LCRed-705">LCRed-705</option>
		<option value="303.21" display="Methylcytosin">Methylcytosin</option>
		<option value="574" display="Oregon Green 488 (HPLC)">Oregon Green 488 (HPLC)</option>
		<option value="81" display="P32">P32</option>
		<option value="82" display="P33">P33</option>
		<option value="80" display="Pho">Pho</option>

		<option value="420" display="Psoralen">Psoralen</option>
		<option value="453" display="Psoralen">Psoralen </option>
		<option value="727" display="Rho">Rho</option>
		<option value="536" display="Rho-Green">Rho-Green</option>
		<option value="698" display="Rox">Rox</option>
		<option value="264" display="Spacer-C12">Spacer-C12</option>

		<option value="138" display="Spacer-C3">Spacer-C3</option>
		<option value="180" display="Spacer-C6">Spacer-C6</option>
		<option value="576" display="Tamra">Tamra</option>
		<option value="675" display="Tet">Tet</option>
		<option value="882" display="Texas Red">Texas Red</option>
		<option value="328" display="Thiol">Thiol</option>

		<option value="154" display="Uni-Link">Uni-Link</option>
	</select>
	</TD>
	<TD><select name="ThreePrime" onChange="RecalcMWConcAndOD(this.form)">
		<option value="0" selected> 
		<option value="554" display="BHQ-1">BHQ-1</option>
		<option value="80" display="Pho">Pho</option>
		<option value="554" display="BHQ-2">BHQ-2</option>

		<option value="380" display="Biotin">Biotin</option>
		<option value="570" display="Biotin TEG">Biotin TEG</option>
		<option value="209" display="C7-Aminolink">C7-Aminolink</option>
		<option value="440" display="CarboxydT">CarboxydT</option>
		<option value="855" display="Chol">Chol</option>
		<option value="587" display="Cy3">Cy3</option>

		<option value="613" display="Cy5">Cy5</option>
		<option value="756" display="Dabcyl">Dabcyl</option>
		<option value="297" display="ddA">ddA</option>
		<option value="273" display="ddC">ddC</option>
		<option value="288" display="ddT">ddT</option>
		<option value="754" display="Dig with C7 Spacer">Dig with C7 Spacer</option>

		<option value="598" display="Fam">Fam</option>
		<option value="598" display="Fluo">Fluo</option>
		<option value="1216" display="Hex">Hex</option>
		<option value="314" display="Inosin">Inosin</option>
		<option value="697" display="Joe">Joe</option>
		<option value="934" display="LCRed-640">LCRed-640</option>

		<option value="757" display="Rho ">Rho </option>
		<option value="1104" display="Rox">Rox</option>
		<option value="715" display="Rox ">Rox </option>
		<option value="999" display="Tamra">Tamra</option>
		<option value="1147" display="Tet">Tet</option>
		<option value="243" display="Thiol (C3)">Thiol (C3)</option>

	</select>
	</TD>
	<TD><select name="deoxy" onChange="RecalcMWConcAndOD(this.form)">
		<option value=ssDNA selected> ssDNA
		<option value=ssRNA> ssRNA
		<option value=dsDNA> dsDNA
		<option value=dsRNA> dsRNA
	</select>
	</TD></TR>

	<tr><td COLSPAN="5">&nbsp; &nbsp; &nbsp; &nbsp; 
	<select name="selfComp" >
		<option value=1> 1
		<option value=2> 2
		<option value=3> 3
		<option value=4 > 4
		<option value=5 selected> 5
		<option value=6> 6
		<option value=7> 7
		<option value=8> 8
	</select>

	(Minimum base pairs required for single primer self-dimerization)
	</TD></TR>
	<tr><td colspan="5">&nbsp; &nbsp; &nbsp; &nbsp; 
	<select name="hairpin" >
		<option value=1> 1
		<option value=2> 2
		<option value=3 > 3
		<option value=4 selected> 4
		<option value=5> 5
		<option value=6> 6
		<option value=7> 7
		<option value=8> 8
	</select>

	(Minimum base pairs required for a hairpin)
	</td></tr>
	
	<tr><td colspan="5">&nbsp; &nbsp; &nbsp; &nbsp; 
	<input type="text" name="primerConcBox" size="7" value="50"
onChange="ReCalculate(this.form)">  n<u>M</u> Primer
	</td></tr>
	<tr><td colspan="5">&nbsp; &nbsp; &nbsp; &nbsp; 
	<input type="text" name="saltConcBox" size="7" value="50"
onChange="ReCalculate(this.form)"> m<u>M</u> Salt (Na<sup>+</sup>)
	</td></tr>

	</TABLE>
	</TD></TR><TR>
	<TD align="center">
	<TABLE BORDER=0>
	<TR><TD >
	<input type="image" value="Calculate" name="Calbutton" onclick="return ReCalculate(this.form)" src="img/Calculate.gif">
	</TD><TD >
	<input type="image" value="SWAP STRANDS" name="Swap"
	onClick="return SwapStrands(this.form)" src="img/SwapStrands.gif">
	</td><TD >

	<input type="image" value="BLAST2" name="BlastMe" onClick="return Blast2()"  src="img/BLAST.gif">
	</TD><TD>
	<input type="image" value="Check Self-Complementarity" name="Complement"
	onClick="return calcPrimer(this.form)"  src="img/selfcomplementarity.gif">
	</TD></TR>
	</TABLE>
</TD></TR>
</TABLE>

</td>
</tr>
<tr>
<th class='darkerblue'>

<div class=H2>Physical Constants</div>
</th><th class='darksalmon'>
<div class=h3>Melting Temperature (T<font size=-1><sub>M</sub></font>) Calculations</div>
</th>
</tr>

<tr><td class='lighterblue'>
Length: <Input type="text" name="lBox" size="6" onFocus="Disallow(this.form)"> bases<br>
GC content: <input type="text" name="gcBox" size="12" onFocus="Disallow(this.form)">%<br>

Molecular Weight: <input type="text" name="mwBox" size="25"
onFocus="Disallow(this.form)"><A HREF="#helpMW"><sup><font size=-1><B>4</B></font></sup></A><br>
1 ml of a sol'n with an Absorbance of
<input type="text" name="ODs" size="3" value="1"  onChange="RecalcMWConcAndOD(this.form)"> at
260 nm<br>
is <input type="text" name="micromolarConc" size="12" onFocus="Disallow(this.form)">
microMolar <A HREF="#helpOD"><sup><font size=-1><B>5</B></font></sup></A>
and contains <input name="micrograms" size="15" onFocus="Disallow(this.form)">
micrograms.<br>
</td>

<td class='lightsalmon'>

<A HREF="#helpbasic"><B>1</B></A> <Input name="tmBox" size="10"
onFocus="Disallow(this.form)"> &#176C (Basic)<br>
<A HREF="#helpadjusted"><B>2</B></A> <Input name="WAKtmBox" size="10"
onFocus="Disallow(this.form)"> &#176C (Salt Adjusted)<br>
<A HREF="#helpthermo"><B>3</B></A> <input type="TEXT" name="nTmBox"
size="10" onFocus="Disallow(this.form)"> &#176C (Nearest Neighbor)<br>

</td>
</tr>
<tr>
<th colspan=2 class='darkerblue'><A HREF="#helpthermo" class='darkerblue'><div class="h2">Thermodynamic
Constants</div></a>
Conditions:  1 <u>M</u> NaCl at 25&#176C at pH 7.</th>
</tr>

<tr  class='lighterblue'>
<td>RlnK <input name="RlogKBox" size="12" onFocus="Disallow(this.form)">
cal/(&#176K*mol)</td>

<td>deltaH <input name="deltaHBox" size="12" onFocus="Disallow(this.form)">
Kcal/mol</td>
</tr>
<tr class='lighterblue'>
<td>deltaG <input name="deltaGBox" size="12" onFocus="Disallow(this.form)">
Kcal/mol</td>
<td>deltaS <input name="deltaSBox" size="12" onFocus="Disallow(this.form)">
cal/(&#176K*mol)</td>
</tr>
</table>

</td>
</tr>
</table>

</Form>
To use this calculator, you must be using Netscape 3.0 or later<br>
or Internet Explorer version 3.0 or later, or another Javascript-capable
browser<br> Self-Complementarity requires a 4.x browser. IE 5.0, Safari, and Mozilla supported. <BR>
This page was written in Javascript.<br>
Extensively rewritten from 12/15/2000-12/19/2000 to isolate javascript Oligo object behaviors for teaching purposes.<br>
This page may be freely distributed for any educational or non-commercial use.<br>

Copyright Northwestern University, 1997-2006.<br>
<a href="OligoCalcHistory.html" target="history">Version history</a><p>
</Center>
<p>
<HR SIZE=4>

<h2>About the Calculations</h2>
<A NAME="helpthermo"></A>
<h3>Thermodynamic Calculations</h3>
The nearest neighbor and thermodynamic calculations are done essentially
as described by
Breslauer <i>et al.</i>,  (1986) <i>Proc. Nat. Acad. Sci.</i> <b>83</b>:3746-50 
(<a
href="http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?db=PubMed&cmd=Retrieve&list_uids=86233311&dopt=Citation">Abstract</a>) but using the values published by Sugimoto <I>et al.</I>, (1996) <I>Nucl. Acids Res.</I> <B>24</B>:4501-4505   (<a
href="http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?db=PubMed&cmd=Retrieve&list_uids=8948641&dopt=Abstract">Abstract</a>). RNA thermodynamic properties were taken from Xia T., SantaLucia J., Burkard M.E., Kierzek R., Schroeder S.J., Jiao X., Cox C., Turner D.H. (1998) <i>Biochemistry</i> <b>37</b>:14719-14735 (<a
href="http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?db=PubMed&cmd=Retrieve&list_uids=9778347&dopt=Abstract">Abstract</a>). 
This program assumes that the sequences are not symmetric and
contain at least one G or C.  The minimum length for the query sequence is
8.<p>

The melting temperature calculations are based on the simple thermodynamic
relationship
between entropy, enthalpy, free energy and temperature, where<p>
<CENTER>
<IMG SRC="img/thermoeq1.gif" ALIGN=MIDDLE
WIDTH="143" HEIGHT="21" BORDER="0" HSPACE="12" VSPACE="12"><p>
</CENTER>
The change in entropy (order or a measure of the randomness of the
oligonucleotide)
and enthalpy (heat released or absorbed by the oligonucleotide) are
directly calculated
by summing the values for nucleotide pairs obtained by Breslauer <i>et
al.</i>, <i>Proc. Nat. Acad. Sci.</i>
<b>83</b>, 3746-50, 1986. The relationship between the free energy and the
concentration
of reactants and products at equilibrium is given by<p>
<CENTER>
<IMG SRC="img/thermoeq2.gif" ALIGN=MIDDLE
BORDER="0" HSPACE="12" VSPACE="12"><p>
</CENTER>
Substituting the two equations gives us<p>

<CENTER>
<IMG SRC="img/thermoeq3.gif" ALIGN=MIDDLE
BORDER="0" HSPACE="12" VSPACE="12"><p>
</CENTER>
and solving for temperature T gives<p>
<CENTER>
<IMG SRC="img/thermoeq4.gif" ALIGN=MIDDLE
BORDER="0" HSPACE="12" VSPACE="12"><p>
</CENTER>
We can assume that the concentration of DNA and the concentration of the
DNA-primer complex are equal, so
this simplifies the equation considerably. It has been determined
empirically that there is a
5 (3.4 by Sugimoto et al.) kcal free energy change during the transition from single stranded to
B-form DNA. This is presumably a helix initiation energy. Finally, adding an adjustment for
salt gives the equation that the Oligo Calculator uses:
<CENTER>
<IMG SRC="img/thermoeq5.gif" ALIGN=MIDDLE
BORDER="0" HSPACE="12" VSPACE="12"><p>
</CENTER>
No adjustment constant for salt concentration is needed, since the various
parameters were determined
at 1 Molar NaCl, and the log of 1 is zero.<p>
<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="4" WIDTH="100%"
bgcolor="FFCECE">
<TR><TD>
<b>ASSUMPTIONS:</b><br>

The thermodynamic calculations assume that the annealing occurs at pH 7.0.
The melting
temperature (Tm) calculations assume the sequences are not symmetric and
contain at least one G or C. The oligonucleotide sequence should be at
least 8 bases
long to give reasonable Tms.<br>
</TD></TR>
</TABLE>
<HR SIZE=4>
<A NAME="helpbasic"></A>
<h3>Basic Melting Temperature (Tm) Calculations</h3>
The two standard approximation calculations are used. For sequences less
than 14 nucleotides
the formula is<p>
<ul>
<DFN><B>	Tm= (wA+xT) * 2 + (yG+zC) * 4</DFN></B><p>

where w,x,y,z are the number of the bases A,T,G,C in the sequence,
respectively.<br>

</ul>

For sequences longer than 13 nucleotides, the equation used is<p>

<ul>
<DFN><B>	Tm= 64.9 +41*(yG+zC-16.4)/(wA+xT+yG+zC)</DFN></B><br>
</ul>

<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="4" WIDTH="100%"
bgcolor="FFCECE">
<TR><TD>
<b>ASSUMPTIONS:</b><br>
<I>Both</I> equations assume that the annealing occurs under the
standard conditions of 50 n<u>M</u> primer,  50 m<u>M</u> Na<sup>+</sup>,
and pH 7.0.<br>

</TD></TR>
</TABLE>
<HR SIZE=4>

<A NAME="helpadjusted"></A>
<h3>Salt Adjusted Melting Temperature (Tm) Calculations</h3>
A variation on two standard approximation calculations are used. For
sequences less
than 14 nucleotides the same formula as the basic calculation is use, with
a salt
concentration adjustment<p>

<ul>
<DFN><B>	Tm= (wA+xT)*2 + (yG+zC)*4 - 16.6*log<sub>10</sub>(0.050) +
16.6*log<sub>10</sub>([Na<sup>+</sup>])</B></DFN><p>

where w,x,y,z are the number of the bases A,T,G,C in the sequence,
respectively.<br>
</ul>

The term
<DFN><B>16.6*log<sub>10</sub>([Na<sup>+</sup>])</B></DFN> adjusts the Tm
for changes in the salt concentration,
and the term <DFN><B>log<sub>10</sub>(0.050)</B></DFN> adjusts for the
salt adjustment at
50 m<u>M</u> Na<sup>+</sup>. Other monovalent and divalent salts will have
an effect
on the Tm of the oligonucleotide, but sodium ions are much more effective
at forming
salt bridges between DNA strands and therefore have the greatest effect in
stabilizing
double-stranded DNA.<br>

</ul>

For sequences longer than 13 nucleotides, the equation used is<p>

<ul>
<DFN><B>Tm= 100.5 + (41 * (yG+zC)/(wA+xT+yG+zC)) - (820/(wA+xT+yG+zC)) +
16.6*log<sub>10</sub>([Na<sup>+</sup>])</B></DFN><br>
</ul>

This equation is very accurate for sequences in the 18-25mer range. OligoCalc uses the above equation for all sequences longer than 13 nucleotides.<p>

The following equation is provided <i>only</i> for your reference. It is not actually used by OligoCalc. It is reportedly more accurate for longer sequences.<p>

<ul>
<DFN><B>Tm= 81.5 + (41 * (yG+zC)/(wA+xT+yG+zC)) - (500/(wA+xT+yG+zC)) +
16.6*log<sub>10</sub>([Na<sup>+</sup>]) - 0.62F</B></DFN><br>
</ul>

This equation is most accurate for sequences longer than 50 nucleotides.
It is valid for oligos longer than 50 nucleotides from pH 5 to 9.

Symbols and salt adjustment term as above, with the  term
<DFN><B>(41 * (yG + zC-16.4)/(wA + xT + yG + zC))</B></DFN>

adjusting for G/C content and the term <DFN><B>(500/(wA + xT + yG +
zC))</B></DFN>
adjusting for the length of the sequence, and  F is the percent concentration of formamide.<p>

For more information please see the reference:<br>

Howley, P.M; Israel, M.F.; Law, M-F.; and M.A. Martin "A rapid method for detecting and mapping homology between heterologous DNAs. Evaluation of polyomavirus genomes." 
<i>J. Biol. Chem.</i> <b>254</b>, 4876-4883, 1979.<p>

RNA melting temperatures<br>

<ul>
<DFN><B>Tm= 79.8 + 18.5*log<sub>10</sub>([Na<sup>+</sup>]) + (58.4 * (yG+zC)/(wA+xT+yG+zC))  
+  (11.8 * ((yG+zC)/(wA+xT+yG+zC))<sup>2</sup>) - (820/(wA+xT+yG+zC)) 
</B></DFN><br>
</ul>

Where yG+zC are the mole fractions of G and C in the oligo, 
L is the length of the shortest strand in the duplex.<br>


<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="4" WIDTH="100%"
bgcolor="FFCECE">

<TR><TD>
<b>ASSUMPTIONS:</b><br>
These equations assume that the annealing occurs under the
standard conditions of 50 n<u>M</u> primer and pH 7.0.<br>
</TD></TR>
</TABLE>
<p>
<HR SIZE=4>
<a name="helpMW"><p></a>
<h3>Molecular Weight Calculations</h3>
<p>
DNA Molecular Weight (for instance synthesized Oligonucleotides)<p>

Anhydrous Molecular Weight = (A<sub>n</sub> x 313.21) + (T<sub>n</sub> x 304.2) + (C<sub>n</sub> x 289.18) + (G<sub>n</sub> x 329.21) - 61.96<p>
A<sub>n</sub>, T<sub>n</sub>, C<sub>n</sub>, and G<sub>n</sub> are the number of each respective nucleotide within the polynucleotide. The subtraction of 61.96 gm/mole from the oligonucleotide molecular weight takes into account the removal of HPO<sub>2</sub> (63.98) and the addition of two hydrogens (2.02).<p>

Please note: this calculation works well for synthesized oligonucleotides. If you would like an accurate MW for restriction enzyme cut DNA, please use:<p>

Molecular Weight = (A<sub>n</sub> x 313.21) + (T<sub>n</sub> x 304.2) + (C<sub>n</sub> x 289.18) + (G<sub>n</sub> x 329.21) + 79.0<p>

The addition of 79.0 gm/mole to the oligonucleotide molecular weight takes into account the 5' monophosphate left by most restriction enzymes. 
No phosphate is present at the 5' end of strands made by primer extension, so no adjustment should be necessary.
<p>
RNA Molecular Weight (for instance from an RNA transcript)<br>
Molecular Weight = (A<sub>n</sub> x 329.21) + (U<sub>n</sub> x 306.17) + (C<sub>n</sub> x 305.18) + (G<sub>n</sub> x 345.21) + 159.0<br>

A<sub>n</sub>, U<sub>n</sub>, C<sub>n</sub>, and G<sub>n</sub> are the number of each respective nucleotide within the polynucleotide.
Addition of 159.0 gm/mole to the molecular weight takes into account the 5' triphosphate.
<p>
<HR SIZE=4>
<a name="helpOD"><p></a>
<h3>OD Calculations</h3>
Molar Absorptivity values in 1/(Moles cm)<p>

<TABLE CELLPADDING="4" CELLSPACING="0" BGCOLOR="lightblue">

<TR>
<TH>Residue</TH><TH>Moles<font size=-1><sup>-1</sup></font> cm<font size=-1><sup>-1</sup></font></TH><TH>A<sub>max</sub>(nm)</TH><TH>Molecular Weight<br>(after protecting groups are removed)</TH>
</TR>

<TR>
	<TD><A HREF="images/dAMP.gif" TARGET="monomer">Adenine</A> (dAMP, Na salt)</TD>	<TD>15200</TD><TD>259</TD><TD>313.21</TD>
</TR>
<TR>
	<TD><A HREF="images/dGMP.gif" TARGET="monomer">Guanine</A> (dGMP, Na salt)</TD>	<TD>12010</TD><TD>253</TD><TD>329.21</TD>

</TR>
<TR>
	<TD><A HREF="images/dCMP.gif" TARGET="monomer">Cytosine</A> (dCMP, Na salt)</TD>	<TD>7050</TD><TD>271</TD><TD>289.18</TD>
</TR>
<TR>
	<TD><A HREF="images/dTMP.gif" TARGET="monomer">Thymidine</A> (dTMP, Na salt)</TD>	<TD>8400</TD><TD>267</TD><TD>304.2</TD>

</TR>
<TR>
	<TD>dUradine</A> (dUMP, Na salt)</TD>	<TD>9800</TD><TD>-</TD><TD>290.169</TD>
</TR>
<TR>
	<TD>dInosine</A> (dUMP, Na salt)</TD>	<TD>-</TD><TD>-</TD><TD>314</TD>

</TR>

<TR>
	<TH colspan=3>RNA nucleotides</TH>
</TR>
<TR>
	<TD>Adenine</A> (AMP, Na salt)</TD>	<TD>15400</TD><TD>259</TD><TD>329.21</TD>

</TR>
<TR>
	<TD>Guanine</A> (GMP, Na salt)</TD>	<TD>13700</TD><TD>253</TD><TD>345.21</TD>
</TR>
<TR>
	<TD>Cytosine</A> (CMP, Na salt)</TD>	<TD>9000</TD><TD>271</TD><TD>305.18</TD>

</TR>
<TR>
	<TD>Uradine</A> (UMP, Na salt)</TD>	<TD>10000</TD><TD>262</TD><TD>306.2</TD>
</TR>
<TR>
	<TH colspan=3>Other nucleotides</TH>
</TR>

<TR>
	<TD><A HREF="images/FAM.gif" TARGET="monomer">6' FAM</A></TD>	<TD>20960</TD> <TD></TD> 	<TD>537.46</TD>
</TR>
<TR>
	<TD><A HREF="images/TET.gif" TARGET="monomer">TET</A></TD>	<TD>16255</TD> <TD></TD> 	<TD>675.24</TD>

</TR>
<TR>
	<TD><A HREF="images/HEX.gif" TARGET="monomer">HEX</A></TD>	<TD>31580</TD> <TD></TD> 	<TD>744.13</TD>
</TR>
<TR>
	<TD>TAMRA</TD>	<TD>31980</TD><TD></TD><TD>

</TR>
</TABLE>

<p>
Assume 1 OD of a standard 1ml solution, measured in a cuvette with a 1 cm
pathlength.<p>
<p>
<a name="FAM"><p></a>
<hr size=4>

<H3><A HREF="images/FAM.gif" TARGET="monomer">6-FAM:</a></H3>

<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0" BGCOLOR="lightblue">
<TR>
	<TD>Chemical name:</TD>	<TD><B>6-carboxyfluorescein</B></TD>

</TR>
<TR>
	<TD>Absorption wavelength maximum:</TD>	<TD><B>495 nm</B></TD>
</TR>
<TR>
	<TD>Emission wavelength maximum:</TD>	<TD><B>521 nm</B></TD>
</TR>
<TR>
	<TD>Molar Absorptivity at 260nm:</TD>	<TD><B>20960</B> Moles<font size=-1><sup>-1</sup></font> cm<font size=-1><sup>-1</sup></TD>

</TR>
</TABLE>
<p>
<a name="TET"><p></a>
<hr size=4>

<H3><A HREF="images/TET.gif" TARGET="monomer">TET:</a></H3>


<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0" BGCOLOR="lightblue">
<TR>
	<TD>Chemical name:</TD>	<TD><B>4, 7, 2',
7'-Tetrachloro-6-carboxyfluorescein</B></TD>

</TR>
<TR>
	<TD>Absorption wavelength maximum:</TD>	<TD><B>519 nm</B></TD>
</TR>
<TR>
	<TD>Emission wavelength maximum:</TD>	<TD><B>539 nm</B></TD>
</TR>
<TR>
	<TD>Molar Absorptivity at 260nm:</TD>	<TD><B>16255</B> Moles<font size=-1><sup>-1</sup></font> cm<font size=-1><sup>-1</sup></TD>

</TR>
</TABLE>

<p>
<a name="HEX"><p></a>
<hr size=4>

<H3><A HREF="images/HEX.gif" TARGET="monomer">HEX:</a></H3>

<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0" BGCOLOR="lightblue">
<TR>
	<TD>Chemical name:</TD>	<TD><B>4, 7, 2', 4', 5',
7'-Hexachloro-6-carboxyfluorescein</B></TD>

</TR>
<TR>
	<TD>Absorption wavelength maximum:</TD>	<TD><B>537 nm</B></TD>
</TR>
<TR>
	<TD>Emission wavelength maximum:</TD>	<TD><B>556 nm</B></TD>
</TR>
<TR>
	<TD>Molar Absorptivity at 260nm:</TD>	<TD><B>31580</B> Moles<font size=-1><sup>-1</sup></font> cm<font size=-1><sup>-1</sup></TD>

</TR>
</TABLE>

<p>
<a name="TAMRA"><p></a>
<hr size=4>

<H3>TAMRA:</H3>

<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0" BGCOLOR="lightblue">
<TR>
	<TD>Chemical name:</TD>	<TD><B>N, N, N',
N'-tetramethyl-6-carboxyrhodamine</B></TD>

</TR>
<TR>
	<TD>Absorption wavelength maximum:</TD>	<TD><B>555 nm</B></TD>
</TR>
<TR>
	<TD>Emission wavelength maximum:</TD>	<TD><B>580 nm</B></TD>
</TR>
<TR>
	<TD>Molar Absorptivity at 260nm:</TD>	<TD><B>31980</B> Moles<font size=-1><sup>-1</sup></font> cm<font size=-1><sup>-1</sup></TD>

</TR>
</TABLE>
<p>
<a name="helpIUPAC"><p></a>
<hr size=4>
<h3>Nucleotide base codes (IUPAC)<h3>
<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0" BGCOLOR="lightblue">
<TR><TH colspan=3>Symbol: nucleotide(s)</TH></TR>
<tr>
<td valign=top>
<ul>
	<TABLE>
	<TR valign=top><TH><B>A</b></TH><TD> adenine	</TD></TR>

	<TR valign=top><TH><b>C</b></TH><TD> cytosine</TD></TR>
	<TR valign=top><TH><b>G</b></TH><TD> guanine</TD></TR>
	<TR valign=top><TH><b>T</b></TH><TD> thymine in DNA;<br>uracil in RNA</TD></TR>
	<TR valign=top><TH><b>U</b></TH><TD> deoxy-Uracil in DNA;<br>uracil in RNA</TD></TR>

	<TR valign=top><TH><b>I</b></TH><TD> inosine</TD></TR>
	<TR valign=top><TH><b>N</b></TH><TD> A or C or G or T</TD></TR>
	</TABLE>
</ul>
</td>
<td valign=top>
	<TABLE>

	<TR valign=top><TH><b>M</b></TH><TD> A or C</TD></TR>
	<TR valign=top><TH><b>R</b></TH><TD> A or G</TD></TR>
	<TR valign=top><TH><b>W</b></TH><TD> A or T</TD></TR>
	<TR valign=top><TH><b>S</b></TH><TD> C or G</TD></TR>

	<TR valign=top><TH><b>Y</b></TH><TD> C or T</TD></TR>
	</TABLE>
</td>
<td valign=top>
	<TABLE>
	<TR valign=top><TH><b>K</b></TH><TD> G or T</TD></TR>
	<TR valign=top><TH><b>V</b></TH><TD> A or C or G; not T</TD></TR>

	<TR valign=top><TH><b>H</b></TH><TD> A or C or T; not G</TD></TR>
	<TR valign=top><TH><b>D</b></TH><TD> A or G or T; not C</TD></TR>
	<TR valign=top><TH><b>B</b></TH><TD> C or G or T; not A</TD></TR>
	</TABLE>

</td>
</tr></table>

<hr size=4>
<p>
Most recent version is available at URL: <A
HREF="http://www.basic.northwestern.edu/biotools/oligocalc.html">http://www.basic.northwestern.edu/biotools/oligocalc.html</a></p>

<HR SIZE=4>
<p>
The current version is the result of efforts by the following people:</p>

<p>Qing Cao, M.S. 
<br>
Research Computing<br>

Northwestern University Medical School<br>
Chicago, IL 60611</p>

<p>Warren A. Kibbe, Ph.D. 
<script>
document.writeln('<A HREF="mailto:wakibbe'+'@'+'northwestern.edu">e-mail</a>');
</script>
and <A HREF="http://directory.northwestern.edu/index.cgi?query=Warren+Kibbe" target="_blank">PH
entry</a>.<br>
Research Computing<br>
Northwestern University Medical School<br>
Chicago, IL 60611</p>

<p>Original code by Eugen Buehler</A><br>
Research Support Facilities<br>
Department of Molecular Genetics and Biochemistry<br>
University of Pittsburgh School of Medicine</p>

<p>Monomer structures and molecular weights provided by Bob Somers, Ph.D.</A>
<script>
document.writeln('<A HREF="mailto:bob'+'@'+'glenres.com">e-mail</a>');
</script>
at 
<A HREF="http://www.glenres.com/">Glen Research Corporation</A></p>

<p>Uppercase/lowercase strand complementation problem described by Alexey Merz 
<script>
document.writeln('<A HREF="mailto:alexey'+'@'+'dartmouth.edu">e-mail</a>');
</script>
</p>

<p>The RNA calculations and functions were requested by Suzanne Kennedy, Ph.D.
<script>
document.writeln('<A HREF="mailto:Suzanne.Kennedy'+'@'+'qiagen.com">e-mail</a>');
</script>
at <a href="http://www.qiagen.com">Qiagen</a>
</p>

<p>The flourescent tags and tagging options were requested by and the data provided by 
Florian Preitauer 
<script>
document.writeln('<A HREF="mailto:f.preitauer'+'@'+'metabion.com">e-mail</a>');
</script>

and Regina Bichlmaier, Ph.D.
<script>
document.writeln('<A HREF="mailto:regina'+'@'+'metabion.com">e-mail</a>');
</script>
at <a href="http://www.metabion.com">metabion GmbH</a>
</p>
<p>
<SCRIPT LANGUAGE="JavaScript">
<!-- begin script
	GetOligoValueFromCookie(document.OligoCalc);
	Calculate(document.OligoCalc);
	document.writeln(" <FONT COLOR='indigo'>Oligo Calculator version " + theVersion + "</font><p>");
// end script -->
</SCRIPT>
</Body>
</html>
