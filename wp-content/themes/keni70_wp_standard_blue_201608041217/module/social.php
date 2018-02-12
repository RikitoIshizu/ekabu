<?php
/*----------------------------------------
	賢威7.0

	SNSの設定
	
	第1版　2015. 9.29
	第2版　2016. 1.20
	第3版　2016. 2.22
	第4版　2016. 3.15
	第5版　2016. 5.18

	株式会社 ウェブライダー
----------------------------------------*/

$social = getSocialInfo();
if (!is_array($social) or count($social) <= 0) {	// 新規登録
	createData();
	$social = getSocialInfo();
}


/* ------------------------------------------
	facebook タグを出力する関数
 ------------------------------------------*/
function facebook_keni() {


	global $social;

	if (($social['fb_view'] == "y") && have_posts()) {

		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';  

		echo "\n<!--OGP-->\n";
		echo (is_front_page() && !get_query_var('paged')) ? "<meta property=\"og:type\" content=\"".$social['fb_type']."\" />\n" : "<meta property=\"og:type\" content=\"article\" />\n";
		echo "<meta property=\"og:url\" content=\"".get_canonical_keni(false, true)."\" />\n";
		
		$title = (get_post_meta( get_the_ID(), 'page_ogp_title', true) != "") ? get_post_meta( get_the_ID(), 'page_ogp_title', true) : get_title_keni();
		echo "<meta property=\"og:title\" content=\"".esc_html($title)."\" />\n";

/*
		if (is_front_page()) {
			$description = get_description_keni();
		} else {
			$description = (get_post_meta( get_the_ID(), 'page_ogp_description', true) != "") ? get_post_meta( get_the_ID(), 'page_ogp_description', true) : get_description_keni();
		}
*/
		$description = (get_post_meta( get_the_ID(), 'page_ogp_description', true) != "") ? get_post_meta( get_the_ID(), 'page_ogp_description', true) : get_description_keni();

		echo "<meta property=\"og:description\" content=\"".esc_html($description)."\" />\n";

		echo "<meta property=\"og:site_name\" content=\"".esc_html(get_bloginfo('name'))."\" />\n";

		$image = getSocialImage('fb_image');

		foreach ($image as $val) {
			echo "<meta property=\"og:image\" content=\"".esc_html($val)."\" />\n";
			break;
		}
		if (!empty($social['fb_app_id'])) echo "<meta property=\"fb:app_id\" content=\"".esc_html($social['fb_app_id'])."\" />\n";
		if (!empty($social['fb_admins'])) echo "<meta property=\"fb:admins\" content=\"".esc_html($social['fb_admins'])."\" />\n";
	
		echo "<meta property=\"og:locale\" content=\"".esc_html($social['fb_lang'])."\" />\n";
		echo "<!--OGP-->\n";
	}
}


/* --------------------------------------------------------
	Twitter情報表示
-------------------------------------------------------- */
function tw_cards_keni() {

	global $social;
	$twc_list = twCardsKey();

	if (($social['tw_view'] == "y") && have_posts()) {
		$view = "y";

		// 対象の投稿の種類を取得
		$tw_card = get_post_meta( get_the_ID(), 'tw_card', true);
		if (empty($tw_card)) $tw_card = key($twc_list);

		if ($twc_list[$tw_card]) {

			foreach ($twc_list[$tw_card] as $key => $val) {
				if ($key != "*info*") {
					$twitter[$key] = get_post_meta( get_the_ID(), $key, true);

					if (empty($twitter[$key])) {
						switch($key) {
							case "site":
								$twitter[$key] = the_keni("tw_screen_name");
								break;
								
							case "title":
								$twitter[$key] = (get_post_meta( get_the_ID(), 'page_ogp_title', true) != "") ? get_post_meta( get_the_ID(), 'page_ogp_title', true) : get_title_keni();
								break;
								
							case "description":
								$twitter[$key] = (get_post_meta( get_the_ID(), 'page_ogp_description', true) != "") ? get_post_meta( get_the_ID(), 'page_ogp_description', true) : get_description_keni();
								break;

							case "image":
							case "image0":
								$image = getSocialImage('tw_image');
								foreach ($image as $img_val) {
									$twitter[$key] = $img_val;
									break;
								}
								break;
						}
					}
					
					if (($val['nec'] == "y") && empty($twitter[$key])) $view = "n";
				}
			}
		}
		
		if ($view == "y") {
			echo "\n<!-- Twitter Cards -->\n";
			echo "<meta name=\"twitter:card\" content=\"".$tw_card."\" />\n";
			foreach ($twitter as $key => $val) if ($val != "") echo "<meta name=\"twitter:".$key."\" content=\"".esc_html($val)."\" />\n";
			echo "<!--Twitter Cards-->\n";
		}
	}
}


