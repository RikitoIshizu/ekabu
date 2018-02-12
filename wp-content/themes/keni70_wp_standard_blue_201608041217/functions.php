<?php
/*----------------------------------------
	賢威7.0

	賢威テンプレート
	
	第1版（7.0）　　2015. 9.29
	第2版（7.0.1）　2015.10.21
	第3版（7.0.2）　2015.11. 4
	第4版（7.0.3）　2015.11.12
	第5版（7.0.4）　2015.12. 3
	第6版（7.0.5）　2015.12.12
	第7版（7.0.5.1）2016. 2.23
	第8版（7.0.5.2）2016. 2.24
	第9版（7.0.5.3）2016. 3. 4
	第10版（7.0.5.4）2016. 3. 16
	第11版（7.0.6） 2016. 4.14
	第12版（7.0.6.1） 2016. 4.15
	第13版（7.0.6.2） 2016. 4.21
	第14版（7.0.6.3） 2016. 5.24
	第15版（7.0.6.4） 2016. 6.3
	第16版（7.0.6.5） 2016. 6.22
	第17版（7.0.6.6） 2016. 8. 2

	株式会社 ウェブライダー
----------------------------------------*/
//---------------------------------------------------------------------------
//	賢威の基本設定
//---------------------------------------------------------------------------
if ( get_option('WPLANG') == 'ja' ) {
	load_textdomain('keni', get_template_directory().'/keni.mo');
}

if (!defined('KENI_SET')) {
	global $wpdb;
	define("KENI_SET",$wpdb->prefix."keni_setting706");

	// ランディングページのディレクトリ名を取得
	if (!defined('LP_DIR')) define('LP_DIR', the_keni('lp_dir'));
}

function keni_setting() {
	include(TEMPLATEPATH . '/module/keni_setting.php');
}

function keni_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );
}
add_action( 'after_setup_theme', 'keni_setup' );


$menu_list = array (
	'top_menu' => __( 'Top Menu', 'keni' ),
	'footer_menu' => __( 'Footer Menu', 'keni' )
);
register_nav_menus ( $menu_list );


//---------------------------------------------------------------------------
// 賢威用各種モジュールの読み込み
//---------------------------------------------------------------------------

// ディレクトリ内のファイルを読み込む
$mod_dir = opendir(get_template_directory()."/module/");

// moduleから自動的に読み出さないファイルのリスト
$ex_files = array("keni_seo_check.php",
									"keni_seo_check_view.php",
									"character.php"
									);

while($file_name = readdir($mod_dir)) {
	if (preg_match('/\.php$/', $file_name)) {
		if (!in_array($file_name, $ex_files, true)) {

			if (isset($_GET['taxonomy']) and $_GET['taxonomy'] != "category") {
				if ($file_name != "add_extra_fields_category.php") require_once(get_template_directory()."/module/".$file_name);
			} else {
				require_once(get_template_directory()."/module/".$file_name);
			}
		}
	}
}

//---------------------------------------------------------------------------
// 賢威用各種モジュールの読み込み
//---------------------------------------------------------------------------
function register_jquery() {
	wp_register_script('my-social', get_bloginfo('template_directory') .'/js/socialButton.js','','',true);
	wp_enqueue_script('my-social');

	wp_register_style( 'keni_base', get_stylesheet_directory_uri(). '/base.css');
	wp_register_style( 'keni_rwd', get_stylesheet_directory_uri(). '/rwd.css');

	wp_enqueue_style('keni_base');
	if (the_keni('mobile_layout') == 'y') wp_enqueue_style('keni_rwd');

	wp_register_script('my-toc', get_bloginfo('template_directory') .'/js/keni_toc.js','','',true);

	if (is_singular()) {
		$view_flug = 'n';
		$toc = get_post_meta( get_the_ID(), 'toc', true);
		if ($toc == "y") {
			$view_flug = 'y';
		} else if ((the_keni('toc_view') && the_keni('toc_view') == 'y') && ($toc == 'def' || $toc == NULL)) {
			$view_flug = 'y';
		}
		if ($view_flug == 'y') wp_enqueue_script('my-toc');
	}

	wp_register_script('my-utility', get_bloginfo('template_directory') .'/js/utility.js','','',true);
	wp_enqueue_script('my-utility');

	/*-------------------------------------------------------------------------------------------------
		独自のJSを読み込ます場合は、賢威テンプレートの /js 内にファイルを入れた後、下記に追記して下さい。
		※ コメントアウト（ // ）を外して下さい。
	-------------------------------------------------------------------------------------------------*/
//	wp_register_script('【JS名】', get_bloginfo('template_directory') .'/js/【JSファイル名】','','',true);
//	wp_enqueue_script('【JS名】');
}

add_action('wp_enqueue_scripts', 'register_jquery');


function keni_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sub Content', 'keni' ),
		'id' => 'sidebar',
		'before_widget' => '<section id="%1$s" class="section-wrap widget-conts %2$s"><div class="section-in">',
		'after_widget' => '</div></section>',
		'before_title' => '<h3 class="section-title">',
		'after_title' => '</h3>',
	) );

}
add_action( 'widgets_init', 'keni_widgets_init' );

add_theme_support('menus');
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 246, 200, true );
add_image_size( 'large_thumb', 320, 320, true );
add_image_size( 'middle_thumb', 200, 200, true );
add_image_size( 'small_thumb', 150, 150, true );
add_image_size( 'ss_thumb', 100, 100, true );


//---------------------------------------------------------------------------
// コメントなどをhtml5に変更
//---------------------------------------------------------------------------
$args = array(
	'search-form',
	'comment-form',
	'comment-list',
	'gallery',
	'caption'
);
add_theme_support( 'html5', $args );


//---------------------------------------------------------------------------
//	メニューのリストを取得
//---------------------------------------------------------------------------
function get_menu_list() {
	global $wpdb;
	$menu_list['-1'] = "メニューの表示をしない";
	$menu_list['0'] = "標準のメニュー表示を利用する";
	$menus = wp_get_nav_menus();
	if (!empty($menus)) {
		foreach ($menus as $val) {
			$menu_list[$val->term_id] = $val->name;
		}
	}
	return $menu_list;
}

//---------------------------------------------------------------------------
//	メニューのカスタマイズ
//---------------------------------------------------------------------------
function get_globalmenu_keni($position) {

	if (!has_nav_menu($position)) {
		return false;
	}

	if (is_front_page()) {
		$menu_no =  (the_keni('top_menu') == "y") ? $menu_no = 0 : -1;
	} else if (is_singular()) {
		$menu_no = (int)get_post_meta(get_the_ID(), 'menu_view', true);
	} else {
		$menu_no = 0;
	}

	if ($menu_no < 0) {
		return false;

	} else {
		$menu_data = array(
			'theme_location'  => '',
			'menu'            => '', 
			'container'       => '', 
			'container_class' => 'menu-header', 
			'container_id'    => '',
			'menu_class'      => '', 
			'menu_id'         => '',
			'echo'            => false,
			'fallback_cb'     => 'wp_page_menu',
			'before'          => '',
			'after'           => '',
			'link_before'     => '',
			'link_after'      => '',
			'items_wrap'      => '%3$s',
			'depth'           => 0,
			'walker'          => ''
		);
		
		if ($menu_no == 0) {
			$menu_data['theme_location'] = $position;
		} else if (preg_match("/^[0-9]+$/", $menu_no)) {
			$menu_data['menu' ] = $menu_no;
		}
	}

	$menu = wp_nav_menu($menu_data);

	return $menu;
}



//---------------------------------------------------------------------------
//	アーカイブのタイトルの表示する関数
//---------------------------------------------------------------------------

function archive_title_keni() {
	echo get_archive_title_keni();
}


function get_archive_title_keni($page="y") {
	$arc_title = "";
	if (is_archive()) {
		if(is_category()){
			global $cat;
			$arc_title = get_post_meta($cat, "title", true);
			if (empty($arc_title)) $arc_title = sprintf( __('Archive List for %s','keni'), single_cat_title("",false));
		} else if(is_day()){
			$arc_title = sprintf( __('Archive List for %s','keni'), get_the_time(__('F j, Y','keni')));
		} else if(is_month()){
			$arc_title = sprintf( __('Archive List for %s','keni'), get_the_time(__('F Y','keni')));
		} else if(is_year()){
			$arc_title = sprintf( __('Archive List for %s','keni'), get_the_time(__('Y','keni')));
		} else if(is_author()) {
			$arc_title = get_the_author().sprintf( __('Archive List for authors','keni'));
		} elseif(is_tag()) {
			$tag_id = get_query_var('tag_id');
			$arc_title = get_post_meta($tag_id, "title", true);
			if (empty($arc_title)) $arc_title = sprintf( __('Tag List for %s','keni'), single_tag_title("",false));
		} else if(isset($_GET['paged']) && !empty($_GET['paged'])) {
			$title = sprintf( __('Archive List for blog','keni'));
		} else {
			$post_type = get_query_var('post_type');
			if (!empty($post_type)) {
				$object = get_post_type_object($post_type);
				$arc_title = (isset($object->labels->name) && !empty($object->labels->name)) ? $object->labels->name : archive_title_keni();
			} else {
				$categoryList = get_post_type_object(get_post_type());			
				if (preg_match('/'.get_post_type().'\/$/', $_SERVER['REQUEST_URI']) && isset($categoryList->labels->name)) {	// カテゴリの上位の場合
					$arc_title = $categoryList->labels->name;
				} else {
					if (isset($categoryList->taxonomies) && !empty($categoryList->taxonomies)) {
						foreach ($categoryList->taxonomies as $taxonomie) {
							$term = get_the_terms(get_the_ID(), $taxonomie);
							if (!isset($term->errors)) {
								if (isset($term)) {
									foreach ($term as $val) {
										$arc_title = $val->name;
										break;
									}
								}
							}
						}
					} else if (is_tax()) {
						$arc_title = single_term_title();
					} else {
						global $wp_query;
						$taxonomy = $wp_query->get_queried_object();
						if (isset($taxonomy->name) && ($taxonomy->name != "")) {
							$arc_title = $taxonomy->name;
						}
					}
				}
			}
		}
	} else if(is_search()){
		$arc_title = sprintf( __('Search Result for %s','keni'), get_search_query());
	}

	if ((get_query_var('paged') > 1)  and ($page=="y")) {
		return $arc_title.show_page_number();
	} else {
		return $arc_title;
	}
}



//---------------------------------------------------------------------------
//	最新情報リスト
//---------------------------------------------------------------------------
function newposts_keni( $target = "new", $num_of_posts = 5, $excerpt = 1, $show_date = "default", $catid = 0) {

	$res_data = "\n\n";

	// 除外IDを取得
	$ex = the_keni('new_info_ex_cat');
	$ex_array = explode(",", $ex);
	foreach ($ex_array as $ex_id) {
		if (preg_match("/^[0-9]+$/", $ex_id)) $ex_ids[] = $ex_id;
	}

	if ($target == "new") {
		
		$res_data .= "<h2>".__('Latest Info','keni')."</h2>\n";
		if (isset($ex_ids) && is_array($ex_ids) && count($ex_ids) > 0) {
			$r = new WP_Query(array('showposts' => $num_of_posts, 'nopaging' => 0, 'post_status' => 'publish', 'cat' => $catid, 'ignore_sticky_posts' => 1, 'category__not_in' => $ex_ids));
		} else {
			$r = new WP_Query(array('showposts' => $num_of_posts, 'nopaging' => 0, 'post_status' => 'publish', 'cat' => $catid, 'ignore_sticky_posts' => 1));
		}
	} else {

		$res_data .= "<h2>".__('Your blog&#8217;s WordPress Pages','keni')."</h2>\n";
		$sticky = get_option( 'sticky_posts' );
		if (isset($ex_ids) && is_array($ex_ids) && count($ex_ids) > 0) {
			$r = new WP_Query(array('showposts' => $num_of_posts, 'nopaging' => 0, 'post_status' => 'publish', 'post__in' => $sticky, 'ignore_sticky_posts' => 1, 'category__not_in' => $ex_ids));
		} else {
			$r = new WP_Query(array('showposts' => $num_of_posts, 'nopaging' => 0, 'post_status' => 'publish', 'post__in' => $sticky, 'ignore_sticky_posts' => 1));
		}
	}

	$res_data .= "<div class=\"news\">\n\n";


	while ($r->have_posts()) : $r->the_post();

		$res_data .= "<article class=\"news-item\">\n";
		$res_data .= "<h3 class=\"news-title\"><a href=\"".esc_attr(get_permalink())."\">".esc_html(get_the_title())."</a></h3>\n";

		if (get_the_post_thumbnail(get_the_ID())) $res_data .= "<div class=\"news-thumb\">\n<a href=\"".esc_attr(get_permalink())."\">".get_the_post_thumbnail(get_the_ID(), 'small_thumb')."</a>\n</div>\n";

		$res_data .= "<div class=\"news-date\"><time datetime=\"".get_the_time("Y-m-d")."\">";

		switch ($show_date) {
			case "default":
				$res_data .= get_the_time(get_option('date_format'));
				break;

			case "year":
				$res_data .= get_the_time(__('F j, Y','keni'));
				break;

			case "month":
				$res_data .= get_the_time(__('F j','keni'));
				break;

			case "diff":
				$difftime = strtotime("now") - strtotime(get_the_time("Y-m-d"));
				$diffday = (int) ($difftime / 86400);
				if ( $diffday >= 2 ) $res_data .= sprintf(__('%s days ago','keni'), $diffday);
				else if ( $diffday >= 1 ) $res_data .= sprintf(__('%s day ago','keni'), $diffday);
				else $res_data .= sprintf(__('Less than a day ago','keni'));
				break;
		}
		$res_data .= "</time></div>\n";
		if (the_keni('pv_view') == "y" && preg_match("/^[0-9]+$/",getViewPV(get_the_ID()))) $res_data .= '<p class="post-pv">'.getViewPV(get_the_ID()).'PV</p>';

		$category_data = get_category_keni(get_the_ID());
		if (!empty($category_data)) $res_data .= "<div class=\"news-cat\">\n".$category_data."\n</div>\n";

		if ((is_front_page() && the_keni('social_top_archive_view') == "y") || (!is_front_page() && the_keni('social_archive_view') == "y")) {
			$res_data .= "<aside class=\"sns-list\">\n";
			$res_data .= "<ul>\n";
			$res_data .= "<li class=\"sb-tweet\">\n";
			$res_data .= "<a href=\"https://twitter.com/share\" data-text=\"".esc_html(get_the_title())." | ".esc_html(get_bloginfo('name'))."\" data-url=\"".get_the_permalink()."\" class=\"twitter-share-button\" data-lang=\"ja\">" . __('Tweet', 'keni') . "</a>\n";
			$res_data .= "</li>\n";
			$res_data .= "<li class=\"sb-hatebu\">\n";
			$res_data .= "<a href=\"http://b.hatena.ne.jp/entry/".get_the_permalink()."\" data-hatena-bookmark-title=\"".esc_html(get_the_title())." | ".esc_html(get_bloginfo('name'))."\" class=\"hatena-bookmark-button\" data-hatena-bookmark-layout=\"simple-balloon\" title=\"" . __('Add this entry to Hatena Bookmark.', 'keni') . "\"><img src=\"https://b.st-hatena.com/images/entry-button/button-only@2x.png\" alt=\"" . __('Add this entry to Hatena Bookmark.', 'keni') . "\" width=\"20\" height=\"20\" style=\"border: none;\" /></a>\n";
			$res_data .= "</li>\n";
			$res_data .= "<li class=\"sb-fb-like\">\n";
			$res_data .= "<div class=\"fb-like\" data-width=\"110\" data-href=\"".get_the_permalink()."\" data-layout=\"button_count\" data-action=\"like\" data-show-faces=\"false\" data-share=\"false\"></div>\n";
			$res_data .= "</li>\n";
			$res_data .= "<li class=\"sb-gplus\">\n";
			$res_data .= "<div class=\"g-plusone\" data-href=\"".get_the_permalink()."\" data-size=\"medium\"></div></li>\n";
			$res_data .= "</ul>\n";
			$res_data .= "</aside>\n";
		}
		
		if ($excerpt == 1) $res_data .= "<p class=\"news-cont\">".get_the_excerpt()."</p>\n";
		$res_data .= "<p class=\"link-next\"><a href=\"".esc_attr(get_permalink())."\">" . __('see more', 'keni') . "</a></p>\n";
		$res_data .= "</article>\n\n";

	endwhile;

	$res_data .= "</div>\n";

	wp_reset_query();

	return $res_data;
}


