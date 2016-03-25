/*
 *********************************************
 * Vertical Menu
 * @date 03.06.2014
 * @author HBWSL
 *********************************************
 */

jQuery(document).ready(function($){

/* for top-right menu */
	$("#TopRightHeader .HBars").click(function(){
		$("#adminUserInfo").toggle(150);
	});
/* for search box */
	$('#div_search').hover(function(){
		 $("#search_text").toggle();
		 $("#search_text_box").focus();
	});

/* to add onhover effects */
	$("#mainmenu > li > a").click(function(){
		var $isVis = $(this).siblings("div").is(":visible");
		$("#mainmenu").find("div").hide();
		$("#mainmenu > li > a").parent().removeClass("hovered");
		if($isVis){
			$(this).siblings("div").hide(100);
			$(this).parent().removeClass("hovered");
		}
		else{
			$(this).siblings("div").show(100);
			$(this).parent().addClass("hovered");
			$(".current > div").addClass("hovered");
		}
	});

/* to collapse-expand menus */
	$(".toggleMenus").click(function(){
		$("#mainmenu > li > a").children("span").toggle();
		$("#menu").toggleClass("hideMenu");
		$(".expandmenu").toggle();
		$(".collapsemenu").toggle();

		var $showMenu = $(".collapsemenu").is(":visible");
		$.get("index.php?showMenu="+$showMenu,function(data,status){});
	});
/* for small screen devices */
	$(window).resize(function(){
		if ($(window).width() <= 480) {
			$("#mainmenu > li > a").children("span").hide();
			$("#menu").addClass("hideMenu");
			$(".expandmenu").show();
			$(".collapsemenu").hide();
		}
 	});

});
