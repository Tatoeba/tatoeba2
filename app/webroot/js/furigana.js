/* ruby characters */
function furigana(){
	// reg = new RegExp("^(([^\\[ ])*)\\[(([^\\]])*)\\]", "gi");
	// reg2 = new RegExp(" (([^\\[ ])*)\\[(([^\\]])*)\\]", "gi");
	// div = document.getElementById("conversion");
	// div.innerHTML = div.innerHTML.replace(reg,"<ruby><rb>$1</rb><rp>[</rp><rt>$3</rt><rp>]</rp></ruby>");
	// div.innerHTML = div.innerHTML.replace(reg2,"<ruby><rb>$1</rb><rp>[</rp><rt>$3</rt><rp>]</rp></ruby>");
	
	reg = new RegExp("^(([^\\[ ])*)\\[(([^\\]])*)\\]", "gi");
	reg2 = new RegExp(" (([^\\[ ])*)\\[(([^\\]])*)\\]", "gi");
	div = document.getElementById("conversion");
	div.innerHTML = div.innerHTML.replace(reg,"<ruby><rb>$1</rb><rp>[</rp><rt>$3</rt><rp>]</rp></ruby>");
	div.innerHTML = div.innerHTML.replace(reg2,"<ruby><rb>$1</rb><rp>[</rp><rt>$3</rt><rp>]</rp></ruby>");
}