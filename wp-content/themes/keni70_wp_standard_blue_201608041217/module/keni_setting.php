<?php
/*----------------------------------------
	賢威7.0

	賢威の設定メニュー
	
	第1版　2015. 9.29
	第2版　2015.11. 4
	第3版　2015.11.11

	株式会社 ウェブライダー
----------------------------------------*/
global $wpdb;

if (!function_exists('getKeniMenuList')) {
	function getKeniMenuList($target=NULL) {
		$list = getKeniSetting();
		if (isset($list) and count($list) > 0) {
			foreach ($list as $no => $list_val) {
				$ks_group = $list_val['ks_group'];
				$keni_menu_list[$ks_group][$no] = $list_val;
			}
		}
		if ($target==NULL) {
			return $keni_menu_list;
		} else {
			return $keni_menu_list[$target];
		}
	}
}

if (isset($_POST) and (isset($_POST['submit']) or isset($_POST['submit_x']))) {

	if (isset($_POST['page'])) $keni_menu_list = getKeniMenuList($_POST['page']);

	// データベースを更新する為のデータを生成し、updateする
	if (isset($_POST['setting'])) {
		
		$reload = "n";
		unset($data);

		if (isset($keni_menu_list) && is_array($keni_menu_list) && count($keni_menu_list) > 0) {
			foreach ($keni_menu_list as $ks_id => $setting) {
				if (isset($ks_id) && preg_match("/[0-9]+$/", $ks_id) && $ks_id > 0) {
					$where['ks_id'] = $ks_id;
					if (isset($_POST['setting'][$ks_id])) {
						$data['ks_val'] = stripslashes($_POST['setting'][$ks_id]);
					} else if ($setting['ks_type'] == "yn" || $setting['ks_type'] == "check" || $setting['ks_type'] == "page2_index") {
						$data['ks_val'] = "y";
					} else if ($setting['ks_type'] == "active") {
						$data['ks_val'] = "n";
					}

					if ($setting['ks_type'] == "custom") {	// カスタム投稿タイプの場合、リライトルールを更新する
						$data['ks_val'] = strtolower(trim(mb_convert_kana($data['ks_val'], "s", mb_detect_encoding($data['ks_val']))));
						if (($setting['ks_val'] != $data['ks_val']) && $data['ks_val'] != "" && preg_match("/^[a-z0-9_-]+$/u", $data['ks_val'])) {
							$wpdb->update(KENI_SET, $data, $where);
							$wpdb->update($wpdb->prefix."posts", array('post_type' => $data['ks_val']), array('post_type' => $setting['ks_val']));
							$templates_dir = str_replace("/module","",dirname(__FILE__));	// テンプレートのリネーム
							if (is_file($templates_dir."/single-".$setting['ks_val'].".php")) rename($templates_dir."/single-".$setting['ks_val'].".php", $templates_dir."/single-".$data['ks_val'].".php");				
						}
						$reload = "y";
					} else {
						$wpdb->update(KENI_SET, $data, $where);
					}
				}
			}
		}

		// タイトルとディスクリプションのアップデート
		if (isset($_POST['blogname'])) {
			update_option('blogname', stripslashes($_POST['blogname']));
		}
		if (isset($_POST['blogdescription'])) {
			update_option('blogdescription', stripslashes($_POST['blogdescription']));
		}

		if ($reload == "y") {			
			header("Location: " . $_SERVER['REQUEST_URI'],true,302);
			exit;
		}
	}
}




