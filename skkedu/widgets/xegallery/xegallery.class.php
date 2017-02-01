<?php
class xegallery extends WidgetHandler {
	function proc($args) {

// 추출대상
		if(!in_array($args->colect_type, array('module','document'))) $args->colect_type = 'module';
		$xGalry_info->colect_type = $args->colect_type;
// 문서 번호
		if(!$args->document_srl) $args->document_srl = '';
		$args->document_srl = urldecode($args->document_srl);
		if($args->colect_type=="document" && $args->document_srl=='') return Context::getLang('컨텐츠 추출대상이 문서인 경우, 문서번호는 반드시 입력해야 합니다.');
		$xGalry_info->document_srl = $args->document_srl;
// 본문 미디어 링크
		if(!$args->in_document) $args->in_document = 'N';
		$xGalry_info->in_document = $args->in_document;
// 카테고리 번호
		if(!$args->category_srl) $args->category_srl = '';
		$xGalry_info->category_srl = $args->category_srl;
// 기간(일) - duration
		if(!$args->duration) $args->duration = 0;
		$xGalry_info->duration = (int)$args->duration;
// 위젯 타이틀
		if(!$args->widget_title) $args->widget_title = '';
		$xGalry_info->widget_title = $args->widget_title;
// 출력 이미지 선택
		if(!$args->attached_list) $args->attached_list = 0;
		$xGalry_info->attached_list = (int)$args->attached_list;
// 전체 목록수
		if(!$args->list_count) $args->list_count = 6;
		$xGalry_info->list_count = (int)$args->list_count;
// 큰이미지 가로 넓이
		if(!$args->image_width) $image_width = 400;
		else $image_width = $args->image_width;
		$xGalry_info->image_width = (int)$image_width;
// 큰이미지 세로 높이
		if(!$args->image_height) $image_height = 280;
		else $image_height = $args->image_height;
		$xGalry_info->image_height = (int)$image_height;
// 썸네일 가로 넓이
		if(!$args->thumb_width) $thumb_width = 132;
		else $thumb_width = $args->thumb_width;
		$xGalry_info->thumb_width = (int)$thumb_width;
// 썸네일 세로 높이
		if(!$args->thumb_height) $thumb_height = 92;
		else $thumb_height = $args->thumb_height;
		$xGalry_info->thumb_height = (int)$thumb_height;
// 썸네일 가로 이미지 수
		if(!$args->cols_list_count) $cols_list_count = 2;
		else $cols_list_count = $args->cols_list_count;
		$xGalry_info->cols_list_count = (int)$cols_list_count;
// 표시항목
		$xGalry_info->show_image = null;
		$xGalry_info->show_thumb = null;
		$xGalry_info->show_title = null;
		$xGalry_info->show_content = null;
		$xGalry_info->show_browser_title = null;
		$xGalry_info->show_category = null;
		$xGalry_info->show_comment_count = null;
		$xGalry_info->show_trackback_count = null;
		$xGalry_info->show_nickname = null;
		$xGalry_info->show_regdate = null;
		$xGalry_info->show_icon = null;

		$xGalry_info->show_list = $args->show_list;
		$args->show_list_arr = explode(',',$args->show_list);
		$xGalry_info->show_list_arr = $args->show_list_arr;
		for($i=0,$c=count($args->show_list_arr);$i<$c;$i++)
		{
			switch($args->show_list_arr[$i])
			{
				case 'show_image':
					$xGalry_info->show_image = "Y";
					break;
				case 'show_thumb':
					$xGalry_info->show_thumb = "Y";
					break;
				case 'show_title':
					$xGalry_info->show_title = "Y";
					break;
				case 'show_content':
					$xGalry_info->show_content = "Y";
					break;
				case 'show_browser_title':
					$xGalry_info->show_browser_title = "Y";
					break;
				case 'show_category':
					$xGalry_info->show_category = "Y";
					break;
				case 'show_comment_count':
					$xGalry_info->show_comment_count = "Y";
					break;
				case 'show_trackback_count':
					$xGalry_info->show_trackback_count = "Y";
					break;
				case 'show_nickname':
					$xGalry_info->show_nickname = "Y";
					break;
				case 'show_regdate':
					$xGalry_info->show_regdate = "Y";
					break;
				case 'show_icon':
					$xGalry_info->show_icon = "Y";
					break;
			}
		}

// new 표시 시간
		if(!$args->duration_new) $args->duration_new = 12;
		$xGalry_info->duration_new = (int)$args->duration_new;
// 정렬 대상 - order_target
		if(!in_array($args->order_target, array('list_order','update_order','voted_count','readed_count','comment_count','trackback_count','uploaded_count','reward_point','download_count'))) $args->order_target = 'list_order';
		$xGalry_info->order_target = $args->order_target;
// 정렬 방법
		if(!$args->order_type) $args->order_type = 'desc';
		$xGalry_info->order_type = $args->order_type;
// 게시물 순서 섞기
		if(!$args->items_shuffle) $args->items_shuffle = 'N';
		$xGalry_info->items_shuffle = $args->items_shuffle;
// 큰이미지 생성 방법
		if(!$args->image_type) $args->image_type = 'ratio';
		$xGalry_info->image_type = $args->image_type;
// 큰이미지 배경색
		if(!$args->image_bgcolor) $image_bgcolor = '#EEF0F5';
		else $image_bgcolor = $args->image_bgcolor;
		$xGalry_info->image_bgcolor = $image_bgcolor;
// 큰이미지 테두리 두께
		if(!$args->image_bdsize) $image_bdsize = 0;
		else $image_bdsize = $args->image_bdsize;
		$xGalry_info->image_bdsize = (int)$image_bdsize;
// 큰이미지 테두리 색
		if(!$args->image_bdcolor) $image_bdcolor = '#CED1DA';
		else $image_bdcolor = $args->image_bdcolor;
		$xGalry_info->image_bdcolor = $image_bdcolor;
// 큰이미지 외부 여백
		if(!$args->image_outer) $image_outer = 0;
		else $image_outer = $args->image_outer;
		$xGalry_info->image_outer = (int)$image_outer;
// 큰이미지 그림자 효과
		if(!$args->image_shadow) $args->image_shadow = 'Y';
		$xGalry_info->image_shadow = $args->image_shadow;
// 큰이미지 라운딩 효과
		if(!$args->image_round) $args->image_round = 'Y';
		$xGalry_info->image_round = $args->image_round;
// 큰이미지 라운딩 수치 입력
		if(!$args->image_round_px) $image_round_px = 3;
		else $image_round_px = $args->image_round_px;
		$xGalry_info->image_round_px = (int)$image_round_px;
// 큰이미지 클릭 이벤트
		if(!$args->image_event) $args->image_event = 'N';
		$xGalry_info->image_event = $args->image_event;
// 큰이미지 링크
		if(!$args->image_newtab) $args->image_newtab = 'N';
		$xGalry_info->image_newtab = $args->image_newtab;
// 워터마크 주소
		if(!$args->watermark_url || !file_exists($args->watermark_url) ) $args->watermark_url = '';
		$xGalry_info->watermark_url = $args->watermark_url;
// 워터마크 가로위치
		if(!in_array($args->watermark_halign, array('R','C','L'))) $args->watermark_halign = 'R';
		if($args->watermark_url=='') $args->watermark_halign = '';
		$xGalry_info->watermark_halign = $args->watermark_halign;
// 워터마크 세로위치
		if(!in_array($args->watermark_valign, array('B','M','T'))) $args->watermark_valign = 'B';
		if($args->watermark_url=='') $args->watermark_valign = '';
		$xGalry_info->watermark_valign = $args->watermark_valign;

// 썸네일 생성 방법
		if(!$args->thumbnail_type) $args->thumbnail_type = 'crop';
		$xGalry_info->thumbnail_type = $args->thumbnail_type;
// 썸네일 배경색
		if(!$args->thumb_bgcolor) $thumb_bgcolor = '#EEF0F5';
		else $thumb_bgcolor = $args->thumb_bgcolor;
		$xGalry_info->thumb_bgcolor = $thumb_bgcolor;
// 썸네일 테두리 두께
		if(!$args->thumb_bdsize) $thumb_bdsize = 0;
		else $thumb_bdsize = $args->thumb_bdsize;
		$xGalry_info->thumb_bdsize = (int)$thumb_bdsize;
// 썸네일 테두리 색
		if(!$args->thumb_bdcolor) $thumb_bdcolor = '#CED1DA';
		else $thumb_bdcolor = $args->thumb_bdcolor;
		$xGalry_info->thumb_bdcolor = $thumb_bdcolor;
// 썸네일 외부 여백
		if(!$args->thumb_outer) $thumb_outer = 0;
		else $thumb_outer = $args->thumb_outer;
		$xGalry_info->thumb_outer = (int)$thumb_outer;
// 썸네일 그림자 효과
		if(!$args->thumb_shadow) $args->thumb_shadow = 'Y';
		$xGalry_info->thumb_shadow = $args->thumb_shadow;
// 썸네일 라운딩 효과
		if(!$args->thumb_round) $args->thumb_round = 'Y';
		$xGalry_info->thumb_round = $args->thumb_round;
// 썸네일 라운딩 수치 입력
		if(!$args->thumb_round_px) $thumb_round_px = 3;
		else $thumb_round_px = $args->thumb_round_px;
		$xGalry_info->thumb_round_px = (int)$thumb_round_px;
// 썸네일 클릭 이벤트
		if(!$args->thumb_event) $args->thumb_event = 'N';
		$xGalry_info->thumb_event = $args->thumb_event;
// 썸네일 링크
		if(!$args->thumb_newtab) $args->thumb_newtab = 'N';
		$xGalry_info->thumb_newtab = $args->thumb_newtab;

// 제목 글자수
		if(!$args->subject_cut_size) $subject_cut_size = 0;
		else $subject_cut_size = $args->subject_cut_size;
		$xGalry_info->subject_cut_size = (int)$subject_cut_size;
// 제목 글씨체
		if(!in_array($args->title_font_family, array('Default','Dotum','Gulim','Batang','Gungsuh','Tahoma','Verdana','Helvetica','Georgia','Sans-serif','webfont','cufon'))) $args->title_font_family = 'Default';
		$xGalry_info->title_font_family = $args->title_font_family;
// 제목 폰트 경로
		if(!$args->title_fonturl) $args->title_fonturl = '';
		$xGalry_info->title_fonturl = $args->title_fonturl;
// 제목 글꼴 직접 입력
		if(!$args->title_font_user) $args->title_font_user = '';
		$xGalry_info->title_font_user = $args->title_font_user;
// 제목 문자 크기
		if(!$args->title_font_size) $title_font_size = 0;
		else $title_font_size = $args->title_font_size;
		$xGalry_info->title_font_size = (int)$title_font_size;
// 제목 글자색
		if(!$args->title_font_color) $args->title_font_color = '';
		$xGalry_info->title_font_color = $args->title_font_color;
// 제목 배경색
		if(!$args->title_bg_color) $args->title_bg_color = '';
		$xGalry_info->title_bg_color = $args->title_bg_color;
// 제목 높이
		if(!$args->title_height) $title_height = 0;
		else $title_height = $args->title_height;
		$xGalry_info->title_height = (int)$title_height;

// 내용 글자수
		if(!$args->content_cut_size) $content_cut_size = 200;
		else $content_cut_size = $args->content_cut_size;
		$xGalry_info->content_cut_size = (int)$content_cut_size;
// 내용 글씨체
		if(!in_array($args->content_font_family, array('Default','Dotum','Gulim','Batang','Gungsuh','Tahoma','Verdana','Helvetica','Georgia','Sans-serif','webfont','cufon'))) $args->content_font_family = 'Default';
		$xGalry_info->content_font_family = $args->content_font_family;
// 내용 폰트 경로
		if(!$args->content_fonturl) $args->content_fonturl = '';
		$xGalry_info->content_fonturl = $args->content_fonturl;
// 내용 글꼴 직접 입력
		if(!$args->content_font_user) $args->content_font_user = '';
		$xGalry_info->content_font_user = $args->content_font_user;
// 내용 문자 크기
		if(!$args->content_font_size) $content_font_size = 0;
		else $content_font_size = $args->content_font_size;
		$xGalry_info->content_font_size = (int)$content_font_size;
// 내용 글자색
		if(!$args->content_font_color) $args->content_font_color = '';
		$xGalry_info->content_font_color = $args->content_font_color;
// 내용 배경색
		if(!$args->content_bg_color) $args->content_bg_color = '';
		$xGalry_info->content_bg_color = $args->content_bg_color;
// 내용 높이
		if(!$args->content_height) $content_height = 0;
		else $content_height = $args->content_height;
		$xGalry_info->content_height = (int)$content_height;

// plugin 활성화
		if($xGalry_info->image_event=='V'||$xGalry_info->thumb_event=='V') $xGalry_info->viewer = 'Y';
		else $xGalry_info->viewer = 'N';
// Viewer Type
		if(!$args->viewer_type) $args->viewer_type = 'prettyPhoto';
		$xGalry_info->viewer_type = $args->viewer_type;
// prettyPhoto 테마
		if(!in_array($args->viewer_theme, array('pp_default','light_rounded','dark_rounded','light_square','dark_square','facebook'))) $args->viewer_theme = 'pp_default';
		$xGalry_info->viewer_theme = $args->viewer_theme;
// Viewer Style
		if(!$args->viewer_style) $args->viewer_style = 'slider';
		$xGalry_info->viewer_style = $args->viewer_style;
// Viewer 소셜버튼
		if(!$args->viewer_social) $args->viewer_social = 'false';
		$xGalry_info->viewer_social = $args->viewer_social;
		if($args->viewer_social=='false') $args->viewer_tools = 'social_tools:false,';
		else $args->viewer_tools = '';
		$xGalry_info->viewer_tools = $args->viewer_tools;
// Viewer 컨텐츠
		if(!$args->viewer_text) $args->viewer_text = 'true';
		$xGalry_info->viewer_text = $args->viewer_text;

// 자동 슬라이드
		if(!$args->slide_auto) $args->slide_auto = 'true';
		$xGalry_info->slide_auto = $args->slide_auto;
// 슬라이드 속도조절 - 
		if(!$args->slide_delay || $args->slide_delay<1000) $args->slide_delay = 5000;
		$xGalry_info->slide_delay = (int)$args->slide_delay;
// 다음/이전 버튼
		if(!$args->slide_control) $args->slide_control = 'true';
		$xGalry_info->slide_control = $args->slide_control;
// 페이지 버튼
		if(!$args->slide_navigation) $args->slide_navigation = 'false';
		$xGalry_info->slide_navigation = $args->slide_navigation;
// 슬라이드 다운
		if(!$args->slide_down) $args->slide_down = 'false';
		$xGalry_info->slide_down = $args->slide_down;

// 이미지 확장변수
		if($args->target_ext1_var!='Y') $args->target_ext1_var = 'N';
		$xGalry_info->target_ext1_var = $args->target_ext1_var;
		if(!$args->ext1_var) $args->ext1_var = '';
		$xGalry_info->ext1_var = $args->ext1_var;
// 링크 확장변수
		if(!$args->target_ext2_var) $args->target_ext2_var = 'N';
		if($args->image_event=='X'||$args->thumb_event=='X') $args->target_ext2_var == 'Y';
		$xGalry_info->target_ext2_var = $args->target_ext2_var;
		if(!$args->ext2_var) $args->ext2_var = '';
		$xGalry_info->ext2_var = $args->ext2_var;

// 글쓴이 확장변수
		if(!$args->target_ext3_var) $args->target_ext3_var = 'N';
		$xGalry_info->target_ext3_var = $args->target_ext3_var;
		if(!$args->ext3_var) $args->ext3_var = '';
		$xGalry_info->ext3_var = $args->ext3_var;
// 제목 확장변수
		if(!$args->target_ext4_var) $args->target_ext4_var = 'N';
		$xGalry_info->target_ext4_var = $args->target_ext4_var;
		if(!$args->ext4_var) $args->ext4_var = '';
		$xGalry_info->ext4_var = $args->ext4_var;
// 부제목 확장변수
		if(!$args->target_ext5_var) $args->target_ext5_var = 'N';
		$xGalry_info->target_ext5_var = $args->target_ext5_var;
		if(!$args->ext5_var) $args->ext5_var = '';
		$xGalry_info->ext5_var = $args->ext5_var;
// 내용 확장변수
		if(!$args->target_ext6_var) $args->target_ext6_var = 'N';
		$xGalry_info->target_ext6_var = $args->target_ext6_var;
		if(!$args->ext6_var) $args->ext6_var = '';
		$xGalry_info->ext6_var = $args->ext6_var;
// 날짜 확장변수
		if(!$args->target_ext7_var) $args->target_ext7_var = 'N';
		$xGalry_info->target_ext7_var = $args->target_ext7_var;
		if(!$args->ext7_var) $args->ext7_var = '';
		$xGalry_info->ext7_var = $args->ext7_var;

		$mobile_agent = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/';
		if (preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])) $args->mobile = 'Y';
		else $args->mobile = 'N';
		$xGalry_info->mobile = $args->mobile;

		if(preg_match('/iP(hone|ad|od)/',$_SERVER['HTTP_USER_AGENT']))
		{
			$args->mobile_os = 'iphone';
		}
		elseif(preg_match('/android/',$_SERVER['HTTP_USER_AGENT']))
		{
			$args->mobile_os = 'android';
		}
		elseif(preg_match('/Windows/',$_SERVER['HTTP_USER_AGENT']))
		{
			$args->mobile_os = 'windows';
		}
		else
		{
			$args->mobile_os = 'android';
		}
		$xGalry_info->mobile_os = $args->mobile_os;


		if(preg_match('/(?i)msie [1-7]/',$_SERVER['HTTP_USER_AGENT']))
		{
			$args->browser = "msie7";
		}
		elseif(preg_match('/(?i)msie 8/',$_SERVER['HTTP_USER_AGENT']))
		{
			$args->browser = "msie8";
		}
		elseif(preg_match('/(?i)msie 9/',$_SERVER['HTTP_USER_AGENT']))
		{
			$args->browser = "msie9";
		}
		elseif(preg_match('/(?i)Chrome/',$_SERVER['HTTP_USER_AGENT']))
		{
			$args->browser = "chrome";
		}
		else
		{
			$args->browser = "other";
		}
		$xGalry_info->browser = $args->browser;

		if($args->skin=='square')
		{
			$xGalry_info->cols_list_count = 3;
			$xGalry_info->image_width = $xGalry_info->thumb_width * ($xGalry_info->cols_list_count-1);
			$xGalry_info->image_height = $xGalry_info->thumb_height * ($xGalry_info->cols_list_count-1);
		}