//---------------------------------------------------------------------------
//	個別ページにタグを設定出来るようにする
//---------------------------------------------------------------------------
function add_tag_to_page() {
 register_taxonomy_for_object_type('post_tag', 'page');
}
add_action('init', 'add_tag_to_page');

function add_page_to_tag_archive( $obj ) {
	if ( is_tag() and $obj->is_main_query()) {
		$obj->query_vars['post_type'] = array( 'post', 'page' );
	}
}
add_action( 'pre_get_posts', 'add_page_to_tag_archive' );



//---------------------------------------------------------------------------
//	固定ページで「抜粋」を入力可に
//---------------------------------------------------------------------------
add_post_type_support('page', 'excerpt');
add_post_type_support(LP_DIR, 'excerpt');


//---------------------------------------------------------------------------
//	「もっと見る」リンクの文字省略時のデザイン変更
//---------------------------------------------------------------------------
function new_excerpt_more($more) {
	return '・・・';
}
add_filter('excerpt_more', 'new_excerpt_more');




//---------------------------------------------------------------------------
//	管理画面上での<h1>エリアの指定
//---------------------------------------------------------------------------
add_action('admin_menu', 'add_h1_box');
 
function add_h1_box() {
	add_meta_box('h1', 'ランディングページのキャッチコピー', 'h1_setting', LP_DIR, 'normal', 'high');
}

function h1_setting() {
	if (isset($_GET['post'])) {
		$page_h1 = get_post_meta( $_GET['post'], 'page_h1', true);
	} else {
		$page_h1 = "";
	}

	$res = "<input class=\"keni_h1_textbox\" type=\"text\" name=\"page_h1\" value=\"".esc_html($page_h1)."\" size=\"64\" />";
	echo $res;
}

function save_h1_string($post_id) {
	if (isset($_POST['page_h1'])) {
		update_post_meta( $post_id, 'page_h1', $_POST['page_h1']);
	}
}


//---------------------------------------------------------------------------
//	<h1>の表示する関数
//---------------------------------------------------------------------------

function h1_keni() {
	echo esc_html(get_h1_keni());
}


function get_h1_keni($post_id = 0) {

	$h1 = "";
	$no_view = "y";

	if ($post_id > 0) {
		$h1 = get_post_meta($post_id,'page_h1', true);
		if (empty($h1)) $h1 = get_the_title($post_id);

	} else if(is_home() or is_front_page()) {

		if ((get_option('page_for_posts') > 0) and (get_the_ID() != get_option('page_on_front'))) {
			$h1 = get_the_title(get_option('page_for_posts'));
		} else if (the_keni('top_h1') != "") {
			$h1 = the_keni('top_h1');
		} else {
			$h1 = title_keni();
			$no_view = "n";
		}
		// 2ページ目以降の場合、ページナンバーを付ける
		if (get_query_var('paged') > 1 && $no_view =="y") $h1 .= show_page_number();

	} else if (is_day() or is_month() or is_year()) {	
		$h1 = archive_title_keni();

	} else if (is_category() or is_tag()) {
		$h1 = get_archive_title_keni();

	} else if (is_singular(LP_DIR)) {
		$h1 = (get_post_meta(get_the_ID(), 'page_h1', true)) ? get_post_meta(get_the_ID(), 'page_h1', true) : get_the_title();

	} else if (is_singular()) {
		$h1 = get_the_title();

	} else if (is_404()) {
		$h1 = __('Sorry, but you are looking for an entry that isn&#8217;t here.', 'keni');

	} else {
		$h1 = archive_title_keni();
	}

	return esc_html($h1);
}


//---------------------------------------------------------------------------
//	管理画面上でのcanonicalエリアの指定
//---------------------------------------------------------------------------

add_action('admin_menu', 'add_canonical_box');

function add_canonical_box() {
	add_meta_box('canonical', 'canonical URL', 'canonical_setting', 'post', 'normal', 'high');
	add_meta_box('canonical', 'canonical URL', 'canonical_setting', 'page', 'normal', 'high');
	add_meta_box('canonical', 'canonical URL', 'canonical_setting', LP_DIR, 'normal', 'high');
}

function canonical_setting() {
	$page_canonical = (isset($_GET['post'])) ? get_post_meta( $_GET['post'], 'page_canonical', true) : "";
	echo  "<input type=\"text\" class=\"keni_canonical_textbox\" name=\"page_canonical\" id=\"page_canonical\" value=\"".esc_html($page_canonical)."\" size=\"64\" />";
}

function save_canonical_string($post_id) {
	if (isset($_POST['page_canonical'])) update_post_meta( $post_id, 'page_canonical', $_POST['page_canonical']);
}


//---------------------------------------------------------------------------
//	canonicalを表示する関数
//---------------------------------------------------------------------------
function canonical_keni() {
	echo get_canonical_keni();
}

function get_canonical_keni($tag=true, $pass=false) {

	remove_action('wp_head', 'rel_canonical');

	if (get_option('blog_public') == false && $tag == true) return "";

	$canonical = $url = "";
	
	$permalink_structure = get_option('permalink_structure');
	if ($permalink_structure == "") {
		$perm_slash = "q";
	} else if (preg_match("/\/$/u", $permalink_structure)) {
		$perm_slash = "y";
	} else {
		$perm_slash = "n";
	}

	$the_page = pageNumber();

	if ($pass == true || !preg_match('/noindex/', getIndexFollow(), $res)) {

		if (is_front_page() || is_home()) {
			if (isset($_GET['post_type']) && $_GET['post_type'] != "") {
				$uri_parth = parse_url($_SERVER['REQUEST_URI']);
				if (isset($uri_parth['query']) && !empty($uri_parth['query'])) $url = get_home_url()."/?".$uri_parth['query'];

			} else if (get_query_var('paged') > 0) {
				if ($perm_slash == "y") {
					$url = get_home_url()."/page/".get_query_var('paged')."/";
				} else if ($perm_slash == "n") {
					$url = get_home_url()."/page/".get_query_var('paged');
				} else {
					$url = get_home_url()."/?page=".get_query_var('paged');
				}

			} else if (is_front_page() && get_option('page_on_front') > 0 && get_post_meta(get_the_ID(),'page_canonical', true) != "") {
				$url = get_post_meta(get_the_ID(),'page_canonical', true);

			} else if ($the_page['now_page'] > 1) {
				if ($perm_slash == "y") {
					$url = get_home_url()."/page/".$the_page['now_page']."/";
				} else if ($perm_slash == "n") {
					$url = get_home_url()."/page/".$the_page['now_page'];
				} else {
					$url = get_home_url()."/?page=".$the_page['now_page'];
				}

			} else {
				$url = get_home_url().'/';	// urlの最後が // と、スラッシュが2つになった場合は、.'/' を削除し、$url = get_home_url(); として下さい。
			}

		} else if (is_singular()) {

			$post_canonical = get_post_meta(get_the_ID(),'page_canonical', true);
			if (isset($post_canonical) and ($post_canonical != "")) {
				$url = $post_canonical;

			} else {
				$index = get_post_meta( get_the_ID(), 'index', true);
				if ($index == "index" || empty($index)) $url = get_permalink(get_the_ID());
				$this_page = pageNumber();

				if ($this_page['now_page'] > 1) {
					if ($perm_slash == "y") {
						$url .= $this_page['now_page']."/";
					} else if ($perm_slash == "n") {
						$url .= "/".$this_page['now_page'];
					} else {
						$url .= "&page=".$this_page['now_page'];
					}
				}
			}

		} else if (is_category()) {

			$now_cat_name = single_cat_title('',false);
			$cat_id = get_cat_ID($now_cat_name);
			$index = get_post_meta( $cat_id, 'meta_index', true);
			if (($index == "def") || empty($index)) $index = the_keni("list_category_index");
			if ($index == "index" || ($index == "noindex_p2" && $the_page['now_page'] < 2) || $pass == true) $url = get_category_link($cat_id);

		} else if (is_tag()) {
			$this_tag_name = single_tag_title('',false);
			$tag_lists = get_the_tags();
			if (isset($tag_lists) && is_array($tag_lists) && count($tag_lists) > 0) {
				foreach ($tag_lists as $tag_val) {
					if ($tag_val->name == $this_tag_name) {
						$tag_id = $tag_val->term_id;
						break;
					}
				}
			}
			$index = get_post_meta( $tag_id, 'meta_index', true);
			if (($index == "def") || empty($index)) $index = the_keni("list_tag_index");
			if ($index == "index" || ($index == "noindex_p2" && $the_page['now_page'] < 2) || $pass == true) $url = get_tag_link($tag_id);

		} else if (is_date()) {

			preg_match("/(\/\?m=[0-9]{4,8})/", $_SERVER['REQUEST_URI'], $url_param);
			$date = "";
			if (!isset($url_param[1])) {
				if (is_year()) {
					preg_match("/(\/[0-9]{4}\/*)/", $_SERVER['REQUEST_URI'], $url_param);
				} else if (is_month()) {
					preg_match("/(\/[0-9]{4}\/[0-9]{2}\/*)/", $_SERVER['REQUEST_URI'], $url_param);
				} else if (is_day()) {
					preg_match("/(\/[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/*)/", $_SERVER['REQUEST_URI'], $url_param);
				}

				preg_match("/\/archives\/date/", $_SERVER['REQUEST_URI'], $date_param);
				if (isset($date_param[0])) {
					$date = "/archives/date";
				} else {
					preg_match("/date/", $_SERVER['REQUEST_URI'], $date_param);
					if (isset($date_param[0])) $date = "/date";
				}
			}

			if (isset($url_param[1])) $url = get_home_url().$date.$url_param[1];

		} else if (is_author()) {
			$url = get_author_posts_url(get_the_author_meta('ID'));

		} else if (is_search()) {

			$now_page = get_query_var('paged');
			if ($now_page > 1) {
				if ($perm_slash == "q") {
					$url = get_home_url()."?s=".urlencode($_GET['s']) ."&paged=".$now_page;
				} else if ($perm_slash == "y") {
					$url = get_home_url()."/page/".$now_page."/?s=".urlencode($_GET['s']);
				} else {
					$url = get_home_url()."/page/".$now_page."?s=".urlencode($_GET['s']);
				}
			} else {
				$url = get_home_url()."/?s=".urlencode($_GET['s']);
			}
		}

	 if (isset($url) && $url != "") {
			if (!is_front_page() && !is_home() && !is_search()) {
				$now_page = get_query_var('paged');
				if ($now_page > 0) {
					if ($perm_slash == "q") {
						$url .= "&paged=".$now_page;
					} else if ($perm_slash == "y") {
						$url .= "page/".$now_page."/";
					} else {
						if (preg_match("/\/$/",$url)) $url = substr($url,0,-1);
						$url .= "/page/".$now_page;
					}
				}
			}
			$canonical = ($tag == true) ? '<link rel="canonical" href="'.$url.'" />'."\n" : $url;
		}
	}

	return $canonical;
}



//---------------------------------------------------------------------------
//	管理画面上での関連記事エリアの指定
//---------------------------------------------------------------------------

add_action('admin_menu', 'add_relation_box');

function add_relation_box() {
	add_meta_box('relation', '関連記事設定', 'relation_setting', 'post', 'normal', 'high');
	add_meta_box('relation', '関連記事設定', 'relation_page_setting', 'page', 'normal', 'high');
}

// 「投稿ページ」用の関連記事設定
function relation_setting() {

	for ($i = 0; $i < 5; $i++) {
		$relation[$i]['title'] = "";
		$relation[$i]['url'] = "";
		$relation[$i]['blank'] = "";
	}

	if (isset($_GET['post'])) {
		$relation_data = get_post_meta( $_GET['post'], 'relation', true);
		if (!empty($relation_data)) { 
			$relation_lies = explode("\n", $relation_data);		// 改行で区切る
			foreach ($relation_lies as $no => $relation_line) {
				$line_array = explode("\t", $relation_line);	// タブで区切る
				if (isset($line_array[0]) && trim($line_array[0]) != "") {
					$relation[$no]['title'] = $line_array[0];
					$relation[$no]['url'] = $line_array[1];
					$relation[$no]['blank'] = $line_array[2];
					$relation[$no]['image'] = $line_array[3];
				}
			}
		}
	}

	$category_relation = (isset($_GET['post'])) ? get_post_meta( $_GET['post'], 'category_relation', true) : "";
	$tag_relation = (isset($_GET['post'])) ? get_post_meta( $_GET['post'], 'tag_relation', true) : "";


	$res = "<p>既に公開されている記事から、記事と同一の「カテゴリー」と「タグ」に含まれているもの5件をランダムに表示する事が出来ます。<br />\n";
	$res .= "両方にチェックが入っている場合には、カテゴリが優先されます。</p>\n";
	
	$res .= "<ul>\n";
	$res .= "<li><span class=\"keni_relation_blank\">";
	if (isset($category_relation) && $category_relation == "y") {
		$res .= "<input type=\"checkbox\" name=\"category_relation\" value=\"y\" id=\"category_relation\" checked=\"checked\" />";
	} else {
		$res .= "<input type=\"checkbox\" name=\"category_relation\" value=\"y\" id=\"category_relation\" />";
	}
	$res .= "<label for=\"category_relation\">カテゴリー</label></span>\n</li>\n";

	$res .= "<li><span class=\"keni_relation_blank\">";
	if (isset($tag_relation) && $tag_relation == "y") {
		$res .= "<input type=\"checkbox\" name=\"tag_relation\" value=\"y\" id=\"tag_relation\" checked=\"checked\" />";
	} else {
		$res .= "<input type=\"checkbox\" name=\"tag_relation\" value=\"y\" id=\"tag_relation\" />";
	}
	$res .= "<label for=\"tag_relation\">" . __('Tags', 'keni') . "</label></span>\n</li>\n";
	$res .= "</ul>\n";

	$res .= "<p>「カテゴリー」「タグ」以外の任意のURLを設定することも可能です。（その場合、以下で設定したURLが優先的に表示されます。）<br />\n";
	$res .= "左から「記事タイトル」「記事URL」を入力して下さい。<br />リンクを新ウィンドウで開きたい場合は、右のチェックボックスにチェックを入れて下さい。</p>\n";
	$res .= "<ol class=\"keni_relation_lists\">\n";

	foreach ($relation as $no => $val) {
		$res .= "<li>\n";
		$res .= "<span class=\"keni_relation_title\"><input type=\"text\" name=\"relation[".$no."][title]\" value=\"".esc_html($val['title'])."\" placeholder=\"記事タイトル\" size=\"32\" /></span>\n";
		$res .= "<span class=\"keni_relation_url\"><input type=\"text\" name=\"relation[".$no."][url]\" value=\"".esc_html($val['url'])."\" placeholder=\"記事URL\" size=\"55\" /></span>\n";
		if ($val['blank'] == "y") {
			$res .= "<span class=\"keni_relation_blank\"><input type=\"checkbox\" name=\"relation[".$no."][blank]\" value=\"y\" checked=\"checked\" /></span>\n";
		} else {
			$res .= "<span class=\"keni_relation_blank\"><input type=\"checkbox\" name=\"relation[".$no."][blank]\" value=\"y\" /></span>\n";
		}
		$res .= "</li>\n";
	}
	$res .= "</ol>\n";
	
	echo $res;
}


