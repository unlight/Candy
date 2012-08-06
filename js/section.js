jQuery(function($){
	// Mask click handler
	var PowerCheck = function() {
		var n = parseInt(this.value, 10);
		if (n & (n-1)) this.value = '';
	};

	var AddDescriptionClick = function() {
		var MaskForm = $('#Mask_Form');
		var NewMask = $("li:last", MaskForm).clone().show();
		$(':input', NewMask).val('');
		$(MaskForm).find('ul:first').append(NewMask);
		var Button = $('a.AddDescription', MaskForm).last();
		$('a.AddDescription', MaskForm).not(Button);
		$('a.AddDescription', MaskForm).not(Button).remove();
	};
	
	$('a.AddDescription').live('click', AddDescriptionClick);
	
});