// xegallery 위젯 정보가져오기
//$oWidgetModel = &getModel('widget');
//$info_widget = $oWidgetModel->getWidgetInfo('xegallery');
//Context::set('info_widget', $info_widget);
//$xGalry_info->info_widget = $args->info_widget;

		// Set variables used internally
		$oModuleModel = &getModel('module');
		$module_srls = $args->modules_info = $args->module_srls_info = array();
		$site_module_info = Context::get('site_module_info');

		// Apply to all modules in the site if a target module is not specified
		if($args->module_srls=='')
		{
			unset($obj);
			$obj->site_srl = (int)$site_module_info->site_srl;
			$output = executeQueryArray('widgets.xegallery.getMids', $obj);
			if($output->data)
			{
				foreach($output->data as $key => $val)
				{
					$args->modules_info[$val->mid] = $val;
					$args->module_srls_info[$val->module_srl] = $val;
					$module_srls[] = $val->module_srl;
				}
			}
			$args->modules_info = $oModuleModel->getMidList($obj);

		} else {
			$obj->module_srls = $args->module_srls;
			$output = executeQueryArray('widgets.xegallery.getMids', $obj);
			if($output->data)
			{
				foreach($output->data as $key => $val)
				{
					$args->modules_info[$val->mid] = $val;
					$args->module_srls_info[$val->module_srl] = $val;
					$module_srls[] = $val->module_srl;
				}
			}
		}
		// Exit if no module is found
		if(!count($args->modules_info)) return Context::get('msg_not_founded');
		$xelist_info->modules_info = $args->modules_info;
		$args->module_srl = implode(',',$module_srls);
		$oDocumentModel = &getModel('document');
		$oDocumentModel->setToAllDocumentExtraVars();

		$obj->list_count = $args->list_count;
		$obj->module_srls = $obj->module_srl = $args->module_srl;
		$obj->direct_download = 'Y';
		$obj->isvalid = 'Y';
		$obj->download_count = 1;
		$obj->sort_index = $args->order_target;
		$obj->category_srl = explode(',', $args->category_srl);

		//기간 날짜 구하기
		if($args->duration!=0){
			$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$subtract = $today - (86400 * $args->duration);
			$obj->regdate = date("YmdHis", $subtract);
		} else $obj->regdate=0;

		// Get a list of documents
		if($xGalry_info->colect_type == "module")
		{
			$obj->order_type = $args->order_type=="desc"?"asc":"desc";
			$obj->statusList = array('PUBLIC');
			if($args->order_target=="download_count") $output = executeQueryArray('widgets.xegallery.getRankDownload', $obj);
			else $output = executeQueryArray('widgets.xegallery.getNewestDocuments', $obj);
			if($output->toBool()=='' || $output->data=='') return;
			$image_items = $tmplist = array();
			if($output->data) {
				foreach($output->data as $key => $val) {
					$showimage = $image_list = $imglist = array();
					$oDocument = $images = $imglink = null;
					$oDocument = $oDocumentModel->getDocument($val->document_srl);
					$doclink = $oDocument->getPermanentUrl();
					$oDocument->setAttribute($val, true);
					$oDocument->domain = $args->module_srls_info[$val->module_srl]->domain;
					$oDocument->add('domain',$oDocument->domain);
					$module_info = $oModuleModel->getModuleInfoByDocumentSrl($val->document_srl);
					$oDocument->add('module_info',$module_info);
					$oDocument->add('mid',$module_info->mid);
					$oDocument->add('browser_title',$module_info->browser_title);
					$oDocument->add('category',$oDocumentModel->getCategory($val->category_srl));
					$oDocument->add('category_srl',$val->category_srl);
					$oDocument->add('category_title',$oDocument->category->title);
					$oDocument->add('doclink',$doclink);
					$content = $args->content_cut_size !='N' ? $oDocument->getSummary($args->content_cut_size) : '';
					$oDocument->add('content',$content);
					$orgcontent = $oDocument->getContent(false);
					$orgcontent = preg_replace('/<([^>]*?)>/i', ' ', $orgcontent);
					$orgcontent = preg_replace('/<[^ei].*?>/i', ' ', $orgcontent);
					$orgcontent = preg_replace('/\s[\s]+/', '__XGaLry01__', $orgcontent);
					$getLine = preg_split('/__XGaLry01__/',$orgcontent);
					if(count($getLine)>0)
					{
						foreach($getLine as $ky1 => $nline)
						{
							if(preg_match("/\.(jpg|png|jpeg|gif|bmp)$/i",strtolower($nline)))
							{
								$images .= '['.$nline.','.$oDocument->getTitleText($args->subject_cut_size).']';
							}
						}
					}
					$oDocument->add('summary',$images);

					$extra_image = $oDocument->printExtraImages($args->duration_new * 60 * 60);
					$oDocument->add('extra_image',$extra_image);
					$oDocument->add('mobile_os',$args->mobile_os);
					$oDocument->add('mobile',$args->mobile);
					$oDocument->add('browser',$args->browser);

					$extvar1 = $oDocument->getExtraEidValue($args->ext1_var); // 이미지
					$extvar2 = $oDocument->getExtraEidValue($args->ext2_var); // 링크
					$extvar3 = $oDocument->getExtraEidValue($args->ext3_var); // 글쓴이
					$extvar4 = $oDocument->getExtraEidValue($args->ext4_var); // 제목
					$extvar5 = $oDocument->getExtraEidValue($args->ext5_var); // 부제목
					$extvar6 = $oDocument->getExtraEidValue($args->ext6_var); // 내용
					$extvar7 = $oDocument->getExtraEidValue($args->ext7_var); // 날짜

					// 확장변수 적용대상 - 이미지인 경우
					if($extvar1!='' && $args->target_ext1_var == 'Y')
					{
						if(preg_match("/\.(jpg|png|jpeg|gif|bmp)$/i",strtolower($extvar1)))
						{
							$image_list = array($extvar1);
							$oDocument->add('image_list',$image_list);
							$showimage[0]['thumb'] = $this->setRimg($extvar1,$thumb_width,$thumb_height,$args->thumbnail_type);
							$thumbnail = $showimage[0]['thumb'];
							$showimage[0]['viewer'] = $this->setRimg($extvar1,$image_width,$image_height,$args->image_type);
							$viewimage = $showimage[0]['viewer'];
						}
					}
					else
					{
						$image_list = $this->getFlist($oDocument->getUploadedFiles(),$xGalry_info->attached_list);
						$oDocument->add('image_list',$image_list);
						$showimage = $this->setCreateImgFile($image_list,$image_width,$image_height,$args->image_type,$thumb_width,$thumb_height,$args->thumbnail_type,$val->document_srl,$args->watermark_url,$args->watermark_halign,$args->watermark_valign);
						$thumbnail = $oDocument->getThumbnail($thumb_width,$thumb_height,$args->thumbnail_type);
						$viewimage = $oDocument->getThumbnail($image_width,$image_height,$args->image_type);
						//$viewer = $this->getImgView($showimage,$img_w,$img_h);
					}
					$oDocument->add('showimage',$showimage);
					$oDocument->add('thumbnail',$thumbnail);
					$oDocument->add('viewimage',$viewimage);

					// 확장변수 적용대상 - 링크인 경우
					if($extvar2!='' && $args->target_ext2_var == 'Y')
					{
						$oDocument->add('link',$extvar2);
					}
					else $oDocument->add('link',$doclink);

					// 확장변수 적용대상 - 글쓴이인 경우
					if($extvar3!='' && $args->target_ext3_var == 'Y')
					{
						$oDocument->add('nick_name',$extvar3);
					} else $oDocument->add('nick_name',$val->nick_name);

					// 확장변수 적용대상 - 제목인 경우
					if($extvar4!='' && $args->target_ext4_var == 'Y')
					{
						$oDocument->add('subject',$extvar4);
					}
					else
					{
						$subject = $args->subject_cut_size !='N' ? $oDocument->getTitleText($args->subject_cut_size) : '';
						$oDocument->add('subject',$subject);
					}

					// 확장변수 적용대상 - 소제목인 경우
					if($extvar5!='' && $args->target_ext5_var == 'Y')
					{
						$oDocument->add('subtitle',$extvar5);
					}

					// 확장변수 적용대상 - 내용인 경우
					if($extvar6!='' && $args->target_ext6_var == 'Y')
					{
						$oDocument->add('article',$extvar6);
					}
					else
					{
						$article = $args->content_cut_size !='N' ? $oDocument->getContentText($args->content_cut_size) : '';
						$oDocument->add('article',$article);
					}

					// 확장변수 적용대상 - 날짜인 경우
					if($extvar7!='' && $args->target_ext7_var == 'Y')
					{
						$oDocument->add('reg_date',$extvar7);
					} else $oDocument->add('reg_date',$oDocument->getRegdate('Y.m.d'));

					$cnt=count($showimage);
					for($i=0;$i<$cnt;$i++)
					{
						if($args->image_event=='D') $imglink = $doclink;
						elseif($args->image_event=='V') $imglink = $oDocument->domain.$showimage[$i]['viewer'];
						elseif($args->image_event=='X') $imglink = $extvar1;
						$tmplist[] = "['".$oDocument->domain.$showimage[$i]['thumb']."','".$imglink."','".htmlspecialchars($subject)."']";
					}
					$oDocument->add('imglist',$tmplist);
					$xDocument = new xegalleryItem( $module_info->browser_title );
					$xDocument->adds($oDocument->getObjectVars());
					$xDocument->setImage($showimage);

					//$xDocument->items = $oDocument;
					$GLOBALS['XE_DOCUMENT_LIST'][$val->document_srl] = $oDocument;
					if($oDocument->thumbnailExists()) $thumbModule = 'Y';
					$image_items[$key] = $xDocument;
				}
			}
			$xGalry_info->xegallery_items = $image_items;
			$xGalry_info->imglist = implode(",", $tmplist);
			if($thumbModule == 'Y') $xGalry_info->thumbModule = 'Y';
		}

		if($xGalry_info->colect_type == "document")
		{
			$obj->order_type = $args->order_type=="desc"?"asc":"desc";
			$obj->document_srl = explode(',', $args->document_srl);
			//$obj->document_srl = $args->document_srl;
			$obj->statusList = array('PUBLIC');
			$output = executeQueryArray('widgets.xegallery.getDocuments', $obj);
			if($output->toBool()=='' || $output->data=='') return;
			$image_items = $tmplist = array();
			if($output->data) {
				foreach($output->data as $key => $val) {
					$showimage = $image_list = $imglist = array();
					$oDocument = $images = $imglink = null;
					$oDocument = $oDocumentModel->getDocument($val->document_srl);
					$doclink = $oDocument->getPermanentUrl();
					$oDocument->setAttribute($val, true);
					$oDocument->domain = $args->module_srls_info[$val->module_srl]->domain;
					$oDocument->add('domain',$oDocument->domain);
					$module_info = $oModuleModel->getModuleInfoByDocumentSrl($val->document_srl);
					$oDocument->add('module_info',$module_info);
					$oDocument->add('mid',$module_info->mid);
					$oDocument->add('browser_title',$module_info->browser_title);
					$oDocument->add('category',$oDocumentModel->getCategory($val->category_srl));
					$oDocument->add('category_srl',$val->category_srl);
					$oDocument->add('category_title',$oDocument->category->title);
					$oDocument->add('doclink',$doclink);
					$content = $args->content_cut_size !='N' ? $oDocument->getSummary($args->content_cut_size) : '';
					$oDocument->add('content',$content);
					$orgcontent = $oDocument->getContent(false);
					$orgcontent = preg_replace('/<([^>]*?)>/i', ' ', $orgcontent);
					$orgcontent = preg_replace('/<[^ei].*?>/i', ' ', $orgcontent);
					$orgcontent = preg_replace('/\s[\s]+/', '__XGaLry01__', $orgcontent);
					$getLine = preg_split('/__XGaLry01__/',$orgcontent);
					if(count($getLine)>0)
					{
						foreach($getLine as $ky1 => $nline)
						{
							if(preg_match("/\.(jpg|png|jpeg|gif|bmp)$/i",strtolower($nline)))
							{
								$images .= '['.$nline.','.$oDocument->getTitleText($args->subject_cut_size).']';
							}
						}
					}
					$oDocument->add('summary',$images);

					$extra_image = $oDocument->printExtraImages($args->duration_new * 60 * 60);
					$oDocument->add('extra_image',$extra_image);
					$oDocument->add('mobile_os',$args->mobile_os);
					$oDocument->add('mobile',$args->mobile);
					$oDocument->add('browser',$args->browser);

					$extvar1 = $oDocument->getExtraEidValue($args->ext1_var); // 이미지
					$extvar2 = $oDocument->getExtraEidValue($args->ext2_var); // 링크
					$extvar3 = $oDocument->getExtraEidValue($args->ext3_var); // 글쓴이
					$extvar4 = $oDocument->getExtraEidValue($args->ext4_var); // 제목
					$extvar5 = $oDocument->getExtraEidValue($args->ext5_var); // 부제목
					$extvar6 = $oDocument->getExtraEidValue($args->ext6_var); // 내용
					$extvar7 = $oDocument->getExtraEidValue($args->ext7_var); // 날짜

					// 확장변수 적용대상 - 이미지인 경우
					if($extvar1!='' && $args->target_ext1_var == 'Y')
					{
						if(preg_match("/\.(jpg|png|jpeg|gif|bmp)$/i",strtolower($extvar1)))
						{
							$image_list = array($extvar1);
							$oDocument->add('image_list',$image_list);
							$showimage[0]['thumb'] = $this->setRimg($extvar1,$thumb_width,$thumb_height,$args->thumbnail_type);
							$thumbnail = $showimage[0]['thumb'];
							$showimage[0]['viewer'] = $this->setRimg($extvar1,$image_width,$image_height,$args->image_type);
							$viewimage = $showimage[0]['viewer'];
						}
					}
					else
					{
						$image_list = $this->getFlist($oDocument->getUploadedFiles(),$xGalry_info->attached_list);
						$oDocument->add('image_list',$image_list);
						$showimage = $this->setCreateImgFile($image_list,$image_width,$image_height,$args->image_type,$thumb_width,$thumb_height,$args->thumbnail_type,$val->document_srl,$args->watermark_url,$args->watermark_halign,$args->watermark_valign);
						$thumbnail = $oDocument->getThumbnail($thumb_width,$thumb_height,$args->thumbnail_type);
						$viewimage = $oDocument->getThumbnail($image_width,$image_height,$args->image_type);
						//$viewer = $this->getImgView($showimage,$img_w,$img_h);
					}
					$oDocument->add('showimage',$showimage);
					$oDocument->add('thumbnail',$thumbnail);
					$oDocument->add('viewimage',$viewimage);

					// 확장변수 적용대상 - 링크인 경우
					if($extvar2!='' && $args->target_ext2_var == 'Y')
					{
						$oDocument->add('link',$extvar2);
					}
					else $oDocument->add('link',$doclink);

					// 확장변수 적용대상 - 글쓴이인 경우
					if($extvar3!='' && $args->target_ext3_var == 'Y')
					{
						$oDocument->add('nick_name',$extvar3);
					} else $oDocument->add('nick_name',$val->nick_name);

					// 확장변수 적용대상 - 제목인 경우
					if($extvar4!='' && $args->target_ext4_var == 'Y')
					{
						$oDocument->add('subject',$extvar4);
					}
					else
					{
						$subject = $args->subject_cut_size !='N' ? $oDocument->getTitleText($args->subject_cut_size) : '';
						$oDocument->add('subject',$subject);
					}

					// 확장변수 적용대상 - 소제목인 경우
					if($extvar5!='' && $args->target_ext5_var == 'Y')
					{
						$oDocument->add('subtitle',$extvar5);
					}

					// 확장변수 적용대상 - 내용인 경우
					if($extvar6!='' && $args->target_ext6_var == 'Y')
					{
						$oDocument->add('article',$extvar6);
					}
					else
					{
						$article = $args->content_cut_size !='N' ? $oDocument->getContentText($args->content_cut_size) : '';
						$oDocument->add('article',$article);
					}

					// 확장변수 적용대상 - 날짜인 경우
					if($extvar7!='' && $args->target_ext7_var == 'Y')
					{
						$oDocument->add('reg_date',$extvar7);
					} else $oDocument->add('reg_date',$oDocument->getRegdate('Y.m.d'));

					$cnt=count($showimage);
					for($i=0;$i<$cnt;$i++)
					{
						if($args->image_event=='D') $imglink = $doclink;
						elseif($args->image_event=='V') $imglink = $oDocument->domain.$showimage[$i]['viewer'];
						elseif($args->image_event=='X') $imglink = $extvar1;
						$tmplist[] = "['".$oDocument->domain.$showimage[$i]['thumb']."','".$imglink."','".htmlspecialchars($subject)."']";
					}
					$oDocument->add('imglist',$tmplist);
					$xDocument = new xegalleryItem( $module_info->browser_title );
					$xDocument->adds($oDocument->getObjectVars());
					$xDocument->setImage($showimage);

					//$xDocument->items = $oDocument;
					$GLOBALS['XE_DOCUMENT_LIST'][$val->document_srl] = $oDocument;
					if($oDocument->thumbnailExists()) $thumbModule = 'Y';
					$image_items[$key] = $xDocument;
				}
			}
			$xGalry_info->xegallery_items = $image_items;
			$xGalry_info->imglist = implode(",", $tmplist);
			if($thumbModule == 'Y') $xGalry_info->thumbModule = 'Y';
		}

		if($xGalry_info->colect_type == "comment")
		{

		}

		if($xGalry_info->colect_type == "direct")
		{

		}

		$oSecurity = new Security($xGalry_info);
		$oSecurity->encodeHTML('..variables.title', '..variables.content', '..variables.user_name', '..variables.nick_name');

		$args->rethumb = 'N'; // 썸네일 재생성 초기화
		$xGalry_info->xs_skin = $args->skin;
		Context::set('xGalry_info', $xGalry_info);
		Context::set('module_srls_info', $args->module_srls_info);