// 「固定ページ」用の関連記事設定
function relation_page_setting() {

	for ($i = 0; $i < 5; $i++) {
		$relation[$i]['title'] = "";
		$relation[$i]['url'] = "";
		$relation[$i]['blank'] = "";
	}

	if (isset($_GET['post'])) {
		$relation_data = get_post_meta( $_GET['post'], 'relation', true);
		if (!empty($relation_data)) { 
			$relation_lies = explode("\n", $relation_data);		// 改行で区切る
			foreach ($relation_lies as $no => $relation_line) {
				$line_array = explode("\t", $relation_line);	// タブで区切る
				if (isset($line_array[0]) && trim($line_array[0]) != "") {
					$relation[$no]['title'] = $line_array[0];
					$relation[$no]['url'] = $line_array[1];
					$relation[$no]['blank'] = $line_array[2];
					$relation[$no]['image'] = $line_array[3];
				}
			}
		}
	}

	$category_relation = (isset($_GET['post'])) ? get_post_meta( $_GET['post'], 'category_relation', true) : "";
	$tag_relation = (isset($_GET['post'])) ? get_post_meta( $_GET['post'], 'tag_relation', true) : "";


	$res = "<p>既に公開されている記事から、記事と同一の「タグ」に含まれているもの5件をランダムに表示する事が出来ます。<br />\n";

	$res .= "<ul>\n";
	$res .= "<li><span class=\"keni_relation_blank\">";
	if (isset($tag_relation) && $tag_relation == "y") {
		$res .= "<input type=\"checkbox\" name=\"tag_relation\" value=\"y\" id=\"tag_relation\" checked=\"checked\" />";
	} else {
		$res .= "<input type=\"checkbox\" name=\"tag_relation\" value=\"y\" id=\"tag_relation\" />";
	}
	$res .= "<label for=\"tag_relation\">" . __('Tags', 'keni') . "</label></span>\n</li>\n";
	$res .= "</ul>\n";

	$res .= "<p>「タグ」以外の任意のURLを設定することも可能です。（その場合、以下で設定したURLが優先的に表示されます。）<br />\n";
	$res .= "左から「記事タイトル」「記事URL」を入力して下さい。<br />リンクを新ウィンドウで開きたい場合は、右のチェックボックスにチェックを入れて下さい。</p>\n";
	$res .= "<ol class=\"keni_relation_lists\">\n";

	foreach ($relation as $no => $val) {
		$res .= "<li>\n";
		$res .= "<span class=\"keni_relation_title\"><input type=\"text\" name=\"relation[".$no."][title]\" value=\"".esc_html($val['title'])."\" placeholder=\"記事タイトル\" size=\"32\" /></span>\n";
		$res .= "<span class=\"keni_relation_url\"><input type=\"text\" name=\"relation[".$no."][url]\" value=\"".esc_html($val['url'])."\" placeholder=\"記事URL\" size=\"55\" /></span>\n";
		if ($val['blank'] == "y") {
			$res .= "<span class=\"keni_relation_blank\"><input type=\"checkbox\" name=\"relation[".$no."][blank]\" value=\"y\" checked=\"checked\" /></span>\n";
		} else {
			$res .= "<span class=\"keni_relation_blank\"><input type=\"checkbox\" name=\"relation[".$no."][blank]\" value=\"y\" /></span>\n";
		}
		$res .= "</li>\n";
	}
	$res .= "</ol>\n";
	
	echo $res;
}


function save_relation_string($post_id) {

	if (isset($_POST['relation'])) {

		// データを取得
		$relation_data = get_post_meta( $post_id, 'relation', true);
		if (!empty($relation_data)) { 
			$relation_lies = explode("\n", $relation_data);		// 改行で区切る
			foreach ($relation_lies as $no => $relation_line) {
				$line_array = explode("\t", $relation_line);	// タブで区切る
				if (isset($line_array[0]) && trim($line_array[0]) != "") {
					$relation[$no]['title'] = $line_array[0];
					$relation[$no]['url'] = $line_array[1];
					$relation[$no]['blank'] = $line_array[2];
					$relation[$no]['image'] = $line_array[3];
				}
			}
		}

		$relation = $update_relation_data = "";
		foreach ($_POST['relation'] as $no => $val) {
			if (!isset($val['blank'])) $val['blank'] = "";
			if (!isset($val['image'])) $val['image'] = "";
			if ($val['url'] != "" && (!isset($relation[$no]['image']) || ($relation[$no]['image'] == ""))) {
				if (function_exists('curl_init')) {
					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.63 Safari/537.36" );
					curl_setopt( $ch, CURLOPT_URL, $val['url'] );
					curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
					curl_setopt( $ch, CURLOPT_ENCODING, "" );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
					curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
					curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
					curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
					curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
					curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
					$content = @curl_exec( $ch );
					$response = curl_getinfo( $ch );
					curl_close ( $ch );
					if ($response['http_code'] != 200) $content = @file_get_contents($val['url']);
				} else {
					$content = @file_get_contents($val['url']);
				}
				if ($content != "") {
					preg_match_all('/og:image.*?content="(http.*?[gif|jpg|jpeg|png])"/u', $content, $images);
					if (!isset($images[1][0])) preg_match_all('/itemprop.*?image.+?content="(http.+?[gif|jpg|jpeg|png])" \/>/u', $content, $images);
					$val['image'] = (isset($images[1][0]) && !empty($images[1][0])) ? $images[1][0] : "";
				} else {
					$val['title'] = "";
					unset($_POST['relation'][$no]);
				}
			}

			if (($val['title'] != "") and ($val['url'] != "")) $update_relation_data .= $val['title']."\t".$val['url']."\t".$val['blank']."\t".$val['image']."\n";
		}

		update_post_meta( $post_id, 'relation', $update_relation_data);
	}


	if (isset($_POST['category_relation'])) {
		update_post_meta( $post_id, 'category_relation', "y");
	} else {
		delete_post_meta( $post_id, 'category_relation');
	}

	if (isset($_POST['tag_relation'])) {
		update_post_meta( $post_id, 'tag_relation', "y");
	} else {
		delete_post_meta( $post_id, 'tag_relation');
	}
}



//---------------------------------------------------------------------------
//	関連記事を表示する関数
//---------------------------------------------------------------------------

function relation_keni() {
	echo get_relation_keni();
}


function get_relation_keni() {
	$relation = "";
	$link_count = 0;

	if(!is_home() && !is_front_page()) {
		$relation_data = get_post_meta(get_the_ID(),'relation', true);
		if ($relation_data != "") {
			$relation_lies = explode("\n", $relation_data);			// 改行で区切る
			foreach ($relation_lies as $no => $relation_line) {
				$line_array = explode("\t", $relation_line);// タブで区切る
				if (trim($line_array[0]) != "") {
					if (isset($line_array[2]) && $line_array[2] == "y") {
						$image =  ($line_array[3] != "") ? "<div class=\"related-thumb\"><a href=\"".$line_array[1]."\" title=\"".esc_attr($line_array[0])."\" target=\"_blank\"><img src=\"".$line_array[3]."\" class=\"relation-image\"></a></div>" : "";
						$relation .= "<li>".$image."<p><a href=\"".$line_array[1]."\" title=\"".esc_attr($line_array[0])."\" target=\"_blank\">".esc_attr($line_array[0])."</a></p></li>\n";
					} else {
						$image =  ($line_array[3] != "") ? "<div class=\"related-thumb\"><a href=\"".$line_array[1]."\" title=\"".esc_attr($line_array[0])."\"><img src=\"".$line_array[3]."\" class=\"relation-image\"></a></div>" : "";
						$relation .= "<li>".$image."<p><a href=\"".$line_array[1]."\" title=\"".esc_attr($line_array[0])."\">".esc_attr($line_array[0])."</a></p></li>\n";
					}
					$link_count++;
				}
			}
		}
	}

	
	if ($link_count < 5) {

		// カテゴリから取得する	
		$category_relation = get_post_meta(get_the_ID(), 'category_relation', true);
		if (isset($category_relation) && $category_relation == "y") {
			$target_category = get_the_category(get_the_ID());
			if (isset($target_category) && is_array($target_category)  && count($target_category) > 0) {
				foreach ($target_category as $cat_val) {
					$cat_list[] = $cat_val->cat_ID;
				}
			}

			if (isset($cat_list) && count($cat_list) > 0) {
				$args = array( 'posts_per_page' => 5,'category' => implode(",", $cat_list), 'orderby' => 'rand', 'exclude' => get_the_ID());
				$rand_posts = get_posts( $args );
				if (count($rand_posts) > 0) {
					foreach ($rand_posts as $cat_posts) {
						if ($link_count < 5) {
							$image = (get_the_post_thumbnail($cat_posts->ID)) ? "<div class=\"related-thumb\"><a href=\"".get_permalink($cat_posts->ID)."\" title=\"".esc_attr($cat_posts->post_title)."\">".get_the_post_thumbnail($cat_posts->ID, 'ss_thumb', array('class' => 'relation-image', 'alt' => esc_attr($cat_posts->post_title)))."</a></div>" : "";
							$relation .= "<li>".$image."<p><a href=\"".get_permalink($cat_posts->ID)."\" title=\"".esc_attr($cat_posts->post_title)."\">".esc_attr($cat_posts->post_title)."</a></p></li>\n";
							$link_count++;
						}
					}
				}
			}
		}
	}


	if ($link_count < 5) {

		// タグから取得する	
		$tag_relation = get_post_meta(get_the_ID(), 'tag_relation', true);
		if (isset($tag_relation) && $tag_relation == "y") {
			$target_tags= get_the_tags();
			if (isset($target_tags) && is_array($target_tags) && count($target_tags) > 0) {
				foreach ($target_tags as $tag_val) {
					$tag_list[] = $tag_val->term_id;
				}
			}

			if (isset($tag_list) && count($tag_list) > 0) {
				query_posts(array('post_type' => array('post', 'page'), 'tag__in' => $tag_list, 'showposts' => 5, 'post__not_in' => array(get_the_ID())));
				if (have_posts()) : while(have_posts()) : the_post();
					if ($link_count < 5) {
						$image = (get_the_post_thumbnail(get_the_ID())) ? "<div class=\"related-thumb\"><a href=\"".get_permalink(get_the_ID())."\" title=\"".esc_html(get_the_title())."\">".get_the_post_thumbnail(get_the_ID(), 'ss_thumb', array('class' => 'relation-image', 'alt' => esc_html(get_the_title())))."</a></div>" : "";
						$relation .= "<li>".$image."<a href=\"".get_permalink()."\" title=\"".esc_html(get_the_title())."\">".esc_html(get_the_title())."</a></p></li>\n";
						$link_count++;
					}
					endwhile;endif;
				}
			}
			wp_reset_query();
	}

	return (!empty($relation)) ? "<div class=\"contents related-articles related-articles-thumbs01\">\n<h2 id=\"keni-relatedposts\">".sprintf( __('Related Posts','keni'))."</h2>\n<ul class=\"keni-relatedposts-list\">\n".$relation."</ul>\n</div>\n" : "";
}



//---------------------------------------------------------------------------
//	管理画面上でのレイアウトの指定
//---------------------------------------------------------------------------
$layout = array("def" => "共通設定を適用",
								"col1" => "1カラム",
								"col2" => "2カラム",
								"col2r" => "2カラムリバース",
								);


add_action('admin_menu', 'add_layout_custom_box');

function add_layout_custom_box() {
	add_meta_box('page_layout', 'レイアウト', 'layout_setting', 'post', 'side', 'low');
	if (!isset($_GET['post']) || $_GET['post'] != get_option( 'page_on_front' )) add_meta_box('page_layout', 'レイアウト', 'layout_setting', 'page', 'side', 'low');
}
 
function layout_setting() {

	// レイアウトの指定
	global $layout;

	if (isset($_GET['post'])) {
		$post_layout = get_post_meta( $_GET['post'], 'page_layout', true);
		if (empty($post_layout)) $post_layout = "def";
	} else {
		$post_layout = "def";
	}

	$view_layout = "<table>\n<tr>\n<td>カラム数：</td>\n<td>\n<select name=\"page_layout\">\n";
	foreach ($layout as $type => $view) {
		if ($type == $post_layout) {
			$view_layout .= "<option value=\"".$type."\" selected=\"selected\" >".$view."</option>\n";
		} else {
			$view_layout .= "<option value=\"".$type."\" >".$view."</option>\n";
		}
	}
	$view_layout .= "</select>\n</td>\n</tr>\n";
	echo $view_layout;

	// メニューバーの表示・非表示
	if (isset($_GET['post'])) {
		$menu_view = get_post_meta( $_GET['post'], 'menu_view', true);
		if (empty($menu_view)) $menu_view =  "y";
	} else {
		$menu_view = "y";
	}
	$view_menubar = "<tr>\n<td>" . __('Menu', 'keni') . "：</td>\n<td>\n";
	if ($menu_view == "n") {
		$view_menubar .= "<input type=\"checkbox\" name=\"menu_view\" value=\"n\" id=\"menu_view\" checked=\"checked\" />";
	} else {
		$view_menubar .=  "<input type=\"checkbox\" name=\"menu_view\" value=\"n\" id=\"menu_view\" />";
	}
	$view_menubar .=  "<label for=\"menu_view\">表示しない</label>\n</td>\n</tr>\n";

	echo $view_menubar;


	// サブコンテンツ（サイドバー）の表示・非表示
	if (isset($_GET['post'])) {
		$side_bar = get_post_meta( $_GET['post'], 'side', true);
		if (empty($side_bar)) $side_bar = "y";
	} else {
		$side_bar = "y";
	}

	$side_var = "<tr>\n<td>サブコンテンツ：</td>\n<td>\n";
	if ($side_bar == "n") {
		$side_var .= "<input type=\"checkbox\" name=\"side\" value=\"n\" id=\"side\" checked=\"checked\" />";
	} else {
		$side_var .=  "<input type=\"checkbox\" name=\"side\" value=\"n\" id=\"side\" />";
	}
	$side_var .=  "<label for=\"side\">表示しない</label><span class=\"keni_note\">（※1カラム時のみ有効）</span></table>\n";

	echo $side_var;

}
 
//---------------------------------------------------------------------------
//	レイアウト設定の保存
//---------------------------------------------------------------------------

function save_custom_field_postdata($post_id) {
	if (isset($_POST['page_layout'])) {
		update_post_meta( $post_id, 'page_layout', $_POST['page_layout']);
	}
	if (isset($_POST['menu_view'])) {
		update_post_meta( $post_id, 'menu_view', "n");
	} else {
		update_post_meta( $post_id, 'menu_view', "y");
	}
	if (isset($_POST['side'])) {
		update_post_meta( $post_id, 'side', "n");
	} else {
		update_post_meta( $post_id, 'side', "y");
	}
	if (isset($_POST['fullscreen_view'])) {
		update_post_meta( $post_id, 'fullscreen_view', "y");
	} else {
		update_post_meta( $post_id, 'fullscreen_view', "n");
	}
}



//---------------------------------------------------------------------------
//	ランディングページ用レイアウト設定
//---------------------------------------------------------------------------
add_action('admin_menu', 'add_lp_layout_custom_box');
 
function add_lp_layout_custom_box() {
	add_meta_box('page_layout', 'レイアウト', 'lp_layout_setting', LP_DIR, 'side', 'low');
}
 

function lp_layout_setting() {
	// メニューバーの表示・非表示
	if (isset($_GET['post'])) {
		$menu_view = get_post_meta( $_GET['post'], 'menu_view', true);
		if (empty($menu_view)) $menu_view =  "y";
	} else {
		$menu_view = "y";
	}
	$view_menubar = "<table>\n<tr>\n<td>" . __('Menu', 'keni') . "：</td>\n<td>\n";
	if ($menu_view == "n") {
		$view_menubar .= "<input type=\"checkbox\" name=\"menu_view\" value=\"n\" id=\"menu_view\" checked=\"checked\" />";
	} else {
		$view_menubar .=  "<input type=\"checkbox\" name=\"menu_view\" value=\"n\" id=\"menu_view\" />";
	}
	$view_menubar .=  "<label for=\"menu_view\">表示しない</label>\n</td>\n</tr>\n";

	echo $view_menubar;


	// サブコンテンツ（サイドバー）の表示・非表示
	if (isset($_GET['post'])) {
		$side_bar = get_post_meta( $_GET['post'], 'side', true);
		if (empty($side_bar)) $side_bar = "y";
	} else {
		$side_bar = "y";
	}

	$side_var = "<tr>\n<td>サブコンテンツ：</td>\n<td>";
	if ($side_bar == "n") {
		$side_var .= "<input type=\"checkbox\" name=\"side\" value=\"n\" id=\"side\" checked=\"checked\" />";
	} else {
		$side_var .=  "<input type=\"checkbox\" name=\"side\" value=\"n\" id=\"side\" />";
	}
	$side_var .=  "<label for=\"side\">表示しない</label></td></tr>\n";

	echo $side_var;


	// サブコンテンツ（サイドバー）の表示・非表示
	if (isset($_GET['post'])) {
		$fullscreen_view = get_post_meta( $_GET['post'], 'fullscreen_view', true);
		if (empty($fullscreen_view)) $fullscreen_view = "n";
	} else {
		$fullscreen_view = "n";
	}


	$fullscreen_var = "<tr>\n<td>フルスクリーン表示：</td>\n<td>";
	if ($fullscreen_view == "y") {
		$fullscreen_var .= "<input type=\"checkbox\" name=\"fullscreen_view\" value=\"y\" id=\"fullscreen_view\" checked=\"checked\" />";
	} else {
		$fullscreen_var .=  "<input type=\"checkbox\" name=\"fullscreen_view\" value=\"y\" id=\"fullscreen_view\" />";
	}
	$fullscreen_var .=  "<label for=\"fullscreen_view\">する</label></td></tr>\n</table>\n";


	echo $fullscreen_var;
}


