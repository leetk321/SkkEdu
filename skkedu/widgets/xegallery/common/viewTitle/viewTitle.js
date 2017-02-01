/*
	
	StreamWave Flash Title Widget _ Javascript for Embedding

	2008. 10. 29.
	scripted by MinsangK (http://minsangk.com)
	
*/


// main function
function showTitle(srcUrl, srcFilename, width, height, titleStr, linkStr, hAlign, tColor)
{	
	var str = "<embed id=\"viewTitle\" name=\"viewTitle\" type=\"application/x-shockwave-flash\" src=\"" +
				srcUrl + srcFilename + "\" width=\"" + width + "\" height=\"" + height + "\" wmode=\"transparent\"" + " allowScriptAccess=\"always\"" + "FlashVars=\"" +
				"articleTitle="+ pEncode(titleStr) +"&linkTo=" + linkStr + "&hAlign=" + hAlign + "&tColor="+ tColor + "\"/>";

//	window.alert(str);
	document.write(str);
}

// percent-encoding
function pEncode(str)
{
	str = str.replace(/&amp;/g, "%26");
	//str = str.replace(/[&]/g, "%26");
	str = str.replace(/[+]/g, "%2b");
	
	return str;
}