// 페이지 수정일 때는 실제 모습은 보이지 않도록
		if (in_array(Context::get('act'), array("procWidgetGenerateCodeInPage", "dispPageAdminContentModify", "dispPageAdminMobileContentModify"))){
			$oTemplate = &TemplateHandler::getInstance();
			return $oTemplate->compile($this->widget_path, 'edit');
		}
		Context::set('colorset', $args->colorset);
		Context::set('skin', $args->skin);

		// 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
		$tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);

		// 템플릿 파일을 지정
		$tpl_file = 'gallery';

		// 템플릿 컴파일
		$oTemplate = &TemplateHandler::getInstance();
		return $oTemplate->compile($tpl_path, $tpl_file);
	}

	function getFlist($attached,$idximg)
	{
		// 초기화
		$getlist = array();
		$cnt = count($attached);
		$idximg = $idximg == 99 ? rand(1,$cnt) : $idximg;
		$idx = $idximg >= $cnt ? $cnt : $idximg;
		for($i=0;$i<$cnt;$i++) {
			$key = $idx==0 ? $i : $idx-1;
			if($i == $key)
			{
				$srcname = strtolower($attached[$i]->source_filename);
				//if(preg_match('/\.(avi|wmv|asf|asx|wma|swf|mov|mpg|mpeg|mp4|m4v|f4v|flv|3gp|3g2|f4a|f4b|m4a|m4b|m4p|rbs|aac|ogg|oga|m3u8|wav|mp3|3gpp|3gpp2|ogv|webm|divx|mkv)$/i',$srcname)) $getlist[] = $attached[$i]->uploaded_filename;
				if(preg_match('/\.(jpg|png|jpeg|gif|bmp)$/i',$srcname) ) $getlist[] = $attached[$i]->uploaded_filename;
			}
		}
		return $getlist;
	}

	function setCreateImgFile($imgsrc,$img_w,$img_h,$image_type,$thumb_w,$thumb_h,$thumb_type,$doc_srl,$wm_url,$wm_h,$wm_v)
	{
		$image_list = array();
		$imgcnt = count($imgsrc);
		if(is_array($imgsrc) && $imgcnt>0)
		{
			for($i=0;$i<$imgcnt;$i++)
			{
			$image_list[$i]['thumb'] = $this->setFimg($imgsrc[$i],$thumb_w,$thumb_h,$thumb_type,$doc_srl,$wm_url,$wm_h,$wm_v);
			$image_list[$i]['viewer'] = $this->setFimg($imgsrc[$i],$img_w,$img_h,$image_type,$doc_srl,$wm_url,$wm_h,$wm_v);
			}
		}
		else
		{
			$image_list[0]['thumb'] = $this->setFimg($imgsrc,$thumb_w,$thumb_h,$thumb_type,$doc_srl,$wm_url,$wm_h,$wm_v);
			$image_list[0]['viewer'] = $this->setFimg($imgsrc,$img_w,$img_h,$image_type,$doc_srl,$wm_url,$wm_h,$wm_v);
		}
		if(count($image_list)>0) return array_values($image_list);
		else return;
	}

