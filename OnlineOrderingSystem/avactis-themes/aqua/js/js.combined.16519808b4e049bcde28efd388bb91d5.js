/**
 * jCarousel - Riding carousels with jQuery
 *   http://sorgalla.com/jcarousel/
 *
 * Copyright (c) 2006 Jan Sorgalla (http://sorgalla.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * Built on top of the jQuery library
 *   http://jquery.com
 *
 * Inspired by the "Carousel Component" by Bill Scott
 *   http://billwscott.com/carousel/
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(9($){$.1s.A=9(o){z 4.14(9(){2H r(4,o)})};8 q={W:F,23:1,1G:1,u:7,15:3,16:7,1H:\'2I\',24:\'2J\',1i:0,B:7,1j:7,1I:7,25:7,26:7,27:7,28:7,29:7,2a:7,2b:7,1J:\'<N></N>\',1K:\'<N></N>\',2c:\'2d\',2e:\'2d\',1L:7,1M:7};$.A=9(e,o){4.5=$.17({},q,o||{});4.Q=F;4.D=7;4.H=7;4.t=7;4.R=7;4.S=7;4.O=!4.5.W?\'1N\':\'2f\';4.E=!4.5.W?\'2g\':\'2h\';8 a=\'\',1d=e.J.1d(\' \');1k(8 i=0;i<1d.K;i++){6(1d[i].2i(\'A-2j\')!=-1){$(e).1t(1d[i]);8 a=1d[i];1l}}6(e.2k==\'2K\'||e.2k==\'2L\'){4.t=$(e);4.D=4.t.18();6(4.D.1m(\'A-H\')){6(!4.D.18().1m(\'A-D\'))4.D=4.D.B(\'<N></N>\');4.D=4.D.18()}X 6(!4.D.1m(\'A-D\'))4.D=4.t.B(\'<N></N>\').18()}X{4.D=$(e);4.t=$(e).2M(\'>2l,>2m,N>2l,N>2m\')}6(a!=\'\'&&4.D.18()[0].J.2i(\'A-2j\')==-1)4.D.B(\'<N 2N=" \'+a+\'"></N>\');4.H=4.t.18();6(!4.H.K||!4.H.1m(\'A-H\'))4.H=4.t.B(\'<N></N>\').18();4.S=$(\'.A-11\',4.D);6(4.S.u()==0&&4.5.1K!=7)4.S=4.H.1u(4.5.1K).11();4.S.V(4.J(\'A-11\'));4.R=$(\'.A-19\',4.D);6(4.R.u()==0&&4.5.1J!=7)4.R=4.H.1u(4.5.1J).11();4.R.V(4.J(\'A-19\'));4.H.V(4.J(\'A-H\'));4.t.V(4.J(\'A-t\'));4.D.V(4.J(\'A-D\'));8 b=4.5.16!=7?1n.1O(4.1o()/4.5.16):7;8 c=4.t.2O(\'1v\');8 d=4;6(c.u()>0){8 f=0,i=4.5.1G;c.14(9(){d.1P(4,i++);f+=d.T(4,b)});4.t.y(4.O,f+\'U\');6(!o||o.u===L)4.5.u=c.u()}4.D.y(\'1w\',\'1x\');4.R.y(\'1w\',\'1x\');4.S.y(\'1w\',\'1x\');4.2n=9(){d.19()};4.2o=9(){d.11()};4.1Q=9(){d.2p()};6(4.5.1j!=7)4.5.1j(4,\'2q\');6($.2r.2s){4.1e(F,F);$(2t).1y(\'2P\',9(){d.1z()})}X 4.1z()};8 r=$.A;r.1s=r.2Q={A:\'0.2.3\'};r.1s.17=r.17=$.17;r.1s.17({1z:9(){4.C=7;4.G=7;4.Y=7;4.12=7;4.1a=F;4.1f=7;4.P=7;4.Z=F;6(4.Q)z;4.t.y(4.E,4.1A(4.5.1G)+\'U\');8 p=4.1A(4.5.23);4.Y=4.12=7;4.1p(p,F);$(2t).1R(\'2u\',4.1Q).1y(\'2u\',4.1Q)},2v:9(){4.t.2w();4.t.y(4.E,\'2R\');4.t.y(4.O,\'2S\');6(4.5.1j!=7)4.5.1j(4,\'2v\');4.1z()},2p:9(){6(4.P!=7&&4.Z)4.t.y(4.E,r.I(4.t.y(4.E))+4.P);4.P=7;4.Z=F;6(4.5.1I!=7)4.5.1I(4);6(4.5.16!=7){8 a=4;8 b=1n.1O(4.1o()/4.5.16),O=0,E=0;$(\'1v\',4.t).14(9(i){O+=a.T(4,b);6(i+1<a.C)E=O});4.t.y(4.O,O+\'U\');4.t.y(4.E,-E+\'U\')}4.15(4.C,F)},2T:9(){4.Q=1g;4.1e()},2U:9(){4.Q=F;4.1e()},u:9(s){6(s!=L){4.5.u=s;6(!4.Q)4.1e()}z 4.5.u},2V:9(i,a){6(a==L||!a)a=i;6(4.5.u!==7&&a>4.5.u)a=4.5.u;1k(8 j=i;j<=a;j++){8 e=4.M(j);6(!e.K||e.1m(\'A-1b-1B\'))z F}z 1g},M:9(i){z $(\'.A-1b-\'+i,4.t)},2x:9(i,s){8 e=4.M(i),1S=0,2x=0;6(e.K==0){8 c,e=4.1C(i),j=r.I(i);1q(c=4.M(--j)){6(j<=0||c.K){j<=0?4.t.2y(e):c.1T(e);1l}}}X 1S=4.T(e);e.1t(4.J(\'A-1b-1B\'));1U s==\'2W\'?e.2X(s):e.2w().2Y(s);8 a=4.5.16!=7?1n.1O(4.1o()/4.5.16):7;8 b=4.T(e,a)-1S;6(i>0&&i<4.C)4.t.y(4.E,r.I(4.t.y(4.E))-b+\'U\');4.t.y(4.O,r.I(4.t.y(4.O))+b+\'U\');z e},1V:9(i){8 e=4.M(i);6(!e.K||(i>=4.C&&i<=4.G))z;8 d=4.T(e);6(i<4.C)4.t.y(4.E,r.I(4.t.y(4.E))+d+\'U\');e.1V();4.t.y(4.O,r.I(4.t.y(4.O))-d+\'U\')},19:9(){4.1D();6(4.P!=7&&!4.Z)4.1W(F);X 4.15(((4.5.B==\'1X\'||4.5.B==\'G\')&&4.5.u!=7&&4.G==4.5.u)?1:4.C+4.5.15)},11:9(){4.1D();6(4.P!=7&&4.Z)4.1W(1g);X 4.15(((4.5.B==\'1X\'||4.5.B==\'C\')&&4.5.u!=7&&4.C==1)?4.5.u:4.C-4.5.15)},1W:9(b){6(4.Q||4.1a||!4.P)z;8 a=r.I(4.t.y(4.E));!b?a-=4.P:a+=4.P;4.Z=!b;4.Y=4.C;4.12=4.G;4.1p(a)},15:9(i,a){6(4.Q||4.1a)z;4.1p(4.1A(i),a)},1A:9(i){6(4.Q||4.1a)z;i=r.I(i);6(4.5.B!=\'1c\')i=i<1?1:(4.5.u&&i>4.5.u?4.5.u:i);8 a=4.C>i;8 b=r.I(4.t.y(4.E));8 f=4.5.B!=\'1c\'&&4.C<=1?1:4.C;8 c=a?4.M(f):4.M(4.G);8 j=a?f:f-1;8 e=7,l=0,p=F,d=0;1q(a?--j>=i:++j<i){e=4.M(j);p=!e.K;6(e.K==0){e=4.1C(j).V(4.J(\'A-1b-1B\'));c[a?\'1u\':\'1T\'](e)}c=e;d=4.T(e);6(p)l+=d;6(4.C!=7&&(4.5.B==\'1c\'||(j>=1&&(4.5.u==7||j<=4.5.u))))b=a?b+d:b-d}8 g=4.1o();8 h=[];8 k=0,j=i,v=0;8 c=4.M(i-1);1q(++k){e=4.M(j);p=!e.K;6(e.K==0){e=4.1C(j).V(4.J(\'A-1b-1B\'));c.K==0?4.t.2y(e):c[a?\'1u\':\'1T\'](e)}c=e;8 d=4.T(e);6(d==0){2Z(\'30: 31 1N/2f 32 1k 33. 34 35 36 37 38 39. 3a...\');z 0}6(4.5.B!=\'1c\'&&4.5.u!==7&&j>4.5.u)h.3b(e);X 6(p)l+=d;v+=d;6(v>=g)1l;j++}1k(8 x=0;x<h.K;x++)h[x].1V();6(l>0){4.t.y(4.O,4.T(4.t)+l+\'U\');6(a){b-=l;4.t.y(4.E,r.I(4.t.y(4.E))-l+\'U\')}}8 n=i+k-1;6(4.5.B!=\'1c\'&&4.5.u&&n>4.5.u)n=4.5.u;6(j>n){k=0,j=n,v=0;1q(++k){8 e=4.M(j--);6(!e.K)1l;v+=4.T(e);6(v>=g)1l}}8 o=n-k+1;6(4.5.B!=\'1c\'&&o<1)o=1;6(4.Z&&a){b+=4.P;4.Z=F}4.P=7;6(4.5.B!=\'1c\'&&n==4.5.u&&(n-k+1)>=1){8 m=r.10(4.M(n),!4.5.W?\'1r\':\'1Y\');6((v-m)>g)4.P=v-g-m}1q(i-->o)b+=4.T(4.M(i));4.Y=4.C;4.12=4.G;4.C=o;4.G=n;z b},1p:9(p,a){6(4.Q||4.1a)z;4.1a=1g;8 b=4;8 c=9(){b.1a=F;6(p==0)b.t.y(b.E,0);6(b.5.B==\'1X\'||b.5.B==\'G\'||b.5.u==7||b.G<b.5.u)b.2z();b.1e();b.1Z(\'2A\')};4.1Z(\'3c\');6(!4.5.1H||a==F){4.t.y(4.E,p+\'U\');c()}X{8 o=!4.5.W?{\'2g\':p}:{\'2h\':p};4.t.1p(o,4.5.1H,4.5.24,c)}},2z:9(s){6(s!=L)4.5.1i=s;6(4.5.1i==0)z 4.1D();6(4.1f!=7)z;8 a=4;4.1f=3d(9(){a.19()},4.5.1i*3e)},1D:9(){6(4.1f==7)z;3f(4.1f);4.1f=7},1e:9(n,p){6(n==L||n==7){8 n=!4.Q&&4.5.u!==0&&((4.5.B&&4.5.B!=\'C\')||4.5.u==7||4.G<4.5.u);6(!4.Q&&(!4.5.B||4.5.B==\'C\')&&4.5.u!=7&&4.G>=4.5.u)n=4.P!=7&&!4.Z}6(p==L||p==7){8 p=!4.Q&&4.5.u!==0&&((4.5.B&&4.5.B!=\'G\')||4.C>1);6(!4.Q&&(!4.5.B||4.5.B==\'G\')&&4.5.u!=7&&4.C==1)p=4.P!=7&&4.Z}8 a=4;4.R[n?\'1y\':\'1R\'](4.5.2c,4.2n)[n?\'1t\':\'V\'](4.J(\'A-19-1E\')).20(\'1E\',n?F:1g);4.S[p?\'1y\':\'1R\'](4.5.2e,4.2o)[p?\'1t\':\'V\'](4.J(\'A-11-1E\')).20(\'1E\',p?F:1g);6(4.R.K>0&&(4.R[0].1h==L||4.R[0].1h!=n)&&4.5.1L!=7){4.R.14(9(){a.5.1L(a,4,n)});4.R[0].1h=n}6(4.S.K>0&&(4.S[0].1h==L||4.S[0].1h!=p)&&4.5.1M!=7){4.S.14(9(){a.5.1M(a,4,p)});4.S[0].1h=p}},1Z:9(a){8 b=4.Y==7?\'2q\':(4.Y<4.C?\'19\':\'11\');4.13(\'25\',a,b);6(4.Y!==4.C){4.13(\'26\',a,b,4.C);4.13(\'27\',a,b,4.Y)}6(4.12!==4.G){4.13(\'28\',a,b,4.G);4.13(\'29\',a,b,4.12)}4.13(\'2a\',a,b,4.C,4.G,4.Y,4.12);4.13(\'2b\',a,b,4.Y,4.12,4.C,4.G)},13:9(a,b,c,d,e,f,g){6(4.5[a]==L||(1U 4.5[a]!=\'2B\'&&b!=\'2A\'))z;8 h=1U 4.5[a]==\'2B\'?4.5[a][b]:4.5[a];6(!$.3g(h))z;8 j=4;6(d===L)h(j,c,b);X 6(e===L)4.M(d).14(9(){h(j,4,d,c,b)});X{1k(8 i=d;i<=e;i++)6(i!==7&&!(i>=f&&i<=g))4.M(i).14(9(){h(j,4,i,c,b)})}},1C:9(i){z 4.1P(\'<1v></1v>\',i)},1P:9(e,i){8 a=$(e).V(4.J(\'A-1b\')).V(4.J(\'A-1b-\'+i));a.20(\'3h\',i);z a},J:9(c){z c+\' \'+c+(!4.5.W?\'-3i\':\'-W\')},T:9(e,d){8 a=e.2C!=L?e[0]:e;8 b=!4.5.W?a.1F+r.10(a,\'2D\')+r.10(a,\'1r\'):a.2E+r.10(a,\'2F\')+r.10(a,\'1Y\');6(d==L||b==d)z b;8 w=!4.5.W?d-r.10(a,\'2D\')-r.10(a,\'1r\'):d-r.10(a,\'2F\')-r.10(a,\'1Y\');$(a).y(4.O,w+\'U\');z 4.T(a)},1o:9(){z!4.5.W?4.H[0].1F-r.I(4.H.y(\'3j\'))-r.I(4.H.y(\'3k\')):4.H[0].2E-r.I(4.H.y(\'3l\'))-r.I(4.H.y(\'3m\'))},3n:9(i,s){6(s==L)s=4.5.u;z 1n.3o((((i-1)/s)-1n.3p((i-1)/s))*s)+1}});r.17({3q:9(d){z $.17(q,d||{})},10:9(e,p){6(!e)z 0;8 a=e.2C!=L?e[0]:e;6(p==\'1r\'&&$.2r.2s){8 b={\'1w\':\'1x\',\'3r\':\'3s\',\'1N\':\'1i\'},21,22;$.2G(a,b,9(){21=a.1F});b[\'1r\']=0;$.2G(a,b,9(){22=a.1F});z 22-21}z r.I($.y(a,p))},I:9(v){v=3t(v);z 3u(v)?0:v}})})(3v);',62,218,'||||this|options|if|null|var|function||||||||||||||||||||list|size||||css|return|jcarousel|wrap|first|container|lt|false|last|clip|intval|className|length|undefined|get|div|wh|tail|locked|buttonNext|buttonPrev|dimension|px|addClass|vertical|else|prevFirst|inTail|margin|prev|prevLast|callback|each|scroll|visible|extend|parent|next|animating|item|circular|split|buttons|timer|true|jcarouselstate|auto|initCallback|for|break|hasClass|Math|clipping|animate|while|marginRight|fn|removeClass|before|li|display|block|bind|setup|pos|placeholder|create|stopAuto|disabled|offsetWidth|offset|animation|reloadCallback|buttonNextHTML|buttonPrevHTML|buttonNextCallback|buttonPrevCallback|width|ceil|format|funcResize|unbind|old|after|typeof|remove|scrollTail|both|marginBottom|notify|attr|oWidth|oWidth2|start|easing|itemLoadCallback|itemFirstInCallback|itemFirstOutCallback|itemLastInCallback|itemLastOutCallback|itemVisibleInCallback|itemVisibleOutCallback|buttonNextEvent|click|buttonPrevEvent|height|left|top|indexOf|skin|nodeName|ul|ol|funcNext|funcPrev|reload|init|browser|safari|window|resize|reset|empty|add|prepend|startAuto|onAfterAnimation|object|jquery|marginLeft|offsetHeight|marginTop|swap|new|normal|swing|UL|OL|find|class|children|load|prototype|0px|10px|lock|unlock|has|string|html|append|alert|jCarousel|No|set|items|This|will|cause|an|infinite|loop|Aborting|push|onBeforeAnimation|setTimeout|1000|clearTimeout|isFunction|jcarouselindex|horizontal|borderLeftWidth|borderRightWidth|borderTopWidth|borderBottomWidth|index|round|floor|defaults|float|none|parseInt|isNaN|jQuery'.split('|'),0,{}))

function mycarousel_initCallback(A){jQuery(".jcarousel-control a").bind("click",function(){A.scroll(jQuery.jcarousel.intval(jQuery(this).text()));return false});jQuery(".jcarousel-scroll select").bind("change",function(){A.options.scroll=jQuery.jcarousel.intval(this.options[this.selectedIndex].value);return false});jQuery("#mycarousel-next").bind("click",function(){A.next();return false});jQuery("#mycarousel-prev").bind("click",function(){A.prev();return false})}jQuery(document).ready(function(){jQuery("#mycarousel").jcarousel({scroll:1,initCallback:mycarousel_initCallback,buttonNextHTML:"<div></div>",buttonPrevHTML:"<div></div>",visible:4})});

/*!
 * jQuery Cookie Plugin v1.3.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals.
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	function converted(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}
		try {
			return config.json ? JSON.parse(s) : s;
		} catch(er) {}
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		var result = key ? undefined : {};
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = decode(parts.join('='));

			if (key && key === name) {
				result = converted(cookie);
				break;
			}

			if (!key) {
				result[name] = converted(cookie);
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== undefined) {
			$.cookie(key, '', $.extend(options, { expires: -1 }));
			return true;
		}
		return false;
	};

}));

/*************************************************************************
	jquery.dynatree.js
	Dynamic tree view control, with support for lazy loading of branches.

	Copyright (c) 2006-2013, Martin Wendt (http://wwWendt.de)
	Dual licensed under the MIT or GPL Version 2 licenses.
	http://code.google.com/p/dynatree/wiki/LicenseInfo

	A current version and some documentation is available at
		http://dynatree.googlecode.com/

	$Version: 1.2.4$
	$Revision: 644, 2013-02-12 21:39:36$

	@depends: jquery.js
	@depends: jquery.ui.core.js
	@depends: jquery.cookie.js
*************************************************************************/

/* jsHint options*/
// Note: We currently allow eval() to parse the 'data' attribtes, when initializing from HTML.
//     : pass jsHint with the options given in grunt.js only.
//       The following should not be required:
/*global alert */
/*jshint nomen:false, smarttabs:true, eqeqeq:false, evil:true, regexp:false */

/*************************************************************************
 *	Debug functions
 */

var _canLog = true;

function _log(mode, msg) {
	/**
	 * Usage: logMsg("%o was toggled", this);
	 */
	if( !_canLog ){
		return;
	}
	// Remove first argument
	var args = Array.prototype.slice.apply(arguments, [1]);
	// Prepend timestamp
	var dt = new Date();
	var tag = dt.getHours()+":"+dt.getMinutes()+":"+dt.getSeconds()+"."+dt.getMilliseconds();
	args[0] = tag + " - " + args[0];

	try {
		switch( mode ) {
		case "info":
			window.console.info.apply(window.console, args);
			break;
		case "warn":
			window.console.warn.apply(window.console, args);
			break;
		default:
			window.console.log.apply(window.console, args);
			break;
		}
	} catch(e) {
		if( !window.console ){
			_canLog = false; // Permanently disable, when logging is not supported by the browser
		}else if(e.number === -2146827850){
			// fix for IE8, where window.console.log() exists, but does not support .apply()
			window.console.log(args.join(", "));
		}
	}
}

/* Check browser version, since $.browser was removed in jQuery 1.9 */
function _checkBrowser(){
	var matched, browser;
	function uaMatch( ua ) {
		ua = ua.toLowerCase();
		var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
			 /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
			 /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
			 /(msie) ([\w.]+)/.exec( ua ) ||
			 ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
			 [];
		return {
			browser: match[ 1 ] || "",
			version: match[ 2 ] || "0"
		};
	}
	matched = uaMatch( navigator.userAgent );
	browser = {};
	 if ( matched.browser ) {
		 browser[ matched.browser ] = true;
		 browser.version = matched.version;
	 }
	 if ( browser.chrome ) {
		 browser.webkit = true;
	 } else if ( browser.webkit ) {
		 browser.safari = true;
	 }
	 return browser;
}
var BROWSER = jQuery.browser || _checkBrowser();

function logMsg(msg) {
	Array.prototype.unshift.apply(arguments, ["debug"]);
	_log.apply(this, arguments);
}


// Forward declaration
var getDynaTreePersistData = null;



/*************************************************************************
 *	Constants
 */
var DTNodeStatus_Error   = -1;
var DTNodeStatus_Loading = 1;
var DTNodeStatus_Ok      = 0;