if (!function_exists('viewMenu')) {
	function viewMenu() {
		
		$keni_menu_list = getKeniMenuList();
	
		preg_match("/page=(.+)/", $_SERVER['REQUEST_URI'], $target);
	
		if (isset($target[1])) {
	
			if (preg_match("/^keni_character/", $target[1])) {
	
				wp_register_script('excolor', get_bloginfo('template_directory') .'/js/colpick.js','','',true);
				wp_enqueue_script('excolor');
				wp_register_script('keni-cc', get_bloginfo('template_directory') .'/js/keni_cc.js','','',false);
				wp_enqueue_script('keni-cc');
				wp_register_style('excolor_css', get_stylesheet_directory_uri() .'/colpick.css');
				wp_enqueue_style('excolor_css');
	
				require_once(TEMPLATEPATH."/module/character.php");
	
			} else {
		
				// 最初のキーを取得
				if (isset($keni_menu_list)) {
					$key_first = key($keni_menu_list);
					
					// 項目を取得
					if (isset($target[1]) and ($target[1] != "") and $target[1] != "keni_admin_menu") {
						$key = urldecode($target[1]);
						
						if (isset($keni_menu_list[$key])) {
							$view_list = $keni_menu_list[$key];
						}
					}	
				}

				if (!isset($view_list)) {
					$view_list = $keni_menu_list[$key_first];
					$key = $key_first;
				}
			}
	
			// カスタムニューのリストを取得
			$menu_list = get_menu_list();
			
			global $layout;
			if (isset($key)) {
				if ($key != "トップページ" && $key != "一覧ページ") {
					unset($layout['def']);
				}
			}
			
			// index, follow のリストを取得
			global $index_area;
			global $index_list_menu;
			
			if (isset($view_list)) {

				foreach ($view_list as $ks_id => $val) {
					if ($val['ks_sub_group'] == "") $val['ks_sub_group'] = "no_sub_group_".$ks_id;
					
					
					$this_list[$val['ks_group']][$val['ks_sub_group']][$ks_id] = $val;
				}

				echo "<div class=\"wrap\">\n";
			
				echo "<div id=\"icon-options-general\" class=\"icon32\"><br />\n</div>\n<h2>".$key."</h2>\n";
			
				if ($key == "賢威の設定（サイト内共通）") {
					echo "<p>賢威テンプレート独自の設定を行います。<br />ここでは「サイト内共通」の項目を設定します。</p>\n";
				} else if ($key != "賢威の設定（サイト内共通）") {
					echo "<p>賢威テンプレート独自の設定を行います。<br />ここでは「".$key."」に関する項目を設定します。</p>\n";
				}

				echo "<p>詳しい賢威の設定方法などは、<a href=\"https://www.keni.jp/support/members/manual/\" target=\"_blank\">賢威7 HTML版・WordPress版導入マニュアル</a>をご覧下さい。（<span class=\"keni_note\">※ 賢威サポートページにログインが必要です</span>）</p>\n";
	
				echo "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\">\n";
				wp_nonce_field('update-options');
			
				if ($key == "一覧ページ") {
	
					// 現在のデフォルト値を取得
					$default_layout = the_keni('layout');
					if (empty($default_layout)) {
						$default_layout = "col2";
					}
			
					$now_count = 0;
					foreach ($view_list as $no => $val) {
						if ($now_count%2 == 0) {
							echo "<div class=\"metabox-holder\" id=\"".$val['ks_sys_cont']."\">\n<div class=\"postbox\">\n";
							echo "<h3 class=\"hndle\"><span>".$val['ks_view_cont']."</span></h3>\n";
							echo "<div class=\"inside\">\n";
							echo "<h4>レイアウト</h4>\n";
			
						} else {
							echo "\n<div class=\"inside\">\n";
							echo "<h4>".$val['ks_view_cont']."</h4>\n";
						}
			
						if ($val['ks_type'] == "column") {
							foreach ($layout as $type => $name) {
								if ($type == $val['ks_val']) {
									echo "<label class=\"lo checked-lo lo-".$type."\" for=\"setting".$no.$type."\"><input type=\"radio\" name=\"setting[".$no."]\" value=\"".$type."\" id=\"setting".$no.$type."\" checked=\"checked\" class=\"select-lo\">";
								} else {
									echo "<label class=\"lo lo-".$type."\" for=\"setting".$no.$type."\"><input type=\"radio\" name=\"setting[".$no."]\" value=\"".$type."\" id=\"setting".$no.$type."\" class=\"select-lo\">";
								}
								if ($type == "def") {
									echo "<span><img src=\"".get_bloginfo('template_directory')."/images/admin/".$default_layout.".png\" alt=\"".$name."\" title=\"".$name."\" /></span>".$name."\n</label>";
								} else {
									echo "<span><img src=\"".get_bloginfo('template_directory')."/images/admin/".$type.".png\" alt=\"".$name."\" title=\"".$name."\" /></span>".$name."\n</label>";
								}
							}
				
						} else if ($val['ks_type'] == "meta_index") {
							unset($index_list_menu['def']);
						echo "<ul class=\"value-list\">";
							foreach ($index_list_menu as $type => $name) {
								if ($type == $val['ks_val']) {
									echo "<li><input type=\"radio\" name=\"setting[".$no."]\" value=\"".$type."\" id=\"setting".$no.$type."\" checked=\"checked\">";
								} else {
									echo "<li><input type=\"radio\" name=\"setting[".$no."]\" value=\"".$type."\" id=\"setting".$no.$type."\">";
								}
								echo "<label for=\"setting".$no.$type."\">".$name."</label></li>\n";
							}
						echo "</ul>\n";
						}
									
						echo "<script type=\"text/javascript\">
						jQuery.noConflict();
						(function($) {
							$(function () {
								$('#list_category .select-lo').on('change',function(){
									$('#list_category .lo').removeClass('checked-lo');
									if($(this).is(':checked')) {
										$(this).parent('label').addClass('checked-lo');
									}
								});
		
								$('#list_tag .select-lo').on('change',function(){
									$('#list_tag .lo').removeClass('checked-lo');
									if($(this).is(':checked')) {
										$(this).parent('label').addClass('checked-lo');
									}
								});
		
								$('#list_archive .select-lo').on('change',function(){
									$('#list_archive .lo').removeClass('checked-lo');
									if($(this).is(':checked')) {
										$(this).parent('label').addClass('checked-lo');
									}
								});
		
								$('#list_author .select-lo').on('change',function(){
									$('#list_author .lo').removeClass('checked-lo');
									if($(this).is(':checked')) {
										$(this).parent('label').addClass('checked-lo');
									}
								});
		
								$('#list_search .select-lo').on('change',function(){
									$('#list_search .lo').removeClass('checked-lo');
									if($(this).is(':checked')) {
										$(this).parent('label').addClass('checked-lo');
									}
								});
							});
						})(jQuery);
						</script>\n</div>\n";
			
						if ($now_count%2 == 1) {
							echo "</div>\n</div>\n";
						}
				
						$now_count++;
					}
	
				} else {
			
			
					if (($key_first == $key) or !isset($key) ) {
						echo "<div class=\"metabox-holder\">\n<div class=\"postbox\">\n";
						echo "<h3 class=\"hndle\"><label for=\"blogname\"><span>サイトのタイトル</span></label></h3>\n";
						echo "<div class=\"inside\">\n";
						echo "<div><input name=\"blogname\" type=\"text\" id=\"blogname\" value=\"".get_bloginfo('name')."\" class=\"regular-text\" /></div>\n";
						echo "</div>\n</div>\n</div>\n";
						
						echo "<div class=\"metabox-holder\">\n<div class=\"postbox\">\n";
						echo "<h3 class=\"hndle\"><label for=\"blogdescription\"><span>サイトの簡単な説明</span></label></h3>\n";
						echo "<div class=\"inside\">\n";
						echo "<div><input name=\"blogdescription\" type=\"text\" id=\"blogdescription\" value=\"".get_bloginfo('description')."\" class=\"regular-text\" />\n";
						echo "<p class=\"description\">このサイトの簡単な説明。メタディスクリプションなどに使用します</p></div>\n";
						echo "</div>\n</div>\n</div>\n";
					}
	
					$color_area = "n";

					foreach ($this_list as $group_name => $group_val) {
	
						if ($val['ks_type'] == "include" && $val['ks_val'] != "") {
							require_once(get_template_directory()."/includes/".$val['ks_sys_cont'].".php");
							eval($val['ks_val']);
						} else if ($group_name != "一覧ページ") {

							foreach ($group_val as $sub_group_name => $sub_val) {

								if (!preg_match("/^no_sub_group/",$sub_group_name)) {
									echo "<div class=\"metabox-holder\">\n<div class=\"postbox\">\n";
									echo "<h3 class=\"hndle\"><span>".$sub_group_name."</span></h3>\n";
								}

								foreach ($sub_val as $no => $val) {
									if (preg_match("/^no_sub_group/",$sub_group_name)) {
										echo "<div class=\"metabox-holder\">\n<div class=\"postbox\">\n";
										echo "<h3 class=\"hndle\"><span>".$val['ks_view_cont']."</span></h3>\n";
									}
									
				
									echo "<div class=\"inside\">\n";

									if (!preg_match("/^no_sub_group/",$sub_group_name)) {
										echo "<h4 class=\"keni-sub-title\">".$val['ks_view_cont']."</h4>\n";
									}
				
									if ($val['ks_type'] == "menu") {
										if (preg_match('/^footermenu/', $val['ks_sys_cont'])) {
											unset($menu_list['-1']);
										}
										echo "<select name=\"setting[".$no."]\">\n";
										foreach ($menu_list as $term_id => $name) {
											if ($term_id == $val['ks_val']) {
												echo "<option value=\"".$term_id."\" selected=\"selected\">".$name."</option>\n";
											} else {
												echo "<option value=\"".$term_id."\">".$name."</option>\n";
											}
										}
										echo "</select>";
			
									} else if ($val['ks_type'] == "radio") {	// TOPページのサイドバーエリアの設定
										$top_side_bar = array("def" => "賢威の設定に従う", "y" => "表示する", "n" => "表示しない");	
										
										foreach ($top_side_bar as $type => $name) {
											if ($type == $val['ks_val']) {
												echo "<input type=\"radio\" name=\"setting[".$no."]\" value=\"".$type."\" id=\"setting".$no.$type."\" checked=\"checked\">";
											} else {
												echo "<input type=\"radio\" name=\"setting[".$no."]\" value=\"".$type."\" id=\"setting".$no.$type."\">";
											}
											echo "<label for=\"setting".$no.$type."\">".$name."</label>\n";
										}
						

									} else if ($val['ks_type'] == "column") {
										foreach ($layout as $type => $name) {
											if ($type == $val['ks_val']) {
												echo "<label class=\"lo checked-lo lo-".$type."\" for=\"".$type."\"><input type=\"radio\" name=\"setting[".$no."]\" id=\"".$type."\" value=\"".$type."\" id=\"setting".$no.$type."\" class=\"select-lo\" checked=\"checked\">";
											} else {
												echo "<label class=\"lo lo-".$type."\" for=\"".$type."\"><input type=\"radio\" name=\"setting[".$no."]\" id=\"".$type."\" value=\"".$type."\" id=\"setting".$no.$type."\" class=\"select-lo\">";
											}
											echo "<span><img src=\"".get_bloginfo('template_directory')."/images/admin/".$type.".png\" alt=\"".$name."\" title=\"".$name."\" /></span>".$name."\n</label>";
				
										}
										
										echo "<script type=\"text/javascript\">
										jQuery.noConflict();
										(function($) {
											$(function () {
												$('.select-lo').on('change',function(){
													$('.lo').removeClass('checked-lo');
													if($(this).is(':checked')) {
														$(this).parent('label').addClass('checked-lo');
													}
												});
											});
										})(jQuery);
										</script>";

									// 2ページ目以降はnoindex
									} else if ($val['ks_type'] == "page2_index") {
										if ($val['ks_val'] == "y") {
											echo "<input type=\"checkbox\" name=\"setting[".$no."]\" value=\"n\" id=\"keni_".$no."\">";
										} else {
											echo "<input type=\"checkbox\" name=\"setting[".$no."]\" value=\"n\" checked=\"checked\" id=\"keni_".$no."\">";
										}
										echo "<label for=\"keni_".$no."\">しない</label>\n";

									// 無効チェック
									} else if ($val['ks_type'] == "yn") {

										if ($val['ks_val'] == "y") {
											echo "<input type=\"checkbox\" name=\"setting[".$no."]\" value=\"n\" id=\"keni_".$no."\">";
										} else {
											echo "<input type=\"checkbox\" name=\"setting[".$no."]\" value=\"n\" checked=\"checked\" id=\"keni_".$no."\">";
										}
										echo "<label for=\"keni_".$no."\">無効にする</label>\n";

									// 有効チェック
									} else if ($val['ks_type'] == "active") {
										if ($val['ks_val'] == "y") {
											echo "<input type=\"checkbox\" name=\"setting[".$no."]\" value=\"y\" checked=\"checked\" id=\"keni_".$no."\">";
										} else {
											echo "<input type=\"checkbox\" name=\"setting[".$no."]\" value=\"y\" id=\"keni_".$no."\">";
										}
										echo "<label for=\"keni_".$no."\">有効にする</label>\n";

									// checkbox
									} else if ($val['ks_type'] == "check") {
										if ($val['ks_val'] == "y") {
											echo "<input type=\"checkbox\" name=\"setting[".$no."]\" value=\"n\" id=\"keni_".$no."\">";
										} else {
											echo "<input type=\"checkbox\" name=\"setting[".$no."]\" value=\"n\" checked=\"checked\" id=\"keni_".$no."\">";
										}
										echo "<label for=\"keni_".$no."\">表示しない</label>\n";
						
									// select
									} else if ($val['ks_type'] == "select") {
										// コメント欄に入っている選択項目を取得する
										preg_match_all("/'(.*?):(.*?)'/us", $val['ks_ext'], $sel_array, PREG_SET_ORDER);
										if (is_array($sel_array) && count($sel_array) > 0) {
											foreach ($sel_array as $sel_val) {
												if ($sel_val[1] == $val['ks_val']) {
													echo "<input type=\"radio\" name=\"setting[".$no."]\" value=\"".$sel_val[1]."\" id=\"keni_".$no."_".$sel_val[1]."\" checked=\"checked\"><label for=\"keni_".$no."_".$sel_val[1]."\">".$sel_val[2]."</label>\n";
												} else {
													echo "<input type=\"radio\" name=\"setting[".$no."]\" value=\"".$sel_val[1]."\" id=\"keni_".$no."_".$sel_val[1]."\"><label for=\"keni_".$no."_".$sel_val[1]."\">".$sel_val[2]."</label>\n";
												}
											}
										}
									} else if ($val['ks_sys_cont'] == "globalmenu" || $val['ks_sys_cont'] == "footermenu1" || $val['ks_sys_cont'] == "footermenu2") {
										echo "<select name=\"setting[".$no."]\">\n";
										foreach ($menu_list as $term_id => $name) {
											if ($term_id == $val['ks_val']) {
												echo "<option value=\"".$term_id."\" selected=\"selected\" />".$name."\n";
											} else {
												echo "<option value=\"".$term_id."\" />".$name."\n";
											}
										}
										echo "</select>\n";
									} else if ($val['ks_sys_cont'] == "author_view") {
										echo "<select name=\"setting[".$no."]\">\n";
										foreach ($user_list as $user_id => $display_name) {
											if ($user_id == $val['ks_val']) {
												echo "<option value=\"".$user_id."\" selected=\"selected\" />".$display_name."\n";
											} else {
												echo "<option value=\"".$user_id."\" />".$display_name."\n";
											}
										}
										echo "</select>\n";
						
									// 画像データ
									} else if ( $val['ks_type'] == "image") {
				
										if($val['ks_val'] !="") {
											$info = @getimagesize($val['ks_val']);
											if (isset($info) and is_array($info)) {
												if ($info[0] > 600) {
													$width = $info[0] / 2;
													$height = $info[1] / 2;
												} else {
													$width = $info[0];
													$height = $info[1];
												}
												echo "<p id=\"keni_img_".$no."\"><img src=\"".$val['ks_val']."\" width=\"".$width."\" height=\"".$height."\" /></p>\n";
												if ($width != $info[0]) echo "<p class=\"keni_note\">※画面サイズより大きい画像は縮小して表示されています。</p>\n";
											} else {
												echo "<p id=\"keni_img_".$no."\"><img src=\"".$val['ks_val']."\"></p>\n";
											}
										} else {
											echo "<p id=\"keni_img_".$no."\"></p>\n";
										}
										echo "<p><input id=\"keni_upload_image_".$no."\" class=\"regular-text\" type=\"text\" size=\"90\" name=\"setting[".$no."]\" value=\"";
										if($val['ks_val'] !="") echo esc_html($val['ks_val']);
										echo "\" /></p>\n";
										echo "<p><input type=\"button\" class=\"keni_upload_image_button\" id=\"keni_upload_image_button_".$no."\" value=\"画像を設定する\" /></p>\n";
						
						
									} else if ($val['ks_sys_cont'] == "top_content") {
										
										wp_editor(the_keni('top_content'), "content", array('media_buttons' => false, 'textarea_name' => "setting[".$no."]", 'editor_css' => keni_rte_css()));
							
									} else if ($val['ks_type'] == "textarea") {
						
										echo "<textarea class=\"keni_textarea\" name=\"setting[".$no."]\" cols=\"70\" rows=\"5\">".$val['ks_val']."</textarea>\n";
						
									} else if ($val['ks_type'] == "richtext") {
										
										wp_editor(the_keni($val['ks_sys_cont']), "content", array('media_buttons' => true, 'textarea_name' => "setting[".$no."]", 'editor_css' => keni_rte_css()));
			
						
									} else if ($val['ks_sys_cont'] == "tw_type") {
										global $tw_type;
										$sel_tw_type = the_keni("tw_type");
										foreach ($tw_type as $tw_type_val) {
											if ($tw_type_val == $sel_tw_type) {
												echo "<label><input type=\"radio\" name=\"setting[".$no."] value=\"".$tw_type_val."\" checked=\"checked\" />".$tw_type_val."</label>\n";
											} else {
												echo "<label><input type=\"radio\" name=\"setting[".$no."] value=\"".$tw_type_val."\" />".$tw_type_val."</label>\n";
											}
										}

									// カラーピッカー
									} else if ($val['ks_type'] == "color") {

										$color =  (isset($val['ks_val']) && $val['ks_val'] != "") ? $val['ks_val'] : "ffffff";
										echo "<script>\ndefault_color='".$color."';\n</script>\n";
										echo "#<input type=\"text\" id=\"picker\" name=\"setting[".$no."]\" maxlength=\"6\" size=\"6\" value=\"".esc_html($color)."\" >\n";
										echo "<style type=\"text/css\">\n#picker {\nmargin:0;\n width:100px;\n outline: 1px solid #ccc;\nborder: 0;\nborder-right:20px solid #".$color.";\n line-height:20px;\n}\n</style>\n";
										$color_area = "y";
							
									} else if ($val['ks_type'] != "include") {
										echo "<input class=\"regular-text\" type=\"text\" name=\"setting[".$no."]\" value=\"".esc_html($val['ks_val'])."\" size=\"64\" />\n";

									}
					
									if ($val['ks_ext'] != "" && $val['ks_type'] != "select") {
										echo "<p class=\"keni_note\">".nl2br($val['ks_ext'])."</p>\n";
									}

									echo "</div>\n";

									if (preg_match("/^no_sub_group/",$sub_group_name)) {
										echo "</div>\n</div>\n";
									}
								}

								if (!preg_match("/^no_sub_group/",$sub_group_name)) {
										echo "</div>\n</div>\n";
								}
							}
						}
					}
				}

				if (isset($color_area) && $color_area == "y") {
					wp_register_script('excolor', get_bloginfo('template_directory') .'/js/colpick.js','','',true);
					wp_enqueue_script('excolor');
					wp_register_script('keni-cc', get_bloginfo('template_directory') .'/js/keni_cc.js','','',false);
					wp_enqueue_script('keni-cc');
					wp_register_style('excolor_css', get_stylesheet_directory_uri() .'/colpick.css');
					wp_enqueue_style('excolor_css');
				}


				if ($key == "賢威の設定（サイト内共通）") {
					echo "「グローバルメニュー」「フッターメニュー」の設定は、<a href=\"".get_admin_url()."/nav-menus.php\">メニュー（「外観」→「メニュー」）</a>より変更可能です。<br />";
				} else if ($key == "フッター") {
					echo "「フッターメニュー」の設定は、<a href=\"".get_admin_url()."/nav-menus.php\">メニュー（「外観」→「メニュー」）</a>より変更可能です。<br />";
				}

				echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" id=\"submit\" class=\"button button-primary button-large\" value=\"変更を保存\" /></p>\n";

				echo "<input type=\"hidden\" name=\"page\" value=\"".$key."\" />\n";
				echo "</form>\n";
			}
		}
		
		echo "</div>\n";
	}
}
?>