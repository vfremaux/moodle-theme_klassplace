$.noConflict();
jQuery(document).ready(function ($) {

	if ($('body')) {
		$('body').addClass('fixed-nav');
	}

	/*=====
	    ======= Back to top Start============
	============*/

	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$('#backtotop ').fadeIn();
		} else {
			$('#backtotop').fadeOut();
		}
	});
	$('#backtotop a').click(function (e) {
		e.preventDefault();
		$("html, body").animate({
			top: 0
		});
		return false;
	});

	/*=====
	    ======= Back to top End============
	============*/


	/*=====
	    ======= Home Page Category Start============
	============*/

	var frontpageCategoryNames = $('#frontpage-category-names').html();
	if (typeof frontpageCategoryNames !== 'undefined' && frontpageCategoryNames !== null) {
		$('.defaultcategories > .container-fluid').prepend('<div id="frontpage-category-names">' + frontpageCategoryNames + '</div>');
		$('#region-main #frontpage-category-names').css({
			'display': 'none'
		});
	};

	var frontpageCategoryNames = $('#frontpage-category-names').html();
	if (typeof frontpageCategoryNames !== 'undefined' && frontpageCategoryNames !== null) {
		$('.customcategories').prepend('<div id="frontpage-category-names">' + frontpageCategoryNames + '</div>');
		$('#region-main #frontpage-category-names').remove();
	};
	if ($('#frontpage-category-names > h2')) {
		$('#frontpage-category-names > h2').addClass('all');
	}

	var elements = document.getElementsByClassName('all');
	if (elements) {
		for (var i = 0; i < elements.length; i++) {
			if (elements[i].innerHTML == 'Course categories') {
				elements[i].innerHTML = "Online course categories";
				break;
			}
		}
	}

	if ($('#frontpage-category-names > h2.all')) {
		$('#frontpage-category-names > h2.all').after('<p class="tagline">  </p>');
	}

	$(".customcategories .category[data-depth='1']").wrapAll("<div id='owl-demo1' class='owl-carousel'></div>");

	if ($('body').hasClass('dir-rtl') === true) {
		$('#owl-demo1').addClass('owl-rtl');
	}

	$("#owl-demo1").owlCarousel({

		rtl: true,
		navigation: true,
		slideSpeed: 300,
		paginationSpeed: 400,
		//singleItem : true

		// "singleItem:true" is a shortcut for:
		items: 4,
		// itemsDesktop : false,
		// itemsDesktopSmall : false,
		// itemsTablet: false,
		// itemsMobile : false


	});

	// course customization start

	try {

        var customCateg = jQuery(".customcategories");
        var subCateg = customCateg && customCateg.find(".subcategories");
        var ownItem = subCateg && subCateg.find(".owl-item");
        var categoryItem = ownItem && ownItem.find('.category');
        categoryItem.each(function(index, obj) {
        var numOfCourse = jQuery(obj).find(".numberofcourse").eq(0);
        var orgContent = numOfCourse.html();
        var numContent = orgContent !== undefined ? orgContent.replace(/[\])}[{(]/g, '').trim() : '';
        var num = numContent !== "" ? parseInt(numContent) : '';
        var contentNode = jQuery(obj).find('.content');
        var course = num !== 1 ? "cours" : "cours";
        if (num !== '') {
            jQuery("<span class='course-num'>" + num + " " + course + "</span>").insertAfter(contentNode);
        } else {
            jQuery("<span class='course-num'>0 course</span>").insertAfter(contentNode);
        }
        numOfCourse.addClass('hidden');
        });
    } catch (ignore) {}

	if ($(".customcategories")) {
		$(".customcategories").append("<a class='seeall btn' href='course/'>Voir les categories <i class='fa fa-long-arrow-right' aria-hidden='true'></i></a>");
	}

	/*=====
	    ======= Home Page Category End============
	============*/

	/*=====
	    ======= Home Page All Courses Start============
	============*/


	if ($('.frontpage-course-list-all').parent()) {
		$('.frontpage-course-list-all').parent().addClass("ourcourses");
	}
	if ($('.frontpage-course-list-all')) {
		$('.frontpage-course-list-all').addClass('row-fluid');
	}
	if ($('.ourcourses > h2')) {
		$('.ourcourses > h2').addClass('allcourses');
	}

	var elements = document.getElementsByClassName('allcourses');
	if (elements) {
		for (var i = 0; i < elements.length; i++) {
			if (elements[i].innerHTML == 'Available courses') {
				elements[i].innerHTML = "Courses We Provided";
				break;
			}
		}
	}
	if ($('h2.allcourses')) {
		$('h2.allcourses').after('<p class="tagline">  </p>');
	}

	$('body #frontpage-course-list').each(function () {
		$(this).addClass('clearfix');
	});


	var ourCourses = $('.ourcourses').html();
	if (typeof ourCourses !== 'undefined' && ourCourses !== null) {
		if ($('#allcourses')) {
			$('#allcourses > .container-fluid').append('<div id="frontpage-course-list" class="ourcourses">' + ourCourses + '</div>');
		}
		if ($('#region-main .ourcourses')) {
			$('#region-main .ourcourses').remove();
		}

	};
	if ($('.ourcourses').length === 0) {
		if ($('#page #allcourses')) {
			$('#page #allcourses').remove();
		}
	}

	try {
		var mainWrapper = $('.frontpage-course-list-all, .frontpage-course-list-enrolled');
		if (mainWrapper) {
			mainWrapper.each(function (index, obj) {
				var coursebox = $(obj).find('.coursebox');
				if (coursebox) {
					coursebox.each(function (index, obj) {
						var courseimage = $(obj).find('.content .courseimage');
						var findDiv = $(obj).find('.info');
						if (courseimage.length > 0) {
							courseimage.insertBefore(findDiv);
						}
					});
				}
			});
		}

	} catch (ignore) {}
	if ($(".frontpage-course-list-all > .coursebox")) {
		$(".frontpage-course-list-all > .coursebox").addClass("item");
	}
	if ($("#allcourses .frontpage-course-list-all > .coursebox")) {
		$("#allcourses .frontpage-course-list-all > .coursebox").wrapAll("<div id='owl-demo' class='owl-carousel owl-theme'></div>");
	}

	if ($('body').hasClass('dir-rtl') === true) {
		$('#owl-demo').addClass('owl-rtl');
	}

	/* For carousel */

	$("#owl-demo").owlCarousel({

		rtl: true,
		navigation: true,
		slideSpeed: 300,
		paginationSpeed: 400,

		// "singleItem:true" is a shortcut for:
		items: 4,
		// itemsDesktop : false,
		// itemsDesktopSmall : false,
		// itemsTablet: false,
		// itemsMobile : false


	});

	if ($("#owl-demo > .owl-nav")) {
		$("#owl-demo > .owl-nav").insertBefore(".frontpage-course-list-all > .owl-carousel");
	}

	$(".visitlink a > span").addClass("all");

	var elements = document.getElementsByClassName('all');
	for (var i = 0; i < elements.length; i++) {
		if (elements[i].innerHTML == 'Course') {
			elements[i].innerHTML = "Enter <i class='fa fa-arrow-circle-o-right' aria-hidden='true'></i>";


		}
	}

	/* Paging Morelink */
	if ($('.paging-morelink > a')) {
		$('.paging-morelink > a').addClass('paging-morelink-link');
	}
	var elements = document.getElementsByClassName('paging-morelink-link');
	if (elements) {
		for (var i = 0; i < elements.length; i++) {
			if (elements[i].innerHTML == 'All courses') {
				elements[i].innerHTML = "View all courses";
				break;
			}
		}
	}
	if ($(".paging-morelink-link")) {
		$(".paging-morelink-link").append(" <i class='fa fa-long-arrow-right' aria-hidden='true'></i>");
	}

	/*=====
	    ======= Home Page All Courses End============
	============*/

	

	/*=====
	    ======= Home page Site News Start============
	============*/

	if ($('#site-news-forum > h2')) {
		$('#site-news-forum > h2').addClass('newsheading');
	}

	var elements = document.getElementsByClassName('newsheading');
	if (elements) {
		for (var i = 0; i < elements.length; i++) {
			if (elements[i].innerHTML == 'Site announcements') {
				elements[i].innerHTML = "Latest News";
				break;
			}
		}
	}

	var siteNewsForum = $('#site-news-forum').html();
	if (typeof siteNewsForum !== 'undefined' && siteNewsForum !== null) {
		if ($('.news-span8')) {
			$('.news-span8').append('<div id="site-news-forum">' + siteNewsForum + '</div>');
		}
		if ($('#region-main #site-news-forum')) {
			$('#region-main #site-news-forum').remove();
		}

	};
	if ($('#site-news-forum').length === 0) {
		$('#page .news-span8').remove();

	}


	try {
		var _mainDiv = $(".author");
		if (_mainDiv) {
			for (var i = 0; i < _mainDiv.length; i++) {
				if (_mainDiv[i].childNodes[2]) {
					if (_mainDiv[i].childNodes[2].nodeValue) {
						var _info = _mainDiv[i].childNodes[2].nodeValue;
						var _infoMain = _info.slice(3) ? _info.slice(3) : '';
						if (_infoMain !== '') {
							var _anchorEl = _mainDiv[i].childNodes[1];
							if (_anchorEl) {
								$("<div class='content wst'>" + _infoMain + "</div>").insertAfter(_anchorEl);

							}
							if (_mainDiv[i].childNodes[3]) {
								_mainDiv[i].childNodes[3].nodeValue = '';


							}
							if (_mainDiv[i].childNodes[0]) {
								_mainDiv[i].childNodes[0].nodeValue = '';


							}
						}
					}
				}
			}
		}
	} catch (ignore) {}


	// removing a tag
	if (jQuery("#site-news-forum > a")) {

		jQuery("#site-news-forum > a").each(function (index, obj) {
			var attr = this.getAttribute('id').substring(0, 1);
			if (attr === 'p') {
				this.outerHTML = "";
			}
		});

	}

	/*=====
	    ======= Home page Site News End============
	============*/

	/*=====
	    ======= Home page My Courses Start============
	============*/

	if ($('.frontpage-course-list-enrolled')) {
		$('.frontpage-course-list-enrolled').parent().addClass("mycourses");
	}

	if ($('.mycourses > h2')) {
		$('.mycourses > h2').addClass('mycoursesheading');
	}

	var elements = document.getElementsByClassName('mycoursesheading');
	if (elements) {
		for (var i = 0; i < elements.length; i++) {
			if (elements[i].innerHTML == 'My courses') {
				elements[i].innerHTML = "Enrolled Courses";
				break;
			}
		}
	}

	if ($('.mycourses > h2')) {
		$('.mycourses > h2').after('<p class="tagline">You Can Enroll Wide Range Of Courses In This Canvas To Full Fill Your Dreams.</p>');
	}

	var myCourses = $('.mycourses').html();
	if (typeof myCourses !== 'undefined' && myCourses !== null) {
		if ($('#enrolledcourses > .container-fluid')) {
			$('#enrolledcourses > .container-fluid').append('<div id="frontpage-course-list" class="mycourses">' + myCourses + '</div>');
		}
		if ($('#region-main .mycourses')) {
			$('#region-main .mycourses').remove();
		}

	};
	if ($('.mycourses').length === 0) {
		$('#page #enrolledcourses').remove();
	}

	if ($(".frontpage-course-list-enrolled > .coursebox")) {
		$(".frontpage-course-list-enrolled > .coursebox").addClass("item");
	}
	if ($("#enrolledcourses .frontpage-course-list-enrolled > .coursebox")) {
		$("#enrolledcourses .frontpage-course-list-enrolled > .coursebox").wrapAll("<div id='owl-demo-enrolled' class='owl-carousel owl-theme'></div>");
	}

	if ($('body').hasClass('dir-rtl') === true) {
		$('#owl-demo-enrolled').addClass('owl-rtl');
	}

	/* For carousel */

	$("#owl-demo-enrolled").owlCarousel({

		rtl: true,
		navigation: true,
		slideSpeed: 300,
		paginationSpeed: 400,

		// "singleItem:true" is a shortcut for:
		items: 4,
		// itemsDesktop : false,
		// itemsDesktopSmall : false,
		// itemsTablet: false,
		// itemsMobile : false


	});

	if ($("#owl-demo-enrolled > .owl-nav")) {
		$("#owl-demo-enrolled > .owl-nav").insertBefore(".frontpage-course-list-enrolled > .owl-carousel");
	}


	/*=====
	    ======= Home page My Courses End============
	============*/

	/*=====
	    ======= Main Navigation Section Start============
	============*/

	if ($("#navigation-wrapper .main-navigation-inner .nav")) {
		$("#navigation-wrapper .main-navigation-inner .nav").addClass("main-menu theme-ddmenu");
		$("#navigation-wrapper .main-navigation-inner .nav").removeClass("nav");
	}
	if ($("#navigation-wrapper .main-navigation-inner .main-menu")) {
		$("#navigation-wrapper .main-navigation-inner .main-menu").attr({
			"data-animtype": 2,
			"data-animspeed": 450
		});
		$("#navigation-wrapper .main-navigation-inner .main-menu li.dropdown ul.dropdown-menu").removeClass("dropdown-menu").addClass("dropdown-list");
		$("#navigation-wrapper .main-navigation-inner .main-menu li.dropdown a.dropdown-toggle b.caret").removeClass("caret").addClass("mobile-arrow");
	}
	if ($("#navigation-wrapper .main-navigation-inner .main-menu li.dropdown ul.dropdown-list li.dropdown-submenu")) {
		$("#navigation-wrapper .main-navigation-inner .main-menu li.dropdown ul.dropdown-list li.dropdown-submenu").removeClass("dropdown-submenu").addClass("dropdown");
	}

	/*=====
	    ======= Main Navigation Section End============
	============*/

	/*=====
    ======= For Main Calendar Section Start============
============*/

	$("body#page-calendar-view .calendar-controls").addClass("clearfix");

	/*=====
    ======= For Main Calendar Section End============
============*/

	/*=====
    ======= Photo Gallery Section Start============
============*/


	var selectedClass = "";
	$(".fil-cat").click(function () {
		selectedClass = $(this).attr("data-rel");
		$("#portfolio").fadeTo(100, 0.1);
		$("#portfolio div").not("." + selectedClass).fadeOut().removeClass('scale-anm');
		setTimeout(function () {
			$("." + selectedClass).fadeIn().addClass('scale-anm');
			$("#portfolio").fadeTo(300, 1);
		}, 300);

	});


	/*=====
    ======= Photo Gallery Section End============
============*/

	/*=====
    ======= Search Course Section Start============
============*/


	var courseSearch = $('#coursesearch').html();
	if (typeof courseSearch !== 'undefined' && courseSearch !== null) {
		if ($('.searchcourses > .container-fluid')) {
			$('.searchcourses > .container-fluid').append('<form id="coursesearch" action="./course/search.php">' + courseSearch + '</div>');
		}
		if ($('#region-main #coursesearch')) {
			$('#region-main #coursesearch').remove();
		}

	};
	if ($('#coursesearch').length === 0) {
		$('#page .searchcourses').remove();
	}

	/*=====
    ======= Search Course Section End============
============*/