// 첨부된 이미지 썸네일 생성
	function setFimg($imgsrc, $width = 180, $height = 120, $thumbnail_type, $document_srl, $watermark, $halign, $valign, $rethumb='N')
	{
		// 높이 지정이 별도로 없으면 정사각형으로 생성
		if($height=='') $height = $width;
		$thumbnail_url = array();

		// 메모리 설정
		@ini_set('memory_limit', '128M');

		// 섬네일 정보 정의
		$thumbnail_path = sprintf('files/cache/thumbnails/%s',getNumberingPath($document_srl, 3));
		if(!file_exists('./files/cache/tmp')) FileHandler::makeDir('./files/cache/tmp');

		$imgidx = substr($imgsrc, -11, 7); // 랜덤이미지일경우를 위해 이미지이름에 난수값입력
		$thumbnail_file = sprintf('%s%s_%dx%d.%s%s%s.png', $thumbnail_path, $imgidx, $width, $height, $thumbnail_type, $halign, $valign);
		$viewimg = sprintf('%s%s__%s__%s.png', $thumbnail_path, 'view', 'large', $imgidx);

		if(!file_exists($thumbnail_file)||$rethumb=='Y')
		{
			$output = $this->setNewThumb($imgsrc, $thumbnail_file, $viewimg, $width, $height, 'png', $thumbnail_type);
			if($watermark!='' && $output)
			{
				//$watermark = './images/mangologo.png';
				$watermark = FileHandler::getRealPath($watermark);

				$renderImage = $this->Watermarkrender($thumbnail_file, $watermark,$halign,$valign);
				$result = imagejpeg($renderImage, $thumbnail_file, 100);
				imagedestroy($renderImage);
			}
		}
		else
		{
			$output = $thumbnail_file;
		}
		if($output!='') return $thumbnail_file;
		else return;
	}