//---------------------------------------------------------------------------
//	管理画面上でのindex/followの指定
//---------------------------------------------------------------------------

$index_area = array("index" => array("index" => "index",
																		 "noindex" => "noindex"),
										"follow" => array("follow" => "follow",
																			"nofollow" => "nofollow")
									);

$index_checkbox = array("index" => "noindexにする",
												"follow" => "nofollowにする");


$index_pulldown = array("index" => "index",
												"noindex" => "noindex",
												"noindex_p2" => "2ページ目以降はnoindex"
												);

// index_menu
$index_menu['def'] = "共通設定を適用";
foreach ($index_area['index'] as $val) {
	$index_menu[$val] = $val;
}

$index_list_menu['def'] = "共通設定を適用";
foreach ($index_pulldown as $no => $val) {
	$index_list_menu[$no] = $val;
}

// follow_menu
$follow_menu['def'] = "共通設定を適用";
foreach ($index_area['follow'] as $val) {
	$follow_menu[$val] = $val;
}


add_action('admin_menu', 'add_index_area');

function add_index_area() {
	add_meta_box('index_area', 'インデックス/フォロー', 'index_setting', 'post', 'side', 'high');
	add_meta_box('index_area', 'インデックス/フォロー', 'index_setting', 'page', 'side', 'high');
	add_meta_box('index_area', 'インデックス/フォロー', 'index_setting', LP_DIR, 'side', 'high');
}
 
 
function index_setting() {

	global $index_checkbox;
	
	foreach ($index_checkbox as $type => $view) {
		$sel_status = isset($_GET['post']) ? get_post_meta( $_GET['post'], $type, true) : "";
		if (preg_match("/^no/",$sel_status)) {
			echo "<input type=\"checkbox\" name=\"".$type."\" value=\"no".$type."\" id=\"".$type."\" checked=\"checked\" />";
		} else {
			echo "<input type=\"checkbox\" name=\"".$type."\" value=\"no".$type."\" id=\"".$type."\" />";
		}
		echo "<label for=\"".$type."\">".$view."</label><br />\n";
	}
}


function save_index_postdata($post_id) {
	global $index_checkbox;
	global $index_area;

	foreach ($index_checkbox as $type => $val) {
		$flug = (isset($_POST[$type]) && preg_match("/^no/", $_POST[$type])) ? end($index_area[$type]) : reset($index_area[$type]);
		update_post_meta( $post_id, $type, $flug);
	}
}



//---------------------------------------------------------------------------
//	管理画面上にサイトタイトルを表示するかどうかのチェック項目を設ける
//---------------------------------------------------------------------------
add_action('admin_menu', 'add_title_view_area');

function add_title_view_area() {
	add_meta_box('contents_area', 'サイトタイトルの表示', 'view_title_setting', 'post', 'side', 'low');
	if (!isset($_GET['post']) || $_GET['post'] != get_option( 'page_on_front' )) add_meta_box('contents_area', 'サイトタイトルの表示', 'view_title_setting', 'page', 'side', 'low');
	add_meta_box('contents_area', 'サイトタイトルの表示', 'view_title_setting', LP_DIR, 'side', 'low');
}

 
function view_title_setting() {
	$title_view =  (isset($_GET['post'])) ? get_post_meta( $_GET['post'], "title_view", true) : "y";
	if ($title_view == "n") {
		echo "<input type=\"checkbox\" name=\"title_view\" value=\"n\" id=\"title_view\" checked=\"checked\" \><label for=\"title_view\">&nbsp;表示しない</label>\n";
	} else {
		echo "<input type=\"checkbox\" name=\"title_view\" value=\"n\" id=\"title_view\" \><label for=\"title_view\">&nbsp;表示しない</label>\n";
	}
}

 
function save_title_view($post_id) {
	$flug = (isset($_POST['title_view'])) ? "n" : "y";
	update_post_meta( $post_id, "title_view", $flug);
}




//---------------------------------------------------------------------------
//	管理画面上にPVランキングの対象にするかどうかのチェック項目を設ける
//---------------------------------------------------------------------------

add_action('admin_menu', 'add_pv_disable_area');

function add_pv_disable_area() {
	add_meta_box('pv_disable_area', 'PVランキングから除外', 'view_pv_setting', 'post', 'side', 'low');
	add_meta_box('pv_disable_area', 'PVランキングから除外', 'view_pv_setting', 'page', 'side', 'low');
	add_meta_box('pv_disable_area', 'PVランキングから除外', 'view_pv_setting', LP_DIR, 'side', 'low');
}

 
function view_pv_setting() {
	$pv_disable = (isset($_GET['post'])) ? get_post_meta( $_GET['post'], "pv_disable", true) : "";
	if (!empty($pv_disable) && $pv_disable == "y") {
		echo "<input type=\"checkbox\" name=\"pv_disable\" value=\"y\" id=\"pv_disable\" checked=\"checked\" \><label for=\"pv_disable\">&nbsp;除外する</label>\n";
	} else {
		echo "<input type=\"checkbox\" name=\"pv_disable\" value=\"y\" id=\"pv_disable\" \><label for=\"pv_disable\">&nbsp;除外する</label>\n";
	}
}

 
function save_pv_disable($post_id) {
	if (isset($_POST['pv_disable'])) {
		update_post_meta( $post_id, 'pv_disable', "y");
	} else {
		delete_post_meta( $post_id, 'pv_disable');
	}
}



//---------------------------------------------------------------------------
//	管理画面上での個別タグエリアの指定
//---------------------------------------------------------------------------
add_action('admin_menu', 'add_tags_area');

function add_tags_area() {	
	add_meta_box('page_tags', 'この投稿だけの個別CSS／JS記述欄', 'tags_setting', 'post', 'normal', 'low');
	add_meta_box('page_tags', 'この投稿だけの個別CSS／JS記述欄', 'tags_setting', 'page', 'normal', 'low');
	add_meta_box('page_tags', 'この投稿だけの個別CSS／JS記述欄', 'tags_setting', LP_DIR, 'normal', 'low');
}


function tags_setting() {
	if (isset($_GET['post'])) {
		$page_tags = get_post_meta( $_GET['post'], 'page_tags', true);
	} else {
		$page_tags = "";
	}

	$res = "<p class=\"keni_note\">※ 記述された内容がそのまま出力されます。<br />\nセキュリティなどに関して、細心の注意をはらって記述をして頂きますようお願い致します。<br />また、記述された内容がそのまま出力されますので、CSSであれば&lt;style&gt;タグ、JavaScriptの場合は&lt;script&gt;タグが必要です。</p>\n";
	$res .= "<textarea class=\"keni_h1_textarea\" name=\"page_tags\" cols=\"100\" rows=\"10\">".esc_html($page_tags)."</textarea>\n";
	echo $res;
}


function save_tags_string($post_id) {
	if (isset($_POST['page_tags'])) {
		update_post_meta( $post_id, 'page_tags', $_POST['page_tags']);
	}
}



//---------------------------------------------------------------------------
//	賢威のSEOチェックファイルを読み込み
//---------------------------------------------------------------------------
add_action('admin_head', 'keni_seo_check');

function keni_seo_check() {
	get_template_part("module/keni_seo_check");
}




//---------------------------------------------------------------------
// ポストIDに設定したレイアウトを取得
//---------------------------------------------------------------------
function getPageLayout($post_id) {

	$post_layout = $top_layout = "";

	if(is_front_page()) {
		$top_layout = the_keni('top_layout');
		if (empty($top_layout)) {
			$top_layout = "col2";
		} else if ($top_layout == "def") {
			$top_layout = the_keni('layout');
		}
		return $top_layout;
	} else if(is_home() and get_option('page_for_posts') > 1) {
		$post_layout = get_post_meta(get_option('page_for_posts'),'page_layout', true);
		if (($post_layout == "def") || empty($post_layout)) $post_layout = the_keni('layout');
	} elseif (is_category()) {
		$post_layout = get_post_meta( get_query_var('cat'), "layout", true);
		if (($post_layout == "def") || empty($post_layout)) $post_layout = the_keni('list_category');
	} elseif (is_tag()) {
		$post_layout = get_post_meta( get_query_var('tag_id'), "layout", true);
		if (($post_layout == "def") || empty($post_layout)) $post_layout = the_keni('list_tag');
	} elseif (is_search()) {
		$post_layout = the_keni('list_search');
	} elseif (is_author()) {
		$post_layout = the_keni('list_author');
	} elseif (is_archive()) {
		$post_layout = the_keni('list_archive');
	} else if (get_post_type() == LP_DIR) {
		$post_layout = "col1";
	} else {
		$post_layout = get_post_meta($post_id,'page_layout', true);
		if (($post_layout == "def") || empty($post_layout)) $post_layout = the_keni('layout');
	}

	if (empty($post_layout)) {
		$post_layout = "col2";
	} else if ($post_layout == "def") {
		$post_layout = the_keni('layout');
	}

	return $post_layout;
}


//---------------------------------------------------------------------------
// ポストIDに設定したレイアウトを表示する
//---------------------------------------------------------------------------
function pageLayoutView($post_id) {
	global $layout;
	if (is_404()) {
		echo the_keni('layout');
	} else {
		$post_layout = getPageLayout($post_id);
		echo $layout[$post_layout];
	}
}


//---------------------------------------------------------------------------
// 対象ページに設定されているレイアウトを返却
//---------------------------------------------------------------------------
function keni_layout($post_id) {
	if(is_home() or is_front_page()) {
		$res = get_body_class(the_keni('top_layout'));
	} else if (isset($post_id)) {
		$res = get_body_class(getPageLayout($post_id));
	} else {
		$res = 	get_body_class(the_keni('layout'));
	}
	return $res;
}



//---------------------------------------------------------------------------
// 検索をした際に、対象の投稿が存在しなかった場合、404を返すようにする
//---------------------------------------------------------------------------
function redirect_404() {
	if (is_search() && !have_posts()) {
		global $wp_query;
		$wp_query->set_404();
		status_header(404);
		nocache_headers();
	}
}
add_action( 'wp', 'redirect_404' );



//---------------------------------------------------------------------------
//	データベースに登録されている内容を取得
//---------------------------------------------------------------------------
function getKeniSetting() {
	global $wpdb;

	$table_alive = $wpdb->get_results("SHOW TABLES LIKE '".KENI_SET."'");
	if (isset($table_alive) and count($table_alive) > 0) {
		$sql = "SELECT * FROM ".KENI_SET." WHERE ks_active='y' ORDER BY ks_sort";
		$res = $wpdb->get_results($sql , ARRAY_A);
		foreach ($res as $val) {
			$ks_id = $val['ks_id'];
			unset($val['ks_id']);
			$list[$ks_id] = $val;
		}
		return $list;
	}
	return array();
}


//---------------------------------------------------------------------------
//	データベースに登録されている特定メニューの内容を取得
//---------------------------------------------------------------------------
function getKeniMenuSetting($key) {
	global $wpdb;

	if ($key == "keni_admin_menu") $key = "サイト内共通";

	$table_alive = $wpdb->get_results("SHOW TABLES LIKE '".KENI_SET."'");
	if (isset($table_alive) and count($table_alive) > 0) {
		$sql = "SELECT * FROM ".KENI_SET." WHERE ks_active='y' AND ks_group='".$wpdb->escape(urldecode($key))."' ORDER BY ks_sort";
		$res = $wpdb->get_results($sql , ARRAY_A);
		foreach ($res as $val) {
			$ks_id = $val['ks_id'];
			unset($val['ks_id']);
			$list[$ks_id] = $val;
		}
		return $list;
	}
	return array();
}



//---------------------------------------------------------------------------
//	データベースに登録されている個別の項目情報を取得
//---------------------------------------------------------------------------
function the_keni($val="") {
	$res = "";	
	if ($val != "") {
		global $wpdb;
		$table_alive = $wpdb->get_results("SHOW TABLES LIKE '".KENI_SET."'");
		if (isset($table_alive) and count($table_alive) > 0) {
			$res = $wpdb->get_var($wpdb->prepare("SELECT ks_val FROM ".KENI_SET." WHERE ks_sys_cont=%s AND ks_active='y'", $val));
		}
	}
	return $res;
}


//---------------------------------------------------------------------------
// カテゴリディレクトリの取得
//---------------------------------------------------------------------------
function get_category_dir() {
	$category_dir = get_option( 'category_base' );
	if (empty($category_dir)) {
		$category_dir = "/category";
	} else if ($category_dir == ".") {
		$category_dir = "/";
	}
	return $category_dir;
}



//---------------------------------------------------------------------------
// タグディレクトリの取得
//---------------------------------------------------------------------------
function get_tag_dir() {
	$tag_dir = get_option( 'tag_base' );
	if (empty($tag_dir)) {
		$tag_dir = "/tag";
	} else if ($tag_dir == ".") {
		$tag_dir = "/";
	}
	return $tag_dir;
}



//---------------------------------------------------------------------------
//	投稿一覧に、項目を追加する
//---------------------------------------------------------------------------
function manage_posts_columns($columns) { 		
		if (isset($_GET['post_type']) and ($_GET['post_type'] == "keni_cc")) {
			$columns['postid'] = "ショートコード";
		} else {
			$columns['column'] = "カラム数";
			$columns['h1'] = "h1";
			$columns['thumbnail'] = "アイキャッチ画像";
		}
		return $columns;
}
function add_column($column_name, $post_id) {
	if ($column_name == 'postid') {
		echo "[cc id=".$post_id."]";
	} else if ($column_name == 'column') { 
		pageLayoutView($post_id);
	} else if ($column_name == 'h1') { 
		echo get_h1_keni($post_id);
	} else if ($column_name == 'thumbnail') {
		$thumbnail_id = get_post_thumbnail_id($post_id);
		$image = wp_get_attachment_image_src($thumbnail_id, "thumbnail");
		if (isset($image[0])) {
			echo '<img src="'.$image[0].'" />';
		} else {
			echo "&#8212;";
		}
	} else if  ( $column_name == 'socialimg' ) {
		echo '<input id="upload_image" type="text" size="36" name="Cat_meta[img]" value="<?php if(isset ( $cat_meta[\'img\'])) echo esc_html($cat_meta[\'img\']) ?>" /><br />';
    echo '画像を追加: <img src="images/media-button-other.gif" alt="画像を追加"  id="upload_image_button" value="Upload Image" style="cursor:pointer;" />';
	}
}




//---------------------------------------------------------------------------
//	投稿画面からフォーマットの項目を非表示にする
//---------------------------------------------------------------------------
function remove_post_metaboxes() {
	remove_meta_box('formatdiv', 'post', 'normal');
}
add_action('admin_menu', 'remove_post_metaboxes');


//---------------------------------------------------------------------------
//	ページャーを表示する
//---------------------------------------------------------------------------
function pager_keni() {

	$pager = "";

	global $wp_query;
	$max_page = $wp_query->max_num_pages;
	$now_page = get_query_var('paged');
	if ($now_page == 0) $now_page = 1;
	if ($max_page > $now_page) $pager .= "<li class=\"page-nav-next\">". get_next_posts_link(__('Next Posts', 'keni')) ."</li>\n";
	if (is_paged()) $pager .= "<li class=\"page-nav-prev\">". get_previous_posts_link(__('Previous Posts', 'keni'))."</li>\n";
	if (!empty($pager)) echo "<div class=\"float-area\">\n<div class=\"page-nav-bf\">\n<ul>\n".$pager."</ul>\n</div>\n</div>\n";
}


//---------------------------------------------------------------------------
//	カテゴリ名を取得
//---------------------------------------------------------------------------
function get_category_keni($id="") {

	$res_data = "";
	$category = (preg_match("/^[0-9]+$/", $id)) ? get_the_category($id) : get_the_category();
	if (is_array($category) and count($category) > 0) {
		foreach ($category as $cat) {
			$category_url = get_category_link($cat->cat_ID);
			$term_data = get_option('term_'.$cat->cat_ID);
			$term_bgcolor = (empty( $term_data['bgcolor'])) ? '#666' : $term_data['bgcolor'];
			$term_txcolor = (empty( $term_data['textcolor'])) ? '#fff' : $term_data['textcolor'];
			$res_data .= "<span class=\"cat cat".sprintf("%03d",$cat->cat_ID)."\" style=\"background-color: ".esc_attr($term_bgcolor).";\"><a href=\"".$category_url."\" style=\"color: ".esc_attr($term_txcolor).";\">".esc_attr($cat->cat_name)."</a></span>\n";
		}
	}
	return $res_data;
}