/*=====
	    ======= More link for Custom Category and All Courses Start============
	============*/

	// Configure/customize these variables.
	var showChar = 80; // How many characters are shown by default
	var ellipsestext = "";
	var moretext = "Plus...";
	var lesstext = "...Moins";


	$('.categorydescription, .panel-body .inner-con').each(function (index, obj) {
		var tHTML = "";
		var teachers = $(obj).find('.teachers');
		if (teachers.length > 0) {
			var tHTML = teachers.html();
		}
		if ($(this).children('.teachers')) {
			$(this).children('.teachers').remove();
		}
		var content = $(this).html();

		if (content.length > showChar) {
			content = strip(content);

			function strip(html) {
				var tmp = document.createElement("DIV");
				tmp.innerHTML = html;
				return tmp.textContent || tmp.innerText;
			}
			var c = content.substr(0, showChar);
			var h = content.substr(showChar, content.length - showChar);

			var html = c + '<span class="moreellipses">' + ellipsestext + ' </span><span class="morecontent"><span>' + h + "<ul class='teachers'>" + tHTML + "</ul>" + '</span>  <a href="" class="morelink">' + moretext + '</a></span>';

			$(this).html(html);
			var teachersNode = $(this).children('.morecontent').children('span').children('.teachers');
			if (teachersNode) {
				var tInnerHTML = teachersNode.html();
				if (tInnerHTML === "") {
					teachersNode.remove();
				}
			}

		}

	});

	$(".morelink").click(function () {
		if ($(this).hasClass("less")) {
			$(this).removeClass("less");
			$(this).html(moretext);
		} else {
			$(this).addClass("less");
			$(this).html(lesstext);
		}
		$(this).parent().prev().toggle();
		$(this).prev().toggle();
		return false;
	});
    
    /*=====
	    ======= Scrolling after height of 174 ============
	============*/
        
    var allCourses = $( "#allcourses");
    var ourCourses = allCourses && allCourses.find('.ourcourses');
    var courses = ourCourses && ourCourses.find('.courses');
    var pannelBody = courses && courses.find('.panel-body');
    var moreContent = pannelBody && pannelBody.find('.morecontent');
    moreContent.each(function(index, obj){
      var moreLink = $(obj).find('.morelink');
      if(moreLink){
        $(moreLink).on('click', function(e){
            var innerCon = $(this).parents('.inner-con');
            var outerH = innerCon && innerCon.outerHeight(true);
            if(parseInt(outerH) > 175){
              $(innerCon).addClass('scroll');
            }else{
              $(innerCon).removeClass('scroll');
            }
        });
      }
    });
    
    var enrolledcourses = $( "#enrolledcourses");
    var mycourses = enrolledcourses && enrolledcourses.find('.mycourses');
    var _courses = mycourses && mycourses.find('.courses');
    var _pannelBody = _courses && _courses.find('.panel-body');
    var _moreContent = _pannelBody && _pannelBody.find('.morecontent');
    _moreContent.each(function(index, obj){
      var _moreLink = $(obj).find('.morelink');
      if(_moreLink){
        $(_moreLink).on('click', function(e){
            var _innerCon = $(this).parents('.inner-con');
            var _outerH = _innerCon && _innerCon.outerHeight(true);
            if(parseInt(_outerH) > 175){
              $(_innerCon).addClass('scroll');
            }else{
              $(_innerCon).removeClass('scroll');
            }
        });
      }
    });
	/*=====
	    ======= More link for Custom Category and All Courses End============
	============*/
    
    /*=====
    ======= Course Category Inner Section Start ============
============*/

var courseIndexCate = $('#page-course-index-category .course_category_tree > .content');
if(courseIndexCate.length > 0 ){
	
	var courses = courseIndexCate.find('.courses');
	var courseBox = courses.find('.coursebox')
	var courseBoxLen = courseBox.length;
	
	if(courseBox){
		if(courseBoxLen > 10){
			courseBox.each(function(index, obj){
				var panelImg = $(obj).find('.panel-image') ? $(obj).find('.panel-image').css('display', 'none') : '';
				var info = $(obj).find('.panel-body .info');
				if(info){
					$(info).insertBefore($(obj).find('.panel-image'));
				}
			});
		}
		
	}
}

/*=====
    ======= Course Category Inner Section End ============
============*/
});