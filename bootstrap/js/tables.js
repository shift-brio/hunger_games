$(document).ready(function(){	   
	var x = location.href.split("?");
	if (x.length > 1) {
		var href = location.href+"&download=true";
	}else{
		var href = location.href+"?download=true";
	}
	var power = '<div class="powered">'+
					'<div class="powered-by">powered by</div>'+
					'<div class="spen-pw"><a target="_blank" href="http://www.spendtrack.app/">SpendTrack</a></div>'+
				'</div>';
	var linker = '<a href="'+href+'" class="downloader" target="_blank">Download Spreadsheet</a>';
   var attr = $("body").attr("data-download");
   linker = attr == "false" ? "": linker;
	$("body").prepend(linker+power)
   /*$('.tooltipped').tooltip({delay: 50,html:true});*/
})