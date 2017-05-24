/**
 * WoWRoster.net WoWRoster
 *
 * Theme Javascript file
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @version    SVN: $Id: theme.js 2493 2012-05-27 06:01:28Z c.treyce@gmail.com $
 * @link       http://www.wowroster.net
 */

var ol_width=220;
var ol_offsetx=15;
var ol_offsety=15;
var ol_hauto=1;
var ol_vauto=1;
var ol_fgclass='overlib_fg';
var ol_bgclass='overlib_border';
var ol_textfontclass='overlib_maintext';
var ol_captionfontclass='overlib_captiontext';
var ol_closefontclass='overlib_closetext';

$(function() {
  // Apply jQuery UI button styles on EVERYTHING
  //$('button, input:submit, input:reset, input:button, .input').button();

 

  // Create button sets for radio and checkbox groups
  //$('.radioset').buttonset();
  //$('.checkset').buttonset();

  // Add a style to the text input and file select boxes
 // $('input[type=file]').addClass('ui-widget');

  // Style select boxes
 // $('select:not([multiple],[class="no-style"])').selectmenu();

  // Apply the multiselect dropdown style
  $('select.multiselect').SumoSelect();//.chosen();

  /*multiselect({
    selectedList: 4,
    selectedText: '# of # selected'
  });
  */

  // Slide down the notification box
  $('#notify .close').hover(
    function() { $(this).addClass('ui-state-hover'); },
    function() { $(this).removeClass('ui-state-hover'); }
  )
  .click(function() { $(this).parent().slideUp('slow'); });


  // Main menu buttons and panels
  $('#top_nav > a').click(function(){
    var menu_div = $(this).attr('href');

    if($(this).hasClass('active') == false) {
      $('#menu-buttons > div.menu-scope-panel').fadeOut();
      $('#top_nav > a').removeClass('active');
      $(this).addClass('active');
      $(menu_div).fadeIn();
    }
    else {
      $(this).removeClass('active');
      $(menu_div).fadeOut();
    }

    return false;
  });
  $('.mini-list-click').click(function(){
    $('.mini-list').fadeOut();
    $('#top_nav > a').removeClass('active');
  });
   // Style checkboxes not in a checkset

  $('input:checkbox').each(function() {
    if (! $(this).parent().hasClass('checkset')) {
      $(this).checkbox();
    };
  });

  $('input:checked').parent('.btn').addClass('active');
	$(".btn").click(function(){
		clk = $(this).children(":input").attr("id");
		sib = $(this).siblings().children(":input").attr("id");
		$(this).siblings().children(":input").each(function() {
			$(this).attr("checked", false);
		});
		$("input#"+clk+"").attr("checked", "checked"); 
	});
	
	$('#region_select').change(
    function(){
         $(this).closest('form').trigger('submit');
         /* or:
         $('#formElementId').trigger('submit');
            or:
         $('#formElementId').submit();
         */
    });
	$('body')
	.on('mouseenter', '[data-toggle="dropdown"].dropdown-hover', function()
	{ 
		if (!$(this).parent('.dropdown').is('.open'))
			$(this).click();
	});
	$('.navbar.main')
	.add('#menu-top')
	.on('mouseleave', function(){
		$(this).find('.dropdown.open').find('> [data-toggle="dropdown"]').click();
	});
});

jQuery(document).on('click', '[data-toggle="collapse"]', function (e) {
	//e.preventDefault();
	console.log('bob '+jQuery(this));
	if (jQuery(this).attr('data-target')){
		e.preventDefault();
		var target = jQuery(this).attr('data-target');
		console.log('im a target'+target);
		jQuery(target).toggle( "slow" );
	}
});

jQuery(document).on('click', '[data-dismiss="alert"]', function (e) {
	//e.preventDefault();
	console.log('bob '+jQuery(this));
	if (jQuery(this).attr('data-dismiss')){
		e.preventDefault();
		var target = jQuery(this).parent();
		console.log('im a target '+target);
		jQuery(target).toggle( "slow" );
	}
});


// new button menue looper
jQuery(document).ready( function($){

	// this is the id of the ul to use
$.urlParam = function(name){
    var results = new RegExp('[\?&#]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}
	jQuery('#bnetloginm').click(function(e)
		{
			e.preventDefault();
			//alert('boo');
			oAuth2AuthWindow = window.open('index.php?p=redirect&state=login', 'masheryOAuth2AuthWindow', 'width=430,height=660');
		});
	jQuery('#bnetlogins').click(function(e)
		{
			e.preventDefault();
			//alert('boo');
			oAuth2AuthWindow = window.open('index.php?p=redirect&state=login', 'masheryOAuth2AuthWindow', 'width=430,height=660');
		});
	
	var tab = $.urlParam("tab");
	var menu;
	jQuery("#menu ul li").click(function(e)
	{
		e.preventDefault();
		menu = jQuery(this).parent().attr("id");
		jQuery("ul#"+menu+" li").removeClass("active");

		var tab_class = jQuery(this).attr("id");
		jQuery("ul#"+menu+" li").each(function() {
			var v = jQuery(this).attr("id");
			jQuery("div#"+v+"").hide();
		});
		
		jQuery("div#" + tab_class).show();
		jQuery("ul#"+menu+" li#" + tab_class).addClass("active");
		
		window.location.hash = 'tab='+tab_class;
	});
	function first(menu)
	{
		var tab_class = jQuery("#menu ul#"+menu+" li").first().attr("id");
		
		jQuery("#menu ul#"+menu+" li").each(function() {
			var v = jQuery(this).attr("id");
			jQuery("div#"+v+"").hide();
		});
		jQuery("div#" + tab_class).show();
		jQuery("#menu ul#"+menu+" li#" + tab_class).addClass("active");
		
	}
	jQuery("#menu ul").map(function() {
		menu = $(this).attr("id");
		first(menu);
	});
	function show_hide(menu,tab_class)
	{
		jQuery("ul#"+menu+" li").removeClass("active");
		jQuery("#menu ul#"+menu+" li").each(function() {
				var v = jQuery(this).attr("id");
				jQuery("div#"+v+"").hide();
			});
		jQuery("div#" + tab_class).show();
		jQuery("ul#"+menu+" li#" + tab_class).addClass("active");
	}
	if ( tab )
	{
		console.log(tab);
		show_hide(menu,tab);
	}
	
	
	//var $talent = jQuery('#summary-talents');

	jQuery('.navbar .btn-navbar').click(function(e)
	{
		console.log('clickie');
		e.preventDefault();
		jQuery('body').toggleClass('sidebar-mini');
		jQuery('#mmenu').toggleClass('hidden-xs');
	});
	
		
	// Show/Hide Talent Builds on spec-button click
	jQuery('.talent-specs').find(".spec-button").each(function(){
		var $this = $(this);
		var specId = $this.data("spec-id");
		var specName = $this.data("spec-name");
		// Assign click to show build
		$this.not(".disabled").click(function(){
			if(!jQuery(this).hasClass("selected")){
				// Remove selection, hide build
				jQuery(".spec-button").removeClass("selected");										
				jQuery(".talent-build").hide();

				// Select spec, show build
			//	Summary.specName = specName;
				jQuery(this).addClass("selected");
				console.log(specId);
				jQuery("#talent-build-" + specId).show();

				// Update export link
				//jQuery("#export-build").attr("href", specLinks[specId]);

				// Select advanced stats
				jQuery(".summary-stats-specs").hide();
				jQuery("#summary-stats-spec"+specId).show();
			}
		});
	});

	


});
/*
js cookie functions we like these....
*/
function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

function tabcontent(){
		
	}