// 외부이미지를 썸네일 생성
	function setRimg($ExtFile, $width = 180, $height = 120, $thumbnail_type, $rethumb='N')
	{
		//if(!preg_match('/\.(jpg|png|jpeg|gif)$/i',strtolower($ExtFile))) return;
		// 메모리 설정
		@ini_set('memory_limit', '128M');

		$pathinfo = pathinfo($ExtFile); // [dirname], [filename]
		$tmpname = preg_replace('/[!#$%@:^&*()?+=\/]/','', $pathinfo['dirname']); // 모든 특수문자 제거
		//$parseinfo = parse_url($ExtFile); //[host]
		//$thumbname = $parseinfo['host'].'_'.$pathinfo['filename'];

		// 높이 지정이 별도로 없으면 정사각형으로 생성
		if($height=='') $height = $width;
		$thumbnail_path = null;
		$source_file = null;
		$thumbnail_file = null;
		$tmp_thumfile = null;

		$filename = $pathinfo['filename'];

		if(!file_exists('./files/cache/tmp')) FileHandler::makeDir('./files/cache/tmp');
		$thumbnail_path = sprintf('files/cache/tmp/%s', $tmpname);
		$source_file = sprintf('%s_%s.png', $thumbnail_path, $filename);
		$thumbnail_file = sprintf('%s_%dx%d.%s_%s.png', $thumbnail_path, $width, $height, $thumbnail_type, $filename);

		if(!file_exists($source_file))
		{
			$output = FileHandler::getRemoteFile($ExtFile,$source_file);
			if(!$output) FileHandler::writeFile($source_file,'');
		}

		if(!file_exists($thumbnail_file)||$rethumb=='Y')
		{
			$tmp_thumfile = $this->setNewThumb($source_file, $thumbnail_file, '', $width, $height, 'png', $thumbnail_type);
			if($tmp_thumfile) return $thumbnail_file;
			else return;
		}
		else
		{
			return $thumbnail_file;
		}
	}

