/**
Core script to handle the entire theme and core functions
**/
var Layout = function() {

    var layoutImgPath = ASC_ADMIN.getAssetsPath() + 'admin/layout2/img/';

    var layoutCssPath = ASC_ADMIN.getAssetsPath() + 'admin/layout2/css/';

    //* BEGIN:CORE HANDLERS *//
    // this function handles responsive layout on screen size resize or mobile device rotate.

    // Set proper height for sidebar and content. The content and sidebar height must be synced always.
    var handleSidebarAndContentHeight = function() {
        var content = jQuery('.page-content');
        var sidebar = jQuery('.page-sidebar');
        var body = jQuery('body');
        var height;

        if (body.hasClass("page-footer-fixed") === true && body.hasClass("page-sidebar-fixed") === false) {
            var available_height = ASC_ADMIN.getViewPort().height - jQuery('.page-footer').outerHeight() - jQuery('.page-header').outerHeight();
            if (content.height() < available_height) {
                content.attr('style', 'min-height:' + available_height + 'px');
            }
        } else {
            if (body.hasClass('page-sidebar-fixed')) {
                height = _calculateFixedSidebarViewportHeight();
                if (body.hasClass('page-footer-fixed') === false) {
                    height = height - jQuery('.page-footer').outerHeight();
                }
            } else {
                var headerHeight = jQuery('.page-header').outerHeight();
                var footerHeight = jQuery('.page-footer').outerHeight();

                if (ASC_ADMIN.getViewPort().width < 992) {
                    height = ASC_ADMIN.getViewPort().height - headerHeight - footerHeight;
                } else {
                    height = sidebar.outerHeight() + 10;
                }

                if ((height + headerHeight + footerHeight) <= ASC_ADMIN.getViewPort().height) {
                    height = ASC_ADMIN.getViewPort().height - headerHeight - footerHeight;
                }
            }
            content.attr('style', 'min-height:' + height + 'px');
        }
    };

    // Handle sidebar menu
    var handleSidebarMenu = function() {
        jQuery('.page-sidebar').on('click', 'li > a', function(e) {

            if (ASC_ADMIN.getViewPort().width >= 992 && jQuery(this).parents('.page-sidebar-menu-hover-submenu').size() === 1) { // exit of hover sidebar menu
                return;
            }

            if (jQuery(this).next().hasClass('sub-menu') === false) {
                if (ASC_ADMIN.getViewPort().width < 992 && jQuery('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                    jQuery('.page-header .responsive-toggler').click();
                }
                return;
            }

            if (jQuery(this).next().hasClass('sub-menu always-open')) {
                return;
            }

            var parent = jQuery(this).parent().parent();
            var the = jQuery(this);
            var menu = jQuery('.page-sidebar-menu');
            var sub = jQuery(this).next();

            var autoScroll = menu.data("auto-scroll");
            var slideSpeed = parseInt(menu.data("slide-speed"));

            parent.children('li.open').children('a').children('.arrow').removeClass('open');
            parent.children('li.open').children('.sub-menu:not(.always-open)').slideUp(slideSpeed);
            parent.children('li.open').removeClass('open');

            var slideOffeset = -200;

            if (sub.is(":visible")) {
                jQuery('.arrow', jQuery(this)).removeClass("open");
                jQuery(this).parent().removeClass("open");
                sub.slideUp(slideSpeed, function() {
                    if (autoScroll === true && jQuery('body').hasClass('page-sidebar-closed') === false) {
                        if (jQuery('body').hasClass('page-sidebar-fixed')) {
                            menu.slimScroll({
                                'scrollTo': (the.position()).top
                            });
                        } else {
                            ASC_ADMIN.scrollTo(the, slideOffeset);
                        }
                    }
                    handleSidebarAndContentHeight();
                });
            } else {
                jQuery('.arrow', jQuery(this)).addClass("open");
                jQuery(this).parent().addClass("open");
                sub.slideDown(slideSpeed, function() {
                    if (autoScroll === true && jQuery('body').hasClass('page-sidebar-closed') === false) {
                        if (jQuery('body').hasClass('page-sidebar-fixed')) {
                            menu.slimScroll({
                                'scrollTo': (the.position()).top
                            });
                        } else {
                            ASC_ADMIN.scrollTo(the, slideOffeset);
                        }
                    }
                    handleSidebarAndContentHeight();
                });
            }

            e.preventDefault();
        });

        // handle ajax links within sidebar menu
        jQuery('.page-sidebar').on('click', ' li > a.ajaxify', function(e) {
            e.preventDefault();
            ASC_ADMIN.scrollTop();

            var url = jQuery(this).attr("href");
            var menuContainer = jQuery('.page-sidebar ul');
            var pageContent = jQuery('.page-content');
            var pageContentBody = jQuery('.page-content .page-content-body');

            menuContainer.children('li.active').removeClass('active');
            menuContainer.children('arrow.open').removeClass('open');

            jQuery(this).parents('li').each(function() {
                jQuery(this).addClass('active');
                jQuery(this).children('a > span.arrow').addClass('open');
            });
            jQuery(this).parents('li').addClass('active');

            if (ASC_ADMIN.getViewPort().width < 992 && jQuery('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                jQuery('.page-header .responsive-toggler').click();
            }

            ASC_ADMIN.startPageLoading();

            var the = jQuery(this);

            jQuery.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                success: function(res) {

                    if (the.parents('li.open').size() === 0) {
                        jQuery('.page-sidebar-menu > li.open > a').click();
                    }

                    ASC_ADMIN.stopPageLoading();
                    pageContentBody.html(res);
                    Layout.fixContentHeight(); // fix content height
                    ASC_ADMIN.initAjax(); // initialize core stuff
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    ASC_ADMIN.stopPageLoading();
                    pageContentBody.html('<h4>Could not load the requested content.</h4>');
                }
            });
        });

        // handle ajax link within main content
        jQuery('.page-content').on('click', '.ajaxify', function(e) {
            e.preventDefault();
            ASC_ADMIN.scrollTop();

            var url = jQuery(this).attr("href");
            var pageContent = jQuery('.page-content');
            var pageContentBody = jQuery('.page-content .page-content-body');

            ASC_ADMIN.startPageLoading();

            if (ASC_ADMIN.getViewPort().width < 992 && jQuery('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                jQuery('.page-header .responsive-toggler').click();
            }

            jQuery.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                success: function(res) {
                    ASC_ADMIN.stopPageLoading();
                    pageContentBody.html(res);
                    Layout.fixContentHeight(); // fix content height
                    ASC_ADMIN.initAjax(); // initialize core stuff
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    pageContentBody.html('<h4>Could not load the requested content.</h4>');
                    ASC_ADMIN.stopPageLoading();
                }
            });
        });
    };

    // Helper function to calculate sidebar height for fixed sidebar layout.
    var _calculateFixedSidebarViewportHeight = function() {
        var sidebarHeight = ASC_ADMIN.getViewPort().height - jQuery('.page-header').outerHeight();
        if (jQuery('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - jQuery('.page-footer').outerHeight();
        }

        return sidebarHeight;
    };

    // Handles fixed sidebar
    var handleFixedSidebar = function() {
        var menu = jQuery('.page-sidebar-menu');

        ASC_ADMIN.destroySlimScroll(menu);

        if (jQuery('.page-sidebar-fixed').size() === 0) {
            handleSidebarAndContentHeight();
            return;
        }

        if (ASC_ADMIN.getViewPort().width >= 992) {
            menu.attr("data-height", _calculateFixedSidebarViewportHeight());
            ASC_ADMIN.initSlimScroll(menu);
            handleSidebarAndContentHeight();
        }
    };

    // Handles sidebar toggler to close/hide the sidebar.
    var handleFixedSidebarHoverEffect = function() {
        var body = jQuery('body');
        if (body.hasClass('page-sidebar-fixed')) {
            jQuery('.page-sidebar-menu').on('mouseenter', function() {
                if (body.hasClass('page-sidebar-closed')) {
                    jQuery(this).removeClass('page-sidebar-menu-closed');
                }
            }).on('mouseleave', function() {
                if (body.hasClass('page-sidebar-closed')) {
                    jQuery(this).addClass('page-sidebar-menu-closed');
                }
            });
        }
    };

    // Hanles sidebar toggler
    var handleSidebarToggler = function() {
        var body = jQuery('body');
        if (jQuery.cookie && jQuery.cookie('sidebar_closed') === '1' && ASC_ADMIN.getViewPort().width >= 992) {
            jQuery('body').addClass('page-sidebar-closed');
            jQuery('.page-sidebar-menu').addClass('page-sidebar-menu-closed');
        }

        // handle sidebar show/hide
        jQuery('.page-sidebar, .page-header').on('click', '.sidebar-toggler', function(e) {
            var sidebar = jQuery('.page-sidebar');
            var sidebarMenu = jQuery('.page-sidebar-menu');
            jQuery(".sidebar-search", sidebar).removeClass("open");

            if (body.hasClass("page-sidebar-closed")) {
                body.removeClass("page-sidebar-closed");
                sidebarMenu.removeClass("page-sidebar-menu-closed");
                if (jQuery.cookie) {
                    jQuery.cookie('sidebar_closed', '0');
                }
            } else {
                body.addClass("page-sidebar-closed");
                sidebarMenu.addClass("page-sidebar-menu-closed");
                if (body.hasClass("page-sidebar-fixed")) {
                    sidebarMenu.trigger("mouseleave");
                }
                if (jQuery.cookie) {
                    jQuery.cookie('sidebar_closed', '1');
                }
            }

            jQuery(window).trigger('resize');
        });

        handleFixedSidebarHoverEffect();

        // handle the search bar close
        jQuery('.page-sidebar').on('click', '.sidebar-search .remove', function(e) {
            e.preventDefault();
            jQuery('.sidebar-search').removeClass("open");
        });

        // handle the search query submit on enter press
        jQuery('.page-sidebar .sidebar-search').on('keypress', 'input.form-control', function(e) {
            if (e.which == 13) {
                jQuery('.sidebar-search').submit();
                return false; //<---- Add this line
            }
        });

        // handle the search submit(for sidebar search and responsive mode of the header search)
        jQuery('.sidebar-search .submit').on('click', function(e) {
            e.preventDefault();
            if (jQuery('body').hasClass("page-sidebar-closed")) {
                if (jQuery('.sidebar-search').hasClass('open') === false) {
                    if (jQuery('.page-sidebar-fixed').size() === 1) {
                        jQuery('.page-sidebar .sidebar-toggler').click(); //trigger sidebar toggle button
                    }
                    jQuery('.sidebar-search').addClass("open");
                } else {
                    jQuery('.sidebar-search').submit();
                }
            } else {
                jQuery('.sidebar-search').submit();
            }
        });

        // handle close on body click
        if (jQuery('.sidebar-search').size() !== 0) {
            jQuery('.sidebar-search .input-group').on('click', function(e) {
                e.stopPropagation();
            });

            jQuery('body').on('click', function() {
                if (jQuery('.sidebar-search').hasClass('open')) {
                    jQuery('.sidebar-search').removeClass("open");
                }
            });
        }
    };

    // Handles the horizontal menu
    var handleHeader = function() {
        // handle search box expand/collapse        
        jQuery('.page-header').on('click', '.search-form', function(e) {
            jQuery(this).addClass("open");
            jQuery(this).find('.form-control').focus();

            jQuery('.page-header .search-form .form-control').on('blur', function(e) {
                jQuery(this).closest('.search-form').removeClass("open");
                jQuery(this).unbind("blur");
            });
        });

        // handle hor menu search form on enter press
        jQuery('.page-header').on('keypress', '.hor-menu .search-form .form-control', function(e) {
            if (e.which == 13) {
                jQuery(this).closest('.search-form').submit();
                return false;
            }
        });

        // handle header search button click
        jQuery('.page-header').on('mousedown', '.search-form.open .submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).closest('.search-form').submit();
        });
    };

    // Handles Bootstrap Tabs.
    var handleTabs = function() {
        // fix content height on tab click
        jQuery('body').on('shown.bs.tab', 'a[data-toggle="tab"]', function() {
            handleSidebarAndContentHeight();
        });
    };

    // Handles the go to top button at the footer
    var handleGoTop = function() {
        var offset = 300;
        var duration = 500;

        if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) { // ios supported
            jQuery(window).bind("touchend touchcancel touchleave", function(e) {
                if (jQuery(this).scrollTop() > offset) {
                    jQuery('.scroll-to-top').fadeIn(duration);
                } else {
                    jQuery('.scroll-to-top').fadeOut(duration);
                }
            });
        } else { // general 
            jQuery(window).scroll(function() {
                if (jQuery(this).scrollTop() > offset) {
                    jQuery('.scroll-to-top').fadeIn(duration);
                } else {
                    jQuery('.scroll-to-top').fadeOut(duration);
                }
            });
        }

        jQuery('.scroll-to-top').click(function(e) {
            e.preventDefault();
            jQuery('html, body').animate({
                scrollTop: 0
            }, duration);
            return false;
        });
    };

    // Hanlde 100% height elements(block, portlet, etc)
    var handle100HeightContent = function() {

        var target = jQuery('.full-height-content');
        var height;

        if (!target.hasClass('portlet')) {
            return;
        }

        height = ASC_ADMIN.getViewPort().height -
            jQuery('.page-header').outerHeight(true) -
            jQuery('.page-footer').outerHeight(true) -
            jQuery('.page-title').outerHeight(true) -
            jQuery('.page-bar').outerHeight(true);

        if (jQuery('body').hasClass('page-header-fixed')) {
            height = height - jQuery('.page-header').outerHeight(true);
        }

        var portletBody = target.find('.portlet-body');

        if (ASC_ADMIN.getViewPort().width < 992) {
            ASC_ADMIN.destroySlimScroll(portletBody.find('.full-height-content-body')); // destroy slimscroll 
            return;
        }

        if (target.find('.portlet-title')) {
            height = height - target.find('.portlet-title').outerHeight(true);
        }

        height = height - parseInt(portletBody.css("padding-top"));
        height = height - parseInt(portletBody.css("padding-bottom"));

        if (target.hasClass("full-height-content-scrollable")) {
            portletBody.find('.full-height-content-body').css('height', height);
            ASC_ADMIN.initSlimScroll(portletBody.find('.full-height-content-body'));
        } else {
            portletBody.css('min-height', height);
        }
    };

    //* END:CORE HANDLERS *//

    return {

        //main function to initiate the theme
        init: function() {
            //IMPORTANT!!!: Do not modify the core handlers call order.

            //layout handlers
            handleFixedSidebar(); // handles fixed sidebar menu
            handleSidebarMenu(); // handles main menu
            handleHeader(); // handles horizontal menu
            handleSidebarToggler(); // handles sidebar hide/show
            handle100HeightContent(); // handles 100% height elements(block, portlet, etc)            
            handleGoTop(); //handles scroll to top functionality in the footer
            handleTabs(); // handle bootstrah tabs

            // reinitialize the layout on window resize
            ASC_ADMIN.addResizeHandler(handleSidebarAndContentHeight); // recalculate sidebar & content height on window resize
            ASC_ADMIN.addResizeHandler(handleFixedSidebar); // reinitialize fixed sidebar on window resize
            ASC_ADMIN.addResizeHandler(handle100HeightContent); // reinitialize content height on window resize 
        },

        //public function to fix the sidebar and content height accordingly
        fixContentHeight: function() {
            handleSidebarAndContentHeight();
        },

        initFixedSidebarHoverEffect: function() {
            handleFixedSidebarHoverEffect();
        },

        initFixedSidebar: function() {
            handleFixedSidebar();
        },

        getLayoutImgPath: function() {
            return layoutImgPath;
        },

        getLayoutCssPath: function() {
            return layoutCssPath;
        }
    };

}();
