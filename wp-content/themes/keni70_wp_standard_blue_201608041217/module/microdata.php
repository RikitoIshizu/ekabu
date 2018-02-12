<?php
/*----------------------------------------
	賢威7.0

	マイクロデータ出力の設定
	
	第1版　2015. 9.29

	株式会社 ウェブライダー
----------------------------------------*/

function getMicroCodeType() {

	if (is_front_page() && is_home()) {
		$type = "Blog";
	} else if (is_front_page()) {
		$type = "WebPage";
	} else if (is_singular(LP_DIR)) {
		$type = "WebPage";
	} else if (is_page()) {
		$type = "WebPage";
	} else if (is_attachment()) {
		$type = "WebPage";
	} else if (is_singular()) {
		$type = "Article";
	} else {
		$type = "Blog";
	}
	
	return $type;
}	
?>