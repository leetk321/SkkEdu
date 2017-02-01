;(function($){
	var base = {
		// GENERAL
		onID: '#xgdfid',
		onClass: '.xgdfid',
		mode: 'left',
		xgLI: 'li',
		showTitle: true,
		showContent: false,
		browserTitle: false,
		showCategory: false,
		showCommand: false,
		showNickname: false,
		image_event: 'N',
		thumb_event: 'N',
		slide_auto: true,
		slide_down: false,
		slide_delay: 5000,

		// CONTROLS
		controls: true,
		nextText: 'Next',
		prevText: 'Prev',

		// CALLBACKS
		onSliderLoad: function() {},
		onSlideBefore: function() {},
		onSlideAfter: function() {},
		onSlideNext: function() {},
		onSlidePrev: function() {}
	}

	$.fn.xeGallery = function (options) {
	// xeGallery 시작부분

		if(this.length > 1){
			this.each(function(){$(this).xeGallery(options)});
			return this;
		}

		var xegalry = {};
		var el = this;
		xgImage = function (img) {
			this.src = { 
				img: img.attr('src'),
				idx: img.attr('idx')
			};
			this.scale = {
				width: img.attr('width'),
				height:  img.attr('height')
			};
			return this;
		}

		xgThumb = function (img) {
			this.src = { 
				img: img.attr('src'),
				idx: img.attr('idx')
			};
			this.scale = {
				width: img.attr('width'),
				height:  img.attr('height')
			};
			this.attrs = {
				link: img.attr('hyperlink'),
				viewer: img.attr('viewer'),
				title: img.attr('alt'),
				author:	img.attr('title'),
				subject: img.attr('subject'),
				article: img.attr('article'),
				mid: img.attr('mid'),
				midtitle: img.attr('midtitle'),
				midlink: img.attr('midlink'),
				category_srl: img.attr('category_srl'),
				category: img.attr('category'),
				categorylink: img.attr('categorylink'),
				commentcnt: img.attr('comment_count')
			};
			return this;
		}
		init = function()
		{
			xegalry.opts = $.extend({}, base, options);
			xegalry.loader = $('<div class="xg-loading" />');
			$(xegalry.opts.onID + ' ul li:first-child').prepend(xegalry.loader);
			xegalry.children = $(xegalry.opts.onID + ' ul').children(xegalry.opts.xgLI);
			//xegalry.index = $(xegalry.opts.onID + ' ul li.photoviewer img').attr("idx");
			xegalry.index = 0;
			var self = this;
			var xgUL = $(xegalry.opts.onID + ' ul');
			xegalry.photoviewer = $(xegalry.opts.onID + ' ul li.photoviewer');
			xegalry.thumbviewer = $(xegalry.opts.onID + ' ul li.thumbviewer');
			$(xegalry.opts.onID + " ul li.thumbviewer img").mouseover(function(){
				var t = $(this);
				setup(t);
				t.animate({opacity:1},100);
			}).mouseout(function(){
				$(this).animate({opacity:0.4},100);
			});

			if(xegalry.opts.browserTitle) xegalry.midlink = xegalry.photoviewer.find('a.midlink');
			if(xegalry.opts.showCategory) xegalry.categorylink = xegalry.photoviewer.find('a.categorylink');
			if(xegalry.opts.showTitle) xegalry.title = xegalry.photoviewer.find('a.xgDFdfT1');
			if(xegalry.opts.showContent) xegalry.content = xegalry.photoviewer.find('a.xgDFdfC1');
			if(xegalry.opts.showCommand) xegalry.command = xegalry.photoviewer.find('span.xgDFdfCMcnt');
			if(xegalry.opts.controls) appendControls();
			preloadImages(function(){
				el.css('overflow', 'visible');
				xegalry.opts.onSliderLoad();
			});
		}
		appendControls = function(){
			xegalry.controls = {}
			xegalry.controls.next = $('<a class="next" href="">' + xegalry.opts.nextText + '</a>');
			xegalry.controls.prev = $('<a class="prev" href="">' + xegalry.opts.prevText + '</a>');
			xegalry.controls.next.bind('click', clickNextBind);
			xegalry.controls.prev.bind('click', clickPrevBind);
			xegalry.controls.directionEl = $('<div class="xg-controls" />');
			xegalry.controls.directionEl.append(xegalry.controls.prev).append(xegalry.controls.next);
			$(xegalry.opts.onID + ' ul li:first-child').prepend(xegalry.controls.directionEl);
		}
		clickNextBind = function(e){
			el.goToNextSlide(xegalry.index);
			e.preventDefault();
		}
		clickPrevBind = function(e){
			el.goToPrevSlide(xegalry.index);
			e.preventDefault();
		}
		el.goToNextSlide = function(i){
			var thumbIndex = i + 1;
			if (thumbIndex >= xegalry.children.length) thumbIndex = 0;
			el.goToSlide(thumbIndex);
		}
		el.goToPrevSlide = function(i){
			var thumbIndex = i - 1;
			if (thumbIndex < 0) thumbIndex = xegalry.children.length - 1;
			el.goToSlide(thumbIndex);
		}
		el.goToSlide = function(slideIndex){
			if(slideIndex < 0){
				xegalry.index = xegalry.children.length - 1;
			}else if(slideIndex >= xegalry.children.length){
				xegalry.index = 0;
			}else{
				xegalry.index = slideIndex;
			}
			xegalry.active = xegalry.children.eq(slideIndex);
			var img = xegalry.active.find('img');
			var xgItem = new xgThumb(img);
			xegalry.photoviewer.find('img').attr({
				src:xgItem.attrs.viewer,
				title:xgItem.attrs.title,
				idx:xgItem.src.idx
			}).fadeIn(120);
			xegalry.photoviewer.animate({opacity:1},100);
			if(xegalry.opts.browserTitle) xegalry.midlink.attr({href:xgItem.attrs.mid}).text(xgItem.attrs.midtitle).fadeIn(120);
			if(xegalry.opts.showCategory) xegalry.categorylink.attr({href:xgItem.attrs.categorylink}).text(xgItem.attrs.category).fadeIn(120);
			if(xegalry.opts.showTitle) xegalry.title.attr({href:xgItem.attrs.link,title:xgItem.attrs.title}).text(xgItem.attrs.title).fadeIn(120);
			if(xegalry.opts.showContent) xegalry.content.attr({href:xgItem.attrs.link,title:xgItem.attrs.title}).text(xgItem.attrs.article).fadeIn(120);
			if(xegalry.opts.showCommand) xegalry.command.text(xgItem.attrs.commentcnt).fadeIn(120);
		}

		var preloadImages = function(callback){
			var images = xegalry.children.find('img[src!=""]');
			var loaded = 0;
			if(images.length > 0){
				images.each(function(index){
					var img = $(this);
					img.load(function(){
						++loaded;
						if(images.length == loaded){
							xegalry.loader.remove();
							callback();
						}
					});
				}).each(function() {
					if(this.complete) $(this).load();
				});
			}else{
				xegalry.loader.remove();
				callback();
			}
		}
		var rolliTem = function(){
			$(xegalry.opts.onID + " ul li:last").slideDown(0,function(){
				var self = $(this);
				if(xegalry.opts.slide_auto) {
					var h = self.find('img');
					setup(h);
				}
				self.insertAfter($(xegalry.opts.onID + " ul li:first-child")).show().slideUp(0).slideDown('slow');
			})
		}
		var setup = function(img){
			var Thumbnail = new xgThumb(img);
			if(xegalry.opts.showTitle) xegalry.subject = img.attr("subject");
			if(xegalry.opts.showContent) xegalry.article = img.attr("article");
			if(xegalry.opts.image_event=='X') xegalry.hyperlink = img.attr("doclink");
			else if(xegalry.opts.image_event=='D') xegalry.hyperlink = img.attr("doclink");
			else if(xegalry.opts.image_event=='V') xegalry.hyperlink = img.attr("viewer");
			else xegalry.hyperlink = img.attr("hyperlink");
			xegalry.doclink = img.attr("doclink");

			$(xegalry.opts.onID + " .photoviewer a.photolink").attr("href",xegalry.hyperlink);
			$(xegalry.opts.onID + " .photoviewer .xgDFdfTxt a:not(.midlink,.categorylink)").attr("href",xegalry.doclink);

			if(xegalry.opts.browserTitle) {
				$(xegalry.opts.onID + " .photoviewer a.midlink").attr("href",Thumbnail.attrs.midlink);
				$(xegalry.opts.onID + " .xgDFdfTxt a.midlink").text(Thumbnail.attrs.midtitle);
			}
			if(xegalry.opts.showCategory) {
				$(xegalry.opts.onID + " .photoviewer a.categorylink").attr("href",Thumbnail.attrs.categorylink);
				$(xegalry.opts.onID + " .xgDFdfTxt a.categorylink").text(Thumbnail.attrs.category);
			}
			if(xegalry.opts.showCommand) $(xegalry.opts.onID + " .photoviewer .xgDFdfCM .xgDFdfCMcnt").text(Thumbnail.attrs.commentcnt);
			$(xegalry.opts.onID + " .xgDFdfTxt a.xgDFdfT1").text(xegalry.subject);
			$(xegalry.opts.onID + " .xgDFdfTxt a.xgDFdfC1").text(xegalry.article);
			$(xegalry.opts.onID + " .photoviewer img[name='xgDFdfView']").attr({
				src:Thumbnail.attrs.viewer,
				title:Thumbnail.attrs.title,
				idx:Thumbnail.src.idx
			}).fadeIn(120);
		}
		init();
		if(xegalry.opts.slide_down) {
			var rollStart = setInterval(function(){ rolliTem()},xegalry.opts.slide_delay);
			$(xegalry.opts.onID + " ul li").bind("mouseenter mouseleave", function(e) {
				if (e.type == 'mouseenter') { clearInterval(rollStart); }
				else { rollStart = setInterval(function(){ rolliTem()},xegalry.opts.slide_delay); }
			})
		}
		return this;
	// xeGallery 마지막부분
	}

})(jQuery);
