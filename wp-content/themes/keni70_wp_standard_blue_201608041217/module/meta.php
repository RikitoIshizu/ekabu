<?php
/*----------------------------------------
	賢威7.0

	メタの制御機能拡張
	
	第1版　2015. 9.29

	株式会社 ウェブライダー
----------------------------------------*/
 
//---------------------------------------------------------------------------
//	タイトルの表示する関数
//---------------------------------------------------------------------------
function title_keni( $blogname = true, $sep = " | " ) {
	echo get_title_keni();
}


function get_title_keni($blogname = true, $sep = " | " ) {
	$title = "";

	if (is_front_page()) {
		$title = (trim(the_keni('top_title')) != "") ? trim(the_keni('top_title')) : trim(get_bloginfo('name'));

	} else if (is_home()) {

		$post_page = get_option(page_for_posts);
		if (!empty($post_page) && $post_page > 0) {
			$top_page_data = get_post($post_page);
			$title = $top_page_data->post_title;
		} else {
			if ((get_option('page_for_posts') > 0) and (get_the_ID() != get_option('page_on_front'))) {
				$title = trim(get_the_title('name'));
			} else {
				$title = trim(get_bloginfo('name'));
			}
		}

		if (is_home() && get_query_var('paged') > 1) $title = sprintf( __('Archive List for %s','keni'),$title);

	} else if (is_singular()){

		$title = trim(get_the_title());

		$this_page = pageNumber();
		if ($this_page['now_page'] > 1) $title .= "（".$this_page['now_page']."/".$this_page['max_pages'].__('Pages', 'keni') . "）";


	} else if(is_category() or is_tag()){
		$title = get_archive_title_keni("n");

	} else if(is_day()){
		$title = sprintf( __('Archive List for %s','keni'), get_the_time(__('F j, Y','keni')));
	} else if(is_month()){
		$title = sprintf( __('Archive List for %s','keni'), get_the_time(__('F Y','keni')));
	} else if(is_year()){
		$title = sprintf( __('Archive List for %s','keni'), get_the_time(__('Y','keni')));
	} else if(is_author()) {

		if(have_posts()):
			while(have_posts()): the_post();
				$title = get_the_author_meta('display_name').sprintf( __('Archive List for authors','keni'));
			endwhile;
			wp_reset_query();
		endif;

	} else if(get_query_var('paged') > 1) {
		$title = sprintf( __('Archive List for blog','keni'));
	} else if(is_search()){
		$title = sprintf( __('Search Result for %s','keni'), get_search_query());
	} else if(is_404()){
		$title = sprintf( __('Sorry, but you are looking for something that isn&#8217;t here.','keni'));
	} else {
		$title = wp_title('', false, 'right');
	}

	if( $title == "" ) $title = get_bloginfo('name');		

	if (get_query_var('paged') > 1) $title .= show_page_number();

	if (is_page() || is_single()) {		
		if (!is_front_page() && the_keni('view_site_title') != "n" && get_post_meta( get_the_ID(), "title_view", true) == "y") $title .= $sep.get_bloginfo('name');
	} else if ((!is_front_page() && !is_home()) && the_keni('view_site_title') != "n") $title .= $sep.get_bloginfo('name');

	wp_reset_query();

	return esc_html($title);
}

//---------------------------------------------------------------------------
//	ディスクリプションの表示する関数
//---------------------------------------------------------------------------