//---------------------------------------------------------------------------
//	カテゴリ/タグにテキストエリアを設置
//---------------------------------------------------------------------------		
function getIndexFollow() {

	$index = "index";
	$follow = "follow";

	$the_page = pageNumber();

	if ((is_home() and get_query_var('paged') > 1) || (is_front_page() && $the_page['now_page'] > 1)) {

		if (get_option('page_on_front') > 0 && get_post_meta(get_the_ID(),'index', true) == "noindex") {
			$index = "noindex";
		} else {
			$index = (the_keni("snd_page_index") == "y") ? "index" : "noindex";
		}

		$follow = (get_option('page_on_front') > 0 && get_post_meta(get_the_ID(),'follow', true) == "nofollow") ? "nofollow" : "follow";

	} else if (is_singular('keni_cc') || get_post_type() == "keni_cc") {	// 共通コンテンツはnoindexにする
		$index = "noindex";	

	} else if (is_attachment()) {
		$index = "noindex";

	} else if (is_singular()) {
		$page_index = get_post_meta(get_the_ID(), 'index', true);
		if (!empty($page_index)) $index = $page_index;
		$page_follow = get_post_meta(get_the_ID(), 'follow', true);
		if (!empty($page_follow)) $follow = $page_follow;

	} else if (is_category()) {
		$index = get_post_meta( get_query_var('cat'), "meta_index", true);
		if ($index == "def" || empty($index)) $index = the_keni("list_category_index");

	} else if (is_tag()) {
		$index = get_post_meta( get_query_var('tag_id'), "meta_index", true);
		if ($index == "def" || empty($index)) $index = the_keni("list_tag_index");

	} else {
		if (is_author()) {
			$index = the_keni("list_author_index");
		} else if (is_search()) {
			$index = the_keni("list_search_index");		
		} else if (is_archive()) {
			$index = the_keni("list_archive_index");
		}
	}

	if ($index == "noindex_p2") {	// 2ページ目は noindexの場合
		$index = (get_query_var('paged') > 1) ? "noindex" : "index";
	}


	// 最終的に出力される内容を制御
	if ($index == "index" && $follow == "follow") {
		$meta_index = "";
	} else if ($index == "noindex" && $follow == "follow") {
		$meta_index = "<meta name=\"robots\" content=\"". $index."\" />\n";
	} else if ($index == "index" && $follow == "nofollow") {		
		$meta_index = "<meta name=\"robots\" content=\"". $follow."\" />\n";
	} else {
		$meta_index = "<meta name=\"robots\" content=\"". $index.",".$follow."\" />\n";
	}
	return $meta_index;
}



//---------------------------------------------------------------------------
//	カテゴリ/タグにテキストエリアを設置
//---------------------------------------------------------------------------		
add_action('category_add_form_fields', 'category_tag_add_form');
add_action('post_tag_add_form_fields', 'category_tag_add_form');

add_action('category_edit_form_fields', 'category_tag_edit_form');
add_action('post_tag_edit_form_fields', 'category_tag_edit_form');

add_action('created_term', 'insert_category_contents');
add_action('edit_term', 'update_category_contents');


function category_tag_add_form(){

	global $layout;
	global $index_menu;
	global $index_list_menu;
	global $follow_menu;
?>
	<div class="form-field">
	<label for="layout">レイアウト</label>
	<select name='layout' id='layout' class='postform' >
	<?php foreach ($layout as $key => $val) {
		if ( $layout_val == $key) { ?>
			<option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
<?php } else { ?>
			<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php }
		} ?>
		</select>
	</div>

	<div class="form-field">
	<label for="index">インデックス</label>
	<select name='meta_index' id='meta_index' class='postform' >
	<?php foreach ($index_list_menu as $key => $val) {
		if ( $index == $key) { ?>
			<option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
<?php } else { ?>
			<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php }
	} ?>
	</select>
	</div>
	<div class="form-field">
	<label for="follow">ページ タイトル</label>
	<input type="text" name="title" id="title" class="regular-text postform" value="" />
	</div>

	<div class="form-field">
	<label for="content">ページコンテンツ</label>
	<?php wp_editor('', "content", array('editor_css' => keni_rte_css())); ?>
	</div>
	<?php
}


function category_tag_edit_form(){

	global $tag_ID;
	global $layout;
	global $index_menu;
	global $index_list_menu;
	global $follow_menu;

	$layout_val = get_post_meta( $tag_ID, "layout", true);	// レイアウト
	$title = get_post_meta( $tag_ID, "title", true);	// タイトル
	$content = get_post_meta( $tag_ID, "content", true);	// コンテンツ
	$index = get_post_meta( $tag_ID, "meta_index", true);	// index	
	$follow = get_post_meta( $tag_ID, "meta_follow", true);	// follow
?>
	<style type="text/css">
		.quicktags-toolbar input { width:auto!important; }
		.wp-editor-area {border: none!important;}
	</style>
	<tr>
		<th scope="row" valign="top">レイアウト</th>
		<td><select name='layout' id='layout' class='postform' >
		<?php foreach ($layout as $key => $val) {
			if ( $layout_val == $key) { ?>
				<option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
<?php } else { ?>
				<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php }
		} ?>
		</select></td>
	</tr>

	<tr>
		<th scope="row" valign="top">インデックス</th>
		<td><select name='meta_index' id='meta_index' class='postform' >
		<?php foreach ($index_list_menu as $key => $val) {
			if ( $index == $key) { ?>
				<option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
<?php } else { ?>
				<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
<?php }
		} ?>
		</select></td>
	</tr>
	<tr>
	<th scope="row" valign="top"><label for="content">ページ タイトル</label></th>
	<td><input type="text" name="title" id="title" class="regular-text postform" value="<?php echo $title; ?>" /></td>
	</tr>

	<tr>
	<th scope="row" valign="top"><label for="content">ページコンテンツ</label></th>
	<td><?php wp_editor($content, "content", array('textarea_name' => "content", 'editor_css' => keni_rte_css())); ?></td>
	</tr>
	<?php
}

	

function keni_rte_css() {
	return '
	<style type="text/css">
		.wp-editor-container .quicktags-toolbar input.ed_button {
			width:auto;
		}
		.html-active .wp-editor-area { border:0;}
	</style>';
}




function insert_category_contents($term_id) {
	if (($term_id > 0) and isset($_POST['taxonomy'])) {
		if (isset($_POST['layout'])) add_metadata('post', $term_id, "layout", $_POST['layout'], true);

		$meta_index = get_post_meta( $_POST['tag_ID'], "meta_index", true);
		if (empty($meta_index)) add_metadata('post', $term_id, "meta_index", $_POST['meta_index'], true);
		
		if (isset($_POST['meta_follow'])) add_metadata('post', $term_id, "meta_follow", $_POST['meta_follow'], true);
		if (isset($_POST['title'])) add_metadata('post', $term_id, "title", $_POST['title'], true);
		if (isset($_POST['content'])) add_metadata('post', $term_id, "content", $_POST['content'], true);
	}
}

function update_category_contents() {
	
	if (isset($_POST['tag_ID']) and isset($_POST['taxonomy'])) {

		// レイアウト
		$layout = get_post_meta( $_POST['tag_ID'], "layout", true);
		if (!empty($layout)) $def_layout_val = $layout;
	
		// タイトル
		$title = get_post_meta( $_POST['tag_ID'], "title", true);
		if (!empty($title)) $def_title = $title;
	
		// コンテンツ
		$content = get_post_meta( $_POST['tag_ID'], "content", true);
		if (!empty($content)) $def_content = $content;
	
		// インデックス
		$index = get_post_meta( $_POST['tag_ID'], "meta_index", true);
		if (!empty($index)) $def_index = $index;

		if (isset($_POST['layout'])) {
			update_metadata('post', $_POST['tag_ID'], "layout", $_POST['layout'], $def_layout_val);
		}
		if (isset($_POST['meta_index'])) {
			update_metadata('post', $_POST['tag_ID'], "meta_index", $_POST['meta_index'], $def_index);
		}
		if (isset($_POST['title'])) {
			update_metadata('post', $_POST['tag_ID'], "title", $_POST['title'], $def_title);
		}
		if (isset($_POST['content'])) {
			update_metadata('post', $_POST['tag_ID'], "content", $_POST['content'], $def_content);
		}
	}
}



//---------------------------------------------------------------------------
// 賢威メニューの表示
//---------------------------------------------------------------------------
function keni_admin_menu() {
  add_menu_page( '賢威 設定メニュー', __('Keni Settings','keni'), 'edit_themes', 'keni_admin_menu', 'viewMenu', '' , 3 );

	$list = getKeniSetting();
	$keni_list = array();

	require_once(get_template_directory()."/module/keni_setting.php");
	if (isset($list) and count($list) > 0) {
		foreach ($list as $no => $list_val) {
			$ks_group = $list_val['ks_group'];
			$keni_list[$ks_group][$no] = $list_val;
		}

		$menu_no = 0;
		foreach ($keni_list as $key => $val) {
			if ($menu_no > 0) $res = add_submenu_page( 'keni_admin_menu', $key, $key, 'edit_themes', $key, 'viewMenu');
			$menu_no++;
		}

		// キャラクタコンテンツエリア
		add_menu_page( 'keni_character_menu', __('Character Setting','keni'), 'edit_theme_options', "keni_character", 'viewMenu', 'dashicons-format-chat' , 9);
		add_submenu_page( 'keni_character', __('Character Registration','keni'), __('Character Registration','keni'), 'edit_theme_options', "keni_character_add", 'viewMenu');
	}

}


//---------------------------------------------------------------------------
// 賢威サポートチームからのお知らせ表示
//---------------------------------------------------------------------------
function add_keni_support_message($screen_id) {
	if ( $screen_id == 'dashboard' ) wp_add_dashboard_widget( 'view_message', __('News from Keni support team','keni'), 'view_message');
}
add_action( 'do_meta_boxes', 'add_keni_support_message' );

function view_message() {
	if (function_exists('simplexml_load_file')) {
		$mon = array("Jan"=>"1", "Feb"=>"2", "Mar"=>"3", "Apr"=>"4", "May"=>"5", "Jun"=>"6", "Jul"=>"7", "Aug"=>"8", "Sep"=>"9", "Oct"=>"10", "Nov"=>"11", "Dec"=>"12");
		$rss = @simplexml_load_file("http://www.keni.jp/news.php");
		$no = 0;
		if (isset($rss->channel->item)) {
			echo "<ul>\n";
			foreach ($rss->channel->item as $item) {
				if ($no < 5) {
					$time = preg_match('/([0-9]{2}) ([A-Z]{1}[a-z]{2}) ([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $item->pubDate, $date);
					$view_date = date("Y年n月j日 H時i分", mktime(($date[4]+9), $date[5], $date[6], $mon[$date[2]], $date[1], $date[3]));
					echo "<li>".$view_date."&nbsp;<a href=\"".$item->link."\" target=\"_blank\">".$item->title."</a></li>\n";
				}
				$no++;
			}
			echo "</ul>\n";
		} else {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, "http://www.keni.jp/news.php" );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 5);
			$res_xml = curl_exec( $ch );
			curl_close( $ch );

			preg_match_all("/<item>.*?<title>(.*?)<\/title>.*?<link>(.*?)<\/link>.*?<pubDate>(.*?)<\/pubDate>.*?<\/item>/us", $res_xml, $info_array, PREG_SET_ORDER);
			if (is_array($info_array) && count($info_array) > 0) {
				$no = 0;
				foreach ($info_array as $no => $item) {
					if ($no < 5) {
						$time = preg_match('/([0-9]{2}) ([A-Z]{1}[a-z]{2}) ([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $item[3], $date);
						$view_date = date("Y年n月j日 H時i分", mktime(($date[4]+9), $date[5], $date[6], $mon[$date[2]], $date[1], $date[3]));
						echo "<li>".$view_date."&nbsp;<a href=\"".$item[2]."\" target=\"_blank\">".$item[1]."</a></li>\n";
					}
					$no++;
				}
				echo "</ul>\n";
			} else {
				echo "<p><span class=\"keni_note\">お使いのサーバでは、phpの制限により、自動取得が出来ないようです。</span><br />定期的に賢威サポートページにログインしてご確認いただきますようお願いいたします。</p>";
			}
		}
	}	else {
		echo "<p><span class=\"keni_note\">お使いのサーバでは、phpの制限により、自動取得が出来ないようです。</span><br />定期的に賢威サポートページにログインしてご確認いただきますようお願いいたします。</p>";
	}
}




//---------------------------------------------------------------------------
// 管理画面に、共通コンテンツエリアを作る
//---------------------------------------------------------------------------
function keni_common_contents() {
	register_post_type( 'keni_cc',
		array(
			'labels' => array(
			'name' => __( 'Common Contents', 'keni' ),
			'singular_name' => __( 'Common Contents', 'keni' )
		),
		'public' => true,
		'menu_icon' => 'dashicons-media-code',
		'has_archive' => false,
		'exclude_from_search' => true,
		'menu_position' =>5, //管理画面のメニュー順位
		'supports' => array( 'title', 'editor'), 
    	)
	);
}

add_action('admin_menu', 'add_common_contents_button');

function add_common_contents_button() {	
	add_meta_box('common_contents_button', '投稿用ページのボタン表示', 'common_contents_button', 'keni_cc', 'normal', 'high');
	if (isset($_GET['post'])) add_meta_box('common_contents_code', '表示をするショートコード', 'common_contents_code', 'keni_cc', 'normal', 'high');
}

function common_contents_button() {

	$button = array("disable" => "出力しない", "enable" => "表示する");
	$button_view = (isset($_GET['post'])) ? get_post_meta( $_GET['post'], 'button_view', true) : key($button);

	foreach ($button as $key => $val) {
		if ($key == $button_view) {
			echo "<input type=\"radio\" name=\"button_view\" value=\"".$key."\" checked=\"checked\" id=\"".$key."\" /><label for=\"".$key."\">".$val."</label>\n";
		} else {
			echo "<input type=\"radio\" name=\"button_view\" value=\"".$key."\" id=\"".$key."\" /><label for=\"".$key."\">".$val."</label>\n";
		}
	}
}

function common_contents_code() {
	echo "この内容を本文中などに表示をする場合には、下記のコードを記述して下さい。<br />";
	echo "[cc id=".$_GET['post']."]";
}

function save_common_contents_button($post_id) {
	if (isset($_POST['button_view'])) {
		update_post_meta( $post_id, 'button_view', $_POST['button_view']);
	}
}



//---------------------------------------------------------------------------
// 管理画面に、ランディングページエリアを作る
//---------------------------------------------------------------------------
function keni_landingpage($dir="") {
	if ($dir == "") $dir = LP_DIR;
	register_post_type( $dir,
		array(
			'labels' => array(
			'name' => __( 'Landing Pages', 'keni' ),
			'singular_name' => $dir,
		),
		'public' => true,
		'menu_icon' => 'dashicons-welcome-widgets-menus',
		'has_archive' => true,
		'exclude_from_search' => true,
		'menu_position' =>8, //管理画面のメニュー順位
		'supports' => array( 'title', 'editor', 'thumbnail'), 
    	)
	);
	flush_rewrite_rules(false);
}

//---------------------------------------------------------------------------
// ランディングページの投稿にサブキャッチコピーを追加する
//---------------------------------------------------------------------------
function add_lp_catch_box() {
	add_meta_box('lp_catch', 'ランディングページのサブキャッチコピー', 'lp_catch_box', LP_DIR, 'normal', 'high');
}

function lp_catch_box() {
	$catch_text = isset($_GET['post']) ? get_post_meta( $_GET['post'], 'catch_text' ) : "";
	if (isset($catch_text[0])) $catch_text = $catch_text[0];
	echo '<input type="text" size="80" name="catch_text" value="'. esc_html($catch_text) .'" />';
}

function save_lp_catch( $post_id ) {
	if (isset($_POST['catch_text']) && $_POST['catch_text'] != "") {
		update_post_meta($post_id, 'catch_text', $_POST['catch_text']);
	} else {
		update_post_meta($post_id, 'catch_text', '');
	}
}