/* ------------------------------------------
	google+ タグを出力する関数
 ------------------------------------------*/
function microdata_keni() {

	global $social;

	if (($social['gp_view'] == "y") && have_posts()) {
		echo "\n<!--microdata-->\n";

		$title = (get_post_meta( get_the_ID(), 'page_ogp_title', true) != "") ? get_post_meta( get_the_ID(), 'page_ogp_title', true) : get_title_keni();
		echo "<meta itemprop=\"name\" content=\"".esc_html($title)."\" />\n";

		$description = (get_post_meta( get_the_ID(), 'page_ogp_description', true) != "") ? get_post_meta( get_the_ID(), 'page_ogp_description', true) : get_description_keni();
		echo "<meta itemprop=\"description\" content=\"".esc_html($description)."\" />\n";

		$image = getSocialImage('gp_image');
		foreach ($image as $val) {
			echo "<meta itemprop=\"image\" content=\"".esc_html($val)."\" />\n";
			break;
		}
		echo "<!--microdata-->\n";
	}
}



//---------------------------------------------------------------------------
//	対象の投稿のソーシャル情報を取得する
//---------------------------------------------------------------------------
function getPostSocial($postid) {
	global $social;
	foreach ($social as $key => $val) {
		$posted_list[$key] = get_post_meta( $postid, $key);
	}
	return $posted_list;
}



//---------------------------------------------------------------------------
//	画像のURLを取得
//---------------------------------------------------------------------------
function getSocialImage($target="") {
	
	global $social;

	$image = array();
	$image_id = "";

	if ((is_front_page() && is_page()) || is_singular()) {
		$image_id = get_post_thumbnail_id(get_the_ID());
		if (preg_match("/^[0-9]+$/",$image_id) && ($image_id > 0)) {
			$image_data = wp_get_attachment_image_src( $image_id, 'large');
			if (isset($image_data[0]) && (trim(mb_convert_kana($image_data[0], "s")) != "")) $image[] = $image_data[0];
		}	
	}

	if (!empty($social[$target]) && trim(mb_convert_kana($social[$target], "s"))) {
		$image[] = $social[$target];
	} else if ($target == "fb_image" && !empty($social['fb_inmage'])) {
		$image[] = $social['fb_inmage'];
	} else if (trim(mb_convert_kana($social['so_image'], "s")) != "") {
		$image[] = $social['so_image'];
	}

	return $image;
}



//---------------------------------------------------------------------------
//	管理画面上での個別title/descriptionの指定
//---------------------------------------------------------------------------
if ($social['fb_view'] == "y") {
	add_action('admin_menu', 'add_ogp_box');
	add_action('save_post', 'save_ogp_string');
}

function add_ogp_box() {
	// ランディングページのディレクトリ名を取得
	if (!defined('LP_DIR')) define('LP_DIR', the_keni('lp_dir'));

	add_meta_box('ogp', 'OGP・Microdata・Twitterカードの個別設定', 'ogp_setting', 'post', 'normal');
	add_meta_box('ogp', 'OGP・Microdata・Twitterカードの個別設定', 'ogp_setting', 'page', 'normal');
	add_meta_box('ogp', 'OGP・Microdata・Twitterカードの個別設定', 'ogp_setting', LP_DIR, 'normal');
}

