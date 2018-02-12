<!DOCTYPE html>
<html lang="ja" class="<?php echo getPageLayout($post->ID); ?>"<?php if (the_keni('gp_view') == "y") { ?> itemscope itemtype="http://schema.org/<?php echo getMicroCodeType().'"'; } ?>>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">

<title><?php title_keni(); ?></title>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php if (the_keni('mobile_layout') == "y") { ?><meta name="viewport" content="width=device-width, initial-scale=1.0"><?php } ?>

<?php if (!the_keni('view_meta')) { ?>
<?php if (the_keni('view_meta_keyword') && the_keni('view_meta_keyword') == "y") { ?>
<meta name="keywords" content="<?php keyword_keni(); ?>">
<?php } ?>
<?php if (the_keni('view_meta_description') && the_keni('view_meta_description') == "y") { ?>
<meta name="description" content="<?php description_keni(); ?>">
<?php }
} else if (the_keni('view_meta') == "y") { ?>
<meta name="keywords" content="<?php keyword_keni(); ?>">
<meta name="description" content="<?php description_keni(); ?>">
<?php }
wp_enqueue_script('jquery');
if (get_option('blog_public') != false) echo getIndexFollow();
canonical_keni();
pageRelNext();
wp_head();

facebook_keni();
tw_cards_keni();
microdata_keni();

if (function_exists("get_site_icon_url") && get_site_icon_url() == "") { ?>
<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/favicon.ico">
<link rel="apple-touch-icon" href="<?php bloginfo('template_url'); ?>/images/apple-touch-icon.png">
<link rel="apple-touch-icon-precomposed" href="<?php bloginfo('template_url'); ?>/images/apple-touch-icon.png">
<link rel="icon" href="<?php bloginfo('template_url'); ?>/images/apple-touch-icon.png">
<?php } ?>
<!--[if lt IE 9]><script src="<?php bloginfo('template_url'); ?>/js/html5.js"></script><![endif]-->
<?php echo do_shortcode(the_keni('meta_text'))."\n";
if (is_single() || is_page()) echo get_post_meta( $post->ID, 'page_tags', true)."\n";
?>
</head>
<?php
$gnav = ((get_globalmenu_keni('top_menu') == "") || ((is_front_page() || is_home() || is_singular()) && get_post_meta($post->ID, 'menu_view', true) == "n")) ? "no-gn" : "";	// メニューを表示しない場合は、classにno-gnを設定する

// ランディングページで画像をフルサイズで表示する
if (is_singular(LP_DIR) && get_post_meta( $post->ID, 'fullscreen_view', true) == "y") {
	$gnav .= ($gnav != "") ? " lp" : "lp"; ?>
	<body <?php body_class($gnav); ?>>
	<?php echo do_shortcode(the_keni('body_text'))."\n"; ?>
	<div class="container">
	<header id="top" class="site-header full-screen"<?php if (get_post_meta( $post->ID, 'header_image', true) != "") { ?> style="background-image: url(<?php echo get_post_meta( $post->ID, 'header_image', true); ?>)"<?php } ?>>
		<div class="site-header-in">
			<div class="site-header-conts">
				<h1 class="site-title"><?php echo (get_post_meta($post->ID, 'page_h1', true)) ? esc_html(get_post_meta($post->ID, 'page_h1', true)) : get_h1_keni(); ?></h1>
				<?php echo get_post_meta($post->ID, 'catch_text', true) ? "<p class=\"lp-catch\">".esc_html(get_post_meta($post->ID, 'catch_text', true))."</p>" : ""; ?>
				<p><a href="#main"><img src="<?php bloginfo('template_url'); ?>/images/common/icon-arw-full-screen.png" alt="メインへ" width="48" height="48"></a></p>
			</div>
		</div>
	</header>
<?php
	if (strpos($gnav, "no-gn") === false) { ?>
	<!--▼グローバルナビ-->
	<nav class="global-nav">
		<div class="global-nav-in">
			<div class="global-nav-panel"><span class="btn-global-nav icon-gn-menu">メニュー</span></div>
			<ul id="menu">
			<?php echo get_globalmenu_keni('top_menu'); ?>
			</ul>
		</div>
	</nav>
	<!--▲グローバルナビ-->
<?php }

// それ以外の場合
} else { ?>
	<body <?php body_class($gnav); ?>>
	<?php echo do_shortcode(the_keni('body_text'))."\n"; ?>
	<div class="container">
		<header id="top" class="site-header <?php if (is_singular(LP_DIR)) { echo 'normal-screen'; } ?>">
		<div id="header_contents" class="clearfix">
			<div id="header_left" class="clearfix">
				<img id="header_img" src="<?php bloginfo('template_url'); ?>/images/header/logo.png" alt="ヘッダーロゴ">
				<div id="page_description">
					<p>
						<span style="font-weight:bold">投資に役立つ銘柄情報のE-kabu</span>
						<br>
						初心者のための株式投資情報サイト
						<br>
						必勝テクニカル分析でこれから騰がる銘柄を逃さない<br><span style="font-weight:bold">デイトレ情報なら「いいかぶ」</span>
					</p>
				</div>
			</div>
			<div id="site_m">
				<p class=site_map>サイトマップ</p>
			</div>
			<div id="header_right" class="clearfix">
				<p>E-kabuでは<span style="color: #ec900c;">無料メルマガ登録</span>が</p>
				<p>絶対おすすめ!!</p>
				<p>専用バナーより今すぐご登録下さい!!</p>
				<p style="margin-top: 6px;">【厳選３銘柄】</p>
				<p>寄り付き前に毎朝配信!!完全無料!!</p>
			</div>
			<img id="header_center" src="<?php bloginfo('template_url'); ?>/images/header/kuroiwa.png" alt="ヘッダーロゴ">
		</div>
	<?php

	if ($gnav == "") {	?>
		<!--▼グローバルナビ-->
		<nav class="global-nav">
			<div class="global-nav-in">
				<div class="global-nav-panel"><span class="btn-global-nav icon-gn-menu">メニュー</span></div>
				<ul id="menu">
				<?php echo get_globalmenu_keni('top_menu'); ?>
				</ul>
			</div>
		</nav>
		<!--▲グローバルナビ-->
	<?php }

	if (is_front_page() && (!isset($_GET['post_type']) || $_GET['post_type'] == "")) { ?>
		<div class="main-image">
<?php	$mainimage = the_keni("mainimage");
		if (!empty($mainimage)) {
			if (the_keni("mainimage_posision") == "image") { ?>
				<div class="main-image-in<?php if (the_keni('mainimage_wide') == "y") { ?> wide<?php } ?>">
				<img  class="header-image" src="<?php echo esc_url( $mainimage ); ?>" alt="<?php echo esc_html(the_keni("mainimage_alt")); ?>" />
				</div>
<?php } else { ?>
				<div class="main-image-in-text<?php if (the_keni('mainimage_wide') == "y") { ?> wide<?php } ?>" style="background-image: url(<?php echo esc_url( $mainimage ); ?>);">
					<div class="main-image-in-text-cont">
					<?php if (the_keni("main_catchcopy") != "") { ?><p class="main-copy"><?php echo esc_html(the_keni("main_catchcopy")); ?></p><?php } ?>

					<?php if (the_keni("sub_catchcopy") != "") { ?><p class="sub-copy"><?php echo esc_html(the_keni("sub_catchcopy")); ?></p><?php } ?>

					<?php if (the_keni("free_catchcopy") != "") { echo "<div class=\"main-image-in-text-box\">\n".the_keni("free_catchcopy")."\n</div>\n"; } ?>

					</div>
				</div>
<?php }
		} else if (the_keni("mainimage_posision") != "image") { ?>
				<div class="main-image-in-text<?php if (the_keni('mainimage_wide') == "y") { ?> wide<?php } ?>" style="background-color: #<?php echo the_keni('mainimage_bg_color'); ?>;">
					<div class="main-image-in-text-cont">
					<?php if (the_keni("main_catchcopy") != "") { ?><p class="main-copy"><?php echo esc_html(the_keni("main_catchcopy")); ?></p><?php } ?>

					<?php if (the_keni("sub_catchcopy") != "") { ?><p class="sub-copy"><?php echo esc_html(the_keni("sub_catchcopy")); ?></p><?php } ?>

					<?php if (the_keni("free_catchcopy") != "") { echo "<div class=\"main-image-in-text-box\">\n".the_keni("free_catchcopy")."\n</div>\n"; } ?>
					</div>
				</div>
<?php } ?>
		</div>

<?php } ?>
	</header>
<?php
}
?>
<!--▲サイトヘッダー-->