add_action('admin_menu', 'add_lp_catch_box');


//---------------------------------------------------------------------------
// ランディングページの投稿に画像アップロード機能を追加する
//---------------------------------------------------------------------------
function add_lp_image_box() {
	add_meta_box('header_img', 'フルスクリーン用画像 （レイアウトの「フルスクリーン表示」にチェックを入れることで、画面全体に画像が表示されます）', 'lp_image_box', LP_DIR, 'normal', 'high');
}

function lp_image_box() {
	$header_image = isset($_GET['post']) ? get_post_meta( $_GET['post'], 'header_image' ) : "";
	if (isset($header_image[0])) $header_image = $header_image[0];
	echo '<p id="keni_img_1">';
	if ($header_image != "") echo '<img src="'.esc_html($header_image).'" />';
	echo '</p>
	<input id="keni_upload_image_1" type="text" size="80" name="header_image" value="'. esc_html($header_image) .'" /><br />
	<input type="button" class="keni_upload_image_button" id="upload_image_button_1" value="画像を設定する" /><br />
	<p class="keni_note">※IE8等の古いブラウザでは画像が画面いっぱいに広がらないことがありますので、それらを対象とするサイトを作成する場合は、できるだけ大きな画像を登録することをオススメいたします。</p>';

}

function save_lp_image( $post_id ) {
	if (isset($_POST['header_image']) && $_POST['header_image'] != "") {
		update_post_meta($post_id, 'header_image', $_POST['header_image']);
	} else {
		update_post_meta($post_id, 'header_image', '');
	}
}

add_action('admin_menu', 'add_lp_image_box');


//---------------------------------------------------------------------------
//	管理画面のみで読み込むファイル
//---------------------------------------------------------------------------
function keni_admin() {

	wp_register_style( 'keni_admin_css', get_stylesheet_directory_uri(). '/keni_admin.css');

	wp_enqueue_style('thickbox');
	wp_enqueue_style('keni_admin_css');
	
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');

	wp_register_script('add-title-count', get_bloginfo('template_directory') .'/js/text_count.js');
	wp_enqueue_script('add-title-count');

	wp_register_script('my-upload', get_bloginfo('template_directory') .'/js/upload.js');
	wp_enqueue_script('my-upload');

	add_filter( 'manage_posts_columns', 'manage_posts_columns' );
	add_filter( 'manage_pages_columns', 'manage_posts_columns' );

	$keni_cc = "n";
	if (isset($_GET['post']) && preg_match("/^[0-9]+$/",$_GET['post']) && isset($_GET['action']) && ($_GET['action'] == "edit")) {
		 $content = get_post($_GET['post'], "ARRAY_A");
		 if (isset($content['post_type']) and ($content['post_type'] == "keni_cc")) {
			 $keni_cc = "y";
		 }
	} else if (isset($_GET['post_type']) and ($_GET['post_type'] == "keni_cc")) {
		 $keni_cc = "y";
	}

	if ($keni_cc == "y") {
		add_action( 'manage_posts_custom_column', 'add_column', 0, 2 );
		add_action( 'manage_pages_custom_column', 'add_column', 0, 2 );
		wp_register_script('keni-cc', get_bloginfo('template_directory') .'/js/keni_cc.js','','',false);
		wp_enqueue_script('keni-cc');
	} else {
		add_action( 'manage_posts_custom_column', 'add_column', 10, 2 );
		add_action( 'manage_pages_custom_column', 'add_column', 10, 2 );
	}

}

add_action('admin_menu', 'keni_admin_menu' );
add_action('template_redirect', 'keni_setting');
add_action('init', 'keni_common_contents' );
add_action('init', 'keni_landingpage' );
add_action('admin_head','keni_admin');


//---------------------------------------------------------------------------
//	エディタにボタンを追加
//---------------------------------------------------------------------------
function add_keni_quicktags() {
	if (wp_script_is('quicktags')){

		$button = "";

		// ワイドエリア用タグ
		global $wpdb;

		if (isset($_GET['post'])) {
			$post_type = $wpdb->get_col($wpdb->prepare("SELECT post_type FROM $wpdb->posts WHERE ID=%d", $_GET['post']));
			if ($post_type[0] == LP_DIR) {
				$button .= "QTags.addButton( 'wide','wide block','[wide]', '[/wide]', '','wide', '')\n";
				$button .= "QTags.addButton( 'normal','normal block','[normal]', '[/normal]', '','normal', '')\n";
			}
		} else if (preg_match("/wp-admin\/post-new.php\?post_type=".LP_DIR."/", $_SERVER['REQUEST_URI'])) {
			$button .= "QTags.addButton( 'wide','wide block','[wide]', '[/wide]', '','wide', '')\n";
			$button .= "QTags.addButton( 'normal','normal block','[normal]', '[/normal]', '','normal', '')\n";
		}

		// 装飾用タグ
		$button .= "QTags.addButton( 'br','改行（br）','[br num=\"1\"]', '', '','br', '')\n";

		$button .= "QTags.addButton( 'hr','水平線（hr）','<hr>', '', '','hr', '')\n";

		$button .= "QTags.addButton( 'h2','見出し（h2）','<h2>', '</h2>', '','h2', '')\n";
		$button .= "QTags.addButton( 'h3','見出し（h3）','<h3>', '</h3>', '','h3', '')\n";
		$button .= "QTags.addButton( 'h4','見出し（h4）','<h4>', '</h4>', '','h4', '')\n";
		$button .= "QTags.addButton( 'h5','見出し（h5）','<h5>', '</h5>', '','h5', '')\n";

		$button .= "QTags.addButton( 'black','太字（黒）','<span class=\"black b\">', '</span>', '','black', '')\n";
		$button .= "QTags.addButton( 'red','太字（赤）','<span class=\"red b\">', '</span>', '','red', '')\n";
		$button .= "QTags.addButton( 'orange','太字（オレンジ）','<span class=\"orange b\">', '</span>', '','orange', '')\n";
		$button .= "QTags.addButton( 'green','太字（緑）','<span class=\"green b\">', '</span>', '','green', '')\n";
		$button .= "QTags.addButton( 'blue','太字（青）','<span class=\"blue b\">', '</span>', '','blue', '')\n";

		$button .= "QTags.addButton( 'f12em','文字1.2倍','<span class=\"f12em\">', '</span>', '','f12em', '')\n";
		$button .= "QTags.addButton( 'f14em','文字1.4倍','<span class=\"f14em\">', '</span>', '','f14em', '')\n";
		$button .= "QTags.addButton( 'f16em','文字1.6倍','<span class=\"f16em\">', '</span>', '','f16em', '')\n";
		$button .= "QTags.addButton( 'f18em','文字1.8倍','<span class=\"f18em\">', '</span>', '','f18em', '')\n";
		$button .= "QTags.addButton( 'f20em','文字2倍','<span class=\"f20em\">', '</span>', '','f20em', '')\n";

		$button .= "QTags.addButton( 'al-l','左寄せ','<div class=\"al-l\">', '</div>', '','al-l', '')\n";
		$button .= "QTags.addButton( 'al-r','右寄せ','<div class=\"al-r\">', '</div>', '','al-r', '')\n";
		$button .= "QTags.addButton( 'al-c','中央寄せ','<div class=\"al-c\">', '</div>', '','al-c', '')\n";

		$button .= "QTags.addButton( 'm60-t','余白（上60px）','<div class=\"m60-t\">', '</div>', '','m60-t', '')\n";
		$button .= "QTags.addButton( 'm60-b','余白（下60px）','<div class=\"m60-b\">', '</div>', '','m60-b', '')\n";
		$button .= "QTags.addButton( 'm120-t','余白（上120px）','<div class=\"m120-t\">', '</div>', '','m120-t', '')\n";
		$button .= "QTags.addButton( 'm120-b','余白（下120px）','<div class=\"m120-b\">', '</div>', '','m120-b', '')\n";

		$button .= "QTags.addButton( 'blockquote','引用セット','<blockquote>'+'\\n'+'<p>ここに引用文が入ります。</p>'+'\\n'+'<p class=\"link-ref\"><cite>引用元: <a href=\"\" target=\"_blank\">参照記事のタイトル</a></cite></p>'+'\\n'+'</blockquote>', '','','blockquote', '')\n";

		// キャラクタリスト取得
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix."keni_character WHERE kc_button_view='y' AND kc_active='y' ORDER BY kc_id";
		$res = $wpdb->get_results($sql , ARRAY_A);
		foreach ($res as $val) {
			$position = ($val['kc_position'] == "left") ? "chat-l" :"chat-r";
			$button .= "QTags.addButton( 'char_".$val['kc_id']."','".$val['kc_name']."','[char no=".$val['kc_id']." char=\"".$val['kc_name']."\"]', '[/char]', '','".$val['kc_name']."', ".(200+$val['kc_id']).")\n";

		}
		// 共通コンテンツ用ボタン表示
		$post_ids_meta = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_id FROM ".$wpdb->postmeta." WHERE meta_key='%s' AND meta_value='%s'", array('button_view', 'enable')));
		foreach ($post_ids_meta as $post_id) {
			$content = get_post($post_id, "ARRAY_A");
			if (($content['post_status'] == "publish") && ($content['post_type'] == "keni_cc")) {
				if (!isset($_GET['post']) || !preg_match("/^[0-9]+$/", $_GET['post']) || ( $_GET['post'] != $content['ID'])) {
					$button .= "QTags.addButton( 'cc_".$content['ID']."','".$content['post_title']."','[cc id=".$content['ID']." title=\"".$content['post_title']."\"]', '', '','".$content['post_title']."', ".(300+$content['ID']).")\n";
				}
			}
		}

		if ($button != "") {
			echo "<script>\n".$button."</script>\n";
		}
	}
}
add_action( 'admin_print_footer_scripts', 'add_keni_quicktags' );