// 새로운 썸네일 생성구문
	function setNewThumb($source_file, $target_file, $imgview, $resize_width = 0, $resize_height = 0, $target_type = 'png', $thumbnail_type = 'crop')
	{
		$source_file = FileHandler::getRealPath($source_file);
		$target_file = FileHandler::getRealPath($target_file);

		if(!file_exists($source_file)) return;
		if(!$resize_width) $resize_width = 100;
		if(!$resize_height) $resize_height = $resize_width;

		// retrieve source image's information
		$imageInfo = getimagesize($source_file);
		if(!FileHandler::checkMemoryLoadImage($imageInfo)) return false;
		list($width, $height, $type, $attrs) = $imageInfo;

		$x = 0;
		$y = 0;
		$offsetX = 0;
		$offsetY = 0;

		if($width<1 || $height<1) return;

		switch($type)
		{
			case '1' :
				$type = 'gif';
				break;
			case '2' :
				$type = 'jpg';
				break;
			case '3' :
				$type = 'png';
				break;
			case '6' :
				$type = 'bmp';
				break;
			default :
				return;
				break;
		}

		if($thumbnail_type == 'crop') {
			$oratio = $width / $height;
			$nratio = $resize_width / $resize_height;
			
			if ($oratio < $nratio)
			{
				$origHeight = $height;
				$height = $width / $nratio;
				$offsetY = ($origHeight - $height) / 2;
			}
			else if ($oratio > $nratio)
			{
				$origWidth = $width;
				$width = $height * $nratio;
				$offsetX = ($origWidth - $width) / 2;
			}
			$xRatio = $resize_width / $width;
			$yRatio = $resize_height / $height;

			if ($xRatio * $height < $resize_height)
			{
				$new_height = ceil($xRatio * $height);
				$new_width = $resize_width;
			}
			else
			{
				$new_width = ceil($yRatio * $width);
				$new_height = $resize_height;
			}
		} else {
			if ($width > $height) {
				$new_width = $resize_width;
				$new_height = floor($height * ($resize_width / $width));
			} else if ($width < $height ) {
				$new_height = $resize_height;
				$new_width = floor($width * ($resize_height / $height));
			} else {
				$new_width = $resize_height;
				$new_height = $resize_height;
			}
		}

		// get type of target file
		if(!$target_type) $target_type = $type;
		$target_type = strtolower($target_type);

		// create temporary image with target size
		if(function_exists('imagecreatetruecolor')) $thumb = imagecreatetruecolor($new_width, $new_height);
		else if(function_exists('imagecreate')) $thumb = imagecreate($new_width, $new_height);
		else return false;
		if(!$thumb) return false;

		$white = imagecolorallocate($thumb, 255,255,255);
		imagefilledrectangle($thumb,0,0,$new_width-1,$new_height-1,$white);

		// create temporary image having original type
		switch($type)
		{
			case 'gif' :
				if(!function_exists('imagecreatefromgif')) return false;
				$source = @imagecreatefromgif($source_file);
				break;
			// jpg
			case 'jpeg' :
			case 'jpg' :
				if(!function_exists('imagecreatefromjpeg')) return false;
				$source = @imagecreatefromjpeg($source_file);
				break;
			// png
			case 'png' :
				if(!function_exists('imagecreatefrompng')) return false;
				$source = @imagecreatefrompng($source_file);
				@imagealphablending($thumb, false);
				@imagesavealpha($thumb, true); // save alphablending setting (important)
				break;
			// bmp
			case 'wbmp' :
			case 'bmp' :
				if(!function_exists('imagecreatefromwbmp')) return false;
				$source = @imagecreatefromwbmp($source_file);
				break;
			default :
				return;
		}

		if($source)
		{
			if(function_exists('imagecopyresampled')) imagecopyresampled($thumb, $source, $x, $y, $offsetX, $offsetY, $new_width, $new_height, $width, $height);
			else imagecopyresized($thumb, $source, $x, $y, $offsetX, $offsetY, $new_width, $new_height, $width, $height);
		} else return false;

		// create directory 
		$path = dirname($target_file);
		if(!is_dir($path)) FileHandler::makeDir($path);

		// write into the file
		switch($target_type)
		{
			case 'gif' :
					if(!function_exists('imagegif')) return false;
					$output = imagegif($thumb, $target_file);
				break;
			case 'jpeg' :
			case 'jpg' :
					if(!function_exists('imagejpeg')) return false;
					$output = imagejpeg($thumb, $target_file, 100);
				break;
			case 'png' :
					if(!function_exists('imagepng')) return false;
					$output = imagepng($thumb, $target_file, 9);
				break;
			case 'wbmp' :
			case 'bmp' :
					if(!function_exists('imagewbmp')) return false;
					$output = imagewbmp($thumb, $target_file, 100);
				break;
		}

		imagedestroy($thumb);
		imagedestroy($source);

		if(!$output) return false;
		if($imgview)
		{
			$imgview = FileHandler::getRealPath($imgview);
			FileHandler::copyFile($source_file, $imgview, 'Y');
			//@copy($source_file, $imgview);
		}
		@chmod($target_file, 0644);
		@chmod($imgview, 0644);

		return true;
	}

