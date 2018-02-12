<!--▼サイトフッター-->
<footer class="site-footer">
	<div class="profile">
		<img id="image_fujii" src="<?php bloginfo('template_url'); ?>//images/footer/fujii.png" alt="藤井英敏">
		<p>カブ知恵　E-kabu代表</p>
		<p class="name">藤井英敏</p>
		<p>
			1965年生まれ。1989年3月、早稲田大学政治経済学部経済学科を卒業後、日興証券、独立系投資顧問を経て、株式会社フィスコ[3807]に入社。<br>
			フィスコでは、同社を代表するマーケット・アナリスト、執行役員として活躍。<br>
			フィスコを退職後、2005年に現在のカブ知恵を設立。証券マン、証券ディーラー、専業デイトレーダーなど業界の友人多数、。この豊富な人脈が強み。
		</p>
		<br>
		<p>
			歯に衣を着せない語り口と独自の投資理論をカブ知恵のサイト上のみならず、雑誌ダイヤモンドZAI、夕刊フジ、ヤフーファイナンス投資の達人などの媒体でコラムや記事を随時発信中。
		</p>
	</div>
	<div class="site-footer-in">
	<div class="site-footer-conts">
<?php	$footer = get_globalmenu_keni('footer_menu');
if ( $footer != "") { ?>
	<ul class="site-footer-nav"><?php	echo $footer; ?></ul>
<?php }
$comment = the_keni('footer_comment');
if ($comment != "") { ?>
<div class="site-footer-conts-area"><?php echo do_shortcode(richtext_formats($comment)); ?></div>
<?php } ?>
	</div>
	</div>
	<div class="copyright">
		<p><small>Copyright (C) <?php echo date("Y"); ?> <?php bloginfo('name'); ?> <span>All Rights Reserved.</span></small></p>
	</div>
</footer>
<!--▲サイトフッター-->


<!--▼ページトップ-->
<p class="page-top"><a href="#top"><img class="over" src="<?php bloginfo('template_url'); ?>/images/common/page-top_off.png" width="80" height="80" alt="<?php _e('To the top', 'keni'); ?>"></a></p>
<!--▲ページトップ-->

</div><!--container-->

<?php wp_footer(); ?>

<?php echo do_shortcode(the_keni('body_bottom_text'))."\n"; ?>
</body>
</html>