function ogp_setting() {
	if (isset($_GET['post'])) {
		$page_ogp_title = get_post_meta( $_GET['post'], 'page_ogp_title',true);
		$page_ogp_description = get_post_meta( $_GET['post'], 'page_ogp_description', true);
	} else {
		$page_ogp_title = "";
		$page_ogp_description = "";
	}

	echo "<table>\n<tbody>\n";
	echo "<tr>\n<th>タイトル</th>\n<td class=\"keni_ogp_title\"><input type=\"text\" name=\"page_ogp_title\" value=\"".esc_html($page_ogp_title)."\" size=\"64\" maxlength=\"64\" /></td>\n</tr>\n";
	echo "<tr>\n<th>ディスクリプション</th>\n<td class=\"keni_ogp_description\"><input type=\"text\" name=\"page_ogp_description\" value=\"".esc_html($page_ogp_description)."\" size=\"64\" maxlength=\"64\" /></td>\n</tr>\n";
	echo "</tbody>\n</table>\n";
}

function save_ogp_string($post_id) {
	if (isset($_POST['page_ogp_title']) && isset($_POST['page_ogp_description']) ) {
		update_post_meta( $post_id, 'page_ogp_title', $_POST['page_ogp_title']);
		update_post_meta( $post_id, 'page_ogp_description', $_POST['page_ogp_description']);
	}
}



//---------------------------------------------------------------------------
//	管理画面上でのTwitterCards個別情報の指定
//---------------------------------------------------------------------------
if ($social['tw_view'] == "y") {
	add_action('admin_menu', 'add_tw_box');
	add_action('save_post', 'save_tw_string');
}

function add_tw_box() {
	// ランディングページのディレクトリ名を取得
	if (!defined('LP_DIR')) define('LP_DIR', the_keni('lp_dir'));

	add_meta_box('twc', 'Twitter Cards の個別設定', 'twc_setting', 'post', 'normal');
	add_meta_box('twc', 'Twitter Cards の個別設定', 'twc_setting', 'page', 'normal');
	add_meta_box('twc', 'Twitter Cards の個別設定', 'twc_setting', LP_DIR, 'normal');
}

function twc_setting() {
	
	$twc_list = twCardsKey();

	$setting_data = getPostSocial(get_the_ID());

	$images_no = 10;

	// デフォルトの値を取得
	$tw_screen_name = the_keni('tw_screen_name');
	$tw_image = the_keni('tw_image');

	if (isset($_GET['post'])) {
		$tw_card = get_post_meta( get_the_ID(), 'tw_card', true);
		if (empty($tw_card)) $tw_card = key($twc_list);
	} else {
		$tw_card = key($twc_list);
	}

	foreach ($twc_list[$tw_card] as $key => $val) {
		if ($key != "*info*") $tw_data[$key] = get_post_meta( get_the_ID(), $key, true);
	}

	echo "<table>\n<tbody>\n";
	foreach ($twc_list as $key => $twc_val) {
		if (isset($tw_card) && ($tw_card == $key)) {
			echo "<tr>\n<th><input type=\"radio\" name=\"tw_card\" value=\"".$key."\" id=\"".$key."\" onclick=\"ChangeTwCards('".$key."')\" checked=\"checked\"><label for=\"".$key."\">".$key."</label></th><td><label for=\"".$key."\">".$twc_val['*info*']."</label></td>\n</tr>\n";
		} else {
			echo "<tr>\n<th><input type=\"radio\" name=\"tw_card\" value=\"".$key."\" id=\"".$key."\" onclick=\"ChangeTwCards('".$key."')\"><label for=\"".$key."\">".$key."</label></th><td><label for=\"".$key."\">".$twc_val['*info*']."</label></td>\n</tr>\n";
		}
	}
	echo "</tbody>\n</table>\n";

	echo "<table>\n<tbody>\n";
	foreach ($twc_list as $key => $twc_val) {
		echo "<tr id=\"tw_".$key."\">\n<td>\n";
		echo "<table>\n";
		foreach ($twc_val as $twc_line_key => $twc_line_val) {
			if ($twc_line_key != '*info*') echo "<tr>\n<th>".$twc_line_key."</th>\n";

			if (is_array($twc_line_val)) {
				switch ($twc_line_val['type']) {
					case "text":
						echo "<td><input type=\"text\" name=\"".$key."_".$twc_line_key."\" value=\"".$tw_data[$twc_line_key]."\" size=\"60\" />";
						break;
					case "image":
						$images_no++;
						echo "<td><div id=\"keni_img_".$images_no."\"></div>\n";
						echo "<input type=\"text\" name=\"".$key."_".$twc_line_key."\" id=\"keni_upload_image_".$images_no."\" value=\"".$tw_data[$twc_line_key]."\" size=\"70\" />\n";
						echo "<input type=\"button\" class=\"keni_upload_image_button\" id=\"keni_upload_image_button_".$images_no."\" value=\"画像を設定する\" />\n";
						break;
				}
				if ($twc_line_val['nec'] == "y") echo "<span class=\"keni_note\">※ 必須</span>";
				if ($twc_line_key != '*info*' && isset($twc_line_val['info'])) echo "<br />".$twc_line_val['info'];
			}
		}
		echo "</tr>\n</table>\n</td>\n</tr>\n";
	}
	echo "</tbody>\n</table>\n";
	
	echo "<script>function ChangeTwCards(sel) {\n";
	echo "(function($) {\n";
	foreach ($twc_list as $key => $twc_val) {
		echo "if (sel == '".$key."') {\n";
		echo "$(\"#tw_".$key."\").show();\n";
		echo "} else {\n";
		echo "$(\"#tw_".$key."\").hide();\n";
		echo "}\n";
	}
	echo "})(jQuery);\n";
	echo "}\n";
	
	echo "jQuery.noConflict();\n";
	echo "(function($) {\n";
	echo "$(function() {\n";
	echo "var tw_sel = $(\"input[name='tw_card']:checked\").val();\n";
	foreach ($twc_list as $key => $twc_val) {
		echo "if (tw_sel == '".$key."') {\n";
		echo "$(\"#tw_".$key."\").show();\n";
		echo "} else {\n";
		echo "$(\"#tw_".$key."\").hide();\n";
		echo "}\n";
	}
	echo "})\n";
	echo "})(jQuery);\n";
	echo "</script>\n";	
}


