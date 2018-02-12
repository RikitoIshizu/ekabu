<?php get_header(); ?>

<div class="main-body">
<div class="main-body-in">

<!--▼パン屑ナビ-->
<?php the_breadcrumbs(); ?>
<!--▲パン屑ナビ-->

	<!--▼メインコンテンツ-->
	<main>
	<div class="main-conts">

		<section class="section-wrap">
			<div class="section-in">

			<h1 class="section-title"><?php _e( 'Sorry, but you are looking for something that isn&#8217;t here.', 'keni' ); ?></h1>
			<div class="contents">
			<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'keni' ); ?></p>
			<p><?php get_search_form(); ?></p>
			</div>

			</div><!--section-in-->
		</section><!--記事-->

		<div class="float-area">
		<?php echo do_shortcode('[newpost rows=5, social=1 show_date="default"]'); ?>
		</div>

	</div><!--main-conts-->
	</main>
	<!--▲メインコンテンツ-->

	<?php get_sidebar(); ?>

</div>
</div>

<?php get_footer(); ?>