<form method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
	<div class="search-box">
		<input class="search" type="text" value="<?php if (!empty($_GET['s'])) echo esc_attr($_GET['s']); ?>" name="s" id="s"><button id="searchsubmit" class="btn-search"><img alt="検索" width="32" height="20" src="<?php bloginfo('template_url'); ?>/images/icon/icon-btn-search.png"></button>
	</div>
</form>