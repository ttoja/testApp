<?php
	/*
	 * 주의사항
	 * 		include된 파일에서 다른 파일을 include하지 말 것!!! 	
	 *		소스를 수정하는 경우
	 * 			변경전 파일을 yyyymmdd.finename으로 변경하여 보관 (예: index.php -> 20140510.index.php)
	 *			변경된 라인에 수정일자, 수정한 사람, 수정사유 등을 기록 (예: 2014.05.10 ~~~ 사유로 수정 (by 홍길동))
	 */
	
	require_once ("./first.inc.php");
	$vod_type = $_GET['vod_type'];
	
	if($vod_type == 'discussion'){
		$vod_page = ($_GET['discussion_page'])? $_GET['discussion_page']:1;
	}else{
		$vod_page = ($_GET['search_page'])? $_GET['search_page']:1;
	}
	
	$broadcast_id = (int)RemoveXSS($_GET['broadcast_id']);
	if (broadcast_id  < 1 ) {
		// 오류처리
		
	}

	$sql = sprintf(" select `election_broadcast_id`, `division_type`,`sido_id`"
		. ",`broadcast_subject`,`live_broadcast_url`,`general_broadcast_url`"
		. ",`broadcast_date`,`broadcast_start_hour`,`broadcast_end_hour`,`broadcast_channel`"
		. ",`broadcast_img_file_name`,`broadcast_img_file_path`"
		. ",`live_broadcast_yn`,`broadcast_contents`,`reg_date`"
		. ", ifnull(`mc`,'') as `mc`"
		. ", ifnull(`participant`,'') as `participant`"
		. ", ifnull(`conduct`,'') as `conduct`"
		. " from `TB_ELECTION_BROADCAST`"
		. " where `election_broadcast_id`=%d", quote_smart($broadcast_id) );
	$result = mysql_query($sql, $dbconn);
	if (!$result) {
		// 오류 
	}

	$row = mysql_fetch_array($result);

	
	$election_broadcast_id 	= (int)$row['election_broadcast_id'];			// int(11)			선거 방송 순번
	$division_type 			= stripcslashes($row['division_type']); 		// varchar(20)		방송 구분 유형 {TV토론방송-BOARDCAST01, 연설방송-BOARDCAST02, 켐페인영상-BOARDCAST03}
	$sido_id 				= stripcslashes($row['sido_id']);				// varchar(4)		시도 id {위원회 코드 사용}
	$broadcast_subject 		= stripcslashes($row['broadcast_subject']);		// varchar(200)		방송 제목
	$live_broadcast_url 	= stripcslashes($row['live_broadcast_url']);	// varchar(200)		방송 URL
	$general_broadcast_url 	= stripcslashes($row['general_broadcast_url']);	// varchar(200)		일반 방송_url
	$broadcast_date 		= stripcslashes($row['broadcast_date']);		// char(8)			방송 일자
	$broadcast_start_hour 	= stripcslashes($row['broadcast_start_hour']);	// char(4)			방송_시작_시간
	$broadcast_end_hour 	= stripcslashes($row['broadcast_end_hour']);	// char(4)			방송_종료_시간
	$broadcast_channel 		= stripcslashes($row['broadcast_channel']);		// varchar(10)		방송_채널 {1TV, 2TV}
	$broadcast_img_file_name= stripcslashes($row['broadcast_img_file_name']);	// varchar(100)		방송_이미지_파일_이름
	$broadcast_img_file_path= stripcslashes($row['broadcast_img_file_path']);	// varchar(100)		방송_이미지_파일_경로
	$live_broadcast_yn 		= stripcslashes($row['live_broadcast_yn']);			// enum('Y','N')	생방송_여부
	$broadcast_contents 	= stripcslashes($row['broadcast_contents']);		// text				내용
	$reg_date 				= $row['reg_date'];								// datetime		등록_일자
	$mc 					= stripcslashes($row['mc']);					// varchar(100)	사회자 {정보가 없을 수 있으므로 null일 경우가 있다.}
	$participant 			= stripcslashes($row['participant']);			// varchar(100)	출연자 {정보가 없을 수 있으므로 null일 경우가 있다.}
	$conduct 				= stripcslashes($row['conduct']);				// varchar(100)	주관 {정보가 없을 수 있으므로 null일 경우가 있다.}
		
		
	$broadcast_date 		= substr($broadcast_date , 0, 4)."년 ".substr($broadcast_date , 4, 2)."월 ".substr($broadcast_date , 6, 2)."일" ; //2014년 5월 20일
	$broadcast_start_hour 	= substr($broadcast_start_hour , 0, 2).":".substr($broadcast_start_hour, 2, 2);
	$broadcast_end_hour 	= substr($broadcast_end_hour , 0, 2).":".substr($broadcast_end_hour, 2, 2);		

	$img_url = "../".$broadcast_img_file_path.$broadcast_img_file_name;

		
	// facebook 등 쇼셜 공유를 위한 메타 태그 
	$meta_title = strip_tags($broadcast_subject); 	// 제목표시
	$share_url 	= _BASE_URL_."/discussion_view.php?broadcast_id=".$election_broadcast_id ; // 표시하고싶은URL
	$meta_image = $img_url; // 이미지경로
	$meta_description = str_replace(array("\r\n","\r","\n"),'',cut_str(strip_tags($broadcast_contentsnews_contents), 0, 100)); // 본문내용

	include (_INC_.DIRECTORY_SEPARATOR."header.html.inc.php");

	
	
	// 스킵메뉴	
	include (_INC_.DIRECTORY_SEPARATOR."skipnav.html.inc.php");
		
	echo ('
	<!-- wrap (s) -->
	<div id="wrap">
		<!-- container (s) -->
		<div id="container">
		
			<!-- header (s) -->
			<div id="headerFix">	
	');
	
	// kbs wrap
	include (_INC_.DIRECTORY_SEPARATOR."top.kbs.html.inc.php");
		
	// naver wrap
	include (_INC_.DIRECTORY_SEPARATOR."top.naver.html.inc.php");

	// navi wrap
	include (_INC_.DIRECTORY_SEPARATOR."top.navi.html.inc.php");
		

	// 내 선거구 찿기 레이어
	include (_INC_.DIRECTORY_SEPARATOR."header.my_area_layer.html.inc.php");
	
	echo ('
			</div>
	');
	
	// 메인엔서 우측 출구조사, 투개표현환 클릭시 우에서 좌로 슬라이딩
	// 선거당일(d-day)에는 해당 레이어가 초기화면으로 변경됨
	include (_INC_.DIRECTORY_SEPARATOR."main2.html.inc.php");
	
	
	// contents area start
	echo ('
			<!-- contents (s) -->
			<div id="contents">
	
				<div class="discussionView">
							
				<!-- 토론방송 왼쪽 내용 -->
				<div class="discussionView_left">
	');
?>	
	
	<!-- viewTitle -->
	<div class="viewTitle">
		
		<!-- subject -->
		<div class="subject">

			<!-- 라이브 -->
<?php if ( $live_broadcast_yn == "Y" ) { ?>			
			<!-- <p class="live"><img src="/html/web/images/icon/ico_live.jpg" alt="라이브" /></p> -->
<?php 	} ?>
			<!-- 제목 -->
			<p class="tit"><?=$broadcast_subject?></p>

		</div>

		<!-- 기자 등 내용 -->
		<div class="subject_info">
			
			<ul class="repot">					
				<!-- li class="first">황현규 기자</li -->
				<li><span>일시</span><?=$broadcast_date?></li>
				<li><span>시간</span><?=$broadcast_start_hour?> ~ <?=$broadcast_end_hour?></li>
				<li><span class="tv"><?=$broadcast_channel?></span></li>
			</ul>
			<!--
			<ul class="sns">
				<li><a href="#none"><img src="/html/web/images/btn/sns_facebook.png" alt="페이스북" /></a></li>
				<li><a href="#none"><img src="/html/web/images/btn/sns_twitter.png" alt="트위터" /></a></li>
				<li><a href="#none"><img src="/html/web/images/btn/sns_goo.png" alt="구글플러스" /></a></li>
			</ul>
			-->
		</div>

	</div>

	<!-- viewCont -->
	<div class="viewCont">

		<?php 
		$broadcastUrl = "";
		if ( $live_broadcast_yn == "Y") {
			if ($broadcast_channel == "2TV") {
				$broadcastUrl = "k.kbs.co.kr/LP/12";
			} else {
				$broadcastUrl = "k.kbs.co.kr/LP/11";
			}
		} else {
			$broadcastUrl = "www.kbs.co.kr/player/openk/player.php?auto=N&type=904&url=".$general_broadcast_url;
		}
		

		if ($broadcastUrl != "") {
		?>
		<div class="video">
			<iframe src="http://<?=$broadcastUrl?>" scrolling="no" frameborder="0" width="630" height="354"></iframe>
		</div>
		<?
		}
		?>


				
		<div class="text">
			<?=$broadcast_contents?>
		</div>

		<!-- 버튼 -->
		<div class="btnList">
			<a href="?vod_type=<?=$vod_type?>&<?=$vod_type?>_page=<?=$vod_page?>#SectDiscussion" class="LinkTrigger btnBlue"><span>목록</span></a>
		</div>

		<!-- 댓글 처리 -->


	</div>

</div>
<!-- //토론방송 왼쪽 내용 -->
<?php

	echo ('
				<!-- 뉴스 오른쪽 내용 -->
				<div class="discussionView_right">
	');
	
	// 시간 많이 본 뉴스
	include (_NEWS_.DIRECTORY_SEPARATOR."news.right.list.html.inc.php");

	// 웹툰
	include (_NEWS_.DIRECTORY_SEPARATOR."news.right.webtoon.html.inc.php");
	
	// 이벤트
	include (_NEWS_.DIRECTORY_SEPARATOR."news.right.event.html.inc.php");
		
	echo ('
				</div>
				<!-- //뉴스 오른쪽 내용 -->	
				
			</div>
			<!-- 뉴스 뷰 -->	

		</div>
		<!-- contents (e) -->
		
	</div>
	<!-- container (e) -->	
	');
	


	// footer 
	include (_INC_.DIRECTORY_SEPARATOR."footer.html.inc.php");
	
	// end wrap and canvas area
	echo ('
		</div>
		<!-- wrap (e) -->
	');
	
	// news popup layer 
	include (_INC_.DIRECTORY_SEPARATOR."popup.news.html.inc.php");
	
	// event popup layer 
	include (_INC_.DIRECTORY_SEPARATOR."popup.event.html.inc.php");

	// webtoon popup layer 
	include (_INC_.DIRECTORY_SEPARATOR."popup.webtoon.html.inc.php");


	// end body and html
	echo ('
</body>
</html>		
	');
?>		