//워터마크 생성
	function Watermarkrender($input, $wmark,$hpos,$vpos)
	{
		$sourceImage = $this->WatermarkimageCreate($input);
		if ( ! is_resource($sourceImage) ) {
			user_error('Invalid image resource', E_USER_NOTICE);
			return false;
		}

		$watermark = $this->WatermarkimageCreate($wmark);
		if ( ! is_resource($watermark) ) {
			user_error('Invalid watermark resource', E_USER_NOTICE);
			return false;
		}

		$image_width = imagesx($sourceImage); 
		$image_height = imagesy($sourceImage);  
		$watermark_width =  imagesx($watermark); 
		$watermark_height =  imagesy($watermark); 

		switch($hpos)
		{
			case 'R' :
				$halign = +5;
				break;
			case 'C' :
				$halign = 0;
				break;
			case 'L' :
				$halign = -5;
				break;
			default :
				$halign = +5;
				break;
		}
		switch($vpos)
		{
			case 'B' :
				$valign = +5;
				break;
			case 'M' :
				$valign = 0;
				break;
			case 'T' :
				$valign = -5;
				break;
			default :
				$valign = +5;
				break;
		}

		$X = $this->Watermarkcoord($halign, $image_width, $watermark_width)-$halign;
		$Y = $this->Watermarkcoord($valign, $image_height, $watermark_height)-$valign;

		imagecopy($sourceImage, $watermark, $X, $Y, 0, 0, $watermark_width, $watermark_height); 
		imagedestroy($watermark); 
			
		return $sourceImage;
	}

	function WatermarkimageCreate($input)
	{
		if ( is_file($input) ) {
			return $this->WatermarkimageCreateFromFile($input);
		} else if ( is_string($input) ) {
			return $this->WatermarkimageCreateFromString($input);
		} else {
			return $input;
		}
	}

	function WatermarkimageCreateFromFile($filename)
	{
		if ( ! is_file($filename) || ! is_readable($filename) ) {
			user_error('Unable to open file "' . $filename . '"', E_USER_NOTICE);
			return false;
		}

		// determine image format
		list( , , $type) = getimagesize($filename);

		switch ($type)
		{
			case IMAGETYPE_GIF:
				return imagecreatefromgif($filename);
				break;
					
			case IMAGETYPE_JPEG:
				return imagecreatefromjpeg($filename);
				break;
					
			case IMAGETYPE_PNG:
				return imagecreatefrompng($filename);
				break;
		}
		user_error('Unsupport image type', E_USER_NOTICE);
		return false;
	}

	function WatermarkimageCreateFromString($string)
	{
		if ( ! is_string($string) || empty($string) ) {
			user_error('Invalid image value in string', E_USER_NOTICE);
			return false;
		}

		return imagecreatefromstring($string);
	}

	function Watermarkcoord($align, $image_dimension, $watermark_dimension)
	{
		if ( $align < 0 ) {
			$result = 0;
		} elseif ( $align > 0 ) {
			$result = $image_dimension - $watermark_dimension;
		} else {
			$result = ($image_dimension - $watermark_dimension) >> 1;
		}
		return $result;
	}

	function getImgView($urlsrc,$zwidth=180,$zheight=120,$ztype='crop',$gview='F')
	{
		if($gview=='R')
		{
			$imgrename = sprintf('%dx%d.%s_', $zwidth,$zheight,$ztype);
			$viewimg = str_replace($imgrename, '', $urlsrc);
		}
		else
		{
			$path_parts = pathinfo($urlsrc);
			$basename = substr($path_parts['basename'], 0, 7);
			$viewimg = sprintf('%s%s__%s__%s.png', $path_parts['dirname'].'/', 'view', 'large', $basename);
		}
		return $viewimg;
	}

}

