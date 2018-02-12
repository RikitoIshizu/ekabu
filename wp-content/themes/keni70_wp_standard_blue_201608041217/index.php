<?php get_header(); ?>

<div class="main-body">
<div class="main-body-in">

<?php if ((is_front_page() && get_query_var('paged') > 1) || is_home() || is_front_page() && (isset($_GET['post_type']) && $_GET['post_type'] != "")) { ?>
<!--▼パン屑ナビ-->
<?php the_breadcrumbs(); ?>
<!--▲パン屑ナビ-->
<?php } ?>
	
	<!--▼メインコンテンツ-->
	<main>
	<div class="main-conts">
	
<?php $post_type = get_query_var('post_type');
if ((is_front_page() && empty($post_type) && the_keni('social_top_view') == "y") || (!empty($post_type) && the_keni('social_archive_view') == "y")) { ?>
	<div class="float-area">
		<?php get_template_part('social-button2') ?>
	</div>
<?php } ?>
<?php
	// ページの先頭に表示をするリスト
	if (is_home() && get_query_var('paged') == 0) do_shortcode('[sticky rows=5, show_date="default"]');

	// 最新情報
	if (is_home() && empty($post_type) && is_front_page() && get_query_var('paged') == 0 && the_keni('new_info') == "y") {
		$num_of_posts = (preg_match("/^[0-9]+$/", mb_convert_kana(the_keni('new_info_rows'), "n"))) ? mb_convert_kana(the_keni('new_info_rows'), "n") : 5;
		echo "<section class=\"section-wrap\">\n<div class=\"section-in \">\n".do_shortcode('[newpost rows='.$num_of_posts.', show_date="default"]')."\n</div>\n</section>\n";
	}
	// 投稿一覧
	get_template_part('cont'); ?>

	</div><!--main-conts-->
	</main>
	<!--▲メインコンテンツ-->

	<?php get_sidebar(); ?>

</div>
</div>

<?php get_footer(); ?>