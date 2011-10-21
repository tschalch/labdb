//Written by Paul Stothard, University of Alberta, Canada
//Adapted by Thomas Schalch, Cold Spring Harbor Lab, USA

function findOrfs (seq, startPos, strand, theLength)	{
	var geneticCode = getGeneticCodeString("transl_table=1");
	geneticCode = geneticCode.split(/,/);
	var startCodons = "a[tu]g";
	var dnaSequence = getSequenceFromFasta(seq);
	verifyDna (dnaSequence);
	dnaSequence = removeNonDna(dnaSequence);
	var i = 0;
	var k = 1;
	var codon = "";
	var foundStart = false;
	var geneticCodeMatchExp = getGeneticCodeMatchExp (geneticCode);
	var geneticCodeMatchResult = getGeneticCodeMatchResult (geneticCode);
	var proteinLength = 0;
	var foundStop = false;
	var orfs = [];

	var geneticCodeMatchExpStop;
	for (var j = 0; j < geneticCodeMatchExp.length; j++)	{
		if (geneticCodeMatchResult[j] == "*")	{
			geneticCodeMatchExpStop = geneticCodeMatchExp[j];
			break;
		}
	}

	var startRe = new RegExp (startCodons, "i");
	var sequenceToTranslate;

	startPos = parseInt(startPos);
	var rf = startPos + 1;
	theLength = parseInt(theLength);

	if (strand == "reverse")	{
		dnaSequence = reverse(complement(dnaSequence));
	}
	while (i <= dnaSequence.length - 3){
		for (var i = startPos; i <= dnaSequence.length - 3; i = i + 3)	{
			codon = dnaSequence.substring(i,(i+3));
			if ((startCodons != "any") && (foundStart == false) && (codon.search(startRe) == -1))	{
				break;
			}
			foundStart = true;
			
			if (codon.search(geneticCodeMatchExpStop) != -1) {
				foundStop = true;
			}
			
			proteinLength++;

			if ((foundStop) && (proteinLength < theLength))	{
				break;
			}
			if (((foundStop) && (proteinLength >= theLength)) || ((i >= dnaSequence.length - 5) && (proteinLength >= theLength)))	{
				sequenceToTranslate = dnaSequence.substring((startPos),i+3);
				if (strand == "direct"){
					newOrf = [startPos - 1, i + 3 - 1, "", 1, -1, 0.015, {"fill": "green", "stroke-width": 1,
						  "stroke-linecap": "butt", cursor:"pointer"}, {fill: "green", "font": '10px "Verdana"'}];
				} else {
					newOrf = [dnaSequence.length-startPos-1, dnaSequence.length-(i+3)-1,
						  "", 0, -1, 0.015, {"fill": "green", "stroke-width": 1,
						  "stroke-linecap": "butt", cursor:"pointer"}, {fill: "green", "font": '10px "Verdana"'}];
				}
				orfs.push(newOrf);
				orfNr++;
				break;
			}
		}
		startPos = i + 3;
		i = startPos;
		foundStart = false;
	  	foundStop = false;
		proteinLength = 0;
	}
	return orfs;
}
