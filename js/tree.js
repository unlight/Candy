$(function(){
	//var $popups = $('.Tree .Options a').not('.PopupConfirm');
	$('.Tree .Options a:not(.PopupConfirm)').popup({
		afterSuccess: function(json, sender) {
            $("#Content").load(gdn.url('candy/section/tree?DeliveryType=VIEW'));
        }
    });
	
	var $divs = $('.Tree ul li > div');
	$divs.mouseenter(function(e){
		var $row = $(e.target);
		if (!$row.is('div')) $row = $row.parents('div').first();
		$divs.removeClass('Hovered');
		$row.addClass('Hovered');
	});
	
	
	
	
	
	
	
	// hold, this is too slooow
	if ($.ui && $.ui.nestedSortable)
		$('.Tree').nestedSortable({
		disableNesting: 'NoNesting',
		errorClass: 'SortableError',
		forcePlaceholderSize: true,
		handle: 'a',
		items: 'li',
		opacity: .6,
		placeholder: 'Placeholder',
		tabSize: 25,
		tolerance: 'pointer',
		toleranceElement: '> a',
		update: function(container, p) {
			var TreeArray = $('.Tree').nestedSortable('toArray', {startDepthCount: 0});
			console.log('update', TreeArray);
			console.log('update', container, p);
/*            $.post(
				gdn.url('/vanilla/settings/sortcategories/'),
				{
				'TreeArray': $('ol.Sortable').nestedSortable('toArray', {startDepthCount: 0}),
				'DeliveryType': 'VIEW',
				'TransientKey': gdn.definition('TransientKey')
				},
				function(response) {
				if (response != 'TRUE') {
					alert("Oops - Didn't save order properly");
				}
				}
			);*/
		}
		});
});