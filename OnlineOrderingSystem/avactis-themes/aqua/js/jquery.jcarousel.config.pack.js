function mycarousel_initCallback(A){jQuery(".jcarousel-control a").bind("click",function(){A.scroll(jQuery.jcarousel.intval(jQuery(this).text()));return false});jQuery(".jcarousel-scroll select").bind("change",function(){A.options.scroll=jQuery.jcarousel.intval(this.options[this.selectedIndex].value);return false});jQuery("#mycarousel-next").bind("click",function(){A.next();return false});jQuery("#mycarousel-prev").bind("click",function(){A.prev();return false})}jQuery(document).ready(function(){jQuery("#mycarousel").jcarousel({scroll:1,initCallback:mycarousel_initCallback,buttonNextHTML:"<div></div>",buttonPrevHTML:"<div></div>",visible:4})});