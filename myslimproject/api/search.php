<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<?php
include_once('simple_html_dom.php');
$songName = $_GET['SONG_NAME']; //검색할 노래 이름
$testKey = $_GET['AUTH_KEY']; //검색할 노래 이름

if($songName != '' && $testKey == ''){
	$url = 'https://www.youtube.com/results?search_query='.$songName;
	$html = file_get_html($url);

	//유투브에서 해당 키워드로 검색했을 때의 첫 번째 영상을 찾습니다.
	$element = $html->find('ol[class=item-section]')[0];
	$lielement = $element->find('li')[0]; //첫 번째 콘텐츠를 해당 영상으로 간주한다. (나중에 광고 필터링 필요)

	//광고 동영상인지를 판단한다.
	foreach($element->find('div[class=yt-lockup-video]') as $divElement){
		if($divElement->attr['data-ad-impressions'] != ''){
			
		} else {
			$div1 = $divElement;
			break;
		}
	}

	$div1 = $div1->find('div[class="yt-lockup-content"]');
	$url_de = 'https://www.youtube.com'.$div1[0]->find('h3')[0]->find('a')[0]->href;

	echo '{"title":"'.$div1[0]->find('h3')[0]->find('a')[0]->innertext.'","url":"'.$url_de.'",';

	//첫번째 영상의 관련 영상을 찾습니다.
	$html_de = file_get_html($url_de);
	$index1 = 0;
	$sectionIdx = 0;
	$html_de = $html_de->find('div[id=watch7-sidebar-modules]')[0];
	$recJson = '';
	$html_section = $html_de->find('div[class=watch-sidebar-section]');
	foreach($html_section as $element_de){
		$video_list = $element_de->find('div[class=watch-sidebar-body]')[0]->find('ul[class=video-list]')[0]->find('li');
		$numItems = count($video_list);
		foreach($video_list as $songElement){
			if($index1 > 4){
				break;
			}
			if($index1 == 0){
				// echo '관련 영상입니다.<br><br>';
				$recJson = '"REC":[';
			}

			$title_de = $songElement->find('span[class=title]')[0]->innertext;
			if(indexOf($title_de, "재생목록") <= 0 && indexOf($title_de, "노래모음") <= 0 && $title_de != "") {
				$index1 = $index1 + 1;
				// echo $title_de.'<br>';
				$checkSection;
				if($sectionIdx != 0){ //0은 바로 다음에 재생할 동영상, 섹션에 하나만 표시되기 때문에 예외처리 해준다.
					$checkSection = true;
				} else {
					$checkSection = false;
				}

				if($checkSection && $sectionIdx != 0 && $index1 == $numItems || $index1 > 4){ //Last Index
					$recJson = $recJson.'{"title":"'.$title_de.'"}';
				} else {
					$recJson = $recJson.'{"title":"'.$title_de.'"},';
				}
			}
		}
		$sectionIdx += 1;
	}

	if($recJson != ''){
		$recJson =$recJson.']}';
	}
	echo $recJson;
}

function indexOf($strVal, $findStr){
	return strlen(stristr($strVal, $findStr));
}

// $logfile = fopen("20160726_log.txt", "w") or die("Unable to open file");
// fwrite($logfile, $output);
// fclose($logfile);
?>
</body>
</html>