function save_tw_string($post_id) {
	if (isset($_POST['tw_card'])) {
		update_post_meta( $post_id, 'tw_card', $_POST['tw_card']);
		$twc_list = twCardsKey();
		foreach ($twc_list[$_POST['tw_card']] as $key => $val) {
			if ($key != "*info*") {
				$post_key = $_POST['tw_card']."_".$key;
				if (isset($_POST[$post_key])) update_post_meta( $post_id, $key, $_POST[$post_key]);
			}
		}
	}
}






//---------------------------------------------------------------------------
//	TwitterCardsの種類と設定内容
//---------------------------------------------------------------------------
function twCardsKey() {
	$site = (the_keni('tw_screen_name') != "") ? "空白の場合の初期値：@".the_keni('tw_screen_name') : "例） @seokyoto";

	$tw_type = array("summary" => array("*info*"  => "通常のツイートに利用します。140文字のテキストの下に画像とテキストを入力する ",
																					 "site" => array("info" => "Twitterのアカウント名を入力します。".$site,
																													 "type" => "text",
																													 "nec" => "y"),
																					 "title" => array("info" => "Twitter Cardsのタイトルにしたい文字を入力します。空白の場合の初期値は「投稿タイトル」になります。",
																														"type" => "text",
																														"nec" => "y"),
																					 "description" => array("info" => "投稿内容の抜粋などを入力します。空白の場合の初期値は「抜粋」になります。",
																																	"type" => "text",
																																	"nec" => "y"),
																					 "image" => array("info" => "Tweetに付ける画像を指定します",
																														"type" => "image",
																														"nec" => "n")
																					),
									 "summary_large_image" =>array("*info*"  => "大きな画像を付けてツイートしたい場合に利用します",
																					 "site" => array("info" => "Twitterのアカウント名を入力します。".$site,
																														"type" => "text",
																														"nec" => "y"),
																					 "title" => array("info" => "Twitter Cardsのタイトルにしたい文字を入力します。空白の場合の初期値は「投稿タイトル」になります。",
																														"type" => "text",
																														"nec" => "y"),
																					 "description" => array("info" => "投稿内容の抜粋などを入力します。空白の場合の初期値は「抜粋」になります。",
																																	"type" => "text",
																																	"nec" => "y"),
																					 "image" => array("info" => "Tweetに付ける画像を指定します",
																														"type" => "image",
																														"nec" => "n")
																					),
									 "photo" =>array("*info*"  => "画像をメインにしたツイートをしたい場合に利用します",
																					 "site" => array("info" => "Twitterのアカウント名を入力します。@".$site,
																														"type" => "text",
																														"nec" => "y"),
																					 "title" => array("info" => "Twitter Cardsのタイトルにしたい文字を入力します。空白の場合の初期値は「投稿タイトル」になります。",
																														"type" => "text",
																														"nec" => "n"),
																					 "image" => array("info" => "Tweetに付ける画像を指定します",
																														"type" => "image",
																														"nec" => "y")
																					),
									 "gallery" =>array("*info*"  => "複数（最大4枚）の画像を付けてツイートをしたい場合に利用します",
																					 "site" => array("info" => "Twitterのアカウント名を入力します。@".$site,
																														"type" => "text",
																														"nec" => "y"),
																					 "title" => array("info" => "Twitter Cardsのタイトルにしたい文字を入力します。空白の場合の初期値は「投稿タイトル」になります。",
																														"type" => "text",
																														"nec" => "y"),
																					 "description" => array("info" => "投稿内容の抜粋などを入力します。空白の場合の初期値は「抜粋」になります。",
																														"type" => "text",
																														"nec" => "n"),
																					 "image0" => array("val" => "",
																													 "info" => "Tweetに付ける画像(1)を指定します",
																														"type" => "image",
																														"nec" => "y"),
																					 "image1" => array("val" => "",
																													 "info" => "Tweetに付ける画像(2)を指定します",
																														"type" => "image",
																														"nec" => "y"),
																					 "image2" => array("val" => "",
																													 "info" => "Tweetに付ける画像(3)を指定します",
																														"type" => "image",
																														"nec" => "y"),
																					 "image3" => array("val" => "",
																													 "info" => "Tweetに付ける画像(4)を指定します",
																														"type" => "image",
																														"nec" => "y")
																					)
							);
	
	return $tw_type;
}




