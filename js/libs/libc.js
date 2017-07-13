(function($){
		var lineInTabs = function() {
			var currentTab = location.hash;
			if ( currentTab != '' ) {
				currentTab = "#" + currentTab.substr(2);
			} else {
				currentTab = "#tab-1";
			}
		
			$('.lif-tab')
				.hide();
				
			$(currentTab).show();
			
			$('.lif-title')
				.addClass('nav-tab-wrapper')
				.addClass('lif-active')
				.find('span')
				.remove();
	
			$('[href="' + currentTab + '"]')
				.addClass('nav-tab-active');
			$('.lif-title .nav-tab')
				.removeClass('hidden')
				.click( function(e) {
					$this = $(this);
					if ( $this.hasClass('nav-tab-active') ) {
						return false;
					} else {
						$('.li-settings').remove();
						$('.lif-tab').hide();
						$($this.attr('href')).show();
						$('.nav-tab').removeClass('nav-tab-active');
						$this.addClass('nav-tab-active');
						var hash = $this.attr('href').substr(1);
						var hash = '_' + hash;
						window.location.hash = hash;
						
					}
					
					return false;
				});;
		}
		
	
		if ( $('.lif-title').hasClass('lif-active') ) {
			
		} else {
			lineInTabs();
		}
	
	
})(jQuery);

jQuery(document).ready(function($) {
	var id = false;
	var sendToEditor = false;
	var set = false;

	
	jQuery('.li-upload').click(function() {
		id = jQuery(this).attr('id').substr(4);
		sendToEditor = "set";
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true#true');
		return false;
	});
	window.original_send_to_editor = window.send_to_editor;

	window.send_to_editor = function(html) {
		if ( sendToEditor ) {
			imgurl = jQuery('img',html).attr('src');
			jQuery('#' + id).val(imgurl);
			tb_remove();
		} else {
			window.original_send_to_editor(html)
		}
		sendToEditor = false;
	}
});
