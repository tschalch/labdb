function Blast2(){
	if (document.OligoCalc.oligoBox.value.length < 8) {
		alert("Please enter at least 8 bases before trying a BLAST search!");
		return false;
	}
	blastwindow=window.open("","BLAST2","toolbar=no,directories=no,status=yes,location=yes,scrollbars=yes,resizable=1");
	blastwindow.document.writeln("\<HTML\>");
	blastwindow.document.writeln("\<BODY\>");
	blastwindow.document.writeln('\<FORM NAME="blastme" ACTION="http://www.ncbi.nlm.nih.gov/blast/Blast.cgi" METHOD="POST"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "QUERY" value="'+document.OligoCalc.oligoBox.value+'"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "QUERY_FROM" value=" "\>');
	blastwindow.document.writeln('\<input type="hidden" name = "QUERY_TO" value=" "\>');
	blastwindow.document.writeln('\<input type="hidden" name = "ENTREZ_QUERY" value="All organisms"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "DATABASE" value="nr"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "EXPECT" value="100"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "WORD_SIZE" value="11"\>');
	
	blastwindow.document.writeln('\<input type="hidden" name = "OTHER_ADVANCED" value=" "\>');
	blastwindow.document.writeln('\<input type="hidden" name = "SHOW_OVERVIEW" value="1"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "SHOW_LINKOUT" value="1"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "NCBI_GI" value="1"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "FORMAT_OBJECT" value="Alignment"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "FORMAT_TYPE" value="HTML"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "DESCRIPTIONS" value="100"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "ALIGNMENTS" value="50"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "ALIGNMENT_VIEW" value="Pairwise"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "FORMAT_ENTREZ_QUERY" value="All organisms"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "EXPECT_LOW" value=" "\>');
	blastwindow.document.writeln('\<input type="hidden" name = "EXPECT_HIGH" value=" "\>');
	blastwindow.document.writeln('\<input type="hidden" name = "LAYOUT" value="TwoWindows"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "FORMAT_BLOCK_ON_RESPAGE" value="None"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "AUTO_FORMAT" value="Semiauto"\>');

	blastwindow.document.writeln('\<input type="hidden" name = "PROGRAM" value="blastn"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "SET_DEFAULTS" value="Yes"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "CLIENT" value="web"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "SERVICE" value="plain"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "PAGE" value="Nucleotides"\>');
	blastwindow.document.writeln('\<input type="hidden" name = "CMD" value="Put"\>');
	
	if (debug) {
		blastwindow.document.writeln('FORM NAME="blastme" ACTION="http://www.ncbi.nlm.nih.gov/blast/Blast.cgi" enctype="application/x-www-form-urlencoded" METHOD="POST"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "QUERY" value="'+document.OligoCalc.oligoBox.value+'"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "QUERY_FROM" value=" "\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "QUERY_TO" value=" "\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "ENTREZ_QUERY" value="All organisms"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "DATABASE" value="nr"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "EXPECT" value="100"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "WORD_SIZE" value="11"\<br\>');
		
		blastwindow.document.writeln('input type="hidden" name = "OTHER_ADVANCED" value=" "\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "SHOW_OVERVIEW" value="1"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "SHOW_LINKOUT" value="1"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "NCBI_GI" value="1"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "FORMAT_OBJECT" value="Alignment"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "FORMAT_TYPE" value="HTML"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "DESCRIPTIONS" value="100"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "ALIGNMENTS" value="50"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "ALIGNMENT_VIEW" value="Pairwise"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "FORMAT_ENTREZ_QUERY" value="All organisms"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "EXPECT_LOW" value=" "\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "EXPECT_HIGH" value=" "\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "LAYOUT" value="TwoWindows"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "FORMAT_BLOCK_ON_RESPAGE" value="None"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "AUTO_FORMAT" value="Semiauto"\<br\>');
	
		blastwindow.document.writeln('input type="hidden" name = "PROGRAM" value="blastn"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "SET_DEFAULTS" value="Yes"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "CLIENT" value="web"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "SERVICE" value="plain"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "PAGE" value="Nucleotides"\<br\>');
		blastwindow.document.writeln('input type="hidden" name = "CMD" value="Put"\<br\>');
		blastwindow.document.writeln('\<input type="submit" name = "Submit" value="DoIt"\>');
	
	}
	blastwindow.document.writeln('\</FORM\>');
	blastwindow.document.writeln("\</BODY\>");
	blastwindow.document.writeln("\</HTML\>");
	blastwindow.document.close();	// without this you never get anywhere!
	if (!debug) {
		blastwindow.document.forms[0].submit();
	}
	return false;
}