//---------------------------------------------------------------------------------------
//	表示をしているページやアーカイブ等の、現在のページ数と、最大ページ数を取得する
//---------------------------------------------------------------------------------------
function pageNumber() {

	$permalink = get_permalink();

	if (is_singular()) {
		$content = get_post();
		$page['max_pages'] = count(explode('<!--nextpage-->', $content->post_content));

		$permalink_structure = get_option('permalink_structure');

		if ($permalink_structure == "") {
			$perm_slash = "q";
		} else if (preg_match("/\/$/u", $permalink_structure)) {
			$perm_slash = "y";
		} else {
			$perm_slash = "n";
		}

		if ($page['max_pages'] > 1) {
			if (preg_match("/\?p=".get_the_ID()."/", $permalink) || preg_match("/\?page_id=".get_the_ID()."/", $permalink)) {	// デフォルト
				$page['now_page'] = isset($_GET['page']) && preg_match("/^[0-9]+$/", $_GET['page']) ? $_GET['page'] : 1;
				$page['permalink'] = "default";

			} else if (preg_match("/\/%post_id%\/*$/", $permalink_structure) && preg_match("/".get_the_ID()."/", $permalink)) {	// 数字ベース
				preg_match("/".get_the_ID()."\/([0-9]+)/", $_SERVER['REQUEST_URI'], $pages);
				$page['now_page'] = isset($pages[1]) ? $pages[1] : 1;
				$page['permalink'] = "number";
			
			} else if ($perm_slash == "y") {	// その他で、最後にスラッシュが入っている場合
				preg_match("/(.+)\/([0-9]+)\/$/", $_SERVER['REQUEST_URI'], $this_page);
				$page['now_page'] = (isset($this_page[2])) ? $this_page[2] : 1;
				$page['permalink'] = "other";

			} else if (is_singular(LP_DIR)) {
				$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
				$page_data = str_ireplace(get_permalink(),"",$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				if (preg_match("/([0-9]+)/", $page_data, $pageno)) {
					$page['now_page'] = (isset($pageno[1])) ? $pageno[1] : 1;
				} else {
					$page['now_page'] = 1;
				}

			} else if (is_front_page()) {
				$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
				$page_data = str_ireplace(get_home_url(),"",$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				preg_match("/\/.*?([0-9]+)\/*$/", $page_data, $this_page);
				$page['now_page'] = (isset($this_page[1])) ? $this_page[1] : 1;
				if ($permalink_structure == "") {
					$page['permalink'] = "default";
				} else if (preg_match("/%post_id%\/*$/", $permalink_structure)) {
					$page['permalink'] = "number";
				} else {
					$page['permalink'] = "other";
				}

			} else {
				preg_match("/(.+)\/([0-9]+)$/", $_SERVER['REQUEST_URI'], $this_page);
				$page['now_page'] = (isset($this_page[2])) ? $this_page[2] : 1;
				$page['permalink'] = "other";
			}
		} else {
			$page['now_page'] = 0;
		}

	} else if (is_search()) {
		$page['now_page'] = get_query_var('paged');

		global $wp_query;
		$rows = $wp_query->found_posts;	
		$page['max_pages'] = ceil($rows / get_option('posts_per_page'));
		
	} else if (is_archive() || is_front_page()) {

		global $wp_query;
		$page['max_pages'] = $wp_query->max_num_pages;
		$page['now_page'] = (get_query_var('paged')) ? get_query_var('paged') : 1;	
		if ($page['max_pages'] > 1) {
			if (is_category() || is_date() || is_tag()) {
				$page['permalink'] = (preg_match("/\?(cat|m|tag|author)=.+/", $_SERVER['REQUEST_URI'])) ? "default" : "other";
			} else if (is_author()) {
				$page['permalink'] = (preg_match("/\?(p|m|cat|tag)=.+/", $permalink)) ? "default" : "other";
			}
		}
	}

	return (isset($page)) ? $page : false;
}


//---------------------------------------------------------------------------
//	meta ページネーションタグの出力
//---------------------------------------------------------------------------
function pageRelNext() {

	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

	$prev = $next = "";
	$view = "n";

	if (get_option('blog_public') == false) {
		return "";
	}

	$this_page = pageNumber();

	if ((is_front_page() || is_home()) && the_keni('snd_page_index') == "y") {	// TOP

		if (isset($this_page['max_pages']) && get_option('page_on_front') > 0 && get_post_meta(get_the_ID(),'page_canonical', true) == "") {	// 固定ページがfrontpageに設定されている場合

			$permalink_structure = get_option('permalink_structure');
			$perm_slash = (preg_match("/.*?\/$/", $permalink_structure)) ? "y" : "n";

			if (get_post_meta( get_the_ID(), 'index', true) == "noindex") return "";
			if (get_post_meta( get_the_ID(), 'follow', true) == "nofollow") return "";

			if ($this_page['max_pages'] > 1 && $this_page['max_pages'] > $this_page['now_page']) {
				if ($permalink_structure == "") {
					$next = get_home_url()."/?page=".($this_page['now_page']+1);
				} else if ($perm_slash == "y") {
					$next = get_home_url()."/page/".($this_page['now_page']+1)."/";
				} else {
					$next = get_home_url()."/page/".($this_page['now_page']+1);
				}
			} else {
				$next = "";
			}
				
			if ($this_page['now_page'] > 2) {
				if ($permalink_structure == "") {
					$prev = get_home_url()."/?page=".($this_page['now_page']-1);
				} else if ($perm_slash == "y") {
					$prev = get_home_url()."/page/".($this_page['now_page']-1)."/";
				} else {
					$prev = get_home_url()."/page/".($this_page['now_page']-1);
				}
			} else if ($this_page['now_page'] == 2) {
				$prev = get_home_url()."/";
			} else {
				$prev = "";
			}
		}

		$view = "y";

	} else if (is_category()) {	// カテゴリ
		$now_cat_name = single_cat_title('',false);
		$cat_id = get_cat_ID($now_cat_name);
		$index = get_post_meta( $cat_id, 'meta_index', true);
		if (($index == "def") || empty($index)) $index = the_keni("list_category_index");
		if ($index == "index") $view = "y";

	} else if (is_tag()) {	// タグ
		$this_tag_name = single_tag_title('',false);
		$tag_lists = get_the_tags();
		if (isset($tag_lists) && is_array($tag_lists) && count($tag_lists) > 0) {	
			foreach ($tag_lists as $tag_val) {
				if ($tag_val->name == $this_tag_name) {
					$tag_id = $tag_val->term_id;
					break;
				}
			}
		}
		$index = get_post_meta( $tag_id, 'meta_index', true);
		if (($index == "def") || empty($index)) $index = the_keni("list_tag_index");
		if ($index == "index") $view = "y";

	} else if (is_date() && (the_keni("list_archive_index") == "index")) {	// 日付
		$view = "y";

	} else if (is_author() && (the_keni("list_author_index") == "index")) {	// 投稿者
		$view = "y";

	} else if (is_search() && (the_keni("list_search_index") == "index")) {	// 検索
		$view = "y";
	
	} else if (!is_front_page() && is_singular() && (get_post_meta( get_the_ID(), 'index', true) == "index")) {

		if (get_post_meta( get_the_ID(), 'follow', true) == "nofollow") return "";

		$post_canonical = get_post_meta(get_the_ID(),'page_canonical', true);
		if (isset($post_canonical) and ($post_canonical != "")) return "";

		$page_link = wp_link_pages(array('echo' => false));

		preg_match_all("/href=\"(.*?)\">([0-9]+)<\/a>/", $page_link, $pages, PREG_SET_ORDER );
		
		foreach($pages as $page_links) {
			if (isset($page_links[2])) {
				if ($prev == "" && $this_page['now_page'] > 1 && $page_links[2] >= ($this_page['now_page'] -1)) {
					$prev = $page_links[1];
				} else if ($next == "" && $page_links[2] > $this_page['now_page']) {
					$next = $page_links[1];
				}
			}
		}
	}

	if ($view == "y") {
		$next_prev = get_posts_nav_link();
		preg_match_all('/href="(.*?)"/', $next_prev, $next_prev_array);
		if (isset($next_prev_array[1])) {
			if (isset($next_prev_array[1][1])) {
				$prev = $next_prev_array[1][0];
				$next = $next_prev_array[1][1];
			} else if (isset($next_prev_array[1][0])) {
				if ($this_page['now_page'] > 1) {
					$prev = $next_prev_array[1][0];
				} else {
					$next = $next_prev_array[1][0];
				}
			}
		}

		$perm_slash = (preg_match("/\/$/u", get_option('permalink_structure'))) ? "y" : "n";

		if ($perm_slash == "n") {
			if ($prev != "" && preg_match("/.+\/$/", $prev) && !is_front_page()) $prev = substr($prev, 0, -1);
			if ($next != "" && preg_match("/.+\/$/", $next)) $next = substr($next, 0, -1);
		}
	}

	if ($prev != "" || $next != "") {
		if ($prev != "") echo "<link rel=\"prev\" href=\"".str_replace("&#038;", "&", $prev)."\" />\n";
		if ($next != "") echo "<link rel=\"next\" href=\"".str_replace("&#038;", "&", $next)."\" />\n";
	}
}



//---------------------------------------------------------------------------
//	ページの閲覧をしたかどうかを判断するcookieをセットする
//---------------------------------------------------------------------------
if (!is_singular(LP_DIR)) add_action( 'get_header', 'set_pv_cookie');

function set_pv_cookie() {
	if (is_singular()) {
		$id = "pv".get_the_ID();
		if (!isset($_COOKIE[$id])) {
			countUpView();
			setcookie($id, time(), 0, "/");
		}
	}
}



//---------------------------------------------------------------------------
//	ページのPV数をカウントする
//---------------------------------------------------------------------------
function countUpView() {
	global $wpdb;
	$meta_id = $wpdb->get_var("SELECT meta_id FROM ".$wpdb->prefix."postmeta WHERE post_id=".get_the_ID()." AND meta_key='pvc_views'");
	if (preg_match("/^[0-9]+$/", $meta_id) && $meta_id > 0) {
		$wpdb->query('UPDATE '.$wpdb->prefix.'postmeta SET meta_value=meta_value+1 WHERE meta_id='.$meta_id);
	} else {
		$wpdb->query('INSERT INTO '.$wpdb->prefix.'postmeta (post_id, meta_key, meta_value) VALUES ('.get_the_ID().', "pvc_views", 1)');
	}

	// 時間毎のPV数を取得する為のカウントをする
	$wpdb->query("INSERT INTO ".$wpdb->prefix."keni_pv (pv_dates, post_id, pv_count) VALUES ('".date("YmdH")."','".get_the_ID()."','1') ON DUPLICATE KEY UPDATE pv_count=pv_count+1");

	$wpdb->flush();
}


//---------------------------------------------------------------------------
//	ページのPV数を表示する
//---------------------------------------------------------------------------
function viewPV() {
	echo getViewPV($id = get_the_ID());
}


function getViewPV($id = "") {
	if ($id == "") get_the_ID();
	return get_post_meta($id,'pvc_views', true);
}


//---------------------------------------------------------------------------
//	ページのPV数を表示するウィジェット
//---------------------------------------------------------------------------
$ranking_style_list = array("1" => array("label" => "王冠アイコンのリスト", "ol_class" => "ranking-list01", "li_class" => ""),
														"2" => array("label" => "メダルアイコンのリスト", "ol_class" => "ranking-list02", "li_class" => ""),
														"3" => array("label" => "シンプルなリスト", "ol_class" => "ranking-list03", "li_class" => ""),
														"4" => array("label" => "画像＋ランキング番号のリスト", "ol_class" => "ranking-list03", "li_class" => " on-image"),
														"5" => array("label" => "画像＋テキストのリスト", "ol_class" => "ranking-list04", "li_class" => " on-image"),
														"6" => array("label" => "背景画像＋テキストのリスト", "ol_class" => "ranking-list05", "li_class" => " on-image")
														);

$tanking_target_list = array("pv" => array("label" => "PV数"),
														 "hbm" => array("label" => "はてブ数")
														);


$ranking_period = array("1d" => "24時間",
												"1w" => "1週間",
												"1m" => "1ヶ月",
												"no" => "全て"
												);

class Keni_PV_Widget extends WP_Widget {


	function __construct() {
		parent::__construct('keni_pv',
												'【賢威】PV数ランキング表示',
												array( 'description' => '賢威テンプレートに付属する 記事PV数ランキングを表示するウィジェットです', )
											);
	}

	function widget($args, $instance) {

		global $ranking_style_list;
		global $ranking_period;
		extract( $args );

		$instance['title'] = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'PV Ranking' , 'keni');

		$title = apply_filters( 'widget_title', $instance['title'] );
		$number = apply_filters( 'widget_number', $instance['number'] );
		$show_pv = apply_filters( 'widget_show_pv', $instance['show_pv'] );
		$target = apply_filters( 'widget_target', $instance['target'] );

		if (empty($target)) $target = "pv";

		$style = apply_filters( 'widget_style', $instance['style'] );
		$period = apply_filters( 'widget_period', $instance['period'] );

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title."\n";
		
		global $wpdb;

		if ($target == "pv") {

			if (empty($period)) $period = end($ranking_period);

			switch ($period) {
				case "1d":
					$start = date("YmdH", mktime((date("H") - 24), 0, 0, date("m"), date("d"), date("Y")));
					break;
				case "1w":
					$start = date("YmdH", mktime(0, 0, 0, date("m"), (date("d") - 7), date("Y")));
					break;
				case "1m":
					$start = date("YmdH", mktime(0, 0, 0, (date("m") - 1), date("d"), date("Y")));
					break;
				default:
					$start = 0;
					break;
			}


			// 除外するpost_idを取得
			$ext_ids = $wpdb->get_col("SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key='pv_disable'");
			$ext_sql =  (is_array($ext_ids) && count($ext_ids) > 0) ? " AND meta.post_id NOT IN (".implode(",",$ext_ids).")" : "";

			if ($start == 0) {
				// PV数の多い記事の情報とPV数を取得
				$counts = $wpdb->get_results("SELECT ID, post_title, meta_value AS pv FROM ".$wpdb->prefix."postmeta AS meta LEFT JOIN ".$wpdb->prefix."posts AS po ON meta.post_id=po.ID WHERE meta_key='pvc_views' AND post_status='publish' AND (post_type='post' OR post_type='page')".$ext_sql." GROUP BY meta.post_id ORDER BY (pv+0) DESC LIMIT 0,".$number, ARRAY_A);
			} else {
				$end = date("YmdH");
				$counts = $wpdb->get_results("SELECT pvs.post_id AS ID, post_title, SUM(pv_count) AS pv FROM ".$wpdb->prefix."keni_pv AS pvs LEFT JOIN ".$wpdb->prefix."posts AS po ON pvs.post_id=po.ID LEFT JOIN ".$wpdb->prefix."postmeta AS meta ON meta.post_id=po.ID WHERE meta_key='pvc_views' AND post_status='publish' AND (post_type='post' OR post_type='page') AND pv_dates BETWEEN ".$start." AND ".$end.$ext_sql." GROUP BY pvs.post_id ORDER BY pv DESC, po.post_modified DESC LIMIT 0,".$number, ARRAY_A);
 			}

			foreach ($counts as $no => $val) {
				$post_data = get_post($val['ID']);
	
				$content = ($post_data->post_excerpt != "") ? strip_tags(strip_shortcodes($post_data->post_excerpt)) : strip_tags(strip_shortcodes($post_data->post_content));
	
				if (mb_strlen($content) > 80)  $content = mb_substr($content,0,80)."...";
	
				if (get_the_post_thumbnail($val['ID']) != "") {
					$image_url = wp_get_attachment_image_src(get_post_thumbnail_id($val['ID']), 'large_thumb');
					$thumbnail_image = $image_url[0];
				} else {
					$thumbnail_image = get_template_directory_uri()."/images/dummy.jpg";
				}
	
				switch ($style) {
					case "1":
					case "2":
						if ($no <= 0) echo "<ol class=\"ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
	
						echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
						echo "<h4 class=\"rank-title\"><a href=\"".get_permalink($val['ID'])."\">".$val['post_title']."</a>";
						if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($val['pv'])."pv)</span>";
						echo "</h4>\n";
						if (get_the_post_thumbnail($val['ID']) != "") echo "<div class=\"rank-thumb\"><a href=\"".get_permalink($val['ID'])."\">".get_the_post_thumbnail($val['ID'], 'middle_thumb')."</a></div>\n";
						echo "<p class=\"rank-desc\">".esc_html(strip_tags($content))."</p>\n";
						echo "</li>\n";
						break;
						
					case "3":
						if ($no <= 0) echo "<ol class=\"ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
	
						echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
						echo "<h4 class=\"rank-title\"><a href=\"".get_permalink($val['ID'])."\">".$val['post_title']."</a>";
						if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($val['pv'])."pv)</span>";
						echo "</h4>\n";
						if (get_the_post_thumbnail($val['ID']) != "") echo "<div class=\"rank-thumb\"><a href=\"".get_permalink($val['ID'])."\">".get_the_post_thumbnail($val['ID'], 'ss_thumb')."</a></div>\n";
						echo "<p class=\"rank-desc\">".esc_html(strip_tags($content))."</p>\n";
						echo "</li>\n";
						break;
	
					case "4":
						if ($no <= 0) echo "<ol class=\"ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
	
						echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
						$thumbnail_image = (get_the_post_thumbnail($val['ID']) != "") ? get_the_post_thumbnail($val['ID'], 'ss_thumb') : "<img src=\"".get_template_directory_uri()."/images/dummy.jpg\" alt=\"\">";
						echo "<div class=\"rank-thumb\"><a href=\"".get_permalink($val['ID'])."\">".$thumbnail_image."</a></div>\n";
						echo "<h4 class=\"rank-title\"><a href=\"".get_permalink($val['ID'])."\">".$val['post_title']."</a>";
						if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($val['pv'])."pv)</span>";
						echo "</h4>\n";
						echo "<p class=\"rank-desc\">".esc_html(strip_tags($content))."</p>\n";
						echo "</li>\n";
						break;
	
					case "5":
						if ($no <= 0) echo "<ol class=\"ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
	
						echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
						echo "<div class=\"rank-box\">\n";
						echo "<a href=\"".get_permalink($val['ID'])."\"><img src=\"".$thumbnail_image."\" width=\"320\" height=\"320\" alt=\"\"></a>\n";
						echo "<p class=\"rank-text\"><a href=\"".get_permalink($val['ID'])."\">".$val['post_title']."</a>";
						if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($val['pv'])."pv)</span>";
						echo "</p>\n</div>\n";
						echo "</li>\n";
						break;
	
					case "6":
						if ($no <= 0) echo "<ol class=\"ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
	
						echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
						echo "<div class=\"rank-box\" ";
						echo "style=\"background-image: url(".$thumbnail_image.");\"";
						echo ">\n";
						echo "<a href=\"".get_permalink($val['ID'])."\"><p class=\"rank-text\">".$val['post_title'];
						if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($val['pv'])."pv)</span>";
						echo "</p></a>";
						echo "</div>\n";
						echo "</li>\n";
						break;
				}
			}
			if (isset($counts) && $counts > 0) echo "</ol>\n";

		} else {
      $now_count = 0;

			$url = "https://b.hatena.ne.jp/entrylist/json?url=".urlencode(get_bloginfo('siteurl'))."&sort=count&callback=json";
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.63 Safari/537.36" );
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $ch, CURLOPT_ENCODING, "" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
			$content = @curl_exec( $ch );
			$response = curl_getinfo( $ch );
			curl_close ( $ch );
			if ($response['http_code'] != 200) $content = @file_get_contents($url);			

			preg_match_all("/({.*?})/", $jsonp, $json);
	
			if (isset($json[1]) && count($json[1]) > 0) {
				$now_count = 0;
	
					foreach ($json[1] as $no => $line) {
	
					$post_id = 0;
	
					if ($number > $now_count) {
						$line_val = json_decode($line, true);
	
						$post_id = url_to_postid($line_val['link']);
	
						if ($post_id > 0 && get_the_post_thumbnail($post_id) != "") {
							$image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post_id, 'large_thumb'));
							$thumbnail_image = $image_url[0];
						} else {
							$thumbnail_image = get_template_directory_uri()."/images/dummy.jpg";
						}
	
						$title = ($post_id > 0 && get_the_title($post_id) != "") ? get_the_title($post_id) : $line_val['title'];
						$content = ($post_id > 0 && get_post_field('excerpt', $post_id)) ? strip_tags(strip_shortcodes(get_post_field('excerpt', $post_id))) : "";
	
						switch ($style) {
							case "1":
							case "2":
								if ($no <= 0) echo "<ol class=\"ranking-list hatena-ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
			
								echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
								echo "<h4 class=\"rank-title\"><a href=\"".$title."\">".$title."</a>";
								if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($line_val['count'])."USERS)</span>";
								echo "</h4>\n";
								if ($post_id > 0 && get_the_post_thumbnail($post_id) != "") echo "<div class=\"rank-thumb\"><a href=\"".$line_val['link']."\">".get_the_post_thumbnail($post_id, 'middle_thumb')."</a></div>\n";
								echo "<p class=\"rank-desc\">".esc_html(strip_tags($content))."</p>\n";
								echo "</li>\n";
								break;
								
							case "3":
								if ($no <= 0) echo "<ol class=\"ranking-list hatena-ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
			
								echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
								echo "<h4 class=\"rank-title\"><a href=\"".$line_val['link']."\">".$title."</a>";
								if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($line_val['count'])."USERS)</span>";
								echo "</h4>\n";
								if ($post_id > 0 && get_the_post_thumbnail($post_id) != "") echo "<div class=\"rank-thumb\"><a href=\"".$line_val['link']."\">".$line_val['link']."</a></div>\n";
								echo "<p class=\"rank-desc\">".esc_html(strip_tags($content))."</p>\n";
								echo "</li>\n";
								break;
			
							case "4":
								if ($no <= 0) echo "<ol class=\"ranking-list hatena-ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
			
								echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
								$thumbnail_image = ($post_id > 0 && get_the_post_thumbnail($post_id) != "") ? $line_val['link'] : "<img src=\"".get_template_directory_uri()."/images/dummy.jpg\" alt=\"\">";
								echo "<div class=\"rank-thumb\"><a href=\"".$line_val['link']."\">".$thumbnail_image."</a></div>\n";
								echo "<h4 class=\"rank-title\"><a href=\"".$line_val['link']."\">".$title."</a>";
								if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($line_val['count'])."USERS)</span>";
								echo "</h4>\n";
								echo "<p class=\"rank-desc\">".esc_html(strip_tags($content))."</p>\n";
								echo "</li>\n";
								break;
			
							case "5":
								if ($no <= 0) echo "<ol class=\"ranking-list hatena-ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
			
								echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
								echo "<div class=\"rank-box\">\n";
								echo "<a href=\"".$line_val['link']."\"><img src=\"".$thumbnail_image."\" width=\"320\" height=\"320\" alt=\"\"></a>\n";
								echo "<p class=\"rank-text\"><a href=\"".$line_val['link']."\">".$title."</a>";
								if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($line_val['count'])."USERS)</span>";
								echo "</p>\n</div>\n";
								echo "</li>\n";
								break;
			
							case "6":
								if ($no <= 0) echo "<ol class=\"ranking-list hatena-ranking-list ".$ranking_style_list[$style]['ol_class']."\">\n";
			
								echo "<li class=\"rank".sprintf("%02d", ($no+1)).$ranking_style_list[$style]['li_class']."\">\n";
								echo "<div class=\"rank-box\" ";
								echo "style=\"background-image: url(".$thumbnail_image.");\"";
								echo ">\n";
								echo "<a href=\"".$line_val['link']."\"><p class=\"rank-text\">".$title;
								if ($show_pv) echo "<span class=\"num-pv\"> (".number_format($line_val['count'])."USERS)</span>";
								echo "</p></a>";
								echo "</div>\n";
								echo "</li>\n";
								break;
						}
	
					} else {
						break;
					}
					$now_count++;
				}
				echo "</ol>\n";
			}
		}
		echo $after_widget;
	}

  function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = trim($new_instance['number']);
		$instance['number'] = mb_convert_kana($instance['number'], "n");
		$instance['target'] = trim($new_instance['target']);

		if (!preg_match("/^[0-9]+$/", $instance['number']) || ($instance['number'] > 10)) $instance['number'] = 10;		
		$instance['show_pv'] = trim($new_instance['show_pv']);
		$instance['style'] = $new_instance['style'];
		$instance['period'] = $new_instance['period'];
		return $instance;
	}

	function form($instance) {

		global $ranking_style_list;
		global $tanking_target_list;
		global $ranking_period;

		$title = (isset($instance['title'])) ? esc_attr($instance['title']) : "";
		$number = (isset($instance['number'])) ? esc_attr($instance['number']) : 5;
		if (!preg_match("/^[0-9]+$/", $number)) $number = 5;
		$show_pv = (isset($instance['show_pv'])) ? esc_attr($instance['show_pv']) : "n";

		$target = (isset($instance['target'])) ? esc_attr($instance['target']) : "pv";
		if (empty($target)) $target = "pv";
		$style = (isset($instance['style'])) ? esc_attr($instance['style']) : 3;

		$period = (isset($instance['style'])) ? esc_attr($instance['period']) : 0;

		echo "<p><label for=\"".$this->get_field_id('title')."\">タイトル:<input class=\"widefat\" id=\"".$this->get_field_id('title')."\" name=\"".$this->get_field_name('title')."\" type=\"text\" value=\"".$title."\" /></p>\n";
		echo "<p><label for=\"".$this->get_field_id('number')."\">表示する投稿数:<input type=\"text\"  id=\"".$this->get_field_id('number')."\" name=\"".$this->get_field_name('number')."\" type=\"text\" value=\"".$number."\" size=\"3\" />(最大 10)</p>\n";


		echo "<p>表示項目:\n";
		if (!empty($show_pv)) {
			echo "<p><input class=\"checkbox\" type=\"checkbox\" id=\"".$this->get_field_id('show_pv')."\"name=\"".$this->get_field_name('show_pv')."\" checked=\"checked\" /><label for=\"".$this->get_field_id('show_pv')."\">「PV数」または「はてブ数」を表示しますか ?</label></p>\n";
		} else {
			echo "<p><input class=\"checkbox\" type=\"checkbox\" id=\"".$this->get_field_id('show_pv')."\"name=\"".$this->get_field_name('show_pv')."\" /><label for=\"".$this->get_field_id('show_pv')."\">「PV数」または「はてブ数」を表示しますか ?</label></p>\n";
		}

		foreach ($tanking_target_list as $target_style => $target_val) {
			if ($target_style == $target) {
				echo "<span><input type=\"radio\" name=\"".$this->get_field_name('target')."\" value=\"".$target_style."\" id=\"".$this->get_field_id('target_'.$target_style)."\" checked=\"checked\"><label for=\"".$this->get_field_id('target_'.$target_style)."\">".$target_val['label']."</label>　</span>\n";
			} else {
				echo "<span><input type=\"radio\" name=\"".$this->get_field_name('target')."\" value=\"".$target_style."\" id=\"".$this->get_field_id('target_'.$target_style)."\"><label for=\"".$this->get_field_id('target_'.$target_style)."\">".$target_val['label']."</label>　</span>\n";
			}
		}
		echo "</p>\n";


		// 期間を区切る
		if (empty($period)) {
			end($ranking_period);
			$period = key($ranking_period);
		}

		echo "<p>PV数を集計する期間:\n";
		echo "<select name=\"".$this->get_field_name('period')."\">\n";
		foreach ($ranking_period as $period_key => $period_val) {
			if ($period == $period_key) {
				echo "<option value=\"". $period_key."\" selected=\"selected\">".$period_val."</option>\n";
			} else {
				echo "<option value=\"". $period_key."\">".$period_val."</option>\n";
			}
		}
		echo "</select></p>\n";


		if (!preg_match("/^[0-9]+$/", $style)) $style = 3;

		echo "<p>表示形式:</p>\n";
		foreach ($ranking_style_list as $style_id => $style_val) {
			if ($style_id == $style) {
				echo "<p><input type=\"radio\" name=\"".$this->get_field_name('style')."\" value=\"".$style_id."\" id=\"".$this->get_field_id('style_'.$style_id)."\" checked=\"checked\"><label for=\"".$this->get_field_id('style_'.$style_id)."\">".$style_val['label']."</label></p>\n";
			} else {
				echo "<p><input type=\"radio\" name=\"".$this->get_field_name('style')."\" value=\"".$style_id."\" id=\"".$this->get_field_id('style_'.$style_id)."\"><label for=\"".$this->get_field_id('style_'.$style_id)."\">".$style_val['label']."</label></p>\n";
			}
		}
	}
}
add_action('widgets_init', create_function('', 'return register_widget("Keni_PV_Widget");'));