/* --------------------------------------------------------
	テーブル情報の設定
-------------------------------------------------------- */
function createData() {
	
	global $social;
	global $wpdb;

	if (!is_array($social) or count($social) < 0) {	// 新規登録
	
		// 過去のテーブルが存在するかどうかを確認
		$before_version = get_option("keni62_before");
	
		if (!empty($before_version)) {
			switch ($before_version) {
				case "6.2":
					$table_name = $wpdb->prefix."keni_setting62";
					break;
				case "6.1":
					$table_name = $wpdb->prefix."keni_setting61";
					break;
				case "6.0":
					$table_name = $wpdb->prefix."keni_setting";
					break;
			}
	
			$old_data = $wpdb->get_results("SELECT ks_sys_cont, ks_val FROM ".$table_name." WHERE ks_group in ('Facebook','Google＋','Twitterカード') ORDER BY ks_sort");
			foreach ($old_data as $cont) {
				$list[$cont->ks_sys_cont] = $cont->ks_val;
			}			
		}
	
		/* --------------------------------------------------------
			ソーシャルネットワークの表示制御
		-------------------------------------------------------- */
		$insert = "INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'ソーシャルボタンの表示', 'social_top_view','トップページ（サイトトップ）','n','n','check','151')";
		$results = $wpdb->query( $insert );
		
		$insert = "INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'ソーシャルボタンの表示', 'social_top_archive_view','トップページ（記事一覧部分）','n','n','check','152')";
		$results = $wpdb->query( $insert );
		
		$insert = "INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'ソーシャルボタンの表示', 'social_archive_view','一覧ページ（トップページを除く）','n','n','check','153')";
		$results = $wpdb->query( $insert );
		
		$insert = "INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'ソーシャルボタンの表示', 'social_post_view','投稿ページ','n','n','check','155')";
		$results = $wpdb->query( $insert );
		
		$insert = "INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'ソーシャルボタンの表示', 'social_page_view','固定ページ','n','n','check','157')";
		$results = $wpdb->query( $insert );

	
		if (isset($list['fb_ogpimage'])) {
			$image_url = $list['fb_ogpimage'];
		} else if (isset($list['gp_image'])) {
			$image_url = $list['gp_image'];
		} else if (isset($list['tw_image'])) {
			$image_url = $list['tw_image'];
		} else {
			$image_url = get_template_directory_uri().'/ogp.jpg';
		}

		$insert = "INSERT INTO ".KENI_SET." (ks_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_ext, ks_type, ks_sort) VALUES ('SNSの設定', 'so_image','共通のサムネイル画像','".$image_url."','".$image_url."','ここに設定された画像が、各ソーシャルメディアの標準画像となります。\n個別に設定をしたい場合は、それぞれの各画像を設定して下さい。','image','161')";
		$results = $wpdb->query( $insert );
	
		/* --------------------------------------------------------
			Facebookに必要な設定
		-------------------------------------------------------- */
		if (!isset($social['fb_view'])) {
			$fb_view =  (isset($list['fb_view'])) ? $list['fb_view'] : "n";
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Facebook', 'fb_view','Facebookのタグ（OGP）の出力','".$fb_view."','n','check','165')");
		}
	
		if (!isset($social['fb_app_id'])) {
			$fb_app_id = (isset($list['fb_app_id'])) ? $list['fb_app_id'] : "";
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Facebook', 'fb_app_id','Facebook App ID','".$fb_app_id."','','text','169')");
		}
	
		if (!isset($social['fb_admins'])) {
			$fb_admins = (isset($list['fb_admins'])) ? $list['fb_admins'] : "";
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Facebook', 'fb_admins','Facebookの管理者ID<br />（カンマ区切りで入力してください）','".$fb_admins."','','text','173')");
		}
	
		if (!isset($social['fb_type'])) {
			$fb_type = (isset($list['fb_type'])) ? $list['fb_type'] : 'website';
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Facebook', 'fb_type','Facebookのサイトタイプ','".$fb_type."','website','text','177')");
		}
	
		if (!isset($social['fb_lang'])) {
			$fb_lang =  (isset($list['fb_lang'])) ? $list['fb_lang'] :'ja_JP';
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Facebook', 'fb_lang','Facebookの言語','".$fb_lang."','ja_JP','text','181')");
		}
	
		if (!isset($social['fb_image'])) {
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Facebook', 'fb_image','Facebookのサムネイル画像','".$image_url."','','image','185')");
		}
	
		/* --------------------------------------------------------
			Twitterに必要な設定
		-------------------------------------------------------- */
		if (!isset($social['tw_view'])) {
			$tw_view =  (isset($list['tw_view'])) ? $list['tw_view'] : 'n';
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Twitter', 'tw_view','Twitterのタグ（Twitterカード）の出力','".$tw_view."','n','check','189')");
		}
	
		if (!isset($social['tw_screen_name'])) {
			$tw_screen_name =  (isset($list['tw_screen_name'])) ? $list['tw_screen_name'] : '';
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_ext, ks_type, ks_sort) VALUES ('SNSの設定', 'Twitter', 'tw_screen_name','Twitterのアカウント名','".$tw_screen_name."','','@で始まるTwitterアカウント名を入力して下さい（必須）','text','193')");
		}
	
		if (!isset($social['tw_image'])) {
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Twitter', 'tw_image','Twitterのサムネイル画像','".$image_url."','','image','197')");
		}
	
		/* --------------------------------------------------------
			Google+に必要な設定
		-------------------------------------------------------- */
		if (!isset($social['gp_view'])) {
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Google+', 'gp_view','Google+（Microdata）のタグ出力','n','n','check','200')");
		}
		if (!isset($social['gp_image'])) {
			$results = $wpdb->query("INSERT INTO ".KENI_SET." (ks_group, ks_sub_group, ks_sys_cont, ks_view_cont, ks_val, ks_def_val, ks_type, ks_sort) VALUES ('SNSの設定', 'Google+', 'gp_image','Google+（Microdata）のサムネイル画像','".$image_url."','','image','201')");
		}
	}
}



/* --------------------------------------------------------
	データベースから情報を取得
-------------------------------------------------------- */
function getSocialInfo() {
	global $wpdb;
	$res = $wpdb->get_results("SELECT ks_id, ks_sys_cont, ks_val FROM ".KENI_SET." WHERE ks_group='SNSの設定' && ks_active='y' ORDER BY ks_sort");
	if (isset($res) && count($res) > 0) {
		foreach ($res as $tw) {
			$social[$tw->ks_sys_cont] = $tw->ks_val;
		}
	}

	if (isset($social)) {
		return $social;
	} else {
		return false;
	}
}

?>