class xegalleryItem extends Object {
	function xegalleryItem($browser_title=''){
		$this->add('browser_title',$browser_title);
	}
	function getBrowserTitle(){
		return $this->get('browser_title');
	}
	function setDomain($domain) {
		static $default_domain = null;
		if(substr($domain, -1)!="/") $domain = $domain."/";
		if($domain=='') {
			if(is_null($default_domain)) 
			{
				$default_domain = Context::getDefaultUrl();
				if(substr($default_domain, -1)!="/") $default_domain = Context::getDefaultUrl()."/";

			}
			$domain = $default_domain;
		}
		$this->add('domain',$domain);
	}
	function getDomain() {
		return $this->get('domain');
	}
	function setMobile($mobile_os)
	{
		$this->add('mobile_os',$mobile_os);
	}
	function setMobileAgent($mobile_agent)
	{
		$this->add('mobile',$mobile_agent);
	}
	function setBrowser($browser)
	{
		$this->add('browser',$browser);
	}
	function setMid($mid)
	{
		$this->add('mid',$mid);
	}
	function getMid()
	{
		return $this->get('mid');
	}

	function setImage($showimage)
	{
		$this->add('image_list',$showimage);
	}
	function getImage()
	{
		return $this->get('image_list');
	}
	function getViewer($idx=0)
	{
		$getimage = $this->get('image_list');
		return $getimage[$idx];
	}
	function getThumb($idx=0)
	{
		$getimage = $this->get('image_list');
		return $getimage[$idx]['thumb'];
	}
	function getView($idx=0)
	{
		$getimage = $this->get('image_list');
		return $getimage[$idx]['viewer'];
	}
	function setLinkDoc($linkdoc){
		$this->add('doclink',$linkdoc);
	}
	function getLinkDoc(){
		return $this->get('doclink');
	}
	function setLink($url){
		$this->add('link',$url);
	}
	function getLink(){
		return $this->get('link');
	}
	function setNickName($nick_name){
		$this->add('nicknametest',$nick_name);
	}
	function getNickName(){
		return $this->get('nickname');
	}
	function setTitle($title){
		$this->add('subject',$title);
	}
	function getTitle($cut_size = 0, $tail='...'){
		$title = strip_tags($this->get('subject'));
		if($cut_size) $title = cut_str($title, $cut_size, $tail);
		$attrs = array();
		if($this->get('title_bold') == 'Y') $attrs[] = 'font-weight:bold';
		if($this->get('title_color') && $this->get('title_color') != 'N') $attrs[] = 'color:#'.$this->get('title_color');
		if(count($attrs)) $title = sprintf("<span style=\"%s\">%s</span>", implode(';', $attrs), htmlspecialchars($title));
		return $title;
	}
	function setSubTitle($subtitle){
		$this->add('subtitle',$subtitle);
	}
	function getSubTitle(){
		return $this->get('subtitle');
	}
	function setContent($content){
		$this->add('article',$content);
	}
	function getContent(){
		return $this->get('article');
	}
	function setCSRegdate($extvalue){
		$this->add('reg_date',$extvalue);
	}
	function getCSRegdate(){
		return $this->get('reg_date');
	}
	function setCategory($category){
		$this->add('category',$category);
	}
	function getCategory(){
		return $this->get('category');
	}
	function setExtraImages($ext_icon){
		$this->add('ext_icon',$ext_icon);
	}
	function getExtraImages() {
		return $this->get('ext_icon');
	}
}
?>