//---------------------------------------------------------------------------
//	「最近の投稿」ウィジェット（サムネイル画像付き）
//---------------------------------------------------------------------------
$new_post_list = array("1" => array("label" => "シンプルなリスト", "ul_class" => "link-menu-image", "li_class" => ""),
											 "2" => array("label" => "画像＋テキストのリスト", "ul_class" => "post-list01", "li_class" => " on-image"),
											 "3" => array("label" => "背景画像＋テキストのリスト", "ul_class" => "post-list02", "li_class" => " on-image")
											);


class Keni_Widget_Recent_Posts extends WP_Widget {

	function __construct() {
		parent::__construct('keni_recent_post',
												'【賢威】サムネイル付き最近の投稿',
												array( 'description' => '最近の投稿にサムネイル画像を付けて表示する賢威のカスタムウィジェットです', )
											);
	}


	public function widget( $args, $instance ) {

		global $new_post_list;

		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_recent_posts', 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts with Images' , 'keni');
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		$style = isset( $instance['style'] ) ? $instance['style'] : 1;


		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true
		) ) );

		if ($r->have_posts()) {

			echo $args['before_widget'];
			if ( $title ) echo $args['before_title'] . $title . $args['after_title']."\n";

			$no = 0;
		
			while ( $r->have_posts() ) : $r->the_post();
	
				if (get_the_post_thumbnail(get_the_ID()) != "") {
					$image_url = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'large_thumb');
					$thumbnail_image = $image_url[0];
				} else {
					$thumbnail_image = get_template_directory_uri()."/images/dummy.jpg";
				}
	
				if ($no <= 0) echo "<ul class=\"".$new_post_list[$style]['ul_class']."\">\n";
	
				echo "<li class=\"".$new_post_list[$style]['li_class']."\">\n";
	
				switch ($style) {
					case "1":
						if (get_the_post_thumbnail(get_the_ID()) != "") echo "<div class=\"link-menu-image-thumb\"><a href=\"". get_the_permalink()."\">".get_the_post_thumbnail(get_the_ID(), 'ss_thumb')."</a></div>\n";
						echo "<p class=\"post-title\"><a href=\"".get_the_permalink() ."\">".esc_html(get_the_title())."</a>";
						if ( $show_date ) echo "<span class=\"post-date\">（".get_the_date()."）</span>";
						echo "</p>\n";
						break;
	
					case "2":
						echo "<div class=\"post-box\">";
						echo "<a href=\"".get_the_permalink()."\"><img src=\"".$thumbnail_image."\" width=\"320\" height=\"320\" alt=\"\"></a>";
						echo "<p class=\"post-text\"><a href=\"".get_the_permalink()."\">".esc_html(get_the_title())."</a>";
						if ( $show_date ) echo "<span class=\"post-date\">（".get_the_date()."）</span>";
						echo "</p>\n";
						echo "</div>\n";
						break;
	
					case "3":
						echo "<div class=\"post-box\" style=\"background-image: url(".$thumbnail_image.");\">\n";
						echo "<a href=\"".get_the_permalink()."\"><p class=\"post-text\">".esc_html(get_the_title());
						if ( $show_date ) echo "<span class=\"post-date\">（".get_the_date()."）</span>";
						echo "</p></a>\n";
						echo "</div>\n";
						break;
				}
					
				echo "</li>\n";
	
				$no++;
			endwhile;

			echo "</ul>\n";
			echo $args['after_widget'];
	
			wp_reset_postdata();
		}

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_recent_posts', $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	}


  function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = mb_convert_kana($new_instance['number'], "n");
		$instance['show_date'] = trim($new_instance['show_date']);
		$instance['style'] = $new_instance['style'];
		return $instance;
	}

	function form($instance) {

		global $new_post_list;

		$title = esc_attr($instance['title']);
		$number = esc_attr($instance['number']);
		$show_date = esc_attr($instance['show_date']);
		$style = esc_attr($instance['style']);

		echo "<p><label for=\"".$this->get_field_id('title')."\">タイトル:<input class=\"widefat\" id=\"".$this->get_field_id('title')."\" name=\"".$this->get_field_name('title')."\" type=\"text\" value=\"".$title."\" /></p>\n";
		echo "<p><label for=\"".$this->get_field_id('number')."\">表示する投稿数:<input type=\"text\"  id=\"".$this->get_field_id('number')."\" name=\"".$this->get_field_name('number')."\" type=\"text\" value=\"".$number."\" size=\"3\" /></p>\n";
		if (!empty($show_date)) {
			echo "<p><input class=\"checkbox\" type=\"checkbox\" id=\"".$this->get_field_id('show_date')."\"name=\"".$this->get_field_name('show_date')."\" checked=\"checked\" /><label for=\"".$this->get_field_id('show_date')."\">投稿日を表示しますか ?</label></p>\n";
		} else {
			echo "<p><input class=\"checkbox\" type=\"checkbox\" id=\"".$this->get_field_id('show_date')."\"name=\"".$this->get_field_name('show_date')."\" /><label for=\"".$this->get_field_id('show_date')."\">投稿日を表示しますか ?</label></p>\n";
		}

		if (!preg_match("/^[0-9]+$/", $style)) $style = 1;

		echo "<p>表示形式:</p>\n";
		foreach ($new_post_list as $style_id => $style_val) {
			if ($style_id == $style) {
				echo "<p><input type=\"radio\" name=\"".$this->get_field_name('style')."\" value=\"".$style_id."\" id=\"".$this->get_field_id('style_'.$style_id)."\" checked=\"checked\"><label for=\"".$this->get_field_id('style_'.$style_id)."\">".$style_val['label']."</label></p>\n";
			} else {
				echo "<p><input type=\"radio\" name=\"".$this->get_field_name('style')."\" value=\"".$style_id."\" id=\"".$this->get_field_id('style_'.$style_id)."\"><label for=\"".$this->get_field_id('style_'.$style_id)."\">".$style_val['label']."</label></p>\n";
			}
		}
	}
}

function new_posts_widget_register() {
	register_widget('Keni_Widget_Recent_Posts');
}
add_action('widgets_init', 'new_posts_widget_register');



//---------------------------------------------------------------------------
//	画像のalt文字を取得
//---------------------------------------------------------------------------
function get_image_alt($url) {
	$alt = "";
  preg_match('/([^\/]+?)(-e\d+)?(-\d+x\d+)?(\.\w+)?$/', $url, $name);
	if (isset($name[1])) {
	  global $wpdb;
 	 	$attachment_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_name = %s", $name[1]));
		if (preg_match("/^[0-9]+$/", $attachment_id)) {
			$alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
		}
	}
	return $alt;
}



//---------------------------------------------------------------------------
//	構造化マークアップ対応のためのhentryクラスの削除
//---------------------------------------------------------------------------
function remove_hentry($this_class) {
 return array_diff($this_class, array('hentry'));
}
add_filter('post_class', 'remove_hentry');



//---------------------------------------------------------------------------
//	予約投稿の際、既に登録されている内容を上書きしないようにする
//---------------------------------------------------------------------------
function future_to_publish_action() {
	remove_action('save_post', 'save_h1_string');
	remove_action('save_post', 'save_canonical_string');
	remove_action('save_post', 'save_relation_string');
	remove_action('save_post', 'save_custom_field_postdata');
	remove_action('save_post', 'save_index_postdata');
	remove_action('save_post', 'save_title_view');	
	remove_action('save_post', 'save_pv_disable');	
	remove_action('save_post', 'save_toc_postdata');
}
add_action('future_to_publish', 'future_to_publish_action');



//---------------------------------------------------------------------------
//	一覧からの一括変更の際に、個別設定されている内容の上書きをしないようにする
//---------------------------------------------------------------------------
function save_post_action_check() {

	if (!preg_match("/wp-admin\/edit.php/", $_SERVER['REQUEST_URI'])) {

		add_action('save_post', 'save_h1_string');
		add_action('save_post', 'save_canonical_string');
		add_action('save_post', 'save_relation_string');
		add_action('save_post', 'save_custom_field_postdata');
		add_action('save_post', 'save_index_postdata');
		add_action('save_post', 'save_title_view');
		add_action('save_post', 'save_pv_disable');	
		add_action('save_post', 'save_tags_string');

		add_action('save_post', 'save_common_contents_button');

		add_action('save_post', 'save_toc_postdata');

		add_action('save_post', 'save_lp_catch');
		add_action('save_post', 'save_lp_image');
	}
}
add_action('admin_menu', 'save_post_action_check');



//---------------------------------------------------------------------------
//	管理画面上での目次表示の指定
//---------------------------------------------------------------------------
$toc = array("def" => "共通設定を適用",
								"y" => "する",
								"n" => "しない",
								);


add_action('admin_menu', 'add_toc_box');

function add_toc_box() {
	add_meta_box('page_toc', '目次の自動生成', 'toc_setting', 'post', 'side', 'low');
	add_meta_box('page_toc', '目次の自動生成', 'toc_setting', 'page', 'side', 'low');
	add_meta_box('page_toc', '目次の自動生成', 'toc_setting', LP_DIR, 'side', 'low');
}
 
function  toc_setting() {

	global $toc;

	if (isset($_GET['post'])) {
		$toc_val = get_post_meta( $_GET['post'], 'toc', true);
		if (empty($toc_val)) $toc_val = "def";
	} else {
		$toc_val = "def";
	}

	$view_toc = "<select name=\"toc\">\n";
	foreach ($toc as $type => $view) {
		if ($type == $toc_val) {
			$view_toc .= "<option value=\"".$type."\" selected=\"selected\" >".$view."</option>\n";
		} else {
			$view_toc .= "<option value=\"".$type."\" >".$view."</option>\n";
		}
	}
	$view_toc .= "</select>\n</td>\n</tr>\n";
	echo $view_toc;
}
 
//---------------------------------------------------------------------------
//	目次表示の保存
//---------------------------------------------------------------------------
function save_toc_postdata($post_id) {
	if (isset($_POST['toc'])) update_post_meta( $post_id, 'toc', $_POST['toc']);
}


//---------------------------------------------------------------------------
//	共通コンテンツ・フッタのリッチテキストを本文扱いにならないようにする
//---------------------------------------------------------------------------
function richtext_formats($content) {
	$res = '';
	$pattern = '{(\[raw\].*?\[/raw\])}is';
	$contents = '{\[raw\](.*?)\[/raw\]}is';
	$pieces = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

	foreach ($pieces as $piece) {
		$res .= (preg_match($contents, $piece, $matches)) ? $matches[1] : wptexturize(wpautop($piece));
	}

	return $res;
}
?>