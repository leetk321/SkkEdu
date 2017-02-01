jQuery(function($){
	
	//메인메뉴 드롭다운
	$(function(){
		var gnb = $('div.menu>div.gnb');
		var frq = 100*menu_effect //메뉴 이펙트 강도
		gnb.find("li").mouseover(function(){
			var t = $(this);
			t.find(">a").siblings("ul:not(:animated)").slideDown(frq);
			t.siblings().find(">a").siblings("ul").slideUp(frq);
		}).mouseleave(function(){
			$(this).find(">a").siblings("ul").slideUp(frq);
		});
	});	
	
	
	//3차메뉴 표식 움직임 효과
	$('#header div.gnb li li ul').mouseenter(function(){
		$(this).parent().find('span.view').animate({right:'2px'},200);
	}).mouseleave(function(){
		$(this).parent().find('span.view').animate({right:'5px'},200);
	});
	
	//4차메뉴 표식 움직임 효과
	$('#header div.gnb li li li ul').mouseenter(function(){
		$(this).parent().find('span.view2').animate({right:'2px'},200);
	}).mouseleave(function(){
		$(this).parent().find('span.view2').animate({right:'5px'},200);
	});
		
	//바로이동 기능
	$(function(){
		var move_target = $('#body div.move_target'); //변수
		$(window).scroll(function(){ //스크롤시 표시
			var x = $(this).scrollTop(); //스크롤위치
			if( x > 20){
				move_target.fadeIn(300);
			} else{
				move_target.fadeOut(300);
			}
		});
		move_target.find('a').click(function(event) { //부드럽게 이동
			var id = $(this).attr("href");
			var offset = 50;
			var target = $(id).offset().top - offset;
			$('html, body').animate({scrollTop:target}, 800);
			event.preventDefault();
		});
	});
	
	//로그인 회원가입 유도기능
	$(function(){
		if(enjoy_site_use == 'yes'){
			var frq = 1000*enjoy_site_start_time; //시작시간
			if($.cookie('enjoy_site_use') != 'close'){  // 쿠키값 확인 조건문
				$('#enjoy_site, #enjoy_site_bg').delay(frq).slideDown(1500,function(){ //생성
					if(enjoy_site_auto == 'yes'){ //가려짐 방지기능
						if(enjoy_site_start == 'top'){ //시작위치 상단일경우
							if(menu_top_fixed == 'no'){ //메뉴 최상단 고정기능 미사용시
								$('#header').css('marginTop',enjoy_site_bg_height+'px'); 
							}else{
								$('#header,#header div.menu_bg,#header div.menu').css('marginTop',enjoy_site_bg_height+'px'); 
							}
						}else{ //시작위치 하단일경우
							$('#bottom,#body div.move_target').css('marginBottom',enjoy_site_bg_height+'px'); 
						}
					}	
				}); 
				$('#enjoy_site a.enjoy_btn_red').click(function(){ //닫기기능
					$.cookie('enjoy_site_use','close',{expries:1}); //쿠키굽기
					$('#enjoy_site, #enjoy_site_bg').slideUp(1000,function(){
						if(enjoy_site_auto == 'yes'){ //가려짐 방지기능
							if(enjoy_site_start == 'top'){ //시작위치 상단일경우
								$('#header, #header div.menu_bg, #header div.menu').css('marginTop','0px'); //유도기능 닫기시 상단마진 초기화
							}else{ //시작위치 하단일경우
								$('#bottom, #body div.move_target').css('marginBottom','0px'); //유도기능 닫기시 하단마진 초기화
							}
						}	
					});
				});	
				$('#enjoy_site a.enjoy_login_btn').click(function(){ //로그인 클릭시 id에 포커스 가져가기
					$('#user_id').focus();
				});
			}
		}
	});
	
	//로그인후 한줄공지 기능
	$(function(){
		if(login_site_use == 'yes'){
			var frq = 1000*login_site_start_time; //시작시간
			if($.cookie('login_site_use') != 'close'){  // 쿠키값 확인 조건문
				if($.cookie('login_keep') != 'ok'){
					$('#login_site, #login_site_bg').delay(frq).slideDown(1500,function(){ //생성
						$.cookie('login_keep','ok',{expries:1}); //쿠키굽기
						if(login_site_auto == 'yes'){ //가려짐 방지기능
							if(login_site_start == 'top'){ //시작위치 상단일경우
								if(menu_top_fixed == 'no'){ //메뉴 최상단 고정기능 미사용시
									$('#header').css('marginTop',login_site_bg_height+'px'); 
								}else{
									$('#header, #header div.menu_bg, #header div.menu').css('marginTop',login_site_bg_height+'px'); 
								}
							}else{ //시작위치 하단일경우
								$('#bottom, #body div.move_target').css('marginBottom',login_site_bg_height+'px'); 
							}
						}	
					}); 
				}else{ //한번 슬라이드후 페이지 이동시 바로 나오도록
					$('#login_site, #login_site_bg').slideDown(0,function(){ //생성
						if(login_site_auto == 'yes'){ //가려짐 방지기능
							if(login_site_start == 'top'){ //시작위치 상단일경우
								if(menu_top_fixed == 'no'){ //메뉴 최상단 고정기능 미사용시
									$('#header').css('marginTop',login_site_bg_height+'px'); 
								}else{
									$('#header, #header div.menu_bg, #header div.menu').css('marginTop',login_site_bg_height+'px'); 
								}
							}else{ //시작위치 하단일경우
								$('#bottom, #body div.move_target').css('marginBottom',login_site_bg_height+'px'); 
							}
						}	
					}); 
				}			
				$('#login_site a.login_btn_red').click(function(){ //닫기기능
					$.cookie('login_site_use','close',{expries:1}); //쿠키굽기
					$('#login_site, #login_site_bg').slideUp(1000,function(){
						if(login_site_auto == 'yes'){ //가려짐 방지기능
							if(login_site_start == 'top'){ //시작위치 상단일경우
								$('#header, #header div.menu_bg, #header div.menu').css('marginTop','0px'); //유도기능 닫기시 상단마진 초기화
							}else{ //시작위치 하단일경우
								$('#bottom, #body div.move_target').css('marginBottom','0px'); //유도기능 닫기시 하단마진 초기화
							}
						}	
					});
				});	
			}
		}
	});
	
	//배경화면 자동롤링
	$(function(){
		var frq = 1000*site_image_roll; // 롤링 간격
		var start = 'yes'
		
		function bg_roll(){ //롤링함수
			if(site_image_autosize == 'yes'){ //자동리사이즈 사용시
				if(start == 'yes' ){
					$('div.auto_resize_wrap').find('img').attr('src',site_bg_url2);
					start = 'no'
				}else{
					$('div.auto_resize_wrap').find('img').attr('src',site_bg_url);
					start = 'yes'
				}
			}else{ // 자동리사이즈 미사용시
				if(start == 'yes' ){
					$('body').css('background-image','url('+site_bg_url2+')');
					start = 'no'
				}else{
					$('body').css('background-image','url('+site_bg_url+')');
					start = 'yes'
				}
			}
		}
		if(site_bg_url != 'noimage' && site_bg_url2 != 'noimage'){ //이미지 없을시 호출안함
			setInterval(bg_roll,frq); //반복호출
		}
	});
	
	//상단배너 
	$(function(){ 
		var banner_length = $('#header div.banner_wrap').find('div').length; //배너길이 파악
		
		if(banner_use == 'yes' && banner_length > 1 ){ //배너 사용시에만 로드
			
			var frq = 1000*banner_image_roll; //롤링간격 
			var idx = 1; // 인덱스
			var mouseover = 'no'; //마우스오버값
			
			var t_banner1 = $('#header div.t_banner1'); //배너1 변수
			var t_banner2 = $('#header div.t_banner2'); //배너2 변수
			var t_banner3 = $('#header div.t_banner3'); //배너3 변수
			
			if( banner_length == 2){ // 배너 2개일때
				if(banner_sign_use == 'yes'){ //버튼생성
					$('#header div.btn_wrap').css('display','block'); 
				}
				function autoview2(){ //롤링함수
					if(mouseover == 'no'){ //마우스 오버값 확인
						if( t_banner1.css('zIndex') == '-1' ){
							t_banner1.stop().animate({zIndex:'0',opacity:'1'},500);
							t_banner2.stop().animate({zIndex:'-1',opacity:'0'},500);
						}else{
							t_banner1.stop().animate({zIndex:'-=1',opacity:'0'},500);
							t_banner2.stop().animate({zIndex:'0',opacity:'1'},500);
						}
					}	
				}
				setInterval(autoview2,frq); //롤링함수 반복	
				$('#header div.banner').mouseenter(function(){	mouseover = banner_stop_use;	}); //마우스 오버값 layout_info
				$('#header div.banner').mouseleave(function(){	mouseover = 'no';	});
				
				$('#header div.left_btn,#header div.right_btn').click(function(){ //클릭작동
					if( t_banner1.css('zIndex') == '-1' ){
						t_banner1.stop().animate({zIndex:'0',opacity:'1'},500);
						t_banner2.stop().animate({zIndex:'-1',opacity:'0'},500);
					}else{
						t_banner1.stop().animate({zIndex:'-=1',opacity:'0'},500);
						t_banner2.stop().animate({zIndex:'0',opacity:'1'},500);
					}
				});
			}
			
			if( banner_length == 3){ // 배너 3개일때
				if(banner_sign_use == 'yes'){ //버튼생성
					$('#header div.btn_wrap').css('display','block'); 
				}
				
				function autoview3(){ //롤링함수
					if(mouseover == 'no'){ //마우스 오버값 확인
						if( t_banner1.css('zIndex') == '0' ){
							t_banner2.stop().animate({zIndex:'0',opacity:'1'},500);
						}
						if( t_banner1.css('zIndex') == '-1'){
							t_banner2.stop().animate({zIndex:'-1',opacity:'0'},500);
							t_banner3.stop().animate({zIndex:'0',opacity:'1'},500);
						}
						if( t_banner1.css('zIndex') == '-2'){
							t_banner1.stop().animate({zIndex:'0',opacity:'1'},500);
							t_banner2.stop().animate({zIndex:'-1',opacity:'0'},500);
							t_banner3.stop().animate({zIndex:'-1',opacity:'0'},500);
						}else{
							t_banner1.stop().animate({zIndex:'-=1',opacity:'0'},500);
						}
					}	
				}
				setInterval(autoview3,frq); //롤링함수 반복	
				$('#header div.banner').mouseenter(function(){	mouseover = banner_stop_use;	});  //마우스 오버값 layout_info
				$('#header div.banner').mouseleave(function(){	mouseover = 'no';	});
				
				$('#header div.left_btn').click(function(){ //클릭작동
					if( t_banner1.css('zIndex') == '0' ){
						t_banner3.stop().animate({zIndex:'0',opacity:'1'},500);
					}
					if( t_banner1.css('zIndex') == '-1'){
						t_banner3.stop().animate({zIndex:'-1',opacity:'0'},500);
						t_banner2.stop().animate({zIndex:'0',opacity:'1'},500);
					}
					if( t_banner1.css('zIndex') == '-2'){
						t_banner1.stop().animate({zIndex:'0',opacity:'1'},500);
						t_banner2.stop().animate({zIndex:'-1',opacity:'0'},500);
						t_banner3.stop().animate({zIndex:'-1',opacity:'0'},500);
					}else{
						t_banner1.stop().animate({zIndex:'-=1',opacity:'0'},500);
					}
				});
				$('#header div.right_btn').click(function(){
					if( t_banner1.css('zIndex') == '0' ){
						t_banner2.stop().animate({zIndex:'0',opacity:'1'},500);
					}
					if( t_banner1.css('zIndex') == '-1'){
						t_banner2.stop().animate({zIndex:'-1',opacity:'0'},500);
						t_banner3.stop().animate({zIndex:'0',opacity:'1'},500);
					}
					if( t_banner1.css('zIndex') == '-2'){
						t_banner1.stop().animate({zIndex:'0',opacity:'1'},500);
						t_banner2.stop().animate({zIndex:'-1',opacity:'0'},500);
						t_banner3.stop().animate({zIndex:'-1',opacity:'0'},500);
					}else{
						t_banner1.stop().animate({zIndex:'-=1',opacity:'0'},500);
					}
				});
			}
		}
	});
	
	//컬러셋 테스트 (패턴 테스트 & 컬러피커 연동)
	$("#preview_pattern").change(function(){
		$($(color_picker_target).val()).css('backgroundImage',$(this).val());
		if($(color_picker_target).val() == 'body'){ //배경패턴일경우 값 변환
		$('div.site_pattern').css('backgroundImage',$(this).val());
		$('body').css('backgroundImage','none');
		}
	});
	
	//상단 전체메뉴(사이트맵)
	$(function(){
		var frq = 100*top_site_map_effect //슬라이드 이펙트 강도
		var top_site_map = $('#header div.top_site_map'); //공통변수1
		var top_site_map_open_btn = $('#header div.top_site_map_open_btn'); //공통변수2
		
		if(	$.cookie('top_site_map_keep') == 'close'){ //쿠키값 적용 조건문
			top_site_map.css('display','none');
			top_site_map_open_btn.css('display','block');
		}
		if(	$.cookie('top_site_map_keep') == 'open'){ //쿠키값 적용 조건문
			top_site_map.css('display','block');
			top_site_map_open_btn.css('display','none');
		}
		
		top_site_map_open_btn.click(function(){
			$.cookie('top_site_map_keep','open',{expries:1}); //쿠키굽기
			top_site_map_open_btn.css('display','none');
			top_site_map.slideDown(frq);
		});
		$('#header div.top_site_map_btn').click(function(){
			$.cookie('top_site_map_keep','close',{expries:1}); //쿠키굽기
			top_site_map.slideUp(frq,function(){
				top_site_map_open_btn.css('display','block');
			});
		});
		
		//최상단 고정기능
		if(site_map_top_fixed == 'yes'){
			//고정변수
			var y = parseInt( $('#header div.menu').height() ); //메뉴바 높이
			var z = parseInt( top_site_map.height() ); //전체 메뉴바 높이
			//계산값
			var a = y + z; //고정기능 작동 조건문
			
			$(window).scroll(function(){
				var x = $(this).scrollTop();
				
				if( x > a ){ //고정기능 작동 조건문
					top_site_map_open_btn.click(function(){ top_site_map.addClass('now_open');	}); //클래스추가
					$('#header div.top_site_map_btn').click(function(){ top_site_map.removeClass('now_open');	}); //클래스삭제
					if(top_site_map.hasClass('now_open')){
						top_site_map.css('display','block');
					}else{
						top_site_map.css('display','none');
					}
					$('#header div.top_site_map, #header div.top_site_map_line, #header div.top_site_map_line_real').css({'position':'fixed','top':'0'});
					$('#header div.top_site_map_line').css('zIndex','2');
					top_site_map_open_btn.css({'display':'block','borderRadius':'0','top':'0','borderBottomLeftRadius':'5px','borderBottomRightRadius':'5px'});
					
				}else{
					top_site_map.css('display','');
					$('#header div.top_site_map, #header div.top_site_map_line, #header div.top_site_map_line_real').css({'position':'absolute','top':''});
					$('#header div.top_site_map_line').css('zIndex','5');
					top_site_map_open_btn.css({'display':'','borderRadius':'','top':''});
					top_site_map.removeClass('now_open'); //클래스삭제
				}
			});
		}
		
	});
	
	
	//심플메뉴
	$('div.simple_menu ul').mouseover(function(){
		$(this).animate({width:'140px'},0,function(){
			$(this).find('a').stop().animate({left:'0'},500);
		});
	}).mouseleave(function(){
		$(this).find('a').stop().animate({left:'-97px'},400,function(){
			$('div.simple_menu ul').css('width','47px');
		});
	}); 
	
	//다국어 가려짐 해결
	$('#header a.language').toggle(function(){
		$('#header div.gnb').css('zIndex','6');
	}, function(){
		$('#header div.gnb').css('zIndex','5');
	});
	
	//서브바, 위젯바 선택효과
	$(function(){	
		if(subbar_select == 'yes'){ //사용여부 조건문
			//서브메뉴
			$('#body div.lnb').mouseenter(function(){
				$(this).css('boxShadow','0px 0px 13px #ccc');
			}).mouseleave(function(){
				$(this).css('boxShadow','none');
			});
			
			//서브위젯, 위젯바
			$('#body div.left_widget_wrap, #body div.widget_bar_wrap').find('div.widget1, div.widget2, div.widget3, div.widget4').mouseenter(function(){
				$(this).css('boxShadow','0px 0px 13px #ccc');
			}).mouseleave(function(){
				$(this).css('boxShadow','none');
			});
		}
		
		//서브메뉴 아코디언 타입 (화살표 클릭)
		if(sub_menu_type == 'accordion'){
			$(function(){
				var t = $('#body div.lnb').find('ul');
				t.find('li').find('span.view2').click(function(){
					$(this).parent('li').find('ul').slideToggle();
				});
				t.find('li').find('li').each(function(){
					if( $(this).hasClass('active_2') ){
						$(this).parent('ul').css('display','block');
					}
				});
			});	
		}
		
		//서브메뉴 아코디언 타입 (메뉴 클릭)
		if(sub_menu_type == 'accordion2'){
			$(function(){
				var t = $('#body div.lnb').find('ul');
				t.find('li').click(function(){
					$(this).find('ul').slideToggle();
				});
				t.find('li').find('li').each(function(){
					if( $(this).hasClass('active_2') ){
						$(this).parent('ul').css('display','block');
					}
				});
			});	
		}
		
		//서브메뉴 마우스 오버 타입
		if(sub_menu_type == 'mouseover'){
			$(function(){
				var t = $('#body div.lnb').find('ul');
				t.find('li').mouseover(function(){
					$(this).find('ul:not(:animated)').slideDown(300);
					$(this).siblings('li').find('ul').slideUp(200);
				});
				t.find('li').find('li').each(function(){
					if( $(this).hasClass('active_2') ){
						$(this).parent('ul').css('display','block');
					}
				});
			});	
		}
	});
	
	
	//패밀리 사이트
	$('div.family_site_btn').toggle(function(){
		$('div.family_wrap').slideDown();
		$(this).find('img').attr('src','./layouts/xecenter/img/family_down.png');
	}, function(){
		$('div.family_wrap').slideUp();
		$(this).find('img').attr('src','./layouts/xecenter/img/family_up.png');
	});
	$('div.family_wrap').mouseover(function(){ //미리보기 기능
		$(this).find('a').mouseover(function(){
			$(this).find('img').css('right', $('div.family_wrap').width()+42);
			$(this).find('img').stop().fadeTo('500',1);
		}).mouseleave(function(){
			$(this).find('img').stop().fadeTo('500',0);
		});
	}).mouseleave(function(){
		$(this).find('a').find('img').css('display','none');
	});
	
	
	
	//알림센터모듈과 호환성 문제 개선
	$(function(){
		if($('#nc_container').width() > '340' ){
			var menu = parseInt( $('#header div.menu').css('top'), 10);
			var banner = parseInt( $('#header div.banner').css('top'), 10);
			$('#header div.menu, #header div.menu_bg').css('top',menu+30);
			$('#header div.banner').css('top',banner+30);
		}
		$('#nc_container a.readall').click(function(){
			$('#header div.menu, #header div.menu_bg').css('top',menu);
			$('#header div.banner').css('top',banner);
		});
	});
	
	
	//하단메뉴 자동위치조정
	$(function(){
		var auto_float = 0;
		$('ul.auto_float li').each(function(){
			auto_float = auto_float + $(this).width(); //메뉴 갯수에 따른 자동넓이
		});
		$('ul.auto_float').css('width',auto_float+10);
	});
	
		
	//내정보창 스크롤바 기능 (내글, 내댓글, 내쪽지)
	/*$(document).ready(function(){ //스크롤바1 플러그인
		$('#scrollbar1').perfectScrollbar(); //내글   (스크롤바1 플러그인)
		$('#scrollbar2').perfectScrollbar(); //내댓글 (스크롤바1 플러그인)
		$('#scrollbar3').perfectScrollbar(); //내쪽지 (스크롤바1 플러그인)
	});*/	
	/*$(document).ready(function(){ //스크롤바2 플러그인
		$("#scrollbar1").mCustomScrollbar({	mouseWheelPixels: "40",	autoHideScrollbar: Boolean	}); //내글   (스크롤바2 플러그인)
		$("#scrollbar2").mCustomScrollbar({	mouseWheelPixels: "40",	autoHideScrollbar: Boolean	}); //내댓글 (스크롤바2 플러그인)
		$("#scrollbar3").mCustomScrollbar({	mouseWheelPixels: "40",	autoHideScrollbar: Boolean	});  //내쪽지 (스크롤바2 플러그인)
	});*/	
	
	//검색바 & 내정보창 
	$('#header a.search').click(function(){ //검색바
		if( $('#header div.bg_info').height() == '0'){
				$('#header div.top_site_map_open_btn').css('display','none'); //전체메뉴 버튼
				$('#header div.bg_info').stop().animate({height:'104px'},500);
				$('#header div.search_wrap').fadeIn('slow');
				$('#header div.my_info_wrap').fadeOut('slow');
				$('#header input.text').focus(); //검색창 포커스
		}else if( $('#header div.bg_info').height() == '104' && $('#header div.my_info_wrap').css('display') == 'block'){
				$('#header .top_site_map_open_btn').css('display','none'); //전체메뉴 버튼
				$('#header div.my_info_wrap').css('display','none');
				$('#header div.search_wrap').fadeIn('slow');
				$('#header input.text').focus(); //검색창 포커스
		}else{
				$('#header div.bg_info').stop().animate({height:'0px'},500);
				$('#header div.search_wrap').fadeOut('slow');
				if( $('#header div.top_site_map').css('display') == 'none'){ $('#header div.top_site_map_open_btn').css('display','block'); } //전체메뉴 버튼
		}
	});
	$('#header a.my_info_btn').click(function(){ //내정보창
		if( $('#header div.bg_info').height() == '0'){
				$('#header div.top_site_map_open_btn').css('display','none'); //전체메뉴 버튼
				$('#header div.bg_info').stop().animate({height:'104px'},500);
				$('#header div.search_wrap').fadeOut('slow');
				$('#header div.my_info_wrap').fadeIn('slow');
				if( $('#scrollbar1, #scrollbar2, #scrollbar3').hasClass('mCustomScrollbar') == false ){ //스크롤바 플러그인 조건문
					$("#scrollbar1").mCustomScrollbar({	mouseWheelPixels: "40",	autoHideScrollbar: Boolean	}); //내글   (스크롤바2 플러그인)
					$("#scrollbar2").mCustomScrollbar({	mouseWheelPixels: "40",	autoHideScrollbar: Boolean	}); //내댓글 (스크롤바2 플러그인)
					$("#scrollbar3").mCustomScrollbar({	mouseWheelPixels: "40",	autoHideScrollbar: Boolean	});  //내쪽지 (스크롤바2 플러그인)
				}	
		}else if( $('#header div.bg_info').height() == '104' && $('#header div.search_wrap').css('display') == 'block'){
				$('#header div.top_site_map_open_btn').css('display','none'); //전체메뉴 버튼
				$('#header div.search_wrap').slideUp();
				$('#header div.my_info_wrap').fadeIn('slow');
		}else{
				if( $('#header div.top_site_map').css('display') == 'none'){ $('#header div.top_site_map_open_btn').css('display','block'); } //전체메뉴 버튼
				$('#header div.bg_info').stop().animate({height:'0px'},500);
				$('#header div.my_info_wrap').fadeOut('slow');
		}
	}); //효과
	$('#header div.info1').find('img').mouseenter(function(){ //프로필 사진
		$(this).stop().fadeTo('',0.2);
		$(this).siblings('div.label').css('display','block');
	}).mouseleave(function(){
		$(this).siblings('div.label').css('display','none');	
		$(this).stop().fadeTo('slow',1);
	});
	$('#header div.info_wrap').find('a').mouseover(function(){  //내글,내쪽지,내댓글 바로가기 효과
		$(this).find('img').stop().animate({marginLeft:'15'},500);
	}).mouseleave(function(){
		$(this).find('img').stop().animate({marginLeft:'2'},500);
	});
	
	// 심플바
	$('#header a.simple_bar_btn').click(function(){ //심플바
		if( $('#header div.simple_bar_bg').height() == '0'){
				$('#header div.simple_bar_bg, #header div.simple_wrap').stop().animate({height:'30px'},500);
				$('#header div.top_site_map_open_btn').css('display','none'); //전체메뉴 버튼
		}else{
				$('#header div.simple_bar_bg, #header div.simple_wrap').stop().animate({height:'0px'},500);
				if( $('#header div.top_site_map').css('display') == 'none'){ $('#header div.top_site_map_open_btn').css('display','block'); } //전체메뉴 버튼
		}
	});
	
	// 채팅바
	$('#header a.chat_bar_btn, div.chat_close').click(function(){ //심플바
		var frq = 100*chat_close_effect //슬라이드 이펙트 강도
		if( $('#header div.chat_wrap').height() == '0'){
				$('#header div.chat_wrap').stop().animate({height:'100%'},frq);
				$('#header div.top_site_map_open_btn').css('display','none'); //전체메뉴 버튼
		}else{
				$('#header div.chat_wrap').stop().animate({height:'0px'},frq);
				if( $('#header div.top_site_map').css('display') == 'none'){ $('#header div.top_site_map_open_btn').css('display','block'); } //전체메뉴 버튼
		}
	});
	
		
	// 로고 효과
	$('#header div.logo').mouseenter(function(){
		$('#header a.text_strong').stop().animate({color:logo_color,fontSize:logo_size},500); //css값 layout변수로 처리
	}).mouseleave(function(){
		$('#header a.text_strong').stop().animate({color:logo_color_o,fontSize:logo_size_o},250); //css값 layout변수로 처리
	});
	
	
	//레이아웃 높이 변경
	$(function(){
		if(screen_height_user_change == 'n'){ //높이여백 임의로 변경시 동작안함
			
			if(parseInt($.cookie('screen_height_ck')) == 10){  // 크기 변환후 반환된 쿠키값 적용 (marginTop)
				$('#body').css('marginTop', parseInt($.cookie('screen_height_ck')) - parseInt($('#header').height()) + parseInt($('.menu').height()) );
				}else{	
				$('#body').css('marginTop', $.cookie('screen_height_ck') );
			}
			if(parseInt($.cookie('screen_height_ck')) == 10){ // 크기 변환후 반환된 쿠키값 적용 (marginBottom)
				$('#body').css('marginBottom', parseInt($.cookie('screen_height_ck')) - parseInt($('.bottom_widget').height()) - parseInt($('.bottom_widget').css('marginBottom')) ); 
				}else{
				$('#body').css('marginBottom',$.cookie('screen_height_ck') );
			}
			
			
			if($('#body').css('marginTop') == '100px'){ /* 쿠키값에 따른 조건문 (큰값) */
				//기본 문구,이미지 출력
				$('div.height_change').html('<img src="./layouts/xecenter/img/screen1.png"></img> 높이축소');
				//기본 hover 이미지	
				$('div.height_change').hover(function(){
					$(this).find('img').attr('src','./layouts/xecenter/img/screen2.png');
						},function(){
					$(this).find('img').attr('src','./layouts/xecenter/img/screen1.png');		
				});		
				//토글
				$('div.height_change').toggle(function(){
					$.cookie('screen_height_ck','10px',{expries:1}); /*변경값 쿠키 굽기 */
					$('#body').animate({marginTop:10 - parseInt($('#header').height()) + parseInt($('.menu').height()) +'px',marginBottom:10 - parseInt($('.bottom_widget').height()) - parseInt($('.bottom_widget').css('marginBottom')) + 'px'},600);
					$('div.height_change').html('<img src="./layouts/xecenter/img/screen1.png"></img> 높이확장');
				},function(){
					$.cookie('screen_height_ck','100px',{expries:1}); /*변경값 쿠키 굽기*/
					$('#body').animate({marginTop:'100px',marginBottom:'100px'},600);
					$('div.height_change').html('<img src="./layouts/xecenter/img/screen1.png"></img> 높이축소');
				});
			}
			if($('#body').css('marginTop') == 10 - parseInt($('#header').height()) + parseInt($('.menu').height()) +'px'){ /* 쿠키값에 따른 조건문 (작은값) */
				//기본 문구,이미지 출력
				$('div.height_change').html('<img src="./layouts/xecenter/img/screen1.png"></img> 높이확장');
				//기본 hover 이미지	
				$('div.height_change').hover(function(){
					$(this).find('img').attr('src','./layouts/xecenter/img/screen2.png');
						},function(){
					$(this).find('img').attr('src','./layouts/xecenter/img/screen1.png');		
				});		
				//토글
				$('div.height_change').toggle(function(){
					$.cookie('screen_height_ck','100px',{expries:1}); /*변경값 쿠키 굽기*/
					$('#body').animate({marginTop:'100px',marginBottom:'100px'},600);
					$('div.height_change').html('<img src="./layouts/xecenter/img/screen1.png"></img> 높이축소');
				},function(){
					$.cookie('screen_height_ck','10px',{expries:1}); /*변경값 쿠키 굽기*/
					$('#body').animate({marginTop:10 - parseInt($('#header').height()) + parseInt($('.menu').height()) +'px',marginBottom:10 - parseInt($('.bottom_widget_wrap').height()) - parseInt($('.bottom_widget_wrap').css('marginBottom')) +'px'},600);
					$('div.height_change').html('<img src="./layouts/xecenter/img/screen1.png"></img> 높이확장');
				});
			}
		}
	});
	
	
	$(function(){ //고정기능 묶음
		if(side_banner_fix != 'no' || left_side_banner_fix != 'no' || sub_menu_fix == 'yes'){ //미사용시 로드않함
			
			//공통변수
			var y =  parseInt( $('#body').css('marginTop'), 10);	//컨텐츠 상단여백값
			if( $('#body').find('div.content_top').height() ){								//컨텐츠 상단바 높이값 
				var g =  parseInt( $('#body').find('div.content_top').height() ,10);
				}else{
				var g = 0;
			}			
			var z =  parseInt( $('#header').height(), 10 ); 								//헤더 높이(상단 배너로 인한 변수)
			var f =  parseInt( $('#body').find('div.content_wrap').height(), 10); 			//본문전체높이 (하단뚫기방지 재료1)
			var l =  parseInt( $('#body').find('div.side_banner').height(), 10);			//우측사이드배너높이 (하단뚫기방지 재료2)
			var l_left =  parseInt( $('#body').find('div.left_side_banner').height(), 10);	//좌측사이드배너높이 (하단뚫기방지 재료2)
			var m =  parseInt( $('#body').css('marginBottom'), 10);	//컨텐츠 하단여백값 (하단뚫기방지 재료3)
			var gnb = 0; //메뉴높이 초기화
			if(menu_top_fixed == 'yes'){ var gnb = parseInt( $('#header div.gnb').height() ,10); } //메뉴 최상단 고정 사용시
			
			//정지형태 변수들
			var c_width = $('#body').width(); //본문넓이
			if($.cookie('screen_width_ck')){ var c_width = $.cookie('screen_width_ck'); } //넓이확장시 본문넓이 쿠키값
			var c_width_half = c_width / 2; //본문넓이 절반
			var r_width = $('#body div.side_banner').width(); //우측배너넓이
			var r_margin = c_width_half + r_width + 10; //우측 배너 결과값
			var l_width = $('#body div.left_side_banner').width(); //좌측배너넓이
			var l_margin = c_width_half + l_width + 10; //좌측 배너 결과값
			
			//결과값
			var a = (y + z); //고정기능 작동 조건문
			var b = (y - z - g) + m; //하단뚫기 방지조건문1
			var c = (f - l); //하단뚫기 방지조건문2
			var c_left = (f - l_left); //하단뚫기 방지조건문2
			var d = (y + z) - (gnb); //사이드위치
			
			//사이드 배너 이동기능 (우측)
			if(side_banner_fix != 'no'){ //사용여부 조건문
				var sb = $('#body div.side_banner'); //배너선택자 변수
				$(window).scroll(function(){
					var x = $(this).scrollTop(); //스크롤위치
					
					if( x > a ){ //고정기능 작동 조건문
						if( x - b < c && side_banner_fix == 'yes'){ //이동형 + 하단뚫기 방지조건문
							sb.stop().animate({top:x - d},700);
						}
						if(side_banner_fix == 'nomove'){ //고정형
								//////////////////////속도 저하시 삭제//////////////////////
								var c_width = $('#body').width(); //본문넓이
								var c_width_half = c_width / 2; //본문넓이 절반
								var r_margin = c_width_half + r_width + 10; //우측 배너 결과값
								///////////////////////속도 저하시 삭제//////////////////////
							sb.css({'position':'fixed','right':'50%','marginRight': -r_margin});
						}	
					}else{  //고정기능 원위치 조건문
						sb.stop().animate({top: 0}, 700);
						if(side_banner_fix == 'nomove'){ //고정형
							sb.css({'position':'absolute','right':'','marginRight':''});
						}
					}	
				});
			}
			//사이드 배너 이동기능 (좌측)
			if(left_side_banner_fix != 'no'){ //사용여부 조건문
				var lsb = $('#body div.left_side_banner'); //배너선택자 변수
				$(window).scroll(function(){
					var x = $(this).scrollTop(); //스크롤위치
					
					if( x > a ){ //고정기능 작동 조건문
						if( x - b < c_left && left_side_banner_fix == 'yes'){ //이동형 + 하단뚫기 방지조건문
							lsb.stop().animate({top:x - d},700);
						}
						if(left_side_banner_fix == 'nomove'){ //고정형
								//////////////////////속도 저하시 삭제//////////////////////
								var c_width = $('#body').width(); //본문넓이
								var c_width_half = c_width / 2; //본문넓이 절반
								var l_margin = c_width_half + l_width + 10; //좌측 배너 결과값
								///////////////////////속도 저하시 삭제//////////////////////
							lsb.css({'position':'fixed','left':'50%','marginLeft': -l_margin});
						}					
					}else{  //고정기능 원위치 조건문
						lsb.stop().animate({top: 0}, 700);
						if(left_side_banner_fix == 'nomove'){ //고정형
							lsb.css({'position':'absolute','left':'','marginLeft':''});
						}
					}	
				});
			}
		
			// 서브메뉴 이동기능 (+ 서브메뉴 바로이동기능 조건문 추가)
			if(sub_menu_fix == 'yes'){ //사용여부 조건문
				
				//고정변수
				var subl = parseInt( $('#body').find('div.lnb').height() ,10); //서브메뉴높이 (하단뚫기방지 재료2)
				var sub_widget = 0; //서브메뉴 상단 위젯높이 초기화
				var sub_widget = parseInt( $('#body div.lnb_widget').height() ,10); //서브메뉴 상단 위젯높이 있을시
				
				//결과값
				var a1 = (y + z + sub_widget); //고정기능 작동 조건문
				var b1 = (y + z) - g; //하단뚫기 방지 조건문1
				var c1 = (f - subl); //하단뚫기 방지 조건문2
				var d1 = (y + z + g + sub_widget) - (gnb + 10); //서브위치
				
				$(window).scroll(function(){
					var x = $(this).scrollTop(); //스크롤위치
					
					if( x > a1 ){ //고정기능 작동 조건문
						if( x - b1 < c1 ){ //하단뚫기 방지조건문
							
							if(sub_move_target_type == 'scroll'){ //서브메뉴 바로이동 기능 조건문
								$('#body div.sub_move_target').slideDown(1500);
							}
							if(sub_menu_fix_effect == 'easeOutBack'){ //바운스 효과
								setTimeout(function(){	
									$('#body div.lnb').stop().animate({top: x - d1}, 1100, 'easeOutBack'); 
								}, 0);
							}
							if(sub_menu_fix_effect == 'linear'){ //기본 효과
								setTimeout(function(){	
									$('#body div.lnb').stop().animate({top: x - d1}, 800); 
								}, 0);
							}
						}
					}else{  //고정기능 원위치 조건문
						$('#body div.lnb').stop().animate({top: 0}, 700);
						
						if(sub_move_target_type == 'scroll'){ //서브메뉴 바로이동 기능 조건문
							$('#body div.sub_move_target').slideUp(1000);
						}
					}	
				});
			}
		}
	});
	
		
	//레이아웃 넓이 변경
	$(function(){
		
		var fix_width = $('div.fix_width'); //공통변수1
		var width_change = $('#body div.width_change'); //공통변수2
		var move_target = $('#body div.move_target'); //공통변수3

		fix_width.css('width',$.cookie('screen_width_ck')); // 크기 변환후 반환된 쿠키값 적용 (넓이)
		
		if(fix_width.width() == screen_width1){ /*쿠키값에 따른 조건문 (기본값)*/
			//바로이동 기능(기본값)
			move_target.css('marginLeft',move_target_width);
			//기본 문구,이미지 출력
			width_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이확장');
			//기본 hover 이미지
			width_change.hover(function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen2.png');
					},function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen1.png');		
			});
			//토글
			width_change.toggle(function(){
				fix_width.animate({width:screen_width2/*info변수처리*/},600);
				move_target.animate({marginLeft:screen_width2},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이확장');
				$.cookie('screen_width_ck',screen_width2,{expries:1}); /*변경값 쿠키 굽기*/
			},function(){
				fix_width.animate({width:screen_width3/*info변수처리*/},600);	
				move_target.animate({marginLeft:screen_width3},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이축소');
				$.cookie('screen_width_ck',screen_width3,{expries:1}); /*변경값 쿠키 굽기*/
			},function(){
				fix_width.animate({width:screen_width1/*info변수처리*/},600);
				move_target.animate({marginLeft:screen_width1},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이확장');
				$.cookie('screen_width_ck',screen_width1),{expries:1}; /*변경값 쿠키 굽기*/
			});
		}	
		if(fix_width.width() == screen_width2){ /*쿠키값에 따른 조건문 (확장값)*/
			//바로이동 기능(기본값)
			move_target.css('marginLeft',move_target_width2);
			//기본 문구,이미지 출력
			width_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이확장');
			//기본 hover 이미지
			width_change.hover(function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen2.png');
					},function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen1.png');		
			});
			//토글
			width_change.toggle(function(){
				fix_width.animate({width:screen_width3/*info변수처리*/},600);
				move_target.animate({marginLeft:screen_width3},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이축소');
				$.cookie('screen_width_ck',screen_width3,{expries:1}); /*변경값 쿠키 굽기*/
			},function(){
				fix_width.animate({width:screen_width1/*info변수처리*/},600);	
				move_target.animate({marginLeft:screen_width1},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이확장');
				$.cookie('screen_width_ck',screen_width1,{expries:1}); /*변경값 쿠키 굽기*/
			},function(){
				fix_width.animate({width:screen_width2/*info변수처리*/},600);
				move_target.animate({marginLeft:screen_width2},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이확장');
				$.cookie('screen_width_ck',screen_width2),{expries:1}; /*변경값 쿠키 굽기*/
			});
		}	
		if(fix_width.width() == screen_width3){ /*쿠키값에 따른 조건문 (최대확장값)*/
			//바로이동 기능(기본값)
			move_target.css('marginLeft',move_target_width3);
			//기본 문구,이미지 출력
			width_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이축소');
			//기본 hover 이미지
			width_change.hover(function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen2.png');
					},function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen1.png');		
			});
			//토글
			width_change.toggle(function(){
				fix_width.animate({width:screen_width1/*info변수처리*/},600);
				move_target.animate({marginLeft:screen_width1},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이확장');
				$.cookie('screen_width_ck',screen_width1,{expries:1}); /*변경값 쿠키 굽기*/
			},function(){
				fix_width.animate({width:screen_width2/*info변수처리*/},600);	
				move_target.animate({marginLeft:screen_width2},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이확장');
				$.cookie('screen_width_ck',screen_width2,{expries:1}); /*변경값 쿠키 굽기*/
			},function(){
				fix_width.animate({width:screen_width3/*info변수처리*/},600);
				move_target.animate({marginLeft:screen_width3},600); /* 바로이동 기능 */
				$(this).html('<img src="./layouts/xecenter/img/screen1.png"></img> 넓이축소');
				$.cookie('screen_width_ck',screen_width3),{expries:1}; /*변경값 쿠키 굽기*/
			});
		}	
	});
	
	//레이아웃 본문 변경 (서브바 토글기능)
	$(function(){
	
		var content_left_bar = $('#body div.content_left_bar'); //공통변수1
		var content_change = $('#body div.content_change'); //공통변수2
		
		if($.cookie('screen_content_ck') == 'block'){  // 쿠키값 적용 (서브바 block일경우)
			content_left_bar.css('display','block');
		}
		if($.cookie('screen_content_ck') == 'none'){   // 쿠키값 적용 (서브바 none일경우)
			content_left_bar.css('display','none');
		}
			
		if(content_left_bar.css('display') == 'block'){ /* 쿠키값에 따른 조건문 (display:block일경우) */
			//기본 문구,이미지 출력
			content_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 본문확장');
			//기본 hover 이미지	
			content_change.hover(function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen2.png');
					},function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen1.png');		
			});		
			//토글
			content_change.toggle(function(){
				$.cookie('screen_content_ck','none',{expries:1}); /*변경값 쿠키 굽기 */
				content_left_bar.css('display','none');
				content_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 본문축소');
			},function(){
				$.cookie('screen_content_ck','block',{expries:1}); /*변경값 쿠키 굽기*/
				content_left_bar.css('display','block');
				content_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 본문확장');
			});
		}
		if(content_left_bar.css('display') == 'none'){ /* 쿠키값에 따른 조건문 (display:none일경우) */
			//기본 문구,이미지 출력
			content_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 본문축소');
			//기본 hover 이미지	
			content_change.hover(function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen2.png');
					},function(){
				$(this).find('img').attr('src','./layouts/xecenter/img/screen1.png');		
			});		
			//토글
			content_change.toggle(function(){
				$.cookie('screen_content_ck','block',{expries:1}); /*변경값 쿠키 굽기*/
				content_left_bar.css('display','block');
				content_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 본문확장');
			},function(){
				$.cookie('screen_content_ck','none',{expries:1}); /*변경값 쿠키 굽기*/
				content_left_bar.css('display','none');
				content_change.html('<img src="./layouts/xecenter/img/screen1.png"></img> 본문축소');
			});
		}
	});
	
	//하단 전체메뉴 (사이트맵)
	$(function(){
		if( site_map_use == 'yes'){ //사용여부 조건문
			var site_map_bg = $('div.site_map_bg'); //공통변수
			$('div.site_map_btn').toggle(function(){
				site_map_bg.slideDown(800);
				$(this).find('img').attr('src','./layouts/xecenter/img/site_map_1.png');
			},function(){
				site_map_bg.slideUp(500);
				$(this).find('img').attr('src','./layouts/xecenter/img/site_map.png');
			});
			
			var sm_width = $('div.fix_width').width();
			var sm_li = $('ul.sitemap_auto_float').children('li').size()-1;
			$('ul.sitemap_auto_float').children('li').css('width', sm_width / sm_li );
			$('div.site_map_pipe, li.site_map_pipe_final').css('height', site_map_bg.height()-27);
		}
	});
	
	
	
});
