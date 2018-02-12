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

		<?php // 投稿一覧
		get_template_part('cont'); ?>

	</div><!--main-conts-->
	</main>
	<!--▲メインコンテンツ-->

	<?php get_sidebar(); ?>

</div>
</div>

<?php get_footer(); ?>