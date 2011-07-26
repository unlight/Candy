jQuery(function() {
	//var WebRoot = gdn.definition('WebRoot');
	//var Url = gdn.combinePaths(WebRoot, 'candy/contenttreenodename');
	
/*	$('#Form_ContentID').autocomplete(Url, {
		multiple: false,
		delay: 400
	});*/
/*		.result(function(dummy, data){
			$(this).val(data[1]);
			$("#Form_Address").val(data[2]);
			$("#Form_Code").val(data[3]);
			$("#Form_UserID").val(data[4]);
		});*/
	
	if ($.fn.textpandable) {
		$("#Form_Body").textpandable({speed:0, maxRows:35});
	}
	var LastTimeStamp = 0;	
	var SlugHandler = function() {
		var Text = $('#Form_Title').val();
		$.post(gdn.url('candy/slug'), {Text:Text}, function(text){
			$("#Form_SectionURI").val(text);
		});
	}
	
	if ($('#Form_Title').val() == '') {
		$("#Form_Title").blur(SlugHandler);
		$("#Form_Title").bind('keypress', function(e){
			var Enabled = $('#Form_CreateSection')[0].checked;
			var ElapsedTime = e.timeStamp - LastTimeStamp; // in miliseconds
			if (Enabled && ElapsedTime > 1000) {
				LastTimeStamp = e.timeStamp;
				SlugHandler();
			}
		});		
	}
	


	
});