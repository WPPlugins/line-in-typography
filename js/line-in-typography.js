/**
 *	Creates typographic overlays for testing line heights and grids
 *	
 *	@version 0.2.1
 *	
 *	@author	Simon Fairbairn
 *
 */


(function($) {
	
	$.fn.LineInTypography = function(options) {
		// Extend our default options with those provided.
		// Note that the first arg to extend is an empty object -
		// this is to keep from overriding our "defaults" object.
		var opts = $.extend({}, $.fn.LineInTypography.defaults, options);		
		opts.selector = $(this).selector;
		opts.lineHeightContainerHeight = $(opts.lineHeightContainer).height();
		if ( 0 ==  opts.lineHeight ) {
		 	opts.lineHeight = Math.round(parseFloat($(opts.lineHeightContainer).css('line-height')));
		 }
				
		if ( $('#lit-button').length < 1 ) {
			loadButtons(opts);
			// $('head').append("<link href='" + opts.pluginUrl + "css/style.css' rel='stylesheet' />");
		}
		
		
			
		return this.each( function() {
			$this = $(this);
			var width = $this.width();
			var maxWidth = $this.css('max-width');
			var gridClass = false;
			if ( opts.gridState == 'on' ) {
				if (opts.grid == '12' ) {
					gridClass = 'gr960-12';
				} else {
					gridClass = 'gr960-16';			
				}
			} 	
			
			$this
				.addClass('columns')
				.addClass(gridClass);
			
			
		});
		
	}
	
	// Create the control panel. This should only be fired once.
	function loadButtons(opts) {
		var buttonDiv = $('<div />')
							.attr('id', 'lit-buttons');							
		var hideLink = $('<a />')
							.attr('href', '#lit-hide')
							.addClass('lit-hide')
							.text('Hide')
							.click( function() {
								if ( $(this).hasClass('show') ) {
									var thisText = 'hide';
									var currentText = 'show';
									$('#lit-buttons').animate({width: 400}, 500);
									$('.lit-controls').show();
								} else {
									var thisText = 'show';
									var currentText = 'hide';
						
									$('#lit-buttons').animate({width: 25}, 500);
									$('.lit-controls').hide();			
								}
								$(this).text(thisText).removeClass(currentText).addClass(thisText);
								return false;
							});
		var buttonControls = $('<div />')
								.addClass('lit-controls');
								
		var buttonImages = $('<input />')
								.attr('type', 'button')
								.attr('name','lit-switch-images')
								.attr('id', 'lit-switch-images')
								.val('Toggle Images')
								.click( function() {
									if ( $(this).hasClass('on') ) {
										$(this).removeClass('on');
										$(opts.lineHeightContainer).removeClass('switchImages');
									} else {
										$(this).addClass('on');
										$(opts.lineHeightContainer).addClass('switchImages');		
									}
								});
		var buttonLines = $('<input />')
								.attr('type', 'button')
								.attr('name','lit-switch-lines')
								.attr('id', 'lit-switch-lines')
								.val('Toggle Lines')
								.click( function() {
									$('#lit-vertical-rhythm').toggle();
									$('.test-p').toggle();
								});
		var buttonGrid = $('<input />')
								.attr('type', 'button')
								.attr('name','lit-switch-grid')
								.attr('id', 'lit-switch-grid')
								.val('Toggle Grid')
								.click( function() {
									if ( $(this).hasClass('on') ) {
										$(this).removeClass('on');
										$('.columns').each( function() {
											$(this).removeClass('gr960-12').removeClass('gr960-16')
										});
									} else {
										$(this).addClass('on');
										
										$('.columns').each( function() {
											$(this).addClass('gr960-' + $('.line-height:checked').val() );
										});
									}
								
								});
								
								;
		var labelRadio12 = $('<label>')
								.attr('for', 'lit-960-12')
								.text('Column Grid 1 (default 12)');
		var buttonRadio12 = $('<input />')
								.attr('type', 'radio')
								.attr('name','lit-960')
								.attr('id', 'lit-960-12')
								.val('12')
								.addClass('line-height')
								.click(function() {changeLineHeight(this)});
								
		var labelRadio16 = $('<label>')
								.attr('for', 'lit-960-16')
								.text('Column Grid 2 (default 16)');
								
		var buttonRadio16 = $('<input />')
								.attr('type', 'radio')
								.attr('name','lit-960')
								.attr('id', 'lit-960-16')
								.val('16')
								.addClass('line-height')
								.click(function() {changeLineHeight(this)});
		
		
		var testP = $('<div />').addClass('test-p').html(opts.testHtml);
		
		if ( opts.lineState != 'on' ) {
			testP.hide();
		}
		
		if ( opts.grid == '12' ) {
			buttonRadio12.attr('checked', 'checked');
			
		} else {
			buttonRadio16.attr('checked', 'checked');
		}


		buttonControls
			.append(buttonImages)
			.append(buttonLines)
			.append(buttonGrid)
			.append(labelRadio12)
			.append(buttonRadio12)
			.append('<br />')
			.append(labelRadio16)
			.append(buttonRadio16);								
								
		buttonDiv.append(hideLink).append(buttonControls);
		var verticalRhythmDiv = $('<div />')
								.attr('id', 'lit-vertical-rhythm')
								.css({
									'background-image' : 'url("' + opts.pluginUrl + 'css/img/vertical-rhythm-' + opts.lineHeight + 'px.png")', 
									'background-position' : '0 ' + opts.backgroundOffset + 'px', 
									'height' : $('body').height() 
								});
								
		if( opts.lineState != 'on' ) {
			verticalRhythmDiv.hide();
		}

		if ( 'true' == opts.showTestHtml ) {
			$(opts.lineHeightContainer)
				.prepend( testP)
				.append( buttonDiv )
				.append( verticalRhythmDiv );
		} else {
			$(opts.lineHeightContainer)
				.append( buttonDiv )
				.append( verticalRhythmDiv );
		}
	}
	
	function changeLineHeight(object) {
		var $this = $(object);
		$('.columns').each( function() {
			$(this)
				.removeClass('gr960-12')
				.removeClass('gr960-16');
		});
		$('.columns').each( function() {
			$(this).addClass('gr960-' + $this.val() )
		});
		$('#lit-switch-grid').addClass('on');
	}
	
	
	// Default options. Can be overriden
	$.fn.LineInTypography.defaults = {
		lineHeightContainer	: 'body',
		backgroundOffset	: 0,
		grid				: '12',
		gridState			: 'off',
		lineState			: 'on',
		testHtml			: "<p>Test paragraph to show line height</p>",
		pluginUrl			: "js/mylibs/",
		showTestHtml		: 'true',
		lineHeight			: 0
	};
	
})(jQuery);

jQuery(document).ready( function($) {
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
	
	
});

