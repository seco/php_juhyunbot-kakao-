<?php
include_once('simple_html_dom.php');
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/keyboard','getKeyboard');
$app->post('/message','getMessage1');
#$app->post('/message/:message','getMessage');
$app->post('/friend', 'getFriend');
$app->delete('/friend/:user_key', 'deleteUser');
$app->delete('/chat_room/:user_key','getChatRoom');
$app->get('/users/search/:query','getUserSearch');

$app->run();

function getKeyboard() {
	echo <<< EOD
{
"type" : "text"
}
EOD;
}

function getMessage1() {
	$keyword = file_get_contents('php://input');
	$data2 =  json_decode($keyword);

	$keyword = "$data2->content";
	$type = "$data2->type";
	$rslt = "";
	$buttonURL = "";

	//텍스트
	if($type == "text"){
		if($keyword == "힝"){
			$rslt = "미워";
		} else if($keyword == "안녕" || $keyword == "안녕하세요"){
			$rslt = "반가워";
		} else if($keyword == "잘가"){
			$rslt = "가지마";
		} else if($keyword == "행복해"){
			$rslt = "떠나지마";
		} else if($keyword == "너누구야" || (indexOf($keyword, "누구") > 0 && indexOf($keyword, "너") > 0)){
			$rslt = "난 주현보다 주현스러운 주현봇이야";
		} else if($keyword == "뭐해" || $keyword == "뭐함"){
			$rslt = "배고파";
		} else if($keyword == "웹툰추천좀"){
			$rslt = "네이버? 다음?";
		} else if($keyword == "네이버"){
			$rslt = "신의탑, 대학일기, 마음의소리, 제로게임, 복학왕, 연놈, 고수";
		} else if($keyword == "다음"){
			$rslt = "쌍갑포차, 노점 묵시록, 잉어왕 재밌어";
		} else if($keyword == "노래추천" || $keyword == "노래추천좀" || $keyword == "노래"){
			$rslt = "요즘 어떤 노래들어? 이렇게 보내볼래? 노래추천[듣는노래이름]";
		} else if(indexOf($keyword, "노래추천[") > 0){

			$keyword = str_replace("노래추천[", "", $keyword);
			$keyword = mb_substr($keyword, 0, -1, 'UTF-8');

			$auth_key = '';
			//내 도메인을 입력하세요.
			$url = "http://mydomain/search.php?AUTH_KEY=".$auth_key."&SONG_NAME=".$keyword;

			$html = file_get_html($url)->find('body')[0]->innertext;
			$rsltJson = json_decode($html, true);
			$title = $rsltJson['title'];
			$rsltStr = "";

			$rsltStr = $rsltStr.$title.' 관련 노래를 들어보세요.\n\n';
			$buttonURL = $rsltJson['url'];

			for($i = 0; $i < count($rsltJson['REC']); $i++){
				$rsltStr = $rsltStr.($i+1).'.'.$rsltJson['REC'][$i]['title'].'\n';
			}

			$rslt = $rsltStr;

		} else if($keyword == "배고파"){
		 	$rslt = "치킨?";
		} else if(indexOf($keyword, "치킨") > 0 && (indexOf($keyword, "어디") > 0 || indexOf($keyword, "추천") > 0) ){
		 	$rslt = "BBQ 후라이드 시켜머겅";
		} else if($keyword == "메뉴얼"){
			$rslt = "NO";
		} else if($keyword == "고마워"){
			$rslt = "별말씀을";
		} else if($keyword == "미안" || $keyword == "미안해"){
			$rslt = "괜찮아 ㅎㅎ";
		} else {
			$rslt = "NO";
		}


		if($rslt != "NO"){
			if($buttonURL != ""){
				echo <<< EOD
{
	"message":{
		"text" : "$rslt",
		"message_button" : {"label":"영상보기", "url":"$buttonURL"}
	}
}
EOD;
			} else {
				echo <<< EOD
{
	"message":{
		"text" : "$rslt"
	}
}
EOD;
			}		
		}
		
	} else {
		//미디어
		
	}
  

}


function indexOf($strVal, $findStr){
	return strlen(stristr($strVal, $findStr));
}

function deleteUser($data){
	$data2 =  json_decode($data);
	echo "friend22";
}

function getFriend() {
	echo "friend";
}

function getChatRoom($user_key) {
	echo <<< EOD
{
	"message":{
		"text" : "안녕 만나서 반가워"
	}
}
EOD;
}

function deleteUpdate($update_id) {
	echo "deleteUpdate method";
}

function getUserSearch($query) {
	echo "getUserSearch method";
}
?>