function get_description_keni( $blogdesc = true ){
	$desc = "";
	if (is_home() or is_front_page()) {

		if ((get_option('page_for_posts') > 0) and (get_the_ID() != get_option('page_on_front'))) {
			$page = get_page(get_option('page_for_posts'));
			$desc = $page->post_excerpt;

		} else {
			$desc = "";
			$blogdesc = false;
		}

		if (get_query_var('paged') > 1) {
			$desc = sprintf(__('A list of archives of %s %s %s', 'keni'), trim(get_bloginfo('name')), show_page_number(), get_bloginfo('description'));
		}

	} else if (is_singular()){

		$desc = trim(do_shortcode(str_replace("[conts]","",get_the_excerpt())));
		
		if ($desc == "") {
			$desc = sprintf(__('A page of %s. %s', 'keni'), trim(get_the_title()), get_bloginfo('description'));
		}
		
		$blogdesc = false;

	} else if (is_archive()) {
		if(is_category() or is_tag()) {

			$desc = trim(strip_tags(category_description()));
			if ($desc == "") {
				$desc = sprintf( __('Archive List for %s','keni'), single_cat_title("",false));
			}
			$blogdesc = false;

		} else if(is_day()){
			$desc = sprintf( __('Archive List for %s','keni'), get_the_time(__('F j, Y','keni')));
		} else if(is_month()){
			$desc = sprintf( __('Archive List for %s','keni'), get_the_time(__('F Y','keni')));
		} else if(is_year()){
			$desc = sprintf( __('Archive List for %s','keni'), get_the_time(__('Y','keni')));
		} else if(is_author()) {
			if(have_posts()):
				while(have_posts()): the_post();
					$desc = get_the_author().sprintf( __('Archive List for authors','keni'));
				endwhile;
				wp_reset_query();
				endif;

		} elseif(is_tag()) {

			$desc = trim(strip_tags(tag_description()));
			if ($desc == "") {
				$desc = sprintf( __('Tag List for %s','keni'), single_tag_title("",false));
			} else {
				$blogdesc = false;
			}

		} else if(isset($_GET['paged']) && !empty($_GET['paged'])) {
			$desc = sprintf( __('Archive List for blog','keni'));
		}

		if (get_query_var('paged') > 1) {
			$desc .= show_page_number();
		}
		$blogdesc = false;


	} else if(is_search()){
		$desc = sprintf( __('Search Result for %s','keni'), get_search_query()).show_page_number();

	} else if(is_404()){
		$desc = sprintf( __('Sorry, but you are looking for something that isn&#8217;t here.','keni'));
		$blogdesc = false;
	} else {

	}

	if( $blogdesc == true )
	{
		$desc .= get_bloginfo('description');
	}
	else
	{
		if( $desc == "" )
		{
			$desc = get_bloginfo('description');

		}
	}
	return do_shortcode(str_replace("\n","",$desc));
}



function description_keni( $blogdesc = true ){	
	echo esc_html(get_description_keni());
}

//---------------------------------------------------------------------------
//	メタ・キーワードの表示する関数
//---------------------------------------------------------------------------

function keyword_keni(){

	global $wp_query;

	$keyword = $cat = $tag = "";

	$keyword = the_keni('keyword');
	if (substr($keyword,-1) != ",") {
		$keyword .= ",";
	}

	if (is_home() or is_front_page()) {
		if ((get_option('page_for_posts') > 0) and (get_the_ID() != get_option('page_on_front'))) {
			$id = $wp_query->post->ID;
		} else {
			$id = 0;
		}
	} else if (is_category()) {
		$keyword .= single_cat_title('',false);
		$id = 0;
	} else if (is_tag()) {
		$keyword .= single_tag_title('', false);
		$id = 0;
	} else {
		$id = $wp_query->post->ID;
	}

	if ($id > 0) {
		// カテゴリー名を取得
		$cat_data = get_the_category($id);
		if( !empty( $cat_data )) {
			foreach ($cat_data as $cat_val) {
				$cat_list[] = $cat_val->cat_name;
			}
			$cat = implode(",",$cat_list).",";
		}

		// タグを取得
		$tags = get_the_tags($id);

		if( !empty( $tags )) {
			foreach ( $tags as $tag_val ) {
				$tag_array[] = esc_html($tag_val->name);
			}
			$tag = implode(",",$tag_array).",";
		}
	
		if(is_day()){
			$keyword .= get_the_time('Y年,n月,j日,');
		} else if(is_month()){
			$keyword .= get_the_time('Y年,n月,');
		} else if( is_year()) {
			$keyword .= get_the_time('Y年,');
		} else if( is_search() ) {
			$keyword .= get_search_query().",";
		} else if( is_singular() ) {
			$keyword .= $cat.$tag;
		} else if (is_category()) {
			$cat = get_the_category();
			$keyword .= $cat[0]->cat_name;
		} else if (is_tag()) {
			$id = key(get_the_tags());
			$tags = get_the_tags();
			$keyword .= $tags[$id]->name;
		} else if (is_home() or is_front_page()) {
			$keyword .= $cat.$tag;
		}
	}

	do {
		if (substr($keyword,0,1) == ",") {
			$keyword = substr($keyword, 1);
		}
	} while(substr($keyword,0,1) == ",");
	do {
		if (substr($keyword,-1) == ",") {
			$keyword = substr($keyword, 0, -1);
		}
	} while(substr($keyword,-1) == ",");


	wp_reset_query();

	echo esc_html(strip_tags($keyword));
	
}


//---------------------------------------------------------------------------
//	最小ページの画像
//---------------------------------------------------------------------------
function page_image_keni($id = "") {

	if (is_singular()) {
		if (empty($id)) $id = get_the_ID();
		$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($id));
		$image =  (isset($image_array[0])) ? $image_array[0] : "";
	} else {
		$image = the_keni('mainimage');
	}

	return $image;
}

?>
