<?php get_header(); ?>

<div class="main-body">
<div class="main-body-in">

<!--▼パン屑ナビ-->
<?php the_breadcrumbs(); ?>
<!--▲パン屑ナビ-->

	<!--▼メインコンテンツ-->
	<main>
	<div class="main-conts">

		<h1 class="archive-title"><?php archive_title_keni(); ?></h1>

<?php if (the_keni('social_archive_view') == "y") {
		echo "<div class=\"float-area\">\n";
		get_template_part('social-button2');
		echo "</div>\n";
		}

		if (is_category() or is_tag()) {
			if (is_category()) {
				$content_araay = get_post_meta( get_query_var('cat'), "content");
			} else {
				$content_araay = get_post_meta( get_query_var('tag_id'), "content");
			}
			if (isset($content_araay[0]) and ($content_araay[0] != "") and (get_query_var('paged') <= 1)) {
				echo "<div class=\"content-area section-wrap\">\n<div class=\"section-in\">\n";
				echo do_shortcode(apply_filters( 'the_content', stripslashes($content_araay[0]), 10 ));
				echo "\n</div>\n</div>\n";
			}
		}
		 ?>

		<?php // 投稿一覧
		get_template_part('cont'); ?>

	</div><!--main-conts-->
	</main>
	<!--▲メインコンテンツ-->

<?php get_sidebar(); ?>

</div>
</div>

<?php get_footer(); ?>