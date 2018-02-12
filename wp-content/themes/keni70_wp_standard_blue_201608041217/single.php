<?php get_header(); ?>

<div class="main-body">
<div class="main-body-in">

<?php the_breadcrumbs(); ?>
	
	<!--▼メインコンテンツ-->
	<main>
	<div class="main-conts">
<?php while (have_posts()) : the_post(); ?>

		<!--記事-->
		<article id="post-<?php the_ID(); ?>" <?php post_class('section-wrap'); ?>>
			<div class="section-in">

			<header class="article-header">
				<h1 class="section-title" itemprop="headline"><?php h1_keni(); ?></h1>
<?php if (get_the_time('Y-m-d') != get_the_modified_date('Y-m-d')) { ?>
				<p class="post-date"><?php _e('Published on','keni') ?> : <time datetime="<?php the_time('Y-m-d'); ?>" itemprop="datePublished" content="<?php the_time('Y-m-d'); ?>" ><?php the_time(get_option('date_format')); ?></time> / <?php _e('Last modified on','keni') ?> : <time datetime="<?php the_modified_date('Y-m-d'); ?>" itemprop="dateModified" content="<?php the_modified_date('Y-m-d'); ?>"><?php echo get_the_modified_date(get_option('date_format')); ?></time></p>
<?php } else { ?>
				<p class="post-date"><time datetime="<?php the_time('Y-m-d'); ?>" itemprop="datePublished" content="<?php the_time('Y-m-d'); ?>" ><?php the_time(get_option('date_format')); ?></time></p>
				<meta itemprop="dateModified" content="<?php the_time('Y-m-d'); ?>">
<?php } ?>
				<?php if (the_keni('pv_view') == "y" && getViewPV(get_the_ID()) > 0) { ?><p class="post-pv"><?php viewPV(); ?>PV</p><?php } ?>
				<?php {
					$site_url = site_url();
					if (!preg_match("/\/$/", $site_url)) $site_url .= "/";

					$category_data = get_category_keni();
					if (!empty($category_data)) echo "<div class=\"post-cat\">\n".$category_data."\n</div>\n";
				} ?>
				<?php if (the_keni('social_post_view') == "y") get_template_part('social-button2'); ?>
			</header>

			<div class="article-body">
			<?php
			if (is_attachment()) {
				the_content();
				if ( has_excerpt() ) {
					echo "<p>".the_excerpt()."</p>";
					echo get_post_meta(get_the_ID(), '_wp_attachment_image_alt', true);
				}
			} else  if (is_singular()) {

					the_content();

					wp_link_pages( array(
						'before'      => '<div class="link-pages"><span class="link-pages-cap">'. __('Pages','keni').':</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
					) );
			}
			?>
			</div><!--article-body-->

			<?php if (the_keni('social_post_view') == "y") get_template_part('social-button2'); ?>
			
			<?php if(get_the_tags()){ ?>
			<div class="post-tag">
			<p><?php _e('Tags', 'keni'); ?> : <?php the_tags('', ', '); ?></p>
			</div>
			<?php }
			
			relation_keni();
			
			$next_link = get_next_post_link('<p class="page-nav-next">「%link」</p>','%title',true);
			if ($next_link != "") {
				preg_match("/(.+rel=\"next\">)(.+?)(<\/a>.+)/", $next_link, $next_title);
				if (isset($next_title[3])) $next_link = $next_title[1].esc_html($next_title[2]).$next_title[3];
			}

			$prev_link = get_previous_post_link('<p class="page-nav-prev">「%link」</p>','%title',true);
			if ($prev_link != "") {
				preg_match("/(.+rel=\"prev\">)(.+?)<\/a>/", $prev_link, $prev_title);
				if (isset($prev_title[3])) $prev_link = $prev_title[1].esc_html($prev_title[2]).$prev_title[3];
			}

			if ($next_link != "" || $prev_link != "") { ?>
			<div class="page-nav-bf cont-nav">
<?php	echo $next_link."\n";
			echo $prev_link."\n"; ?>
			</div>
			<?php } ?>

			<section id="comments" class="comments-area">
			<?php comments_template(); ?>
			</section>

			</div><!--section-in-->
		</article><!--記事-->

<?php endwhile; ?>

	</div><!--main-conts-->
	</main>
	<!--▲メインコンテンツ-->

	<?php get_sidebar(); ?>

</div>
</div>

<?php get_footer(); ?>