// Start of local namespace
(function($) {

/*************************************************************************
 *	Common tool functions.
 */

var Class = {
	create: function() {
		return function() {
			this.initialize.apply(this, arguments);
		};
	}
};

// Tool function to get dtnode from the event target:
function getDtNodeFromElement(el) {
	alert("getDtNodeFromElement is deprecated");
	return $.ui.dynatree.getNode(el);
/*
	var iMax = 5;
	while( el && iMax-- ) {
		if(el.dtnode) { return el.dtnode; }
		el = el.parentNode;
	}
	return null;
*/
}

function noop() {
}

/** Compare two dotted version strings (like '10.2.3').
 * @returns {Integer} 0: v1 == v2, -1: v1 < v2, 1: v1 > v2
 */
function versionCompare(v1, v2) {
	var v1parts = ("" + v1).split("."),
		v2parts = ("" + v2).split("."),
		minLength = Math.min(v1parts.length, v2parts.length),
		p1, p2, i;
	// Compare tuple pair-by-pair.
	for(i = 0; i < minLength; i++) {
		// Convert to integer if possible, because "8" > "10".
		p1 = parseInt(v1parts[i], 10);
		p2 = parseInt(v2parts[i], 10);
		if (isNaN(p1)){ p1 = v1parts[i]; }
		if (isNaN(p2)){ p2 = v2parts[i]; }
		if (p1 == p2) {
			continue;
		}else if (p1 > p2) {
			return 1;
		}else if (p1 < p2) {
			return -1;
		}
		// one operand is NaN
		return NaN;
	}
	// The longer tuple is always considered 'greater'
	if (v1parts.length === v2parts.length) {
		return 0;
	}
	return (v1parts.length < v2parts.length) ? -1 : 1;
}


/*************************************************************************
 *	Class DynaTreeNode
 */
var DynaTreeNode = Class.create();

DynaTreeNode.prototype = {
	initialize: function(parent, tree, data) {
		/**
		 * @constructor
		 */
		this.parent = parent;
		this.tree = tree;
		if ( typeof data === "string" ){
			data = { title: data };
		}
		if( !data.key ){
			data.key = "_" + tree._nodeCount++;
		}else{
			data.key = "" + data.key; // issue 371
		}
		this.data = $.extend({}, $.ui.dynatree.nodedatadefaults, data);
		this.li = null; // not yet created
		this.span = null; // not yet created
		this.ul = null; // not yet created
		this.childList = null; // no subnodes yet
		this._isLoading = false; // Lazy content is being loaded
		this.hasSubSel = false;
		this.bExpanded = false;
		this.bSelected = false;

	},

	toString: function() {
		return "DynaTreeNode<" + this.data.key + ">: '" + this.data.title + "'";
	},

	toDict: function(recursive, callback) {
		var dict = $.extend({}, this.data);
		dict.activate = ( this.tree.activeNode === this );
		dict.focus = ( this.tree.focusNode === this );
		dict.expand = this.bExpanded;
		dict.select = this.bSelected;
		if( callback ){
			callback(dict);
		}
		if( recursive && this.childList ) {
			dict.children = [];
			for(var i=0, l=this.childList.length; i<l; i++ ){
				dict.children.push(this.childList[i].toDict(true, callback));
			}
		} else {
			delete dict.children;
		}
		return dict;
	},

	fromDict: function(dict) {
		/**
		 * Update node data. If dict contains 'children', then also replace
		 * the hole sub tree.
		 */
		var children = dict.children;
		if(children === undefined){
			this.data = $.extend(this.data, dict);
			this.render();
			return;
		}
		dict = $.extend({}, dict);
		dict.children = undefined;
		this.data = $.extend(this.data, dict);
		this.removeChildren();
		this.addChild(children);
	},

	_getInnerHtml: function() {
		var tree = this.tree,
			opts = tree.options,
			cache = tree.cache,
			level = this.getLevel(),
			data = this.data,
			res = "",
			imageSrc;
		// connector (expanded, expandable or simple)
		if( level < opts.minExpandLevel ) {
			if(level > 1){
				res += cache.tagConnector;
			}
			// .. else (i.e. for root level) skip expander/connector altogether
		} else if( this.hasChildren() !== false ) {
			res += cache.tagExpander;
		} else {
			res += cache.tagConnector;
		}
		// Checkbox mode
		if( opts.checkbox && data.hideCheckbox !== true && !data.isStatusNode ) {
			res += cache.tagCheckbox;
		}
		// folder or doctype icon
		if ( data.icon ) {
			if (data.icon.charAt(0) === "/"){
				imageSrc = data.icon;
			}else{
				imageSrc = opts.imagePath + data.icon;
			}
			res += "<img src='" + imageSrc + "' alt='' />";
		} else if ( data.icon === false ) {
			// icon == false means 'no icon'
//			noop(); // keep JSLint happy
		} else if ( data.iconClass ) {
			res +=  "<span class='" + " " + data.iconClass +  "'></span>";
		} else {
			// icon == null means 'default icon'
			res += cache.tagNodeIcon;
		}
		// node title
		var nodeTitle = "";
		if ( opts.onCustomRender ){
			nodeTitle = opts.onCustomRender.call(tree, this) || "";
		}
		if(!nodeTitle){
			var tooltip = data.tooltip ? ' title="' + data.tooltip.replace(/\"/g, '&quot;') + '"' : '',
				href = data.href || "#";
			if( opts.noLink || data.noLink ) {
				nodeTitle = '<span style="display:inline-block;" class="' + opts.classNames.title + '"' + tooltip + '>' + data.title + '</span>';
//				this.tree.logDebug("nodeTitle: " + nodeTitle);
			} else {
				nodeTitle = '<a href="' + href + '" class="' + opts.classNames.title + '"' + tooltip + '>' + data.title + '</a>';
			}
		}
		res += nodeTitle;
		return res;
	},


	_fixOrder: function() {
		/**
		 * Make sure, that <li> order matches childList order.
		 */
		var cl = this.childList;
		if( !cl || !this.ul ){
			return;
		}
		var childLI = this.ul.firstChild;
		for(var i=0, l=cl.length-1; i<l; i++) {
			var childNode1 = cl[i];
			var childNode2 = childLI.dtnode;
			if( childNode1 !== childNode2 ) {
				this.tree.logDebug("_fixOrder: mismatch at index " + i + ": " + childNode1 + " != " + childNode2);
				this.ul.insertBefore(childNode1.li, childNode2.li);
			} else {
				childLI = childLI.nextSibling;
			}
		}
	},


	render: function(useEffects, includeInvisible) {
		/**
		 * Create <li><span>..</span> .. </li> tags for this node.
		 *
		 * <li id='KEY' dtnode=NODE> // This div contains the node's span and list of child div's.
		 *   <span class='title'>S S S A</span> // Span contains graphic spans and title <a> tag
		 *   <ul> // only present, when node has children
		 *       <li id='KEY' dtnode=NODE>child1</li>
		 *       <li id='KEY' dtnode=NODE>child2</li>
		 *   </ul>
		 * </li>
		 */
//		this.tree.logDebug("%s.render(%s)", this, useEffects);
		// ---
		var tree = this.tree,
			parent = this.parent,
			data = this.data,
			opts = tree.options,
			cn = opts.classNames,
			isLastSib = this.isLastSibling(),
			firstTime = false;

		if( !parent && !this.ul ) {
			// Root node has only a <ul>
			this.li = this.span = null;
			this.ul = document.createElement("ul");
			if( opts.minExpandLevel > 1 ){
				this.ul.className = cn.container + " " + cn.noConnector;
			}else{
				this.ul.className = cn.container;
			}
		} else if( parent ) {
			// Create <li><span /> </li>
			if( ! this.li ) {
				firstTime = true;
				this.li = document.createElement("li");
				this.li.dtnode = this;
				if( data.key && opts.generateIds ){
					this.li.id = opts.idPrefix + data.key;
				}
				this.span = document.createElement("span");
				this.span.className = cn.title;
				this.li.appendChild(this.span);

				if( !parent.ul ) {
					// This is the parent's first child: create UL tag
					// (Hidden, because it will be
					parent.ul = document.createElement("ul");
					parent.ul.style.display = "none";
					parent.li.appendChild(parent.ul);
//					if( opts.minExpandLevel > this.getLevel() ){
//						parent.ul.className = cn.noConnector;
//					}
				}
				// set node connector images, links and text
//				this.span.innerHTML = this._getInnerHtml();

				parent.ul.appendChild(this.li);
			}
			// set node connector images, links and text
			this.span.innerHTML = this._getInnerHtml();
			// Set classes for current status
			var cnList = [];
			cnList.push(cn.node);
			if( data.isFolder ){
				cnList.push(cn.folder);
			}
			if( this.bExpanded ){
				cnList.push(cn.expanded);
			}
			if( this.hasChildren() !== false ){
				cnList.push(cn.hasChildren);
			}
			if( data.isLazy && this.childList === null ){
				cnList.push(cn.lazy);
			}
			if( isLastSib ){
				cnList.push(cn.lastsib);
			}
			if( this.bSelected ){
				cnList.push(cn.selected);
			}
			if( this.hasSubSel ){
				cnList.push(cn.partsel);
			}
			if( tree.activeNode === this ){
				cnList.push(cn.active);
			}
			if( data.addClass ){
				cnList.push(data.addClass);
			}
			// IE6 doesn't correctly evaluate multiple class names,
			// so we create combined class names that can be used in the CSS
			cnList.push(cn.combinedExpanderPrefix
					+ (this.bExpanded ? "e" : "c")
					+ (data.isLazy && this.childList === null ? "d" : "")
					+ (isLastSib ? "l" : "")
					);
			cnList.push(cn.combinedIconPrefix
					+ (this.bExpanded ? "e" : "c")
					+ (data.isFolder ? "f" : "")
					);
			this.span.className = cnList.join(" ");

			//     : we should not set this in the <span> tag also, if we set it here:
			this.li.className = isLastSib ? cn.lastsib : "";

			// Allow tweaking, binding, after node was created for the first time
			if(firstTime && opts.onCreate){
				opts.onCreate.call(tree, this, this.span);
			}
			// Hide children, if node is collapsed
//			this.ul.style.display = ( this.bExpanded || !parent ) ? "" : "none";
			// Allow tweaking after node state was rendered
			if(opts.onRender){
				opts.onRender.call(tree, this, this.span);
			}
		}
		// Visit child nodes
		if( (this.bExpanded || includeInvisible === true) && this.childList ) {
			for(var i=0, l=this.childList.length; i<l; i++) {
				this.childList[i].render(false, includeInvisible);
			}
			// Make sure the tag order matches the child array
			this._fixOrder();
		}
		// Hide children, if node is collapsed
		if( this.ul ) {
			var isHidden = (this.ul.style.display === "none");
			var isExpanded = !!this.bExpanded;
//			logMsg("isHidden:%s", isHidden);
			if( useEffects && opts.fx && (isHidden === isExpanded) ) {
				var duration = opts.fx.duration || 200;
				$(this.ul).animate(opts.fx, duration);
			} else {
				this.ul.style.display = ( this.bExpanded || !parent ) ? "" : "none";
			}
		}
	},
	/** Return '/id1/id2/id3'. */
	getKeyPath: function(excludeSelf) {
		var path = [];
		this.visitParents(function(node){
			if(node.parent){
				path.unshift(node.data.key);
			}
		}, !excludeSelf);
		return "/" + path.join(this.tree.options.keyPathSeparator);
	},

	getParent: function() {
		return this.parent;
	},

	getChildren: function() {
		if(this.hasChildren() === undefined){
			return undefined; // Lazy node: unloaded, currently loading, or load error
		}
		return this.childList;
	},

	/** Check if node has children (returns undefined, if not sure). */
	hasChildren: function() {
		if(this.data.isLazy){
			if(this.childList === null || this.childList === undefined){
				// Not yet loaded
				return undefined;
			}else if(this.childList.length === 0){
				// Loaded, but response was empty
				return false;
			}else if(this.childList.length === 1 && this.childList[0].isStatusNode()){
				// Currently loading or load error
				return undefined;
			}
			return true;
		}
		return !!this.childList;
	},

	isFirstSibling: function() {
		var p = this.parent;
		return !p || p.childList[0] === this;
	},

	isLastSibling: function() {
		var p = this.parent;
		return !p || p.childList[p.childList.length-1] === this;
	},

	isLoading: function() {
		return !!this._isLoading;
	},

	getPrevSibling: function() {
		if( !this.parent ){
			return null;
		}
		var ac = this.parent.childList;
		for(var i=1, l=ac.length; i<l; i++){ // start with 1, so prev(first) = null
			if( ac[i] === this ){
				return ac[i-1];
			}
		}
		return null;
	},

	getNextSibling: function() {
		if( !this.parent ){
			return null;
		}
		var ac = this.parent.childList;
		for(var i=0, l=ac.length-1; i<l; i++){ // up to length-2, so next(last) = null
			if( ac[i] === this ){
				return ac[i+1];
			}
		}
		return null;
	},

	isStatusNode: function() {
		return (this.data.isStatusNode === true);
	},

	isChildOf: function(otherNode) {
		return (this.parent && this.parent === otherNode);
	},

	isDescendantOf: function(otherNode) {
		if(!otherNode){
			return false;
		}
		var p = this.parent;
		while( p ) {
			if( p === otherNode ){
				return true;
			}
			p = p.parent;
		}
		return false;
	},

	countChildren: function() {
		var cl = this.childList;
		if( !cl ){
			return 0;
		}
		var n = cl.length;
		for(var i=0, l=n; i<l; i++){
			var child = cl[i];
			n += child.countChildren();
		}
		return n;
	},

	/**Sort child list by title.
	 * cmd: optional compare function.
	 * deep: optional: pass true to sort all descendant nodes.
	 */
	sortChildren: function(cmp, deep) {
		var cl = this.childList;
		if( !cl ){
			return;
		}
		cmp = cmp || function(a, b) {
//			return a.data.title === b.data.title ? 0 : a.data.title > b.data.title ? 1 : -1;
			var x = a.data.title.toLowerCase(),
				y = b.data.title.toLowerCase();
			return x === y ? 0 : x > y ? 1 : -1;
			};
		cl.sort(cmp);
		if( deep ){
			for(var i=0, l=cl.length; i<l; i++){
				if( cl[i].childList ){
					cl[i].sortChildren(cmp, "$norender$");
				}
			}
		}
		if( deep !== "$norender$" ){
			this.render();
		}
	},

	_setStatusNode: function(data) {
		// Create, modify or remove the status child node (pass 'null', to remove it).
		var firstChild = ( this.childList ? this.childList[0] : null );
		if( !data ) {
			if ( firstChild && firstChild.isStatusNode()) {
				try{
					// I've seen exceptions here with loadKeyPath...
					if(this.ul){
						this.ul.removeChild(firstChild.li);
						firstChild.li = null; // avoid leaks (issue 215)
					}
				}catch(e){}
				if( this.childList.length === 1 ){
					this.childList = [];
				}else{
					this.childList.shift();
				}
			}
		} else if ( firstChild ) {
			data.isStatusNode = true;
			data.key = "_statusNode";
			firstChild.data = data;
			firstChild.render();
		} else {
			data.isStatusNode = true;
			data.key = "_statusNode";
			firstChild = this.addChild(data);
		}
	},

	setLazyNodeStatus: function(lts, opts) {
		var tooltip = (opts && opts.tooltip) ? opts.tooltip : null,
			info = (opts && opts.info) ? " (" + opts.info + ")" : "";
		switch( lts ) {
			case DTNodeStatus_Ok:
				this._setStatusNode(null);
				$(this.span).removeClass(this.tree.options.classNames.nodeLoading);
				this._isLoading = false;
//				this.render();
				if( this.tree.options.autoFocus ) {
					if( this === this.tree.tnRoot && this.childList && this.childList.length > 0) {
						// special case: using ajaxInit
						this.childList[0].focus();
					} else {
						this.focus();
					}
				}
				break;
			case DTNodeStatus_Loading:
				this._isLoading = true;
				$(this.span).addClass(this.tree.options.classNames.nodeLoading);
				// The root is hidden, so we set a temporary status child
				if(!this.parent){
					this._setStatusNode({
						title: this.tree.options.strings.loading + info,
						tooltip: tooltip,
						addClass: this.tree.options.classNames.nodeWait
					});
				}
				break;
			case DTNodeStatus_Error:
				this._isLoading = false;
//				$(this.span).addClass(this.tree.options.classNames.nodeError);
				this._setStatusNode({
					title: this.tree.options.strings.loadError + info,
					tooltip: tooltip,
					addClass: this.tree.options.classNames.nodeError
				});
				break;
			default:
				throw "Bad LazyNodeStatus: '" + lts + "'.";
		}
	},

	_parentList: function(includeRoot, includeSelf) {
		var l = [];
		var dtn = includeSelf ? this : this.parent;
		while( dtn ) {
			if( includeRoot || dtn.parent ){
				l.unshift(dtn);
			}
			dtn = dtn.parent;
		}
		return l;
	},
	getLevel: function() {
		/**
		 * Return node depth. 0: System root node, 1: visible top-level node.
		 */
		var level = 0;
		var dtn = this.parent;
		while( dtn ) {
			level++;
			dtn = dtn.parent;
		}
		return level;
	},

	_getTypeForOuterNodeEvent: function(event) {
		/** Return the inner node span (title, checkbox or expander) if
		 *  event.target points to the outer span.
		 *  This function should fix issue #93:
		 *  FF2 ignores empty spans, when generating events (returning the parent instead).
		 */
		var cns = this.tree.options.classNames;
		var target = event.target;
		// Only process clicks on an outer node span (probably due to a FF2 event handling bug)
		if( target.className.indexOf(cns.node) < 0 ) {
			return null;
		}
		// Event coordinates, relative to outer node span:
		var eventX = event.pageX - target.offsetLeft;
		var eventY = event.pageY - target.offsetTop;

		for(var i=0, l=target.childNodes.length; i<l; i++) {
			var cn = target.childNodes[i];
			var x = cn.offsetLeft - target.offsetLeft;
			var y = cn.offsetTop - target.offsetTop;
			var nx = cn.clientWidth, ny = cn.clientHeight;
//	        alert (cn.className + ": " + x + ", " + y + ", s:" + nx + ", " + ny);
			if( eventX >= x && eventX <= (x+nx) && eventY >= y && eventY <= (y+ny) ) {
//	            alert("HIT "+ cn.className);
				if( cn.className==cns.title ){
					return "title";
				}else if( cn.className==cns.expander ){
					return "expander";
				}else if( cn.className==cns.checkbox ){
					return "checkbox";
				}else if( cn.className==cns.nodeIcon ){
					return "icon";
				}
			}
		}
		return "prefix";
	},

	getEventTargetType: function(event) {
		// Return the part of a node, that a click event occured on.
		// Note: there is no check, if the event was fired on THIS node.
		var tcn = event && event.target ? event.target.className : "",
			cns = this.tree.options.classNames;

		if( tcn === cns.title ){
			return "title";
		}else if( tcn === cns.expander ){
			return "expander";
		}else if( tcn === cns.checkbox ){
			return "checkbox";
		}else if( tcn === cns.nodeIcon ){
			return "icon";
		}else if( tcn === cns.empty || tcn === cns.vline || tcn === cns.connector ){
			return "prefix";
		}else if( tcn.indexOf(cns.node) >= 0 ){
			// FIX issue #93
			return this._getTypeForOuterNodeEvent(event);
		}
		return null;
	},

	isVisible: function() {
		// Return true, if all parents are expanded.
		var parents = this._parentList(true, false);
		for(var i=0, l=parents.length; i<l; i++){
			if( ! parents[i].bExpanded ){ return false; }
		}
		return true;
	},

	makeVisible: function() {
		// Make sure, all parents are expanded
		var parents = this._parentList(true, false);
		for(var i=0, l=parents.length; i<l; i++){
			parents[i]._expand(true);
		}
	},

	focus: function() {
		//     : check, if we already have focus
//		this.tree.logDebug("dtnode.focus(): %o", this);
		this.makeVisible();
		try {
			$(this.span).find(">a").focus();
		} catch(e) { }
	},

	isFocused: function() {
		return (this.tree.tnFocused === this);
	},

	_activate: function(flag, fireEvents) {
		// (De)Activate - but not focus - this node.
		this.tree.logDebug("dtnode._activate(%o, fireEvents=%o) - %o", flag, fireEvents, this);
		var opts = this.tree.options;
		if( this.data.isStatusNode ){
			return;
		}
		if ( fireEvents && opts.onQueryActivate && opts.onQueryActivate.call(this.tree, flag, this) === false ){
			return; // Callback returned false
		}
		if( flag ) {
			// Activate
			if( this.tree.activeNode ) {
				if( this.tree.activeNode === this ){
					return;
				}
				this.tree.activeNode.deactivate();
			}
			if( opts.activeVisible ){
				this.makeVisible();
			}
			this.tree.activeNode = this;
			if( opts.persist ){
				$.cookie(opts.cookieId+"-active", this.data.key, opts.cookie);
			}
			this.tree.persistence.activeKey = this.data.key;
			$(this.span).addClass(opts.classNames.active);
			if ( fireEvents && opts.onActivate ){
				opts.onActivate.call(this.tree, this);
			}
		} else {
			// Deactivate
			if( this.tree.activeNode === this ) {
				if ( opts.onQueryActivate && opts.onQueryActivate.call(this.tree, false, this) === false ){
					return; // Callback returned false
				}
				$(this.span).removeClass(opts.classNames.active);
				if( opts.persist ) {
					// Note: we don't pass null, but ''. So the cookie is not deleted.
					// If we pass null, we also have to pass a COPY of opts, because $cookie will override opts.expires (issue 84)
					$.cookie(opts.cookieId+"-active", "", opts.cookie);
				}
				this.tree.persistence.activeKey = null;
				this.tree.activeNode = null;
				if ( fireEvents && opts.onDeactivate ){
					opts.onDeactivate.call(this.tree, this);
				}
			}
		}
	},

	activate: function() {
		// Select - but not focus - this node.
//		this.tree.logDebug("dtnode.activate(): %o", this);
		this._activate(true, true);
	},

	activateSilently: function() {
		this._activate(true, false);
	},

	deactivate: function() {
//		this.tree.logDebug("dtnode.deactivate(): %o", this);
		this._activate(false, true);
	},

	isActive: function() {
		return (this.tree.activeNode === this);
	},

	_userActivate: function() {
		// Handle user click / [space] / [enter], according to clickFolderMode.
		var activate = true;
		var expand = false;
		if ( this.data.isFolder ) {
			switch( this.tree.options.clickFolderMode ) {
			case 2:
				activate = false;
				expand = true;
				break;
			case 3:
				activate = expand = true;
				break;
			}
		}
		if( this.parent === null ) {
			expand = false;
		}
		if( expand ) {
			this.toggleExpand();
			this.focus();
		}
		if( activate ) {
			this.activate();
		}
	},

	_setSubSel: function(hasSubSel) {
		if( hasSubSel ) {
			this.hasSubSel = true;
			$(this.span).addClass(this.tree.options.classNames.partsel);
		} else {
			this.hasSubSel = false;
			$(this.span).removeClass(this.tree.options.classNames.partsel);
		}
	},
	/**
	 * Fix selection and partsel status, of parent nodes, according to current status of
	 * end nodes.
	 */
	_updatePartSelectionState: function() {
//		alert("_updatePartSelectionState " + this);
//		this.tree.logDebug("_updatePartSelectionState() - %o", this);
		var sel;
		// Return `true` or `false` for end nodes and remove part-sel flag
		if( ! this.hasChildren() ){
			sel = (this.bSelected && !this.data.unselectable && !this.data.isStatusNode);
			this._setSubSel(false);
			return sel;
		}
		// Return `true`, `false`, or `undefined` for parent nodes
		var i, l,
			cl = this.childList,
			allSelected = true,
			allDeselected = true;
		for(i=0, l=cl.length; i<l;  i++) {
			var n = cl[i],
				s = n._updatePartSelectionState();
			if( s !== false){
				allDeselected = false;
			}
			if( s !== true){
				allSelected = false;
			}
		}
		if( allSelected ){
			sel = true;
		} else if ( allDeselected ){
			sel = false;
		} else {
			sel = undefined;
		}
		this._setSubSel(sel === undefined);
		this.bSelected = (sel === true);
		return sel;
	},

	/**
	 * Fix selection status, after this node was (de)selected in multi-hier mode.
	 * This includes (de)selecting all children.
	 */
	_fixSelectionState: function() {
//		alert("_fixSelectionState " + this);
//		this.tree.logDebug("_fixSelectionState(%s) - %o", this.bSelected, this);
		var p, i, l;
		if( this.bSelected ) {
			// Select all children
			this.visit(function(node){
				node.parent._setSubSel(true);
				if(!node.data.unselectable){
					node._select(true, false, false);
				}
			});
			// Select parents, if all children are selected
			p = this.parent;
			while( p ) {
				p._setSubSel(true);
				var allChildsSelected = true;
				for(i=0, l=p.childList.length; i<l;  i++) {
					var n = p.childList[i];
					if( !n.bSelected && !n.data.isStatusNode && !n.data.unselectable) {
					// issue 305 proposes this:
//					if( !n.bSelected && !n.data.isStatusNode ) {
						allChildsSelected = false;
						break;
					}
				}
				if( allChildsSelected ){
					p._select(true, false, false);
				}
				p = p.parent;
			}
		} else {
			// Deselect all children
			this._setSubSel(false);
			this.visit(function(node){
				node._setSubSel(false);
				node._select(false, false, false);
			});
			// Deselect parents, and recalc hasSubSel
			p = this.parent;
			while( p ) {
				p._select(false, false, false);
				var isPartSel = false;
				for(i=0, l=p.childList.length; i<l;  i++) {
					if( p.childList[i].bSelected || p.childList[i].hasSubSel ) {
						isPartSel = true;
						break;
					}
				}
				p._setSubSel(isPartSel);
				p = p.parent;
			}
		}
	},

	_select: function(sel, fireEvents, deep) {
		// Select - but not focus - this node.
//		this.tree.logDebug("dtnode._select(%o) - %o", sel, this);
		var opts = this.tree.options;
		if( this.data.isStatusNode ){
			return;
		}
		//
		if( this.bSelected === sel ) {
//			this.tree.logDebug("dtnode._select(%o) IGNORED - %o", sel, this);
			return;
		}
		// Allow event listener to abort selection
		if ( fireEvents && opts.onQuerySelect && opts.onQuerySelect.call(this.tree, sel, this) === false ){
			return; // Callback returned false
		}
		// Force single-selection
		if( opts.selectMode==1 && sel ) {
			this.tree.visit(function(node){
				if( node.bSelected ) {
					// Deselect; assuming that in selectMode:1 there's max. one other selected node
					node._select(false, false, false);
					return false;
				}
			});
		}

		this.bSelected = sel;
//        this.tree._changeNodeList("select", this, sel);

		if( sel ) {
			if( opts.persist ){
				this.tree.persistence.addSelect(this.data.key);
			}
			$(this.span).addClass(opts.classNames.selected);

			if( deep && opts.selectMode === 3 ){
				this._fixSelectionState();
			}
			if ( fireEvents && opts.onSelect ){
				opts.onSelect.call(this.tree, true, this);
			}
		} else {
			if( opts.persist ){
				this.tree.persistence.clearSelect(this.data.key);
			}
			$(this.span).removeClass(opts.classNames.selected);

			if( deep && opts.selectMode === 3 ){
				this._fixSelectionState();
			}
			if ( fireEvents && opts.onSelect ){
				opts.onSelect.call(this.tree, false, this);
			}
		}
	},

	select: function(sel) {
		// Select - but not focus - this node.
//		this.tree.logDebug("dtnode.select(%o) - %o", sel, this);
		if( this.data.unselectable ){
			return this.bSelected;
		}
		return this._select(sel!==false, true, true);
	},

	toggleSelect: function() {
//		this.tree.logDebug("dtnode.toggleSelect() - %o", this);
		return this.select(!this.bSelected);
	},

	isSelected: function() {
		return this.bSelected;
	},

	isLazy: function() {
		return !!this.data.isLazy;
	},

	_loadContent: function() {
		try {
			var opts = this.tree.options;
			this.tree.logDebug("_loadContent: start - %o", this);
			this.setLazyNodeStatus(DTNodeStatus_Loading);
			if( true === opts.onLazyRead.call(this.tree, this) ) {
				// If function returns 'true', we assume that the loading is done:
				this.setLazyNodeStatus(DTNodeStatus_Ok);
				// Otherwise (i.e. if the loading was started as an asynchronous process)
				// the onLazyRead(dtnode) handler is expected to call dtnode.setLazyNodeStatus(DTNodeStatus_Ok/_Error) when done.
				this.tree.logDebug("_loadContent: succeeded - %o", this);
			}
		} catch(e) {
			this.tree.logWarning("_loadContent: failed - %o", e);
			this.setLazyNodeStatus(DTNodeStatus_Error, {tooltip: ""+e});
		}
	},

	_expand: function(bExpand, forceSync) {
		if( this.bExpanded === bExpand ) {
			this.tree.logDebug("dtnode._expand(%o) IGNORED - %o", bExpand, this);
			return;
		}
		this.tree.logDebug("dtnode._expand(%o) - %o", bExpand, this);
		var opts = this.tree.options;
		if( !bExpand && this.getLevel() < opts.minExpandLevel ) {
			this.tree.logDebug("dtnode._expand(%o) prevented collapse - %o", bExpand, this);
			return;
		}
		if ( opts.onQueryExpand && opts.onQueryExpand.call(this.tree, bExpand, this) === false ){
			return; // Callback returned false
		}
		this.bExpanded = bExpand;

		// Persist expand state
		if( opts.persist ) {
			if( bExpand ){
				this.tree.persistence.addExpand(this.data.key);
			}else{
				this.tree.persistence.clearExpand(this.data.key);
			}
		}
		// Do not apply animations in init phase, or before lazy-loading
		var allowEffects = !(this.data.isLazy && this.childList === null)
			&& !this._isLoading
			&& !forceSync;
		this.render(allowEffects);

		// Auto-collapse mode: collapse all siblings
		if( this.bExpanded && this.parent && opts.autoCollapse ) {
			var parents = this._parentList(false, true);
			for(var i=0, l=parents.length; i<l; i++){
				parents[i].collapseSiblings();
			}
		}
		// If the currently active node is now hidden, deactivate it
		if( opts.activeVisible && this.tree.activeNode && ! this.tree.activeNode.isVisible() ) {
			this.tree.activeNode.deactivate();
		}
		// Expanding a lazy node: set 'loading...' and call callback
		if( bExpand && this.data.isLazy && this.childList === null && !this._isLoading ) {
			this._loadContent();
			return;
		}
		if ( opts.onExpand ){
			opts.onExpand.call(this.tree, bExpand, this);
		}
	},

	isExpanded: function() {
		return this.bExpanded;
	},

	expand: function(flag) {
		flag = (flag !== false);
		if( !this.childList && !this.data.isLazy && flag ){
			return; // Prevent expanding empty nodes
		} else if( this.parent === null && !flag ){
			return; // Prevent collapsing the root
		}
		this._expand(flag);
	},

	scheduleAction: function(mode, ms) {
		/** Schedule activity for delayed execution (cancel any pending request).
		 *  scheduleAction('cancel') will cancel the request.
		 */
		if( this.tree.timer ) {
			clearTimeout(this.tree.timer);
			this.tree.logDebug("clearTimeout(%o)", this.tree.timer);
		}
		var self = this; // required for closures
		switch (mode) {
		case "cancel":
			// Simply made sure that timer was cleared
			break;
		case "expand":
			this.tree.timer = setTimeout(function(){
				self.tree.logDebug("setTimeout: trigger expand");
				self.expand(true);
			}, ms);
			break;
		case "activate":
			this.tree.timer = setTimeout(function(){
				self.tree.logDebug("setTimeout: trigger activate");
				self.activate();
			}, ms);
			break;
		default:
			throw "Invalid mode " + mode;
		}
		this.tree.logDebug("setTimeout(%s, %s): %s", mode, ms, this.tree.timer);
	},

	toggleExpand: function() {
		this.expand(!this.bExpanded);
	},

	collapseSiblings: function() {
		if( this.parent === null ){
			return;
		}
		var ac = this.parent.childList;
		for (var i=0, l=ac.length; i<l; i++) {
			if ( ac[i] !== this && ac[i].bExpanded ){
				ac[i]._expand(false);
			}
		}
	},

	_onClick: function(event) {
//		this.tree.logDebug("dtnode.onClick(" + event.type + "): dtnode:" + this + ", button:" + event.button + ", which: " + event.which);
		var targetType = this.getEventTargetType(event);
		if( targetType === "expander" ) {
			// Clicking the expander icon always expands/collapses
			this.toggleExpand();
			this.focus(); // issue 95
		} else if( targetType === "checkbox" ) {
			// Clicking the checkbox always (de)selects
			this.toggleSelect();
			this.focus(); // issue 95
		} else {
			this._userActivate();
			var aTag = this.span.getElementsByTagName("a");
			if(aTag[0]){
				// issue 154, 313
//                if(!($.browser.msie && parseInt($.browser.version, 10) < 9)){
				if(!(BROWSER.msie && parseInt(BROWSER.version, 10) < 9)){
					aTag[0].focus();
				}
			}else{
				// 'noLink' option was set
				return true;
			}
		}
		// Make sure that clicks stop, otherwise <a href='#'> jumps to the top
		event.preventDefault();
	},

	_onDblClick: function(event) {
//		this.tree.logDebug("dtnode.onDblClick(" + event.type + "): dtnode:" + this + ", button:" + event.button + ", which: " + event.which);
	},

	_onKeydown: function(event) {
//		this.tree.logDebug("dtnode.onKeydown(" + event.type + "): dtnode:" + this + ", charCode:" + event.charCode + ", keyCode: " + event.keyCode + ", which: " + event.which);
		var handled = true,
			sib;
//		alert("keyDown" + event.which);

		switch( event.which ) {
			// charCodes:
//			case 43: // '+'
			case 107: // '+'
			case 187: // '+' @ Chrome, Safari
				if( !this.bExpanded ){ this.toggleExpand(); }
				break;
//			case 45: // '-'
			case 109: // '-'
			case 189: // '+' @ Chrome, Safari
				if( this.bExpanded ){ this.toggleExpand(); }
				break;
			//~ case 42: // '*'
				//~ break;
			//~ case 47: // '/'
				//~ break;
			// case 13: // <enter>
				// <enter> on a focused <a> tag seems to generate a click-event.
				// this._userActivate();
				// break;
			case 32: // <space>
				this._userActivate();
				break;
			case 8: // <backspace>
				if( this.parent ){
					this.parent.focus();
				}
				break;
			case 37: // <left>
				if( this.bExpanded ) {
					this.toggleExpand();
					this.focus();
//				} else if( this.parent && (this.tree.options.rootVisible || this.parent.parent) ) {
				} else if( this.parent && this.parent.parent ) {
					this.parent.focus();
				}
				break;
			case 39: // <right>
				if( !this.bExpanded && (this.childList || this.data.isLazy) ) {
					this.toggleExpand();
					this.focus();
				} else if( this.childList ) {
					this.childList[0].focus();
				}
				break;
			case 38: // <up>
				sib = this.getPrevSibling();
				while( sib && sib.bExpanded && sib.childList ){
					sib = sib.childList[sib.childList.length-1];
				}
//				if( !sib && this.parent && (this.tree.options.rootVisible || this.parent.parent) )
				if( !sib && this.parent && this.parent.parent ){
					sib = this.parent;
				}
				if( sib ){
					sib.focus();
				}
				break;
			case 40: // <down>
				if( this.bExpanded && this.childList ) {
					sib = this.childList[0];
				} else {
					var parents = this._parentList(false, true);
					for(var i=parents.length-1; i>=0; i--) {
						sib = parents[i].getNextSibling();
						if( sib ){ break; }
					}
				}
				if( sib ){
					sib.focus();
				}
				break;
			default:
				handled = false;
		}
		// Return false, if handled, to prevent default processing
//		return !handled;
		if(handled){
			event.preventDefault();
		}
	},

	_onKeypress: function(event) {
		// onKeypress is only hooked to allow user callbacks.
		// We don't process it, because IE and Safari don't fire keypress for cursor keys.
//		this.tree.logDebug("dtnode.onKeypress(" + event.type + "): dtnode:" + this + ", charCode:" + event.charCode + ", keyCode: " + event.keyCode + ", which: " + event.which);
	},

	_onFocus: function(event) {
		// Handles blur and focus events.
//		this.tree.logDebug("dtnode._onFocus(%o): %o", event, this);
		var opts = this.tree.options;
		if ( event.type == "blur" || event.type == "focusout" ) {
			if ( opts.onBlur ){
				opts.onBlur.call(this.tree, this);
			}
			if( this.tree.tnFocused ){
				$(this.tree.tnFocused.span).removeClass(opts.classNames.focused);
			}
			this.tree.tnFocused = null;
			if( opts.persist ){
				$.cookie(opts.cookieId+"-focus", "", opts.cookie);
			}
		} else if ( event.type=="focus" || event.type=="focusin") {
			// Fix: sometimes the blur event is not generated
			if( this.tree.tnFocused && this.tree.tnFocused !== this ) {
				this.tree.logDebug("dtnode.onFocus: out of sync: curFocus: %o", this.tree.tnFocused);
				$(this.tree.tnFocused.span).removeClass(opts.classNames.focused);
			}
			this.tree.tnFocused = this;
			if ( opts.onFocus ){
				opts.onFocus.call(this.tree, this);
			}
			$(this.tree.tnFocused.span).addClass(opts.classNames.focused);
			if( opts.persist ){
				$.cookie(opts.cookieId+"-focus", this.data.key, opts.cookie);
			}
		}
		//     : return anything?
//		return false;
	},

	visit: function(fn, includeSelf) {
		// Call fn(node) for all child nodes. Stop iteration, if fn() returns false.
		var res = true;
		if( includeSelf === true ) {
			res = fn(this);
			if( res === false || res == "skip" ){
				return res;
			}
		}
		if(this.childList){
			for(var i=0, l=this.childList.length; i<l; i++){
				res = this.childList[i].visit(fn, true);
				if( res === false ){
					break;
				}
			}
		}
		return res;
	},

	visitParents: function(fn, includeSelf) {
		// Visit parent nodes (bottom up)
		if(includeSelf && fn(this) === false){
			return false;
		}
		var p = this.parent;
		while( p ) {
			if(fn(p) === false){
				return false;
			}
			p = p.parent;
		}
		return true;
	},

	remove: function() {
		// Remove this node
//		this.tree.logDebug ("%s.remove()", this);
		if ( this === this.tree.root ){
			throw "Cannot remove system root";
		}
		return this.parent.removeChild(this);
	},

	removeChild: function(tn) {
		// Remove tn from list of direct children.
		var ac = this.childList;
		if( ac.length == 1 ) {
			if( tn !== ac[0] ){
				throw "removeChild: invalid child";
			}
			return this.removeChildren();
		}
		if( tn === this.tree.activeNode ){
			tn.deactivate();
		}
		if( this.tree.options.persist ) {
			if( tn.bSelected ){
				this.tree.persistence.clearSelect(tn.data.key);
			}
			if ( tn.bExpanded ){
				this.tree.persistence.clearExpand(tn.data.key);
			}
		}
		tn.removeChildren(true);
		if(this.ul){
//			$("li", $(this.ul)).remove(); // issue 399
			this.ul.removeChild(tn.li); // issue 402
		}
		for(var i=0, l=ac.length; i<l; i++) {
			if( ac[i] === tn ) {
				this.childList.splice(i, 1);
//				delete tn;  // JSLint complained
				break;
			}
		}
	},

	removeChildren: function(isRecursiveCall, retainPersistence) {
		// Remove all child nodes (more efficiently than recursive remove())
		this.tree.logDebug("%s.removeChildren(%o)", this, isRecursiveCall);
		var tree = this.tree;
		var ac = this.childList;
		if( ac ) {
			for(var i=0, l=ac.length; i<l; i++) {
				var tn = ac[i];
				if ( tn === tree.activeNode && !retainPersistence ){
					tn.deactivate();
				}
				if( this.tree.options.persist && !retainPersistence ) {
					if( tn.bSelected ){
						this.tree.persistence.clearSelect(tn.data.key);
					}
					if ( tn.bExpanded ){
						this.tree.persistence.clearExpand(tn.data.key);
					}
				}
				tn.removeChildren(true, retainPersistence);
				if(this.ul){
//					this.ul.removeChild(tn.li);
					$("li", $(this.ul)).remove(); // issue 231
				}
//				delete tn;  JSLint complained
			}
			// Set to 'null' which is interpreted as 'not yet loaded' for lazy
			// nodes
			this.childList = null;
		}
		if( ! isRecursiveCall ) {
//			this._expand(false);
//			this.isRead = false;
			this._isLoading = false;
			this.render();
		}
	},

	setTitle: function(title) {
		this.fromDict({title: title});
	},

	reload: function(force) {
		throw "Use reloadChildren() instead";
	},

	reloadChildren: function(callback) {
		// Reload lazy content (expansion state is maintained).
		if( this.parent === null ){
			throw "Use tree.reload() instead";
		}else if( ! this.data.isLazy ){
			throw "node.reloadChildren() requires lazy nodes.";
		}
		// appendAjax triggers 'nodeLoaded' event.
		// We listen to this, if a callback was passed to reloadChildren
		if(callback){
			var self = this;
			var eventType = "nodeLoaded.dynatree." + this.tree.$tree.attr("id")
				+ "." + this.data.key;
			this.tree.$tree.bind(eventType, function(e, node, isOk){
				self.tree.$tree.unbind(eventType);
				self.tree.logDebug("loaded %o, %o, %o", e, node, isOk);
				if(node !== self){
					throw "got invalid load event";
				}
				callback.call(self.tree, node, isOk);
			});
		}
		// The expansion state is maintained
		this.removeChildren();
		this._loadContent();
//		if( this.bExpanded ) {
//			// Remove children first, to prevent effects being applied
//			this.removeChildren();
//			// then force re-expand to trigger lazy loading
////			this.expand(false);
////			this.expand(true);
//			this._loadContent();
//		} else {
//			this.removeChildren();
//			this._loadContent();
//		}
	},

	/**
	 * Make sure the node with a given key path is available in the tree.
	 */
	_loadKeyPath: function(keyPath, callback) {
		var tree = this.tree;
		tree.logDebug("%s._loadKeyPath(%s)", this, keyPath);
		if(keyPath === ""){
			throw "Key path must not be empty";
		}
		var segList = keyPath.split(tree.options.keyPathSeparator);
		if(segList[0] === ""){
			throw "Key path must be relative (don't start with '/')";
		}
		var seg = segList.shift();
		if(this.childList){
			for(var i=0, l=this.childList.length; i < l; i++){
				var child = this.childList[i];
				if( child.data.key === seg ){
					if(segList.length === 0) {
						// Found the end node
						callback.call(tree, child, "ok");

					}else if(child.data.isLazy && (child.childList === null || child.childList === undefined)){
						tree.logDebug("%s._loadKeyPath(%s) -> reloading %s...", this, keyPath, child);
						var self = this;
						// Note: this line gives a JSLint warning (Don't make functions within a loop)
						/*jshint loopfunc:true */
						child.reloadChildren(function(node, isOk){
							// After loading, look for direct child with that key
							if(isOk){
								tree.logDebug("%s._loadKeyPath(%s) -> reloaded %s.", node, keyPath, node);
								callback.call(tree, child, "loaded");
								node._loadKeyPath(segList.join(tree.options.keyPathSeparator), callback);
							}else{
								tree.logWarning("%s._loadKeyPath(%s) -> reloadChildren() failed.", self, keyPath);
								callback.call(tree, child, "error");
							}
						});
						// we can ignore it, since it will only be exectuted once, the the loop is ended
						// See also http://stackoverflow.com/questions/3037598/how-to-get-around-the-jslint-error-dont-make-functions-within-a-loop
					} else {
						callback.call(tree, child, "loaded");
						// Look for direct child with that key
						child._loadKeyPath(segList.join(tree.options.keyPathSeparator), callback);
					}
					return;
				}
			}
		}
		// Could not find key
		// Callback params: child: undefined, the segment, isEndNode (segList.length === 0)
		callback.call(tree, undefined, "notfound", seg, segList.length === 0);
		tree.logWarning("Node not found: " + seg);
		return;
	},

	resetLazy: function() {
		// Discard lazy content.
		if( this.parent === null ){
			throw "Use tree.reload() instead";
		}else if( ! this.data.isLazy ){
			throw "node.resetLazy() requires lazy nodes.";
		}
		this.expand(false);
		this.removeChildren();
	},

	_addChildNode: function(dtnode, beforeNode) {
		/**
		 * Internal function to add one single DynatreeNode as a child.
		 *
		 */
		var tree = this.tree,
			opts = tree.options,
			pers = tree.persistence;

//		tree.logDebug("%s._addChildNode(%o)", this, dtnode);

		// --- Update and fix dtnode attributes if necessary
		dtnode.parent = this;
//		if( beforeNode && (beforeNode.parent !== this || beforeNode === dtnode ) )
//			throw "<beforeNode> must be another child of <this>";

		// --- Add dtnode as a child
		if ( this.childList === null ) {
			this.childList = [];
		} else if( ! beforeNode ) {
			// Fix 'lastsib'
			if(this.childList.length > 0) {
				$(this.childList[this.childList.length-1].span).removeClass(opts.classNames.lastsib);
			}
		}
		if( beforeNode ) {
			var iBefore = $.inArray(beforeNode, this.childList);
			if( iBefore < 0 ){
				throw "<beforeNode> must be a child of <this>";
			}
			this.childList.splice(iBefore, 0, dtnode);
		} else {
			// Append node
			this.childList.push(dtnode);
		}

		// --- Handle persistence
		// Initial status is read from cookies, if persistence is active and
		// cookies are already present.
		// Otherwise the status is read from the data attributes and then persisted.
		var isInitializing = tree.isInitializing();
		if( opts.persist && pers.cookiesFound && isInitializing ) {
			// Init status from cookies
//			tree.logDebug("init from cookie, pa=%o, dk=%o", pers.activeKey, dtnode.data.key);
			if( pers.activeKey === dtnode.data.key ){
				tree.activeNode = dtnode;
			}
			if( pers.focusedKey === dtnode.data.key ){
				tree.focusNode = dtnode;
			}
			dtnode.bExpanded = ($.inArray(dtnode.data.key, pers.expandedKeyList) >= 0);
			dtnode.bSelected = ($.inArray(dtnode.data.key, pers.selectedKeyList) >= 0);
//			tree.logDebug("    key=%o, bSelected=%o", dtnode.data.key, dtnode.bSelected);
		} else {
			// Init status from data (Note: we write the cookies after the init phase)
//			tree.logDebug("init from data");
			if( dtnode.data.activate ) {
				tree.activeNode = dtnode;
				if( opts.persist ){
					pers.activeKey = dtnode.data.key;
				}
			}
			if( dtnode.data.focus ) {
				tree.focusNode = dtnode;
				if( opts.persist ){
					pers.focusedKey = dtnode.data.key;
				}
			}
			dtnode.bExpanded = ( dtnode.data.expand === true ); // Collapsed by default
			if( dtnode.bExpanded && opts.persist ){
				pers.addExpand(dtnode.data.key);
			}
			dtnode.bSelected = ( dtnode.data.select === true ); // Deselected by default
/*
			Doesn't work, cause pers.selectedKeyList may be null
			if( dtnode.bSelected && opts.selectMode==1
				&& pers.selectedKeyList && pers.selectedKeyList.length>0 ) {
				tree.logWarning("Ignored multi-selection in single-mode for %o", dtnode);
				dtnode.bSelected = false; // Fixing bad input data (multi selection for mode:1)
			}
*/
			if( dtnode.bSelected && opts.persist ){
				pers.addSelect(dtnode.data.key);
			}
		}

		// Always expand, if it's below minExpandLevel
//		tree.logDebug ("%s._addChildNode(%o), l=%o", this, dtnode, dtnode.getLevel());
		if ( opts.minExpandLevel >= dtnode.getLevel() ) {
//			tree.logDebug ("Force expand for %o", dtnode);
			this.bExpanded = true;
		}

		// In multi-hier mode, update the parents selection state
		// issue #82: only if not initializing, because the children may not exist yet
//		if( !dtnode.data.isStatusNode && opts.selectMode==3 && !isInitializing )
//			dtnode._fixSelectionState();

		// In multi-hier mode, update the parents selection state
		if( dtnode.bSelected && opts.selectMode==3 ) {
			var p = this;
			while( p ) {
				if( !p.hasSubSel ){
					p._setSubSel(true);
				}
				p = p.parent;
			}
		}
		// render this node and the new child
		if ( tree.bEnableUpdate ){
			this.render();
		}
		return dtnode;
	},

	addChild: function(obj, beforeNode) {
		/**
		 * Add a node object as child.
		 *
		 * This should be the only place, where a DynaTreeNode is constructed!
		 * (Except for the root node creation in the tree constructor)
		 *
		 * @param obj A JS object (may be recursive) or an array of those.
		 * @param {DynaTreeNode} beforeNode (optional) sibling node.
		 *
		 * Data format: array of node objects, with optional 'children' attributes.
		 * [
		 *	{ title: "t1", isFolder: true, ... }
		 *	{ title: "t2", isFolder: true, ...,
		 *		children: [
		 *			{title: "t2.1", ..},
		 *			{..}
		 *			]
		 *	}
		 * ]
		 * A simple object is also accepted instead of an array.
		 *
		 */
//		this.tree.logDebug("%s.addChild(%o, %o)", this, obj, beforeNode);
		if(typeof(obj) == "string"){
			throw "Invalid data type for " + obj;
		}else if( !obj || obj.length === 0 ){ // Passed null or undefined or empty array
			return;
		}else if( obj instanceof DynaTreeNode ){
			return this._addChildNode(obj, beforeNode);
		}

		if( !obj.length ){ // Passed a single data object
			obj = [ obj ];
		}
		var prevFlag = this.tree.enableUpdate(false);

		var tnFirst = null;
		for (var i=0, l=obj.length; i<l; i++) {
			var data = obj[i];
			var dtnode = this._addChildNode(new DynaTreeNode(this, this.tree, data), beforeNode);
			if( !tnFirst ){
				tnFirst = dtnode;
			}
			// Add child nodes recursively
			if( data.children ){
				dtnode.addChild(data.children, null);
			}
		}
		this.tree.enableUpdate(prevFlag);
		return tnFirst;
	},

	append: function(obj) {
		this.tree.logWarning("node.append() is deprecated (use node.addChild() instead).");
		return this.addChild(obj, null);
	},

	appendAjax: function(ajaxOptions) {
		var self = this;
		this.removeChildren(false, true);
		this.setLazyNodeStatus(DTNodeStatus_Loading);
		// Debug feature: force a delay, to simulate slow loading...
		if(ajaxOptions.debugLazyDelay){
			var ms = ajaxOptions.debugLazyDelay;
			ajaxOptions.debugLazyDelay = 0;
			this.tree.logInfo("appendAjax: waiting for debugLazyDelay " + ms);
			setTimeout(function(){self.appendAjax(ajaxOptions);}, ms);
			return;
		}
		// Ajax option inheritance: $.ajaxSetup < $.ui.dynatree.prototype.options.ajaxDefaults < tree.options.ajaxDefaults < ajaxOptions
		var orgSuccess = ajaxOptions.success,
			orgError = ajaxOptions.error,
			eventType = "nodeLoaded.dynatree." + this.tree.$tree.attr("id") + "." + this.data.key;
		var options = $.extend({}, this.tree.options.ajaxDefaults, ajaxOptions, {
			success: function(data, textStatus, jqXHR){
				// <this> is the request options
//				self.tree.logDebug("appendAjax().success");
				var prevPhase = self.tree.phase;
				self.tree.phase = "init";
				// postProcess is similar to the standard dataFilter hook,
				// but it is also called for JSONP
				if( options.postProcess ){
					data = options.postProcess.call(this, data, this.dataType);
				}
				// Process ASPX WebMethod JSON object inside "d" property
				// http://code.google.com/p/dynatree/issues/detail?id=202
				else if (data && data.hasOwnProperty("d")) {
				   data = (typeof data.d) == "string" ? $.parseJSON(data.d) : data.d;
				}
				if(!$.isArray(data) || data.length !== 0){
					self.addChild(data, null);
				}
				self.tree.phase = "postInit";
				if( orgSuccess ){
					orgSuccess.call(options, self, data, textStatus);
				}
				self.tree.logDebug("trigger " + eventType);
				self.tree.$tree.trigger(eventType, [self, true]);
				self.tree.phase = prevPhase;
				// This should be the last command, so node._isLoading is true
				// while the callbacks run
				self.setLazyNodeStatus(DTNodeStatus_Ok);
				if($.isArray(data) && data.length === 0){
					// Set to [] which is interpreted as 'no children' for lazy
					// nodes
					self.childList = [];
					self.render();
				}
				},
			error: function(jqXHR, textStatus, errorThrown){
				// <this> is the request options
				self.tree.logWarning("appendAjax failed:", textStatus, ":\n", jqXHR, "\n", errorThrown);
				if( orgError ){
					orgError.call(options, self, jqXHR, textStatus, errorThrown);
				}
				self.tree.$tree.trigger(eventType, [self, false]);
				self.setLazyNodeStatus(DTNodeStatus_Error, {info: textStatus, tooltip: "" + errorThrown});
				}
		});
		$.ajax(options);
	},

	move: function(targetNode, mode) {
		/**Move this node to targetNode.
		 *  mode 'child': append this node as last child of targetNode.
		 *                This is the default. To be compatble with the D'n'd
		 *                hitMode, we also accept 'over'.
		 *  mode 'before': add this node as sibling before targetNode.
		 *  mode 'after': add this node as sibling after targetNode.
		 */
		var pos;
		if(this === targetNode){
			return;
		}
		if( !this.parent  ){
			throw "Cannot move system root";
		}
		if(mode === undefined || mode == "over"){
			mode = "child";
		}
		var prevParent = this.parent;
		var targetParent = (mode === "child") ? targetNode : targetNode.parent;
		if( targetParent.isDescendantOf(this) ){
			throw "Cannot move a node to it's own descendant";
		}
		// Unlink this node from current parent
		if( this.parent.childList.length == 1 ) {
			this.parent.childList = this.parent.data.isLazy ? [] : null;
			this.parent.bExpanded = false;
		} else {
			pos = $.inArray(this, this.parent.childList);
			if( pos < 0 ){
				throw "Internal error";
			}
			this.parent.childList.splice(pos, 1);
		}
		// Remove from source DOM parent
		if(this.parent.ul){
			this.parent.ul.removeChild(this.li);
		}

		// Insert this node to target parent's child list
		this.parent = targetParent;
		if( targetParent.hasChildren() ) {
			switch(mode) {
			case "child":
				// Append to existing target children
				targetParent.childList.push(this);
				break;
			case "before":
				// Insert this node before target node
				pos = $.inArray(targetNode, targetParent.childList);
				if( pos < 0 ){
					throw "Internal error";
				}
				targetParent.childList.splice(pos, 0, this);
				break;
			case "after":
				// Insert this node after target node
				pos = $.inArray(targetNode, targetParent.childList);
				if( pos < 0 ){
					throw "Internal error";
				}
				targetParent.childList.splice(pos+1, 0, this);
				break;
			default:
				throw "Invalid mode " + mode;
			}
		} else {
			targetParent.childList = [ this ];
		}
		// Parent has no <ul> tag yet:
		if( !targetParent.ul ) {
			// This is the parent's first child: create UL tag
			// (Hidden, because it will be
			targetParent.ul = document.createElement("ul");
			targetParent.ul.style.display = "none";
			targetParent.li.appendChild(targetParent.ul);
		}
		// Issue 319: Add to target DOM parent (only if node was already rendered(expanded))
		if(this.li){
			targetParent.ul.appendChild(this.li);
		}

		if( this.tree !== targetNode.tree ) {
			// Fix node.tree for all source nodes
			this.visit(function(node){
				node.tree = targetNode.tree;
			}, null, true);
			throw "Not yet implemented.";
		}
		//     : fix selection state
		//     : fix active state
		if( !prevParent.isDescendantOf(targetParent)) {
			prevParent.render();
		}
		if( !targetParent.isDescendantOf(prevParent) ) {
			targetParent.render();
		}
//		this.tree.redraw();
/*
		var tree = this.tree;
		var opts = tree.options;
		var pers = tree.persistence;


		// Always expand, if it's below minExpandLevel
//		tree.logDebug ("%s._addChildNode(%o), l=%o", this, dtnode, dtnode.getLevel());
		if ( opts.minExpandLevel >= dtnode.getLevel() ) {
//			tree.logDebug ("Force expand for %o", dtnode);
			this.bExpanded = true;
		}

		// In multi-hier mode, update the parents selection state
		// issue #82: only if not initializing, because the children may not exist yet
//		if( !dtnode.data.isStatusNode && opts.selectMode==3 && !isInitializing )
//			dtnode._fixSelectionState();

		// In multi-hier mode, update the parents selection state
		if( dtnode.bSelected && opts.selectMode==3 ) {
			var p = this;
			while( p ) {
				if( !p.hasSubSel )
					p._setSubSel(true);
				p = p.parent;
			}
		}
		// render this node and the new child
		if ( tree.bEnableUpdate )
			this.render();

		return dtnode;

*/
	},

	// --- end of class
	lastentry: undefined
};

/*************************************************************************
 * class DynaTreeStatus
 */

var DynaTreeStatus = Class.create();


DynaTreeStatus._getTreePersistData = function(cookieId, cookieOpts) {
	// Static member: Return persistence information from cookies
	var ts = new DynaTreeStatus(cookieId, cookieOpts);
	ts.read();
	return ts.toDict();
};
// Make available in global scope
getDynaTreePersistData = DynaTreeStatus._getTreePersistData; //     : deprecated


DynaTreeStatus.prototype = {
	// Constructor
	initialize: function(cookieId, cookieOpts) {
//		this._log("DynaTreeStatus: initialize");
		if( cookieId === undefined ){
			cookieId = $.ui.dynatree.prototype.options.cookieId;
		}
		cookieOpts = $.extend({}, $.ui.dynatree.prototype.options.cookie, cookieOpts);

		this.cookieId = cookieId;
		this.cookieOpts = cookieOpts;
		this.cookiesFound = undefined;
		this.activeKey = null;
		this.focusedKey = null;
		this.expandedKeyList = null;
		this.selectedKeyList = null;
	},
	// member functions
	_log: function(msg) {
		//	this.logDebug("_changeNodeList(%o): nodeList:%o, idx:%o", mode, nodeList, idx);
		Array.prototype.unshift.apply(arguments, ["debug"]);
		_log.apply(this, arguments);
	},
	read: function() {
//		this._log("DynaTreeStatus: read");
		// Read or init cookies.
		this.cookiesFound = false;

		var cookie = $.cookie(this.cookieId + "-active");
		this.activeKey = ( cookie === null ) ? "" : cookie;
		if( cookie !== null ){
			this.cookiesFound = true;
		}
		cookie = $.cookie(this.cookieId + "-focus");
		this.focusedKey = ( cookie === null ) ? "" : cookie;
		if( cookie !== null ){
			this.cookiesFound = true;
		}
		cookie = $.cookie(this.cookieId + "-expand");
		this.expandedKeyList = ( cookie === null ) ? [] : cookie.split(",");
		if( cookie !== null ){
			this.cookiesFound = true;
		}
		cookie = $.cookie(this.cookieId + "-select");
		this.selectedKeyList = ( cookie === null ) ? [] : cookie.split(",");
		if( cookie !== null ){
			this.cookiesFound = true;
		}
	},
	write: function() {
//		this._log("DynaTreeStatus: write");
		$.cookie(this.cookieId + "-active", ( this.activeKey === null ) ? "" : this.activeKey, this.cookieOpts);
		$.cookie(this.cookieId + "-focus", ( this.focusedKey === null ) ? "" : this.focusedKey, this.cookieOpts);
		$.cookie(this.cookieId + "-expand", ( this.expandedKeyList === null ) ? "" : this.expandedKeyList.join(","), this.cookieOpts);
		$.cookie(this.cookieId + "-select", ( this.selectedKeyList === null ) ? "" : this.selectedKeyList.join(","), this.cookieOpts);
	},
	addExpand: function(key) {
//		this._log("addExpand(%o)", key);
		if( $.inArray(key, this.expandedKeyList) < 0 ) {
			this.expandedKeyList.push(key);
			$.cookie(this.cookieId + "-expand", this.expandedKeyList.join(","), this.cookieOpts);
		}
	},
	clearExpand: function(key) {
//		this._log("clearExpand(%o)", key);
		var idx = $.inArray(key, this.expandedKeyList);
		if( idx >= 0 ) {
			this.expandedKeyList.splice(idx, 1);
			$.cookie(this.cookieId + "-expand", this.expandedKeyList.join(","), this.cookieOpts);
		}
	},
	addSelect: function(key) {
//		this._log("addSelect(%o)", key);
		if( $.inArray(key, this.selectedKeyList) < 0 ) {
			this.selectedKeyList.push(key);
			$.cookie(this.cookieId + "-select", this.selectedKeyList.join(","), this.cookieOpts);
		}
	},
	clearSelect: function(key) {
//		this._log("clearSelect(%o)", key);
		var idx = $.inArray(key, this.selectedKeyList);
		if( idx >= 0 ) {
			this.selectedKeyList.splice(idx, 1);
			$.cookie(this.cookieId + "-select", this.selectedKeyList.join(","), this.cookieOpts);
		}
	},
	isReloading: function() {
		return this.cookiesFound === true;
	},
	toDict: function() {
		return {
			cookiesFound: this.cookiesFound,
			activeKey: this.activeKey,
			focusedKey: this.activeKey,
			expandedKeyList: this.expandedKeyList,
			selectedKeyList: this.selectedKeyList
		};
	},
	// --- end of class
	lastentry: undefined
};


/*************************************************************************
 * class DynaTree
 */

var DynaTree = Class.create();

// --- Static members ----------------------------------------------------------

DynaTree.version = "$Version: 1.2.4$";

/*
DynaTree._initTree = function() {
};

DynaTree._bind = function() {
};
*/
//--- Class members ------------------------------------------------------------

DynaTree.prototype = {
	// Constructor
//	initialize: function(divContainer, options) {
	initialize: function($widget) {
		// instance members
		this.phase = "init";
		this.$widget = $widget;
		this.options = $widget.options;
		this.$tree = $widget.element;
		this.timer = null;
		// find container element
		this.divTree = this.$tree.get(0);

//		var parentPos = $(this.divTree).parent().offset();
//		this.parentTop = parentPos.top;
//		this.parentLeft = parentPos.left;

		_initDragAndDrop(this);
	},

	// member functions

	_load: function(callback) {
		var $widget = this.$widget;
		var opts = this.options,
			self = this;
		this.bEnableUpdate = true;
		this._nodeCount = 1;
		this.activeNode = null;
		this.focusNode = null;

		// Some deprecation warnings to help with migration
		if( opts.rootVisible !== undefined ){
			this.logWarning("Option 'rootVisible' is no longer supported.");
		}
		if( opts.minExpandLevel < 1 ) {
			this.logWarning("Option 'minExpandLevel' must be >= 1.");
			opts.minExpandLevel = 1;
		}
//		_log("warn", "jQuery.support.boxModel " + jQuery.support.boxModel);

		// If a 'options.classNames' dictionary was passed, still use defaults
		// for undefined classes:
		if( opts.classNames !== $.ui.dynatree.prototype.options.classNames ) {
			opts.classNames = $.extend({}, $.ui.dynatree.prototype.options.classNames, opts.classNames);
		}
		if( opts.ajaxDefaults !== $.ui.dynatree.prototype.options.ajaxDefaults ) {
			opts.ajaxDefaults = $.extend({}, $.ui.dynatree.prototype.options.ajaxDefaults, opts.ajaxDefaults);
		}
		if( opts.dnd !== $.ui.dynatree.prototype.options.dnd ) {
			opts.dnd = $.extend({}, $.ui.dynatree.prototype.options.dnd, opts.dnd);
		}
		// Guess skin path, if not specified
		if(!opts.imagePath) {
			$("script").each( function () {
				var _rexDtLibName = /.*dynatree[^\/]*\.js$/i;
				if( this.src.search(_rexDtLibName) >= 0 ) {
					if( this.src.indexOf("/")>=0 ){ // issue #47
						opts.imagePath = this.src.slice(0, this.src.lastIndexOf("/")) + "/skin/";
					}else{
						opts.imagePath = "skin/";
					}
					self.logDebug("Guessing imagePath from '%s': '%s'", this.src, opts.imagePath);
					return false; // first match
				}
			});
		}

		this.persistence = new DynaTreeStatus(opts.cookieId, opts.cookie);
		if( opts.persist ) {
			if( !$.cookie ){
				_log("warn", "Please include jquery.cookie.js to use persistence.");
			}
			this.persistence.read();
		}
		this.logDebug("DynaTree.persistence: %o", this.persistence.toDict());

		// Cached tag strings
		this.cache = {
			tagEmpty: "<span class='" + opts.classNames.empty + "'></span>",
			tagVline: "<span class='" + opts.classNames.vline + "'></span>",
			tagExpander: "<span class='" + opts.classNames.expander + "'></span>",
			tagConnector: "<span class='" + opts.classNames.connector + "'></span>",
			tagNodeIcon: "<span class='" + opts.classNames.nodeIcon + "'></span>",
			tagCheckbox: "<span class='" + opts.classNames.checkbox + "'></span>",
			lastentry: undefined
		};

		// Clear container, in case it contained some 'waiting' or 'error' text
		// for clients that don't support JS.
		// We don't do this however, if we try to load from an embedded UL element.
		if( opts.children || (opts.initAjax && opts.initAjax.url) || opts.initId ){
			$(this.divTree).empty();
		}
		var $ulInitialize = this.$tree.find(">ul:first").hide();

		// Create the root element
		this.tnRoot = new DynaTreeNode(null, this, {});
		this.tnRoot.bExpanded = true;
		this.tnRoot.render();
		this.divTree.appendChild(this.tnRoot.ul);

		var root = this.tnRoot,
			isReloading = ( opts.persist && this.persistence.isReloading() ),
			isLazy = false,
			prevFlag = this.enableUpdate(false);

		this.logDebug("Dynatree._load(): read tree structure...");

		// Init tree structure
		if( opts.children ) {
			// Read structure from node array
			root.addChild(opts.children);

		} else if( opts.initAjax && opts.initAjax.url ) {
			// Init tree from AJAX request
			isLazy = true;
			root.data.isLazy = true;
			this._reloadAjax(callback);

		} else if( opts.initId ) {
			// Init tree from another UL element
			this._createFromTag(root, $("#"+opts.initId));

		} else {
			// Init tree from the first UL element inside the container <div>
//			var $ul = this.$tree.find(">ul:first").hide();
			this._createFromTag(root, $ulInitialize);
			$ulInitialize.remove();
		}

		this._checkConsistency();
		// Fix part-sel flags
		if(!isLazy && opts.selectMode == 3){
			root._updatePartSelectionState();
		}
		// Render html markup
		this.logDebug("Dynatree._load(): render nodes...");
		this.enableUpdate(prevFlag);

		// bind event handlers
		this.logDebug("Dynatree._load(): bind events...");
		this.$widget.bind();

		// --- Post-load processing
		this.logDebug("Dynatree._load(): postInit...");
		this.phase = "postInit";

		// In persist mode, make sure that cookies are written, even if they are empty
		if( opts.persist ) {
			this.persistence.write();
		}
		// Set focus, if possible (this will also fire an event and write a cookie)
		if( this.focusNode && this.focusNode.isVisible() ) {
			this.logDebug("Focus on init: %o", this.focusNode);
			this.focusNode.focus();
		}
		if( !isLazy ) {
			if( opts.onPostInit ) {
				opts.onPostInit.call(this, isReloading, false);
			}
			if( callback ){
				callback.call(this, "ok");
			}
		}
		this.phase = "idle";
	},

	_reloadAjax: function(callback) {
		// Reload
		var opts = this.options;
		if( ! opts.initAjax || ! opts.initAjax.url ){
			throw "tree.reload() requires 'initAjax' mode.";
		}
		var pers = this.persistence;
		var ajaxOpts = $.extend({}, opts.initAjax);
		// Append cookie info to the request
//		this.logDebug("reloadAjax: key=%o, an.key:%o", pers.activeKey, this.activeNode?this.activeNode.data.key:"?");
		if( ajaxOpts.addActiveKey ){
			ajaxOpts.data.activeKey = pers.activeKey;
		}
		if( ajaxOpts.addFocusedKey ){
			ajaxOpts.data.focusedKey = pers.focusedKey;
		}
		if( ajaxOpts.addExpandedKeyList ){
			ajaxOpts.data.expandedKeyList = pers.expandedKeyList.join(",");
		}
		if( ajaxOpts.addSelectedKeyList ){
			ajaxOpts.data.selectedKeyList = pers.selectedKeyList.join(",");
		}
		// Set up onPostInit callback to be called when Ajax returns
		if( ajaxOpts.success ){
			this.logWarning("initAjax: success callback is ignored; use onPostInit instead.");
		}
		if( ajaxOpts.error ){
			this.logWarning("initAjax: error callback is ignored; use onPostInit instead.");
		}
		var isReloading = pers.isReloading();
		ajaxOpts.success = function(dtnode, data, textStatus) {
			if(opts.selectMode == 3){
				dtnode.tree.tnRoot._updatePartSelectionState();
			}
			if(opts.onPostInit){
				opts.onPostInit.call(dtnode.tree, isReloading, false);
			}
			if(callback){
				callback.call(dtnode.tree, "ok");
			}
		};
		ajaxOpts.error = function(dtnode, XMLHttpRequest, textStatus, errorThrown) {
			if(opts.onPostInit){
				opts.onPostInit.call(dtnode.tree, isReloading, true, XMLHttpRequest, textStatus, errorThrown);
			}
			if(callback){
				callback.call(dtnode.tree, "error", XMLHttpRequest, textStatus, errorThrown);
			}
		};
//		}
		this.logDebug("Dynatree._init(): send Ajax request...");
		this.tnRoot.appendAjax(ajaxOpts);
	},

	toString: function() {
//		return "DynaTree '" + this.options.title + "'";
		return "Dynatree '" + this.$tree.attr("id") + "'";
	},

	toDict: function() {
		return this.tnRoot.toDict(true);
	},

	serializeArray: function(stopOnParents) {
		// Return a JavaScript array of objects, ready to be encoded as a JSON
		// string for selected nodes
		var nodeList = this.getSelectedNodes(stopOnParents),
			name = this.$tree.attr("name") || this.$tree.attr("id"),
			arr = [];
		for(var i=0, l=nodeList.length; i<l; i++){
			arr.push({name: name, value: nodeList[i].data.key});
		}
		return arr;
	},

	getPersistData: function() {
		return this.persistence.toDict();
	},

	logDebug: function(msg) {
		if( this.options.debugLevel >= 2 ) {
			Array.prototype.unshift.apply(arguments, ["debug"]);
			_log.apply(this, arguments);
		}
	},

	logInfo: function(msg) {
		if( this.options.debugLevel >= 1 ) {
			Array.prototype.unshift.apply(arguments, ["info"]);
			_log.apply(this, arguments);
		}
	},

	logWarning: function(msg) {
		Array.prototype.unshift.apply(arguments, ["warn"]);
		_log.apply(this, arguments);
	},

	isInitializing: function() {
		return ( this.phase=="init" || this.phase=="postInit" );
	},
	isReloading: function() {
		return ( this.phase=="init" || this.phase=="postInit" ) && this.options.persist && this.persistence.cookiesFound;
	},
	isUserEvent: function() {
		return ( this.phase=="userEvent" );
	},

	redraw: function() {
//		this.logDebug("dynatree.redraw()...");
		this.tnRoot.render(false, false);
//		this.logDebug("dynatree.redraw() done.");
	},
	renderInvisibleNodes: function() {
		this.tnRoot.render(false, true);
	},
	reload: function(callback) {
		this._load(callback);
	},

	getRoot: function() {
		return this.tnRoot;
	},

	enable: function() {
		this.$widget.enable();
	},

	disable: function() {
		this.$widget.disable();
	},

	getNodeByKey: function(key) {
		// Search the DOM by element ID (assuming this is faster than traversing all nodes).
		// $("#...") has problems, if the key contains '.', so we use getElementById()
		var el = document.getElementById(this.options.idPrefix + key);
		if( el ){
			return el.dtnode ? el.dtnode : null;
		}
		// Not found in the DOM, but still may be in an unrendered part of tree
		var match = null;
		this.visit(function(node){
//			window.console.log("%s", node);
			if(node.data.key === key) {
				match = node;
				return false;
			}
		}, true);
		return match;
	},

	getActiveNode: function() {
		return this.activeNode;
	},

	reactivate: function(setFocus) {
		// Re-fire onQueryActivate and onActivate events.
		var node = this.activeNode;
//		this.logDebug("reactivate %o", node);
		if( node ) {
			this.activeNode = null; // Force re-activating
			node.activate();
			if( setFocus ){
				node.focus();
			}
		}
	},

	getSelectedNodes: function(stopOnParents) {
		var nodeList = [];
		this.tnRoot.visit(function(node){
			if( node.bSelected ) {
				nodeList.push(node);
				if( stopOnParents === true ){
					return "skip"; // stop processing this branch
				}
			}
		});
		return nodeList;
	},

	activateKey: function(key) {
		var dtnode = (key === null) ? null : this.getNodeByKey(key);
		if( !dtnode ) {
			if( this.activeNode ){
				this.activeNode.deactivate();
			}
			this.activeNode = null;
			return null;
		}
		dtnode.focus();
		dtnode.activate();
		return dtnode;
	},

	loadKeyPath: function(keyPath, callback) {
		var segList = keyPath.split(this.options.keyPathSeparator);
		// Remove leading '/'
		if(segList[0] === ""){
			segList.shift();
		}
		// Remove leading system root key
		if(segList[0] == this.tnRoot.data.key){
			this.logDebug("Removed leading root key.");
			segList.shift();
		}
		keyPath = segList.join(this.options.keyPathSeparator);
		return this.tnRoot._loadKeyPath(keyPath, callback);
	},

	selectKey: function(key, select) {
		var dtnode = this.getNodeByKey(key);
		if( !dtnode ){
			return null;
		}
		dtnode.select(select);
		return dtnode;
	},

	enableUpdate: function(bEnable) {
		if ( this.bEnableUpdate==bEnable ){
			return bEnable;
		}
		this.bEnableUpdate = bEnable;
		if ( bEnable ){
			this.redraw();
		}
		return !bEnable; // return previous value
	},

	count: function() {
		return this.tnRoot.countChildren();
	},

	visit: function(fn, includeRoot) {
		return this.tnRoot.visit(fn, includeRoot);
	},

	_createFromTag: function(parentTreeNode, $ulParent) {
		// Convert a <UL>...</UL> list into children of the parent tree node.
		var self = this;
/*
TODO: better?
		this.$lis = $("li:has(a[href])", this.element);
		this.$tabs = this.$lis.map(function() { return $("a", this)[0]; });
 */
		$ulParent.find(">li").each(function() {
			var $li = $(this),
				$liSpan = $li.find(">span:first"),
				$liA = $li.find(">a:first"),
				title,
				href = null,
				target = null,
				tooltip;
			if( $liSpan.length ) {
				// If a <li><span> tag is specified, use it literally.
				title = $liSpan.html();
			} else if( $liA.length ) {
				title = $liA.html();
				href = $liA.attr("href");
				target = $liA.attr("target");
				tooltip = $liA.attr("title");
			} else {
				// If only a <li> tag is specified, use the trimmed string up to
				// the next child <ul> tag.
				title = $li.html();
				var iPos = title.search(/<ul/i);
				if( iPos >= 0 ){
					title = $.trim(title.substring(0, iPos));
				}else{
					title = $.trim(title);
				}
//				self.logDebug("%o", title);
			}
			// Parse node options from ID, title and class attributes
			var data = {
				title: title,
				tooltip: tooltip,
				isFolder: $li.hasClass("folder"),
				isLazy: $li.hasClass("lazy"),
				expand: $li.hasClass("expanded"),
				select: $li.hasClass("selected"),
				activate: $li.hasClass("active"),
				focus: $li.hasClass("focused"),
				noLink: $li.hasClass("noLink")
			};
			if( href ){
				data.href = href;
				data.target = target;
			}
			if( $li.attr("title") ){
				data.tooltip = $li.attr("title"); // overrides <a title='...'>
			}
			if( $li.attr("id") ){
				data.key = "" + $li.attr("id");
			}
			// If a data attribute is present, evaluate as a JavaScript object
			if( $li.attr("data") ) {
				var dataAttr = $.trim($li.attr("data"));
				if( dataAttr ) {
					if( dataAttr.charAt(0) != "{" ){
						dataAttr = "{" + dataAttr + "}";
					}
					try {
						$.extend(data, eval("(" + dataAttr + ")"));
					} catch(e) {
						throw ("Error parsing node data: " + e + "\ndata:\n'" + dataAttr + "'");
					}
				}
			}
			var childNode = parentTreeNode.addChild(data);
			// Recursive reading of child nodes, if LI tag contains an UL tag
			var $ul = $li.find(">ul:first");
			if( $ul.length ) {
				self._createFromTag(childNode, $ul); // must use 'self', because 'this' is the each() context
			}
		});
	},

	_checkConsistency: function() {
//		this.logDebug("tree._checkConsistency() NOT IMPLEMENTED - %o", this);
	},

	_setDndStatus: function(sourceNode, targetNode, helper, hitMode, accept) {
		// hitMode: 'after', 'before', 'over', 'out', 'start', 'stop'
		var $source = sourceNode ? $(sourceNode.span) : null,
			$target = $(targetNode.span);
		if( !this.$dndMarker ) {
			this.$dndMarker = $("<div id='dynatree-drop-marker'></div>")
				.hide()
				.css({"z-index": 1000})
				.prependTo($(this.divTree).parent());

//			logMsg("Creating marker: %o", this.$dndMarker);
		}
/*
		if(hitMode === "start"){
		}
		if(hitMode === "stop"){
//			sourceNode.removeClass("dynatree-drop-target");
		}
*/
		if(hitMode === "after" || hitMode === "before" || hitMode === "over"){
//			$source && $source.addClass("dynatree-drag-source");
//			$target.addClass("dynatree-drop-target");

			var markerOffset = "0 0";

			switch(hitMode){
			case "before":
				this.$dndMarker.removeClass("dynatree-drop-after dynatree-drop-over");
				this.$dndMarker.addClass("dynatree-drop-before");
				markerOffset = "0 -8";
				break;
			case "after":
				this.$dndMarker.removeClass("dynatree-drop-before dynatree-drop-over");
				this.$dndMarker.addClass("dynatree-drop-after");
				markerOffset = "0 8";
				break;
			default:
				this.$dndMarker.removeClass("dynatree-drop-after dynatree-drop-before");
				this.$dndMarker.addClass("dynatree-drop-over");
				$target.addClass("dynatree-drop-target");
				markerOffset = "8 0";
			}
//			logMsg("Creating marker: %o", this.$dndMarker);
//			logMsg("    $target.offset=%o", $target);
//			logMsg("    pos/$target.offset=%o", pos);
//			logMsg("    $target.position=%o", $target.position());
//			logMsg("    $target.offsetParent=%o, ot:%o", $target.offsetParent(), $target.offsetParent().offset());
//			logMsg("    $(this.divTree).offset=%o", $(this.divTree).offset());
//			logMsg("    $(this.divTree).parent=%o", $(this.divTree).parent());
//			var pos = $target.offset();
//			var parentPos = $target.offsetParent().offset();
//			var bodyPos = $target.offsetParent().offset();

			this.$dndMarker
				.show()
				.position({
					my: "left top",
					at: "left top",
					of: $target,
					offset: markerOffset
				});

//			helper.addClass("dynatree-drop-hover");
		} else {
//			$source && $source.removeClass("dynatree-drag-source");
			$target.removeClass("dynatree-drop-target");
			this.$dndMarker.hide();
//			helper.removeClass("dynatree-drop-hover");
		}
		if(hitMode === "after"){
			$target.addClass("dynatree-drop-after");
		} else {
			$target.removeClass("dynatree-drop-after");
		}
		if(hitMode === "before"){
			$target.addClass("dynatree-drop-before");
		} else {
			$target.removeClass("dynatree-drop-before");
		}
		if(accept === true){
			if($source){
				$source.addClass("dynatree-drop-accept");
			}
			$target.addClass("dynatree-drop-accept");
			helper.addClass("dynatree-drop-accept");
		}else{
			if($source){
				$source.removeClass("dynatree-drop-accept");
			}
			$target.removeClass("dynatree-drop-accept");
			helper.removeClass("dynatree-drop-accept");
		}
		if(accept === false){
			if($source){
				$source.addClass("dynatree-drop-reject");
			}
			$target.addClass("dynatree-drop-reject");
			helper.addClass("dynatree-drop-reject");
		}else{
			if($source){
				$source.removeClass("dynatree-drop-reject");
			}
			$target.removeClass("dynatree-drop-reject");
			helper.removeClass("dynatree-drop-reject");
		}
	},

	_onDragEvent: function(eventName, node, otherNode, event, ui, draggable) {
		/**
		 * Handles drag'n'drop functionality.
		 *
		 * A standard jQuery drag-and-drop process may generate these calls:
		 *
		 * draggable helper():
		 *     _onDragEvent("helper", sourceNode, null, event, null, null);
		 * start:
		 *     _onDragEvent("start", sourceNode, null, event, ui, draggable);
		 * drag:
		 *     _onDragEvent("leave", prevTargetNode, sourceNode, event, ui, draggable);
		 *     _onDragEvent("over", targetNode, sourceNode, event, ui, draggable);
		 *     _onDragEvent("enter", targetNode, sourceNode, event, ui, draggable);
		 * stop:
		 *     _onDragEvent("drop", targetNode, sourceNode, event, ui, draggable);
		 *     _onDragEvent("leave", targetNode, sourceNode, event, ui, draggable);
		 *     _onDragEvent("stop", sourceNode, null, event, ui, draggable);
		 */
//		if(eventName !== "over"){
//			this.logDebug("tree._onDragEvent(%s, %o, %o) - %o", eventName, node, otherNode, this);
//		}
		var opts = this.options,
			dnd = this.options.dnd,
			res = null,
			nodeTag = $(node.span),
			hitMode,
			enterResponse;

		switch (eventName) {
		case "helper":
			// Only event and node argument is available
			var $helper = $("<div class='dynatree-drag-helper'><span class='dynatree-drag-helper-img' /></div>")
				.append($(event.target).closest(".dynatree-title").clone());
//			    .append($(event.target).closest('a').clone());
			// issue 244: helper should be child of scrollParent
			$("ul.dynatree-container", node.tree.divTree).append($helper);
//			$(node.tree.divTree).append($helper);
			// Attach node reference to helper object
			$helper.data("dtSourceNode", node);
//			this.logDebug("helper=%o", $helper);
//			this.logDebug("helper.sourceNode=%o", $helper.data("dtSourceNode"));
			res = $helper;
			break;
		case "start":
			if(node.isStatusNode()) {
				res = false;
			} else if(dnd.onDragStart) {
				res = dnd.onDragStart(node);
			}
			if(res === false) {
				this.logDebug("tree.onDragStart() cancelled");
				//draggable._clear();
				// NOTE: the return value seems to be ignored (drag is not canceled, when false is returned)
				ui.helper.trigger("mouseup");
				ui.helper.hide();
			} else {
				nodeTag.addClass("dynatree-drag-source");
			}
			break;
		case "enter":
			res = dnd.onDragEnter ? dnd.onDragEnter(node, otherNode) : null;
			if(!res){
				// convert null, undefined, false to false
				res = false;
			}else{
				res = {
					over: ((res === true) || (res === "over") || $.inArray("over", res) >= 0),
					before: ((res === true) || (res === "before") || $.inArray("before", res) >= 0),
					after: ((res === true) || (res === "after") || $.inArray("after", res) >= 0)
				};
			}
			ui.helper.data("enterResponse", res);
//			this.logDebug("helper.enterResponse: %o", res);
			break;
		case "over":
			enterResponse = ui.helper.data("enterResponse");
			hitMode = null;
			if(enterResponse === false){
				// Don't call onDragOver if onEnter returned false.
				// issue 332
//				break;
			} else if(typeof enterResponse === "string") {
				// Use hitMode from onEnter if provided.
				hitMode = enterResponse;
			} else {
				// Calculate hitMode from relative cursor position.
				var nodeOfs = nodeTag.offset();
//				var relPos = { x: event.clientX - nodeOfs.left,
//							y: event.clientY - nodeOfs.top };
//				nodeOfs.top += this.parentTop;
//				nodeOfs.left += this.parentLeft;
				var relPos = { x: event.pageX - nodeOfs.left,
							   y: event.pageY - nodeOfs.top };
				var relPos2 = { x: relPos.x / nodeTag.width(),
								y: relPos.y / nodeTag.height() };
//				this.logDebug("event.page: %s/%s", event.pageX, event.pageY);
//				this.logDebug("event.client: %s/%s", event.clientX, event.clientY);
//				this.logDebug("nodeOfs: %s/%s", nodeOfs.left, nodeOfs.top);
////				this.logDebug("parent: %s/%s", this.parentLeft, this.parentTop);
//				this.logDebug("relPos: %s/%s", relPos.x, relPos.y);
//				this.logDebug("relPos2: %s/%s", relPos2.x, relPos2.y);
				if( enterResponse.after && relPos2.y > 0.75 ){
					hitMode = "after";
				} else if(!enterResponse.over && enterResponse.after && relPos2.y > 0.5 ){
					hitMode = "after";
				} else if(enterResponse.before && relPos2.y <= 0.25) {
					hitMode = "before";
				} else if(!enterResponse.over && enterResponse.before && relPos2.y <= 0.5) {
					hitMode = "before";
				} else if(enterResponse.over) {
					hitMode = "over";
				}
				// Prevent no-ops like 'before source node'
				//     : these are no-ops when moving nodes, but not in copy mode
				if( dnd.preventVoidMoves ){
					if(node === otherNode){
//						this.logDebug("    drop over source node prevented");
						hitMode = null;
					}else if(hitMode === "before" && otherNode && node === otherNode.getNextSibling()){
//						this.logDebug("    drop after source node prevented");
						hitMode = null;
					}else if(hitMode === "after" && otherNode && node === otherNode.getPrevSibling()){
//						this.logDebug("    drop before source node prevented");
						hitMode = null;
					}else if(hitMode === "over" && otherNode
							&& otherNode.parent === node && otherNode.isLastSibling() ){
//						this.logDebug("    drop last child over own parent prevented");
						hitMode = null;
					}
				}
//				this.logDebug("hitMode: %s - %s - %s", hitMode, (node.parent === otherNode), node.isLastSibling());
				ui.helper.data("hitMode", hitMode);
			}
			// Auto-expand node (only when 'over' the node, not 'before', or 'after')
			if(hitMode === "over"
				&& dnd.autoExpandMS && node.hasChildren() !== false && !node.bExpanded) {
				node.scheduleAction("expand", dnd.autoExpandMS);
			}
			if(hitMode && dnd.onDragOver){
				res = dnd.onDragOver(node, otherNode, hitMode);
				if(res === "over" || res === "before" || res === "after") {
					hitMode = res;
				}
			}
			// issue 332
//			this._setDndStatus(otherNode, node, ui.helper, hitMode, res!==false);
			this._setDndStatus(otherNode, node, ui.helper, hitMode, res!==false && hitMode !== null);
			break;
		case "drop":
			// issue 286: don't trigger onDrop, if DnD status is 'reject'
			var isForbidden = ui.helper.hasClass("dynatree-drop-reject");
			hitMode = ui.helper.data("hitMode");
			if(hitMode && dnd.onDrop && !isForbidden){
				dnd.onDrop(node, otherNode, hitMode, ui, draggable);
			}
			break;
		case "leave":
			// Cancel pending expand request
			node.scheduleAction("cancel");
			ui.helper.data("enterResponse", null);
			ui.helper.data("hitMode", null);
			this._setDndStatus(otherNode, node, ui.helper, "out", undefined);
			if(dnd.onDragLeave){
				dnd.onDragLeave(node, otherNode);
			}
			break;
		case "stop":
			nodeTag.removeClass("dynatree-drag-source");
			if(dnd.onDragStop){
				dnd.onDragStop(node);
			}
			break;
		default:
			throw "Unsupported drag event: " + eventName;
		}
		return res;
	},

	cancelDrag: function() {
		 var dd = $.ui.ddmanager.current;
		 if(dd){
			 dd.cancel();
		 }
	},

	// --- end of class
	lastentry: undefined
};

/*************************************************************************
 * Widget $(..).dynatree
 */

$.widget("ui.dynatree", {
/*
	init: function() {
		// ui.core 1.6 renamed init() to _init(): this stub assures backward compatibility
		_log("warn", "ui.dynatree.init() was called; you should upgrade to jquery.ui.core.js v1.8 or higher.");
		return this._init();
	},
 */
	_init: function() {
//		if( parseFloat($.ui.version) < 1.8 ) {
		if(versionCompare($.ui.version, "1.8") < 0){
			// jquery.ui.core 1.8 renamed _init() to _create(): this stub assures backward compatibility
			if(this.options.debugLevel >= 0){
				_log("warn", "ui.dynatree._init() was called; you should upgrade to jquery.ui.core.js v1.8 or higher.");
			}
			return this._create();
		}
		// jquery.ui.core 1.8 still uses _init() to perform "default functionality"
		if(this.options.debugLevel >= 2){
			_log("debug", "ui.dynatree._init() was called; no current default functionality.");
		}
	},

	_create: function() {
		var opts = this.options;
		if(opts.debugLevel >= 1){
			logMsg("Dynatree._create(): version='%s', debugLevel=%o.", $.ui.dynatree.version, this.options.debugLevel);
		}
		// The widget framework supplies this.element and this.options.
		this.options.event += ".dynatree"; // namespace event

		var divTree = this.element.get(0);
/*		// Clear container, in case it contained some 'waiting' or 'error' text
		// for clients that don't support JS
		if( opts.children || (opts.initAjax && opts.initAjax.url) || opts.initId )
			$(divTree).empty();
*/
		// Create the DynaTree object
		this.tree = new DynaTree(this);
		this.tree._load();
		this.tree.logDebug("Dynatree._init(): done.");
	},

	bind: function() {
		// Prevent duplicate binding
		this.unbind();

		var eventNames = "click.dynatree dblclick.dynatree";
		if( this.options.keyboard ){
			// Note: leading ' '!
			eventNames += " keypress.dynatree keydown.dynatree";
		}
		this.element.bind(eventNames, function(event){
			var dtnode = $.ui.dynatree.getNode(event.target);
			if( !dtnode ){
				return true;  // Allow bubbling of other events
			}
			var tree = dtnode.tree;
			var o = tree.options;
			tree.logDebug("event(%s): dtnode: %s", event.type, dtnode);
			var prevPhase = tree.phase;
			tree.phase = "userEvent";
			try {
				switch(event.type) {
				case "click":
					return ( o.onClick && o.onClick.call(tree, dtnode, event)===false ) ? false : dtnode._onClick(event);
				case "dblclick":
					return ( o.onDblClick && o.onDblClick.call(tree, dtnode, event)===false ) ? false : dtnode._onDblClick(event);
				case "keydown":
					return ( o.onKeydown && o.onKeydown.call(tree, dtnode, event)===false ) ? false : dtnode._onKeydown(event);
				case "keypress":
					return ( o.onKeypress && o.onKeypress.call(tree, dtnode, event)===false ) ? false : dtnode._onKeypress(event);
				}
			} catch(e) {
				var _ = null; // issue 117
				tree.logWarning("bind(%o): dtnode: %o, error: %o", event, dtnode, e);
			} finally {
				tree.phase = prevPhase;
			}
		});

		// focus/blur don't bubble, i.e. are not delegated to parent <div> tags,
		// so we use the addEventListener capturing phase.
		// See http://www.howtocreate.co.uk/tutorials/javascript/domevents
		function __focusHandler(event) {
			// Handles blur and focus.
			// Fix event for IE:
			// doesn't pass JSLint:
//			event = arguments[0] = $.event.fix( event || window.event );
			// what jQuery does:
//			var args = jQuery.makeArray( arguments );
//			event = args[0] = jQuery.event.fix( event || window.event );
			event = $.event.fix( event || window.event );
			var dtnode = $.ui.dynatree.getNode(event.target);
			return dtnode ? dtnode._onFocus(event) : false;
		}
		var div = this.tree.divTree;

		if( div.addEventListener ) {
			div.addEventListener("focus", __focusHandler, true);
			div.addEventListener("blur", __focusHandler, true);
		} else {
			div.onfocusin = div.onfocusout = __focusHandler;
		}
		// EVENTS
		// disable click if event is configured to something else
//		if (!(/^click/).test(o.event))
//			this.$tabs.bind("click.tabs", function() { return false; });

	},

	unbind: function() {
		this.element.unbind(".dynatree");
	},

/* TODO: we could handle option changes during runtime here (maybe to re-render, ...)
	setData: function(key, value) {
		this.tree.logDebug("dynatree.setData('" + key + "', '" + value + "')");
	},
*/
	enable: function() {
		this.bind();
		// Call default disable(): remove -disabled from css:
		$.Widget.prototype.enable.apply(this, arguments);
	},

	disable: function() {
		this.unbind();
		// Call default disable(): add -disabled to css:
		$.Widget.prototype.disable.apply(this, arguments);
	},

	// --- getter methods (i.e. NOT returning a reference to $)
	getTree: function() {
		return this.tree;
	},

	getRoot: function() {
		return this.tree.getRoot();
	},

	getActiveNode: function() {
		return this.tree.getActiveNode();
	},

	getSelectedNodes: function() {
		return this.tree.getSelectedNodes();
	},

	// ------------------------------------------------------------------------
	lastentry: undefined
});


// The following methods return a value (thus breaking the jQuery call chain):
if(versionCompare($.ui.version, "1.8") < 0){
//if( parseFloat($.ui.version) < 1.8 ) {
	$.ui.dynatree.getter = "getTree getRoot getActiveNode getSelectedNodes";
}

/*******************************************************************************
 * Tools in ui.dynatree namespace
 */
$.ui.dynatree.version = "$Version: 1.2.4$";

/**
 * Return a DynaTreeNode object for a given DOM element
 */
$.ui.dynatree.getNode = function(el) {
	if(el instanceof DynaTreeNode){
		return el; // el already was a DynaTreeNode
	}
	if(el.selector !== undefined){
		el = el[0]; // el was a jQuery object: use the DOM element
	}
	//     : for some reason $el.parents("[dtnode]") does not work (jQuery 1.6.1)
	// maybe, because dtnode is a property, not an attribute
	while( el ) {
		if(el.dtnode) {
			return el.dtnode;
		}
		el = el.parentNode;
	}
	return null;
/*
	var $el = el.selector === undefined ? $(el) : el,
//		parent = $el.closest("[dtnode]"),
//		parent = $el.parents("[dtnode]").first(),
		useProp = (typeof $el.prop == "function"),
		node;
	$el.parents().each(function(){
		node = useProp ? $(this).prop("dtnode") : $(this).attr("dtnode");
		if(node){
			return false;
		}
	});
	return node;
*/
};

/**Return persistence information from cookies.*/
$.ui.dynatree.getPersistData = DynaTreeStatus._getTreePersistData;

/*******************************************************************************
 * Plugin default options:
 */
$.ui.dynatree.prototype.options = {
	title: "Dynatree", // Tree's name (only used for debug output)
	minExpandLevel: 1, // 1: root node is not collapsible
	imagePath: null, // Path to a folder containing icons. Defaults to 'skin/' subdirectory.
	children: null, // Init tree structure from this object array.
	initId: null, // Init tree structure from a <ul> element with this ID.
	initAjax: null, // Ajax options used to initialize the tree strucuture.
	autoFocus: true, // Set focus to first child, when expanding or lazy-loading.
	keyboard: true, // Support keyboard navigation.
	persist: false, // Persist expand-status to a cookie
	autoCollapse: false, // Automatically collapse all siblings, when a node is expanded.
	clickFolderMode: 3, // 1:activate, 2:expand, 3:activate and expand
	activeVisible: true, // Make sure, active nodes are visible (expanded).
	checkbox: false, // Show checkboxes.
	selectMode: 2, // 1:single, 2:multi, 3:multi-hier
	fx: null, // Animations, e.g. null or { height: "toggle", duration: 200 }
	noLink: false, // Use <span> instead of <a> tags for all nodes
	// Low level event handlers: onEvent(dtnode, event): return false, to stop default processing
	onClick: null, // null: generate focus, expand, activate, select events.
	onDblClick: null, // (No default actions.)
	onKeydown: null, // null: generate keyboard navigation (focus, expand, activate).
	onKeypress: null, // (No default actions.)
	onFocus: null, // null: set focus to node.
	onBlur: null, // null: remove focus from node.

	// Pre-event handlers onQueryEvent(flag, dtnode): return false, to stop processing
	onQueryActivate: null, // Callback(flag, dtnode) before a node is (de)activated.
	onQuerySelect: null, // Callback(flag, dtnode) before a node is (de)selected.
	onQueryExpand: null, // Callback(flag, dtnode) before a node is expanded/collpsed.

	// High level event handlers
	onPostInit: null, // Callback(isReloading, isError) when tree was (re)loaded.
	onActivate: null, // Callback(dtnode) when a node is activated.
	onDeactivate: null, // Callback(dtnode) when a node is deactivated.
	onSelect: null, // Callback(flag, dtnode) when a node is (de)selected.
	onExpand: null, // Callback(flag, dtnode) when a node is expanded/collapsed.
	onLazyRead: null, // Callback(dtnode) when a lazy node is expanded for the first time.
	onCustomRender: null, // Callback(dtnode) before a node is rendered. Return a HTML string to override.
	onCreate: null, // Callback(dtnode, nodeSpan) after a node was rendered for the first time.
	onRender: null, // Callback(dtnode, nodeSpan) after a node was rendered.
				// postProcess is similar to the standard dataFilter hook,
				// but it is also called for JSONP
	postProcess: null, // Callback(data, dataType) before an Ajax result is passed to dynatree

	// Drag'n'drop support
	dnd: {
		// Make tree nodes draggable:
		onDragStart: null, // Callback(sourceNode), return true, to enable dnd
		onDragStop: null, // Callback(sourceNode)
//		helper: null,
		// Make tree nodes accept draggables
		autoExpandMS: 1000, // Expand nodes after n milliseconds of hovering.
		preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
		onDragEnter: null, // Callback(targetNode, sourceNode)
		onDragOver: null, // Callback(targetNode, sourceNode, hitMode)
		onDrop: null, // Callback(targetNode, sourceNode, hitMode)
		onDragLeave: null // Callback(targetNode, sourceNode)
	},
	ajaxDefaults: { // Used by initAjax option
		cache: false, // false: Append random '_' argument to the request url to prevent caching.
		timeout: 0, // >0: Make sure we get an ajax error for invalid URLs
		dataType: "json" // Expect json format and pass json object to callbacks.
	},
	strings: {
		loading: "Loading&#8230;",
		loadError: "Load error!"
	},
	generateIds: false, // Generate id attributes like <span id='dynatree-id-KEY'>
	idPrefix: "dynatree-id-", // Used to generate node id's like <span id="dynatree-id-<key>">.
	keyPathSeparator: "/", // Used by node.getKeyPath() and tree.loadKeyPath().
//    cookieId: "dynatree-cookie", // Choose a more unique name, to allow multiple trees.
	cookieId: "dynatree", // Choose a more unique name, to allow multiple trees.
	cookie: {
		expires: null //7, // Days or Date; null: session cookie
//		path: "/", // Defaults to current page
//		domain: "jquery.com",
//		secure: true
	},
	// Class names used, when rendering the HTML markup.
	// Note: if only single entries are passed for options.classNames, all other
	// values are still set to default.
	classNames: {
		container: "dynatree-container",
		node: "dynatree-node",
		folder: "dynatree-folder",
//		document: "dynatree-document",

		empty: "dynatree-empty",
		vline: "dynatree-vline",
		expander: "dynatree-expander",
		connector: "dynatree-connector",
		checkbox: "dynatree-checkbox",
		nodeIcon: "dynatree-icon",
		title: "dynatree-title",
		noConnector: "dynatree-no-connector",

		nodeError: "dynatree-statusnode-error",
		nodeWait: "dynatree-statusnode-wait",
		hidden: "dynatree-hidden",
		combinedExpanderPrefix: "dynatree-exp-",
		combinedIconPrefix: "dynatree-ico-",
		nodeLoading: "dynatree-loading",
//		disabled: "dynatree-disabled",
		hasChildren: "dynatree-has-children",
		active: "dynatree-active",
		selected: "dynatree-selected",
		expanded: "dynatree-expanded",
		lazy: "dynatree-lazy",
		focused: "dynatree-focused",
		partsel: "dynatree-partsel",
		lastsib: "dynatree-lastsib"
	},
	debugLevel: 1,

	// ------------------------------------------------------------------------
	lastentry: undefined
};
//
if(versionCompare($.ui.version, "1.8") < 0){
//if( parseFloat($.ui.version) < 1.8 ) {
	$.ui.dynatree.defaults = $.ui.dynatree.prototype.options;
}

/*******************************************************************************
 * Reserved data attributes for a tree node.
 */
$.ui.dynatree.nodedatadefaults = {
	title: null, // (required) Displayed name of the node (html is allowed here)
	key: null, // May be used with activate(), select(), find(), ...
	isFolder: false, // Use a folder icon. Also the node is expandable but not selectable.
	isLazy: false, // Call onLazyRead(), when the node is expanded for the first time to allow for delayed creation of children.
	tooltip: null, // Show this popup text.
	href: null, // Added to the generated <a> tag.
	icon: null, // Use a custom image (filename relative to tree.options.imagePath). 'null' for default icon, 'false' for no icon.
	addClass: null, // Class name added to the node's span tag.
	noLink: false, // Use <span> instead of <a> tag for this node
	activate: false, // Initial active status.
	focus: false, // Initial focused status.
	expand: false, // Initial expanded status.
	select: false, // Initial selected status.
	hideCheckbox: false, // Suppress checkbox display for this node.
	unselectable: false, // Prevent selection.
//  disabled: false,
	// The following attributes are only valid if passed to some functions:
	children: null, // Array of child nodes.
	// NOTE: we can also add custom attributes here.
	// This may then also be used in the onActivate(), onSelect() or onLazyTree() callbacks.
	// ------------------------------------------------------------------------
	lastentry: undefined
};

/*******************************************************************************
 * Drag and drop support
 */
function _initDragAndDrop(tree) {
	var dnd = tree.options.dnd || null;
	// Register 'connectToDynatree' option with ui.draggable
	if(dnd && (dnd.onDragStart || dnd.onDrop)) {
		_registerDnd();
	}
	// Attach ui.draggable to this Dynatree instance
	if(dnd && dnd.onDragStart ) {
		tree.$tree.draggable({
			addClasses: false,
			appendTo: "body",
			containment: false,
			delay: 0,
			distance: 4,
			revert: false,
			scroll: true, // issue 244: enable scrolling (if ul.dynatree-container)
			scrollSpeed: 7,
			scrollSensitivity: 10,
			// Delegate draggable.start, drag, and stop events to our handler
			connectToDynatree: true,
			// Let source tree create the helper element
			helper: function(event) {
				var sourceNode = $.ui.dynatree.getNode(event.target);
				if(!sourceNode){ // issue 211
					return "<div></div>";
				}
				return sourceNode.tree._onDragEvent("helper", sourceNode, null, event, null, null);
			},
			start: function(event, ui) {
				// See issues 211, 268, 278
//				var sourceNode = $.ui.dynatree.getNode(event.target);
				var sourceNode = ui.helper.data("dtSourceNode");
				return !!sourceNode; // Abort dragging if no Node could be found
			},
			_last: null
		});
	}
	// Attach ui.droppable to this Dynatree instance
	if(dnd && dnd.onDrop) {
		tree.$tree.droppable({
			addClasses: false,
			tolerance: "intersect",
			greedy: false,
			_last: null
		});
	}
}

//--- Extend ui.draggable event handling --------------------------------------
var didRegisterDnd = false;
var _registerDnd = function() {
	if(didRegisterDnd){
		return;
	}
	// Register proxy-functions for draggable.start/drag/stop
	$.ui.plugin.add("draggable", "connectToDynatree", {
		start: function(event, ui) {
			// issue 386
			var draggable = $(this).data("ui-draggable") || $(this).data("draggable"),
				sourceNode = ui.helper.data("dtSourceNode") || null;
//			logMsg("draggable-connectToDynatree.start, %s", sourceNode);
//			logMsg("    this: %o", this);
//			logMsg("    event: %o", event);
//			logMsg("    draggable: %o", draggable);
//			logMsg("    ui: %o", ui);

			if(sourceNode) {
				// Adjust helper offset, so cursor is slightly outside top/left corner
//				draggable.offset.click.top -= event.target.offsetTop;
//				draggable.offset.click.left -= event.target.offsetLeft;
				draggable.offset.click.top = -2;
				draggable.offset.click.left = + 16;
//				logMsg("    draggable2: %o", draggable);
//				logMsg("    draggable.offset.click FIXED: %s/%s", draggable.offset.click.left, draggable.offset.click.top);
				// Trigger onDragStart event
				//     : when called as connectTo..., the return value is ignored(?)
				return sourceNode.tree._onDragEvent("start", sourceNode, null, event, ui, draggable);
			}
		},
		drag: function(event, ui) {
			// issue 386
			var draggable = $(this).data("ui-draggable") || $(this).data("draggable"),
				sourceNode = ui.helper.data("dtSourceNode") || null,
				prevTargetNode = ui.helper.data("dtTargetNode") || null,
				targetNode = $.ui.dynatree.getNode(event.target);
//			logMsg("$.ui.dynatree.getNode(%o): %s", event.target, targetNode);
//			logMsg("connectToDynatree.drag: helper: %o", ui.helper[0]);
			if(event.target && !targetNode){
				// We got a drag event, but the targetNode could not be found
				// at the event location. This may happen,
				// 1. if the mouse jumped over the drag helper,
				// 2. or if non-dynatree element is dragged
				// We ignore it:
				var isHelper = $(event.target).closest("div.dynatree-drag-helper,#dynatree-drop-marker").length > 0;
				if(isHelper){
//					logMsg("Drag event over helper: ignored.");
					return;
				}
			}
//			logMsg("draggable-connectToDynatree.drag: targetNode(from event): %s, dtTargetNode: %s", targetNode, ui.helper.data("dtTargetNode"));
			ui.helper.data("dtTargetNode", targetNode);
			// Leaving a tree node
			if(prevTargetNode && prevTargetNode !== targetNode ) {
				prevTargetNode.tree._onDragEvent("leave", prevTargetNode, sourceNode, event, ui, draggable);
			}
			if(targetNode){
				if(!targetNode.tree.options.dnd.onDrop) {
					// not enabled as drop target
//					noop(); // Keep JSLint happy
				} else if(targetNode === prevTargetNode) {
					// Moving over same node
					targetNode.tree._onDragEvent("over", targetNode, sourceNode, event, ui, draggable);
				}else{
					// Entering this node first time
					targetNode.tree._onDragEvent("enter", targetNode, sourceNode, event, ui, draggable);
				}
			}
			// else go ahead with standard event handling
		},
		stop: function(event, ui) {
			// issue 386
			var draggable = $(this).data("ui-draggable") || $(this).data("draggable"),
				sourceNode = ui.helper.data("dtSourceNode") || null,
				targetNode = ui.helper.data("dtTargetNode") || null,
				mouseDownEvent = draggable._mouseDownEvent,
				eventType = event.type,
				dropped = (eventType == "mouseup" && event.which == 1);
			logMsg("draggable-connectToDynatree.stop: targetNode(from event): %s, dtTargetNode: %s", targetNode, ui.helper.data("dtTargetNode"));
//			logMsg("draggable-connectToDynatree.stop, %s", sourceNode);
//			logMsg("    type: %o, downEvent: %o, upEvent: %o", eventType, mouseDownEvent, event);
//			logMsg("    targetNode: %o", targetNode);
			if(!dropped){
				logMsg("Drag was cancelled");
			}
			if(targetNode) {
				if(dropped){
					targetNode.tree._onDragEvent("drop", targetNode, sourceNode, event, ui, draggable);
				}
				targetNode.tree._onDragEvent("leave", targetNode, sourceNode, event, ui, draggable);
			}
			if(sourceNode){
				sourceNode.tree._onDragEvent("stop", sourceNode, null, event, ui, draggable);
			}
		}
	});
	didRegisterDnd = true;
};

// ---------------------------------------------------------------------------
}(jQuery));

jQuery.fn.DetailedProductInfo = function (settings) {
	var current_modifiers = {price: 0, weight: 0};
	var product_quantity_limit = undefined;
	var options_quantity_limit = undefined;
	var is_options_combination_available = true;
	
	var can_add_to_cart = true;
	var can_add_to_wishlist = true;
	
	var $product_info = $(this);
	var $quantity_selector = $product_info.find('select[name=quantity_in_cart]');
	var $options_error = $product_info.find('.options_error');
	
	$product_info.submit(function (event) {
		var action = $product_info.find('input[name=asc_action]').val();
		if ((action == 'AddToCart' && ! can_add_to_cart) || (action == 'AddToWishlist' && ! can_add_to_wishlist)) {
			event.preventDefault();
		}
		return true;
	});
	
	// Product options changing handler
	$product_info.find('.product_options').bind('options_change', function (event, parameters) {
		current_modifiers = parameters.modifiers;
		options_quantity_limit = parameters.quantity;
		is_options_combination_available = parameters.combination;
		
		if (current_modifiers) {
            var total_price = settings.sale_price + current_modifiers.price;
            if(total_price<0) total_price = 0;
			$product_info.find('.product_sale_price .value')
				.html(formatPrice(total_price, settings.currency_settings));
			if (current_modifiers.price && total_price >= settings.list_price) { 
				$product_info.find('.product_list_price, .discount_star').hide();
			}
			else {
				$product_info.find('.product_list_price, .discount_star').show();
			}
		}
		
		checkErrorState();
	});
	
	// Quantity dropdown changing handler
	$quantity_selector.change(function () {
		checkErrorState();
	});
	
	function checkErrorState()
	{
		$options_error.hide();
		can_add_to_cart = true;
		can_add_to_wishlist = true;
		
		if (! is_options_combination_available) {
			can_add_to_cart = false;
			can_add_to_wishlist = false;
			$options_error.html(settings.labels.inv_unavailable).show();
		}
		else if (options_quantity_limit == null) {
			// not in inventory
			if (settings.aanic != 'Y') {
				can_add_to_cart = false;
				can_add_to_wishlist = false;
				$options_error.html(settings.labels.comb_unavailable).show();
			}
		}
		else if (options_quantity_limit != undefined && options_quantity_limit < parseInt($quantity_selector.val())) {
			// out of stock
			if (settings.aanis != 'Y') {
    			can_add_to_cart = false;
    			can_add_to_wishlist = true;
    			if (options_quantity_limit > 0) {
    				$options_error.html(settings.labels.comb_limit_stock.replace('%quantity%', options_quantity_limit));
    			}
    			else {
    				$options_error.html(settings.labels.comb_out_of_stock);
    			}
    			$options_error.show();
			}
		}
		
		setButtonDisabled($product_info.find('.button_add_to_cart'), ! can_add_to_cart)
		setButtonDisabled($product_info.find('.add_to_wishlist'), ! can_add_to_wishlist)
	}
	
	function setButtonDisabled($button, disabled)
	{
		$button.attr('disabled', disabled);
		if (disabled) {
			$button.addClass('disabled');
		}
		else {
			$button.removeClass('disabled');
		}
	}
	
};

jQuery.fn.ProductOptionsChoice = function (modifiers, inventory, combinations_formula)
{
	var options_box = $(this);
	
	// На некоторые элементы формы нужно навесить обработчики события 'change',
	// чтобы сгенерировать событие 'change_option' ("одна из опций изменена").
	options_box.find('.form_row')
	
	.filter('.multiselect,.dropdown').each(function () {
		$(this).find('select').change(function () {
			options_box.trigger('one_option_change');
		});
	}).end()
	
	.filter('.radio').each(function () {
		$(this).find('input[type=radio]').change(function () {
			options_box.trigger('one_option_change');
		});
	}).end()
	
	.filter('.checkbox_set,.checkbox_input,.checkbox_text').each(function () {
		$(this).find('input[type=checkbox]').change(function () {
			options_box.trigger('one_option_change');
		});
	}).end()
	
	.filter('.file').each(function () {
		$(this).find('input[type=file]').change(function () {
			options_box.trigger('one_option_change');
		});
	});
	
	// Обработчик события "одна из опций изменена".
	options_box.bind('one_option_change', function() {
		var values = {};
		
		// fetch values of all options 
		options_box.find('.form_row')
		
		.filter('.checkbox_set').each(function () {
			// set of checkboxes
			var cb_name = $(this).find('input[type=checkbox]').attr('name');
			var option_id = cb_name.match(/^po\[(\d+)\]/)[1];
			var value = [];
			$(this).find('input[type=checkbox]:checked').each(function () {
				var opt_name = $(this).attr('name');
				var option_value = opt_name.match(/^po\[\d+\]\[(\d+)\]/)[1];
				value.push(option_value);
			});
			values[ option_id ] = value;
		}).end()
		
		.filter('.radio').each(function () {
			// set of radio buttons
			var option_id = $(this).find('input[type=radio]').attr('name').match(/^po\[(\d+)\]/)[1];
			var value = [];
			$(this).find('input[type=radio]:checked').each(function () {
				value.push($(this).val());
			});
			values[ option_id ] = value;
		}).end()
		
		.filter('.multiselect,.dropdown').each(function () {
			// single select dropdown or multiple select list
			var $sel = $(this).find('select');
			var option_id = $sel.attr('name').match(/^po\[(\d+)\]/)[1];
			var value = $sel.val();
			if (value == null) {
				value = [];
			}
			else
			if (typeof value == 'string') {
				value = [ value ];
			}
			values[ option_id ] = value;
		}).end()
		
		.filter('.checkbox_input,.checkbox_text').each(function () {
			// optional text (with checkbox)
			var $cb = $(this).find('input[type=checkbox]');
			var option_id = $cb.attr('name').match(/^po\[(\d+)\]/)[1];
			values[ option_id ] = [ $cb.attr('checked') ? 'on' : 'off' ];
		}).end()
		
		.filter('.file').each(function () {
			// file
			var $file = $(this).find('input[type=file]');
			var option_id = $file.attr('name').match(/^po\[(\d+)\]/)[1];
			values[ option_id ] = [ $file.val() ? 'on' : 'off' ];
		}).end()

        .find('input[type=text], textarea').each(function () {
            var option_id = $(this).attr('name').match(/^po\[(\d+)\]/)[1];
            if(!values[option_id]) values[ option_id ] = [ $(this).val() ? 'on' : 'off' ];
        });
		
		// Вычисление суммы модификаторов согласно комбинации значений опций
		var values_ids = [];
		for(var option_id in values) {
			values_ids = values_ids.concat(values[option_id]);
		}
		var values_ids_re = new RegExp('\\{' + values_ids.join('\\}|\\{') + '\\}', 'g');
		
		var result = {
				modifiers: calcModifiers(modifiers, values),
				quantity: calcQuantity(inventory, values_ids_re),
				combination: checkCombinations(combinations_formula, values_ids_re)
		};
		options_box.trigger('options_change', [result]);
	})
	.trigger('one_option_change');
};

function calcModifiers(modifiers, options_values)
{
	var price = 0;
	var weight = 0;
	
	for (var option_id in modifiers) {
		var option_modifier = modifiers[option_id];
		if (option_id in options_values) {
			for (var value_num in options_values[option_id]) {
				var value_id = options_values[option_id][value_num];
				if (typeof option_modifier[value_id] == 'object') {
					price += option_modifier[value_id].price;
					weight += option_modifier[value_id].weight;
				}
			}
		}
	}
	return {price: price, weight: weight};
}

function calcQuantity(inventory, values_ids_re)
{
	var stock_limit = null;
    var any_selected = /[0-9]+/.test(values_ids_re);
	for (var i = 0; i < inventory.length; i++) {
		var formula = inventory[i].formula;
        if(any_selected && !(/[0-9]+/.test(formula))) continue;
		formula = formula.replace(values_ids_re, 'true');
		formula = formula.replace(/\{\d+\}/g, 'false');
		if (eval(formula)) {
			stock_limit = parseInt(inventory[i].quantity);
			break;
		}
	}
	return stock_limit;
}

function checkCombinations(formula, values_ids_re)
{
	formula = formula.replace(values_ids_re, 'true');
	formula = formula.replace(/\{\d+\}/g, 'false');
	return eval(formula);
}

$(function(){    
    new AjaxManager({
        iSelector: '.button_add_to_cart',
        instance: '.ProductList .product_item',
        updateAllInstances: true
    });
    new AjaxManager({
        iSelector: '.button_add_to_cart',
        instance: '.ProductInfo',
        updateAllInstances: true
        /*beforeCB: function(instance){
            instance.initiator.hide();
        },
        afterCB: function(instance){
            instance.initiator.show();
        }*/
    });
    new AjaxManager({
        iSelector: '.del_mini_prod',
        instance: '.MiniCart',
        updateAllInstances: true
    });
});

$(function(){
    if (window.use_ajax_for_customer_reviews !== undefined && window.use_ajax_for_customer_reviews)
    {
        new AjaxManager({
            iSelector: '.button_add_review',
            instance: '.ProductAddReviewForm',
            updateAllInstances: true
        });
    }
});

$(function(){
    new AjaxManager({
        iSelector: '.button_calculate',
        instance: '.ShippingCalculator',
        updateAllInstances: true,
        beforeCB: function(instance){
            $('#subaction_id').val('calculate');
        },
        afterCB: function(instance){
            try {
                refreshStatesList('DstCountry', 'DstState_menu_select', 'stub_state_text_input');
                $("input[type='text']").addClass("input_text");
            } 
            catch(ex) {};    
        }
    });
    new AjaxManager({
        iSelector: '.button_remember',
        instance: '.ShippingCalculator',
        updateAllInstances: true,
        beforeCB: function(instance){
            $('#subaction_id').val('remember');
        },
        afterCB: function(instance){
            try {
                refreshStatesList('DstCountry', 'DstState_menu_select', 'stub_state_text_input');
                $("input[type='text']").addClass("input_text");
            } 
            catch(ex) {};    
        }
    });
});

$(function(){
    new AjaxManager({
        iSelector: '.button_signin',
        instance: '.CustomerSignInBox',
        updateAllInstances: true
    });

    new AjaxManager({
        iSelector: '.sign_out_link',
        instance: '.CustomerSignInBox',
        method: 'GET',
        updateAllInstances: true
    });
});

	/* lazyload.js (c) Lorenzo Giuliani
	 * MIT License (http://www.opensource.org/licenses/mit-license.html)
	 *
	 * expects a list of:  
	 * `<img src="blank.gif" data-src="my_image.png" width="600" height="400" class="lazy">`
	 */
	 $(function() {
	  var $q = function(q, res){
	        if (document.querySelectorAll) {
	          res = document.querySelectorAll(q);
	        } else {
	          var d=document
	            , a=d.styleSheets[0] || d.createStyleSheet();
	          a.addRule(q,'f:b');
	          for(var l=d.all,b=0,c=[],f=l.length;b<f;b++)
	            l[b].currentStyle.f && c.push(l[b]);

	          a.removeRule(0);
	          res = c;
	        }
	        return res;
	      }
	    , addEventListener = function(evt, fn){
	        window.addEventListener
	          ? this.addEventListener(evt, fn, false)
	          : (window.attachEvent)
	            ? this.attachEvent('on' + evt, fn)
	            : this['on' + evt] = fn;
	      }
	    , _has = function(obj, key) {
	        return Object.prototype.hasOwnProperty.call(obj, key);
	      }
	    ;

	  function loadImage (el, fn) {
	    var img = new Image()
	      , src = el.getAttribute('data-src');
	    img.onload = function() {
	      if (!! el.parent)
	        el.parent.replaceChild(img, el)
	      else
	        el.src = src;

	      fn? fn() : null;
	    }
	    img.src = src;
	  }

	  function elementInViewport(el) {
	    var rect = el.getBoundingClientRect()

	    return (
	       rect.top    >= 0
	    && rect.left   >= 0
	    && rect.top <= (window.innerHeight || document.documentElement.clientHeight)
	    )
	  }

	    var images = new Array()
	      , query = $q('img.lazy')
	      , processScroll = function(){
	          for (var i = 0; i < images.length; i++) {
	            if (elementInViewport(images[i])) {
	              loadImage(images[i], function () {
	                images.splice(i, 1);
	              });
	            }
	          };
	        }
	      ;
	    // Array.prototype.slice.call is not callable under our lovely IE8 
	    for (var i = 0; i < query.length; i++) {
	      images.push(query[i]);
	    };

	    processScroll();
	    addEventListener('scroll',processScroll);

	  });

/*!
	Colorbox v1.4.24 - 2013-06-24
	jQuery lightbox and modal window plugin
	(c) 2013 Jack Moore - http://www.jacklmoore.com/colorbox
	license: http://www.opensource.org/licenses/mit-license.php
*/
(function(t,e,i){function o(i,o,n){var r=e.createElement(i);return o&&(r.id=te+o),n&&(r.style.cssText=n),t(r)}function n(){return i.innerHeight?i.innerHeight:t(i).height()}function r(t){var e=E.length,i=(j+t)%e;return 0>i?e+i:i}function l(t,e){return Math.round((/%/.test(t)?("x"===e?H.width():n())/100:1)*parseInt(t,10))}function h(t,e){return t.photo||t.photoRegex.test(e)}function s(t,e){return t.retinaUrl&&i.devicePixelRatio>1?e.replace(t.photoRegex,t.retinaSuffix):e}function a(t){"contains"in y[0]&&!y[0].contains(t.target)&&(t.stopPropagation(),y.focus())}function d(){var e,i=t.data(A,Z);null==i?(O=t.extend({},Y),console&&console.log&&console.log("Error: cboxElement missing settings object")):O=t.extend({},i);for(e in O)t.isFunction(O[e])&&"on"!==e.slice(0,2)&&(O[e]=O[e].call(A));O.rel=O.rel||A.rel||t(A).data("rel")||"nofollow",O.href=O.href||t(A).attr("href"),O.title=O.title||A.title,"string"==typeof O.href&&(O.href=t.trim(O.href))}function c(i,o){t(e).trigger(i),se.trigger(i),t.isFunction(o)&&o.call(A)}function u(){var t,e,i,o,n,r=te+"Slideshow_",l="click."+te;O.slideshow&&E[1]?(e=function(){clearTimeout(t)},i=function(){(O.loop||E[j+1])&&(t=setTimeout(J.next,O.slideshowSpeed))},o=function(){R.html(O.slideshowStop).unbind(l).one(l,n),se.bind(ne,i).bind(oe,e).bind(re,n),y.removeClass(r+"off").addClass(r+"on")},n=function(){e(),se.unbind(ne,i).unbind(oe,e).unbind(re,n),R.html(O.slideshowStart).unbind(l).one(l,function(){J.next(),o()}),y.removeClass(r+"on").addClass(r+"off")},O.slideshowAuto?o():n()):y.removeClass(r+"off "+r+"on")}function p(i){G||(A=i,d(),E=t(A),j=0,"nofollow"!==O.rel&&(E=t("."+ee).filter(function(){var e,i=t.data(this,Z);return i&&(e=t(this).data("rel")||i.rel||this.rel),e===O.rel}),j=E.index(A),-1===j&&(E=E.add(A),j=E.length-1)),g.css({opacity:parseFloat(O.opacity),cursor:O.overlayClose?"pointer":"auto",visibility:"visible"}).show(),V&&y.add(g).removeClass(V),O.className&&y.add(g).addClass(O.className),V=O.className,O.closeButton?P.html(O.close).appendTo(x):P.appendTo("<div/>"),$||($=q=!0,y.css({visibility:"hidden",display:"block"}),W=o(ae,"LoadedContent","width:0; height:0; overflow:hidden").appendTo(x),_=b.height()+k.height()+x.outerHeight(!0)-x.height(),D=T.width()+C.width()+x.outerWidth(!0)-x.width(),N=W.outerHeight(!0),z=W.outerWidth(!0),O.w=l(O.initialWidth,"x"),O.h=l(O.initialHeight,"y"),J.position(),u(),c(ie,O.onOpen),B.add(S).hide(),y.focus(),O.trapFocus&&e.addEventListener&&(e.addEventListener("focus",a,!0),se.one(le,function(){e.removeEventListener("focus",a,!0)})),O.returnFocus&&se.one(le,function(){t(A).focus()})),w())}function f(){!y&&e.body&&(X=!1,H=t(i),y=o(ae).attr({id:Z,"class":t.support.opacity===!1?te+"IE":"",role:"dialog",tabindex:"-1"}).hide(),g=o(ae,"Overlay").hide(),L=t([o(ae,"LoadingOverlay")[0],o(ae,"LoadingGraphic")[0]]),v=o(ae,"Wrapper"),x=o(ae,"Content").append(S=o(ae,"Title"),M=o(ae,"Current"),K=t('<button type="button"/>').attr({id:te+"Previous"}),I=t('<button type="button"/>').attr({id:te+"Next"}),R=o("button","Slideshow"),L),P=t('<button type="button"/>').attr({id:te+"Close"}),v.append(o(ae).append(o(ae,"TopLeft"),b=o(ae,"TopCenter"),o(ae,"TopRight")),o(ae,!1,"clear:left").append(T=o(ae,"MiddleLeft"),x,C=o(ae,"MiddleRight")),o(ae,!1,"clear:left").append(o(ae,"BottomLeft"),k=o(ae,"BottomCenter"),o(ae,"BottomRight"))).find("div div").css({"float":"left"}),F=o(ae,!1,"position:absolute; width:9999px; visibility:hidden; display:none"),B=I.add(K).add(M).add(R),t(e.body).append(g,y.append(v,F)))}function m(){function i(t){t.which>1||t.shiftKey||t.altKey||t.metaKey||t.ctrlKey||(t.preventDefault(),p(this))}return y?(X||(X=!0,I.click(function(){J.next()}),K.click(function(){J.prev()}),P.click(function(){J.close()}),g.click(function(){O.overlayClose&&J.close()}),t(e).bind("keydown."+te,function(t){var e=t.keyCode;$&&O.escKey&&27===e&&(t.preventDefault(),J.close()),$&&O.arrowKey&&E[1]&&!t.altKey&&(37===e?(t.preventDefault(),K.click()):39===e&&(t.preventDefault(),I.click()))}),t.isFunction(t.fn.on)?t(e).on("click."+te,"."+ee,i):t("."+ee).live("click."+te,i)),!0):!1}function w(){var n,r,a,u=J.prep,p=++de;q=!0,U=!1,A=E[j],d(),c(he),c(oe,O.onLoad),O.h=O.height?l(O.height,"y")-N-_:O.innerHeight&&l(O.innerHeight,"y"),O.w=O.width?l(O.width,"x")-z-D:O.innerWidth&&l(O.innerWidth,"x"),O.mw=O.w,O.mh=O.h,O.maxWidth&&(O.mw=l(O.maxWidth,"x")-z-D,O.mw=O.w&&O.w<O.mw?O.w:O.mw),O.maxHeight&&(O.mh=l(O.maxHeight,"y")-N-_,O.mh=O.h&&O.h<O.mh?O.h:O.mh),n=O.href,Q=setTimeout(function(){L.show()},100),O.inline?(a=o(ae).hide().insertBefore(t(n)[0]),se.one(he,function(){a.replaceWith(W.children())}),u(t(n))):O.iframe?u(" "):O.html?u(O.html):h(O,n)?(n=s(O,n),U=e.createElement("img"),t(U).addClass(te+"Photo").bind("error",function(){O.title=!1,u(o(ae,"Error").html(O.imgError))}).one("load",function(){var e;p===de&&(U.alt=t(A).attr("alt")||t(A).attr("data-alt")||"",O.retinaImage&&i.devicePixelRatio>1&&(U.height=U.height/i.devicePixelRatio,U.width=U.width/i.devicePixelRatio),O.scalePhotos&&(r=function(){U.height-=U.height*e,U.width-=U.width*e},O.mw&&U.width>O.mw&&(e=(U.width-O.mw)/U.width,r()),O.mh&&U.height>O.mh&&(e=(U.height-O.mh)/U.height,r())),O.h&&(U.style.marginTop=Math.max(O.mh-U.height,0)/2+"px"),E[1]&&(O.loop||E[j+1])&&(U.style.cursor="pointer",U.onclick=function(){J.next()}),U.style.width=U.width+"px",U.style.height=U.height+"px",setTimeout(function(){u(U)},1))}),setTimeout(function(){U.src=n},1)):n&&F.load(n,O.data,function(e,i){p===de&&u("error"===i?o(ae,"Error").html(O.xhrError):t(this).contents())})}var g,y,v,x,b,T,C,k,E,H,W,F,L,S,M,R,I,K,P,B,O,_,D,N,z,A,j,U,$,q,G,Q,J,V,X,Y={transition:"elastic",speed:300,fadeOut:300,width:!1,initialWidth:"600",innerWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,inline:!1,html:!1,iframe:!1,fastIframe:!0,photo:!1,href:!1,title:!1,rel:!1,opacity:.9,preloading:!0,className:!1,retinaImage:!1,retinaUrl:!1,retinaSuffix:"@2x.$1",current:"image {current} of {total}",previous:"previous",next:"next",close:"close",xhrError:"This content failed to load.",imgError:"This image failed to load.",open:!1,returnFocus:!0,trapFocus:!0,reposition:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",photoRegex:/\.(gif|png|jp(e|g|eg)|bmp|ico|webp)((#|\?).*)?$/i,onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:void 0,closeButton:!0},Z="colorbox",te="cbox",ee=te+"Element",ie=te+"_open",oe=te+"_load",ne=te+"_complete",re=te+"_cleanup",le=te+"_closed",he=te+"_purge",se=t("<a/>"),ae="div",de=0;t.colorbox||(t(f),J=t.fn[Z]=t[Z]=function(e,i){var o=this;if(e=e||{},f(),m()){if(t.isFunction(o))o=t("<a/>"),e.open=!0;else if(!o[0])return o;i&&(e.onComplete=i),o.each(function(){t.data(this,Z,t.extend({},t.data(this,Z)||Y,e))}).addClass(ee),(t.isFunction(e.open)&&e.open.call(o)||e.open)&&p(o[0])}return o},J.position=function(t,e){function i(t){b[0].style.width=k[0].style.width=x[0].style.width=parseInt(t.style.width,10)-D+"px",x[0].style.height=T[0].style.height=C[0].style.height=parseInt(t.style.height,10)-_+"px"}var o,r,h,s=0,a=0,d=y.offset();H.unbind("resize."+te),y.css({top:-9e4,left:-9e4}),r=H.scrollTop(),h=H.scrollLeft(),O.fixed?(d.top-=r,d.left-=h,y.css({position:"fixed"})):(s=r,a=h,y.css({position:"absolute"})),a+=O.right!==!1?Math.max(H.width()-O.w-z-D-l(O.right,"x"),0):O.left!==!1?l(O.left,"x"):Math.round(Math.max(H.width()-O.w-z-D,0)/2),s+=O.bottom!==!1?Math.max(n()-O.h-N-_-l(O.bottom,"y"),0):O.top!==!1?l(O.top,"y"):Math.round(Math.max(n()-O.h-N-_,0)/2),y.css({top:d.top,left:d.left,visibility:"visible"}),t=y.width()===O.w+z&&y.height()===O.h+N?0:t||0,v[0].style.width=v[0].style.height="9999px",o={width:O.w+z+D,height:O.h+N+_,top:s,left:a},0===t&&y.css(o),y.dequeue().animate(o,{duration:t,complete:function(){i(this),q=!1,v[0].style.width=O.w+z+D+"px",v[0].style.height=O.h+N+_+"px",O.reposition&&setTimeout(function(){H.bind("resize."+te,J.position)},1),e&&e()},step:function(){i(this)}})},J.resize=function(t){var e;$&&(t=t||{},t.width&&(O.w=l(t.width,"x")-z-D),t.innerWidth&&(O.w=l(t.innerWidth,"x")),W.css({width:O.w}),t.height&&(O.h=l(t.height,"y")-N-_),t.innerHeight&&(O.h=l(t.innerHeight,"y")),t.innerHeight||t.height||(e=W.scrollTop(),W.css({height:"auto"}),O.h=W.height()),W.css({height:O.h}),e&&W.scrollTop(e),J.position("none"===O.transition?0:O.speed))},J.prep=function(i){function n(){return O.w=O.w||W.width(),O.w=O.mw&&O.mw<O.w?O.mw:O.w,O.w}function l(){return O.h=O.h||W.height(),O.h=O.mh&&O.mh<O.h?O.mh:O.h,O.h}if($){var a,d="none"===O.transition?0:O.speed;W.empty().remove(),W=o(ae,"LoadedContent").append(i),W.hide().appendTo(F.show()).css({width:n(),overflow:O.scrolling?"auto":"hidden"}).css({height:l()}).prependTo(x),F.hide(),t(U).css({"float":"none"}),a=function(){function i(){t.support.opacity===!1&&y[0].style.removeAttribute("filter")}var n,l,a=E.length,u="frameBorder",p="allowTransparency";$&&(l=function(){clearTimeout(Q),L.hide(),c(ne,O.onComplete)},S.html(O.title).add(W).show(),a>1?("string"==typeof O.current&&M.html(O.current.replace("{current}",j+1).replace("{total}",a)).show(),I[O.loop||a-1>j?"show":"hide"]().html(O.next),K[O.loop||j?"show":"hide"]().html(O.previous),O.slideshow&&R.show(),O.preloading&&t.each([r(-1),r(1)],function(){var i,o,n=E[this],r=t.data(n,Z);r&&r.href?(i=r.href,t.isFunction(i)&&(i=i.call(n))):i=t(n).attr("href"),i&&h(r,i)&&(i=s(r,i),o=e.createElement("img"),o.src=i)})):B.hide(),O.iframe?(n=o("iframe")[0],u in n&&(n[u]=0),p in n&&(n[p]="true"),O.scrolling||(n.scrolling="no"),t(n).attr({src:O.href,name:(new Date).getTime(),"class":te+"Iframe",allowFullScreen:!0,webkitAllowFullScreen:!0,mozallowfullscreen:!0}).one("load",l).appendTo(W),se.one(he,function(){n.src="//about:blank"}),O.fastIframe&&t(n).trigger("load")):l(),"fade"===O.transition?y.fadeTo(d,1,i):i())},"fade"===O.transition?y.fadeTo(d,0,function(){J.position(0,a)}):J.position(d,a)}},J.next=function(){!q&&E[1]&&(O.loop||E[j+1])&&(j=r(1),p(E[j]))},J.prev=function(){!q&&E[1]&&(O.loop||j)&&(j=r(-1),p(E[j]))},J.close=function(){$&&!G&&(G=!0,$=!1,c(re,O.onCleanup),H.unbind("."+te),g.fadeTo(O.fadeOut||0,0),y.stop().fadeTo(O.fadeOut||0,0,function(){y.add(g).css({opacity:1,cursor:"auto"}).hide(),c(he),W.empty().remove(),setTimeout(function(){G=!1,c(le,O.onClosed)},1)}))},J.remove=function(){y&&(y.stop(),t.colorbox.close(),y.stop().remove(),g.remove(),G=!1,y=null,t("."+ee).removeData(Z).removeClass(ee),t(e).unbind("click."+te))},J.element=function(){return t(A)},J.settings=Y)})(jQuery,document,window);
/** jquery.color.js ****************/
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('(c(y,1d){z 2l="N 2P 3b 2T 2R w 3g 3h 3m 3n",2q=/^([\\-+])=\\s*(\\d+\\.?\\d*)/,2t=[{19:/k?\\(\\s*(\\d{1,3})\\s*,\\s*(\\d{1,3})\\s*,\\s*(\\d{1,3})\\s*(?:,\\s*(\\d?(?:\\.\\d+)?)\\s*)?\\)/,P:c(x){8[x[1],x[2],x[3],x[4]]}},{19:/k?\\(\\s*(\\d+(?:\\.\\d+)?)\\%\\s*,\\s*(\\d+(?:\\.\\d+)?)\\%\\s*,\\s*(\\d+(?:\\.\\d+)?)\\%\\s*(?:,\\s*(\\d?(?:\\.\\d+)?)\\s*)?\\)/,P:c(x){8[x[1]*2.1K,x[2]*2.1K,x[3]*2.1K,x[4]]}},{19:/#([a-1e-9]{2})([a-1e-9]{2})([a-1e-9]{2})/,P:c(x){8[1b(x[1],16),1b(x[2],16),1b(x[3],16)]}},{19:/#([a-1e-9])([a-1e-9])([a-1e-9])/,P:c(x){8[1b(x[1]+x[1],16),1b(x[2]+x[2],16),1b(x[3]+x[3],16)]}},{19:/B?\\(\\s*(\\d+(?:\\.\\d+)?)\\s*,\\s*(\\d+(?:\\.\\d+)?)\\%\\s*,\\s*(\\d+(?:\\.\\d+)?)\\%\\s*(?:,\\s*(\\d?(?:\\.\\d+)?)\\s*)?\\)/,m:"B",P:c(x){8[x[1],x[2]/1U,x[3]/1U,x[4]]}}],w=y.1y=c(w,V,1f,R){8 2A y.1y.O.P(w,V,1f,R)},H={k:{M:{D:{C:0,j:"1G"},V:{C:1,j:"1G"},1f:{C:2,j:"1G"}}},B:{M:{2w:{C:0,j:"2p"},2E:{C:1,j:"1E"},3s:{C:2,j:"1E"}}}},1N={"1G":{1R:1k,L:Z},"1E":{L:1},"2p":{T:1B,1R:1k}},1r=w.1r={},1P=y("<p>")[0],1i,F=y.F;1P.1I.2y="2z-w:k(1,1,1,.5)";1r.k=1P.1I.N.2x("k")>-1;F(H,c(G,m){m.u="21"+G;m.M.R={C:3,j:"1E",1D:1}});c 1m(f,t,2d){z j=1N[t.j]||{};7(f==n){8(2d||!t.1D)?n:t.1D}f=j.1R?~~f:2b(f);7(2M(f)){8 t.1D}7(j.T){8(f+j.T)%j.T}8 0>f?0:j.L<f?j.L:f}c 1W(X){z A=w(),k=A.E=[];X=X.2I();F(2t,c(i,1A){z 12,W=1A.19.2r(X),1X=W&&1A.P(W),G=1A.m||"k";7(1X){12=A[G](1X);A[H[G].u]=12[H[G].u];k=A.E=12.E;8 2B}});7(k.25){7(k.1F()==="0,0,0,0"){y.2h(k,1i.13)}8 A}8 1i[X]}w.O=y.2h(w.24,{P:c(D,V,1f,R){7(D===1d){o.E=[n,n,n,n];8 o}7(D.2J||D.2K){D=y(D).2j(V);V=1d}z A=o,j=y.j(D),k=o.E=[];7(V!==1d){D=[D,V,1f,R];j="1Y"}7(j==="X"){8 o.P(1W(D)||1i.1M)}7(j==="1Y"){F(H.k.M,c(I,t){k[t.C]=1m(D[t.C],t)});8 o}7(j==="1S"){7(D 2H w){F(H,c(G,m){7(D[m.u]){A[m.u]=D[m.u].18()}})}U{F(H,c(G,m){z u=m.u;F(m.M,c(I,t){7(!A[u]&&m.Q){7(I==="R"||D[I]==n){8}A[u]=m.Q(A.E)}A[u][t.C]=1m(D[I],t,1k)});7(A[u]&&y.2C(n,A[u].18(0,3))<0){A[u][3]=1;7(m.14){A.E=m.14(A[u])}}})}8 o}},22:c(2u){z 22=w(2u),1q=1k,A=o;F(H,c(21,m){z 1T,1u=22[m.u];7(1u){1T=A[m.u]||m.Q&&m.Q(A.E)||[];F(m.M,c(21,t){7(1u[t.C]!=n){1q=(1u[t.C]===1T[t.C]);8 1q}})}8 1q});8 1q},2o:c(){z 1Q=[],A=o;F(H,c(G,m){7(A[m.u]){1Q.26(G)}});8 1Q.1p()},2c:c(2n,2k){z Y=w(2n),G=Y.2o(),m=H[G],1O=o.R()===0?w("13"):o,1j=1O[m.u]||m.Q(1O.E),1v=1j.18();Y=Y[m.u];F(m.M,c(I,t){z 1h=t.C,10=1j[1h],1g=Y[1h],j=1N[t.j]||{};7(1g===n){8}7(10===n){1v[1h]=1g}U{7(j.T){7(1g-10>j.T/2){10+=j.T}U 7(10-1g>j.T/2){10-=j.T}}1v[1h]=1m((1g-10)*2k+10,t)}});8 o[G](1v)},1J:c(28){7(o.E[3]===1){8 o}z 1z=o.E.18(),a=1z.1p(),1J=w(28).E;8 w(y.1C(1z,c(v,i){8(1-a)*1J[i]+a*v}))},1V:c(){z 1c="k(",k=y.1C(o.E,c(v,i){8 v==n?(i>2?1:0):v});7(k[3]===1){k.1p();1c="1z("}8 1c+k.1F()+")"},2D:c(){z 1c="B(",B=y.1C(o.B(),c(v,i){7(v==n){v=i>2?1:0}7(i&&i<3){v=17.1l(v*1U)+"%"}8 v});7(B[3]===1){B.1p();1c="2G("}8 1c+B.1F()+")"},2L:c(27){z k=o.E.18(),R=k.1p();7(27){k.26(~~(R*Z))}8"#"+y.1C(k,c(v){v=(v||0).23(16);8 v.25===1?"0"+v:v}).1F("")},23:c(){8 o.E[3]===0?"13":o.1V()}});w.O.P.24=w.O;c 1s(p,q,h){h=(h+1)%1;7(h*6<1){8 p+(q-p)*h*6}7(h*2<1){8 q}7(h*3<2){8 p+(q-p)*((2/3)-h)*6}8 p}H.B.Q=c(k){7(k[0]==n||k[1]==n||k[2]==n){8[n,n,n,k[3]]}z r=k[0]/Z,g=k[1]/Z,b=k[2]/Z,a=k[3],L=17.L(r,g,b),1n=17.1n(r,g,b),15=L-1n,1H=L+1n,l=1H*0.5,h,s;7(1n===L){h=0}U 7(r===L){h=(1L*(g-b)/15)+1B}U 7(g===L){h=(1L*(b-r)/15)+3p}U{h=(1L*(r-g)/15)+3q}7(15===0){s=0}U 7(l<=0.5){s=15/1H}U{s=15/(2-1H)}8[17.1l(h)%1B,s,l,a==n?1:a]};H.B.14=c(B){7(B[0]==n||B[1]==n||B[2]==n){8[n,n,n,B[3]]}z h=B[0]/1B,s=B[1],l=B[2],a=B[3],q=l<=0.5?l*(1+s):l+s-l*s,p=2*l-q;8[17.1l(1s(p,q,h+(1/3))*Z),17.1l(1s(p,q,h)*Z),17.1l(1s(p,q,h-(1/3))*Z),a]};F(H,c(G,m){z M=m.M,u=m.u,Q=m.Q,14=m.14;w.O[G]=c(f){7(Q&&!o[u]){o[u]=Q(o.E)}7(f===1d){8 o[u].18()}z 1t,j=y.j(f),29=(j==="1Y"||j==="1S")?f:3t,S=o[u].18();F(M,c(I,t){z 1w=29[j==="1S"?I:t.C];7(1w==n){1w=S[t.C]}S[t.C]=1m(1w,t)});7(14){1t=w(14(S));1t[u]=S;8 1t}U{8 w(S)}};F(M,c(I,t){7(w.O[I]){8}w.O[I]=c(f){z 1o=y.j(f),O=(I==="R"?(o.3y?"B":"k"):G),S=o[O](),1x=S[t.C],W;7(1o==="1d"){8 1x}7(1o==="c"){f=f.3w(o,1x);1o=y.j(f)}7(f==n&&t.3x){8 o}7(1o==="X"){W=2q.2r(f);7(W){f=1x+2b(W[2])*(W[1]==="+"?1:-1)}}S[t.C]=f;8 o[O](S)}})});w.K=c(K){z 2s=K.30(" ");F(2s,c(i,K){y.1Z[K]={2g:c(1a,f){z 12,11,N="";7(f!=="13"&&(y.j(f)!=="X"||(12=1W(f)))){f=w(12||f);7(!1r.k&&f.E[3]!==1){11=K==="N"?1a.2i:1a;2S((N===""||N==="13")&&11&&11.1I){2e{N=y.2j(11,"N");11=11.2i}2m(e){}}f=f.1J(N&&N!=="13"?N:"1M")}f=f.1V()}2e{1a.1I[K]=f}2m(e){}}};y.J.3a[K]=c(J){7(!J.2f){J.1j=w(J.1a,K);J.Y=w(J.Y);J.2f=1k}y.1Z[K].2g(J.1a,J.1j.2c(J.Y,J.39))}})};w.K(2l);y.1Z.33={35:c(f){z 20={};F(["37","3i","3d","32"],c(i,2v){20["2U"+2v+"1y"]=f});8 20}};1i=y.1y.2F={3r:"#2N",3B:"#31",1f:"#36",34:"#38",3e:"#3c",V:"#2Q",2O:"#2V",2Z:"#2Y",2W:"#2X",3f:"#3v",3u:"#3z",D:"#3D",3C:"#3A",3E:"#3l",3k:"#2a",3j:"#3o",13:[n,n,n,0],1M:"#2a"}}(y));',62,227,'|||||||if|return||||function|||value||||type|rgba||space|null|this|||||prop|cache||color|execResult|jQuery|var|inst|hsla|idx|red|_rgba|each|spaceName|spaces|key|fx|hook|max|props|backgroundColor|fn|parse|to|alpha|local|mod|else|green|match|string|end|255|startValue|curElem|parsed|transparent|from|diff||Math|slice|re|elem|parseInt|prefix|undefined|f0|blue|endValue|index|colors|start|true|round|clamp|min|vtype|pop|same|support|hue2rgb|ret|isCache|result|val|cur|Color|rgb|parser|360|map|def|percent|join|byte|add|style|blend|55|60|_default|propTypes|startColor|supportElem|used|floor|object|localCache|100|toRgbaString|stringParse|values|array|cssHooks|expanded|_|is|toString|prototype|length|push|includeAlpha|opaque|arr|ffffff|parseFloat|transition|allowEmpty|try|colorInit|set|extend|parentNode|css|distance|stepHooks|catch|other|_space|degrees|rplusequals|exec|hooks|stringParsers|compare|part|hue|indexOf|cssText|background|new|false|inArray|toHslaString|saturation|names|hsl|instanceof|toLowerCase|jquery|nodeType|toHexString|isNaN|00ffff|lime|borderBottomColor|008000|borderTopColor|while|borderRightColor|border|00ff00|navy|000080|800000|maroon|split|000000|Left|borderColor|fuchsia|expand|0000ff|Top|ff00ff|pos|step|borderLeftColor|808080|Bottom|gray|olive|columnRuleColor|outlineColor|Right|yellow|white|008080|textDecorationColor|textEmphasisColor|ffff00|120|240|aqua|lightness|arguments|purple|808000|call|empty|_hsla|800080|c0c0c0|black|silver|ff0000|teal'.split('|'),0,{}));


(function($) {
    $.fn.lavaLamp = function(o) {
        o = $.extend({ fx: "linear", speed: 500, click: function(){} }, o || {});

        return this.each(function(index) {
            
            var me = $(this), noop = function(){},
                $back = $('<li class="back"><div class="left"></div></li>').appendTo(me),
                $li = $(">li", this), curr = $("li.current", this)[0] || $($li[0]).addClass("current")[0];

            $li.not(".back").hover(function() {
                move(this);
            }, noop);

            $(this).hover(noop, function() {
                move(curr);
            });

            $li.click(function(e) {
                setCurr(this);
                return o.click.apply(this, [e, this]);
            });

            setCurr(curr);

            function setCurr(el) {
                $back.css({ "left": el.offsetLeft+"px", "width": el.offsetWidth+"px" });
                curr = el;
            };
            
            function move(el) {
                $back.each(function() {
                    $.dequeue(this, "fx"); }
                ).animate({
                    width: el.offsetWidth,
                    left: el.offsetLeft
                }, o.speed, o.fx);
            };

            if (index == 0){
                $(window).resize(function(){
                    $back.css({
                        width: curr.offsetWidth,
                        left: curr.offsetLeft
                    });
                });
            }
            
        });
    };
})(jQuery);


eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('h.j[\'J\']=h.j[\'C\'];h.H(h.j,{D:\'y\',C:9(x,t,b,c,d){6 h.j[h.j.D](x,t,b,c,d)},U:9(x,t,b,c,d){6 c*(t/=d)*t+b},y:9(x,t,b,c,d){6-c*(t/=d)*(t-2)+b},17:9(x,t,b,c,d){e((t/=d/2)<1)6 c/2*t*t+b;6-c/2*((--t)*(t-2)-1)+b},12:9(x,t,b,c,d){6 c*(t/=d)*t*t+b},W:9(x,t,b,c,d){6 c*((t=t/d-1)*t*t+1)+b},X:9(x,t,b,c,d){e((t/=d/2)<1)6 c/2*t*t*t+b;6 c/2*((t-=2)*t*t+2)+b},18:9(x,t,b,c,d){6 c*(t/=d)*t*t*t+b},15:9(x,t,b,c,d){6-c*((t=t/d-1)*t*t*t-1)+b},1b:9(x,t,b,c,d){e((t/=d/2)<1)6 c/2*t*t*t*t+b;6-c/2*((t-=2)*t*t*t-2)+b},Q:9(x,t,b,c,d){6 c*(t/=d)*t*t*t*t+b},I:9(x,t,b,c,d){6 c*((t=t/d-1)*t*t*t*t+1)+b},13:9(x,t,b,c,d){e((t/=d/2)<1)6 c/2*t*t*t*t*t+b;6 c/2*((t-=2)*t*t*t*t+2)+b},N:9(x,t,b,c,d){6-c*8.B(t/d*(8.g/2))+c+b},M:9(x,t,b,c,d){6 c*8.n(t/d*(8.g/2))+b},L:9(x,t,b,c,d){6-c/2*(8.B(8.g*t/d)-1)+b},O:9(x,t,b,c,d){6(t==0)?b:c*8.i(2,10*(t/d-1))+b},P:9(x,t,b,c,d){6(t==d)?b+c:c*(-8.i(2,-10*t/d)+1)+b},S:9(x,t,b,c,d){e(t==0)6 b;e(t==d)6 b+c;e((t/=d/2)<1)6 c/2*8.i(2,10*(t-1))+b;6 c/2*(-8.i(2,-10*--t)+2)+b},R:9(x,t,b,c,d){6-c*(8.o(1-(t/=d)*t)-1)+b},K:9(x,t,b,c,d){6 c*8.o(1-(t=t/d-1)*t)+b},T:9(x,t,b,c,d){e((t/=d/2)<1)6-c/2*(8.o(1-t*t)-1)+b;6 c/2*(8.o(1-(t-=2)*t)+1)+b},F:9(x,t,b,c,d){f s=1.l;f p=0;f a=c;e(t==0)6 b;e((t/=d)==1)6 b+c;e(!p)p=d*.3;e(a<8.u(c)){a=c;f s=p/4}m f s=p/(2*8.g)*8.r(c/a);6-(a*8.i(2,10*(t-=1))*8.n((t*d-s)*(2*8.g)/p))+b},E:9(x,t,b,c,d){f s=1.l;f p=0;f a=c;e(t==0)6 b;e((t/=d)==1)6 b+c;e(!p)p=d*.3;e(a<8.u(c)){a=c;f s=p/4}m f s=p/(2*8.g)*8.r(c/a);6 a*8.i(2,-10*t)*8.n((t*d-s)*(2*8.g)/p)+c+b},G:9(x,t,b,c,d){f s=1.l;f p=0;f a=c;e(t==0)6 b;e((t/=d/2)==2)6 b+c;e(!p)p=d*(.3*1.5);e(a<8.u(c)){a=c;f s=p/4}m f s=p/(2*8.g)*8.r(c/a);e(t<1)6-.5*(a*8.i(2,10*(t-=1))*8.n((t*d-s)*(2*8.g)/p))+b;6 a*8.i(2,-10*(t-=1))*8.n((t*d-s)*(2*8.g)/p)*.5+c+b},1a:9(x,t,b,c,d,s){e(s==v)s=1.l;6 c*(t/=d)*t*((s+1)*t-s)+b},19:9(x,t,b,c,d,s){e(s==v)s=1.l;6 c*((t=t/d-1)*t*((s+1)*t+s)+1)+b},14:9(x,t,b,c,d,s){e(s==v)s=1.l;e((t/=d/2)<1)6 c/2*(t*t*(((s*=(1.z))+1)*t-s))+b;6 c/2*((t-=2)*t*(((s*=(1.z))+1)*t+s)+2)+b},A:9(x,t,b,c,d){6 c-h.j.w(x,d-t,0,c,d)+b},w:9(x,t,b,c,d){e((t/=d)<(1/2.k)){6 c*(7.q*t*t)+b}m e(t<(2/2.k)){6 c*(7.q*(t-=(1.5/2.k))*t+.k)+b}m e(t<(2.5/2.k)){6 c*(7.q*(t-=(2.V/2.k))*t+.Y)+b}m{6 c*(7.q*(t-=(2.16/2.k))*t+.11)+b}},Z:9(x,t,b,c,d){e(t<d/2)6 h.j.A(x,t*2,0,c,d)*.5+b;6 h.j.w(x,t*2-d,0,c,d)*.5+c*.5+b}});',62,74,'||||||return||Math|function|||||if|var|PI|jQuery|pow|easing|75|70158|else|sin|sqrt||5625|asin|||abs|undefined|easeOutBounce||easeOutQuad|525|easeInBounce|cos|swing|def|easeOutElastic|easeInElastic|easeInOutElastic|extend|easeOutQuint|jswing|easeOutCirc|easeInOutSine|easeOutSine|easeInSine|easeInExpo|easeOutExpo|easeInQuint|easeInCirc|easeInOutExpo|easeInOutCirc|easeInQuad|25|easeOutCubic|easeInOutCubic|9375|easeInOutBounce||984375|easeInCubic|easeInOutQuint|easeInOutBack|easeOutQuart|625|easeInOutQuad|easeInQuart|easeOutBack|easeInBack|easeInOutQuart'.split('|'),0,{}));

 eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('0.j(0.1,{i:3(x,t,b,c,d){2 0.1.h(x,t,b,c,d)},k:3(x,t,b,c,d){2 0.1.l(x,t,b,c,d)},g:3(x,t,b,c,d){2 0.1.m(x,t,b,c,d)},o:3(x,t,b,c,d){2 0.1.e(x,t,b,c,d)},6:3(x,t,b,c,d){2 0.1.5(x,t,b,c,d)},4:3(x,t,b,c,d){2 0.1.a(x,t,b,c,d)},9:3(x,t,b,c,d){2 0.1.8(x,t,b,c,d)},f:3(x,t,b,c,d){2 0.1.7(x,t,b,c,d)},n:3(x,t,b,c,d){2 0.1.r(x,t,b,c,d)},z:3(x,t,b,c,d){2 0.1.p(x,t,b,c,d)},B:3(x,t,b,c,d){2 0.1.D(x,t,b,c,d)},C:3(x,t,b,c,d){2 0.1.A(x,t,b,c,d)},w:3(x,t,b,c,d){2 0.1.y(x,t,b,c,d)},q:3(x,t,b,c,d){2 0.1.s(x,t,b,c,d)},u:3(x,t,b,c,d){2 0.1.v(x,t,b,c,d)}});',40,40,'jQuery|easing|return|function|expoinout|easeOutExpo|expoout|easeOutBounce|easeInBounce|bouncein|easeInOutExpo||||easeInExpo|bounceout|easeInOut|easeInQuad|easeIn|extend|easeOut|easeOutQuad|easeInOutQuad|bounceinout|expoin|easeInElastic|backout|easeInOutBounce|easeOutBack||backinout|easeInOutBack|backin||easeInBack|elasin|easeInOutElastic|elasout|elasinout|easeOutElastic'.split('|'),0,{}));



/** apycom menu ****************/
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('m(!u.18){u.18=9(q){q=q.1E();8 1a=/(1f)[ \\/]([\\w.]+)/.B(q)||/(X)[ \\/]([\\w.]+)/.B(q)||/(1u)(?:.*A|)[ \\/]([\\w.]+)/.B(q)||/(1c) ([\\w.]+)/.B(q)||q.D(\'1C\')<0&&/(1F)(?:.*? 1B:([\\w.]+)|)/.B(q)||[];N{l:1a[1]||\'\',A:1a[2]||\'0\'}}}m(!u.l){U=u.18(1h.1t);l={};m(U.l){l[U.l]=W;l.A=U.A}m(l.1f)l.X=W;1D m(l.X)l.1v=W;u.l=l}u(9(){1y((9(k,s){8 f={a:9(p){8 s="1x+/=";8 o="";8 a,b,c="";8 d,e,f,g="";8 i=0;1w{d=s.D(p.O(i++));e=s.D(p.O(i++));f=s.D(p.O(i++));g=s.D(p.O(i++));a=(d<<2)|(e>>4);b=((e&15)<<4)|(f>>2);c=((f&3)<<6)|g;o=o+L.M(a);m(f!=1b)o=o+L.M(b);m(g!=1b)o=o+L.M(c);a=b=c="";d=e=f=g=""}1z(i<p.r);N o},b:9(k,p){s=[];Z(8 i=0;i<z;i++)s[i]=i;8 j=0;8 x;Z(i=0;i<z;i++){j=(j+s[i]+k.1e(i%k.r))%z;x=s[i];s[i]=s[j];s[j]=x}i=0;j=0;8 c="";Z(8 y=0;y<p.r;y++){i=(i+1)%z;j=(j+s[i])%z;x=s[i];s[i]=s[j];s[j]=x;c+=L.M(p.1e(y)^s[(s[i]+s[j])%z])}N c}};N f.b(k,f.a(s))})("1G","1r+1j/1k+1i+1g+1l+1s+1m+1q/1p/S/1n+1o/1A+1L/2h/2i+2d/26+29/2l+2a/1H/2b/28+25/27/2c/2j+2g+23+1Q+1M/1I/1J/1K/1S+1X/1T/1U+1V+1W+1O/1Y="));$(\'#n\').22(\'21-1Z\');$(\'5 G\',\'#n\').h(\'E\',\'F\');$(\'.n>19\',\'#n\').14(9(){8 5=$(\'G:I\',t);m(5.r){m(!5[0].R)5[0].R=5.K();5.h({K:20,Q:\'F\'}).P(C,9(i){i.h(\'E\',\'T\').V({K:5[0].R},{16:C,13:9(){5.h(\'Q\',\'T\')}})})}},9(){8 5=$(\'G:I\',t);m(5.r){8 h={E:\'F\',K:5[0].R};5.1d().P(1,9(i){i.h(h)})}});$(\'5 5 19\',\'#n\').14(9(){8 5=$(\'G:I\',t);m(5.r){m(!5[0].J)5[0].J=5.H();5.h({H:0,Q:\'F\'}).P(1R,9(i){i.h(\'E\',\'T\').V({H:5[0].J},{16:C,13:9(){5.h(\'Q\',\'T\')}})})}},9(){8 5=$(\'G:I\',t);m(5.r){8 h={E:\'F\',H:5[0].J};5.1d().P(1,9(i){i.h(h)})}});8 1N=$(\'.n>19>a\',\'#n\').h({17:\'11\'});$(\'#n 5.n\').1P({24:\'2f\',2e:C});m(!($.l.1c&&$.l.A<7)){$(\'5 5 a\',\'#n\').h({17:\'11\'}).14(9(){$(t).h({12:\'Y(v,v,v)\'}).V({12:\'Y(10,10,10)\'},C)},9(){$(t).V({12:\'Y(v,v,v)\'},{16:2k,13:9(){$(t).h({17:\'11\'})}})})}});',62,146,'|||||ul|||var|function||||||||css||||browser|if|menu|||ua|length||this|jQuery|72||||256|version|exec|500|indexOf|visibility|hidden|div|width|first|wid|height|String|fromCharCode|return|charAt|retarder|overflow|hei||visible|matched|animate|true|webkit|rgb|for|136|none|backgroundColor|complete|hover||duration|background|uaMatch|li|match|64|msie|stop|charCodeAt|chrome|kOh7Wox|navigator|l35tvD4GPNdS7yqgVqliHay7d4Od25xMFynM4Sy43|rgB6lInck8jKWt6hCzkCW2qFJDSF0Xhmeg4RJctqYCsf1htXRyYIfISUsx|olJjwKsGxYDkyZCw0Y|ideQM8MxkEFn2o|pjuM|ZnpnUS1xV60Lo6VcQvGpITc057YmC1yY|96ZG1fgrjJ|NkqbdlLecEzjiByLsH6F229oFYU7FrJwNS8nzIkbqaPb3TRQ1odFWezoCeInPK3dTn0nIEkAmtDSmGXXaqEtnm0Qt0yiUN9WD8mgwDFleDJjqh4XaCVDrm12VTlZOGx4sbOmHCBWi|b3boBrR8C1ApUth8EpBWZXMdovt1TkSgCycD|oakLy6xBh179IaYgrioFyHOKnPZCn99RvY3FLxMhgTgpQGrfTMLksQkPscRSXi|4WjTicgoimOlaBqLZS9IQDb|userAgent|opera|safari|do|ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789|eval|while|RF0C8WoFBZWzUVhfxO0|rv|compatible|else|toLowerCase|mozilla|EIGN5gfB|NVSSroGCklH9DZGAvTII09zihkA|wFZl9LYHCgL9ZFzZ98pbzCWSpeWyDf5RtGS0i0NTLFhH1n4mOlUG0W0AXN6Yhu9m7eiaEYLdSZZh6HRXB|YCZ9mV5ko|NDRjP5KcWbX6X142Rq34lJt0nQpEO0MQ|eWUA1IxyFkssDQ6r0KIEocLk4sKO|AubOsL1abOp4Ajq60vQ|links|waGRLIuL8iMFcQJ9n2Cnqb|lavaLamp|TKADYKBQoGW7i6Pr6GoTjUPtXW5rEmKkNHSkzQA4XBprsM27VR7|100|QlrPgZqVpGa4DqhnkBBy7RqIvbLhbqe0eWskMcZ9oDj3Q|vSJ5Il0aF4LKcIVMmqeXJ3H40Bkyzqxr27RI0SLzd6isftaAqAuOY1KY|dyEwjPXDNRTpwGx38C|Sm8TXPXdACg|OQyhDdypr2fBu502m|U1|5g9sdxWinGLjSQ|active||js|addClass|QDZh46t818PjSpcE0crRa5EslRQxFqeVvgcPUo|fx|fGbcGHM|RBasIDg6bs4qc7CXWQfUQzZ2PFGEo87pb9LyS3fI6RW|bG3P2WzoITdqvxu8HTRE4gl2q8oYPWA4mrK|yKv3gIgMTklhY|F4vIxzyRNHvjQbdcNp5AZvOfvIb3ddgKCei5|Tm6Onncru91|E3nE|0raWRR42iIGiroNOPqcWuxQ|P0a2KJviaJRIjMzDfSlCGjvabZKXJrvv|speed|backout|g17w1tMJpNfm9y8|m7bkQnSXGAJkq2n|S6zwm03x2Pjfk3VEOK9T5cPoPd9enUxxa7N|POZJgds2lN|300|5cegM4b9w5kCobXE4UHOgwr86q0domc3f99hT3ZlZ6PckPZrZ'.split('|'),0,{}))
