jQuery(function() {

	if ($.fn.textpandable) {
		$("#Form_Body").textpandable({speed:0, maxRows:35});
	}
	var LastTimeStamp = 0;	
	var SlugHandler = function() {
		var Enabled = $('#Form_CreateSection')[0].checked;
		if (Enabled) {
			var Text = $('#Form_Title').val();
			$.post(gdn.url('candy/slug'), {Text:Text}, function(text){
				$("#Form_SectionURI").val(text);
			});
		}
	}
	
	if ($('#Form_Title').val() == '') {
		$("#Form_Title").blur(SlugHandler);
		$("#Form_Title").bind('keypress', function(e){
			
			var ElapsedTime = e.timeStamp - LastTimeStamp; // in miliseconds
			if (ElapsedTime > 1000) {
				LastTimeStamp = e.timeStamp;
				SlugHandler();
			}
		});		
	}
	
	$('.ToggleButton').click(function(){
		var bRemoveSelf = $(this).hasClass('RemoveSelf');
		var classname = $.trim($(this).attr('class').replace('ToggleButton', '')).split(' ')[0];
		var items = $(this).parents('ul').find('.'+classname).not(this);
		var func = (items.is(':visible')) ? 'fadeOut' : 'fadeIn';
		if (bRemoveSelf) $(this).fadeOut('fast');
		//console.log(bRemoveSelf, items, '"'+classname+'"', items.is(':visible'));
		items[func]('fast');
		return false;
	});

	
});