<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>コンテンツの品質チェックシート</title>
<style>
.check-list-keni{
	margin-bottom: 2em;
	margin-left: 0;
	padding-left: 0;
}

.check-list-keni li{
	list-style: none;
	margin-bottom: 1em;
	padding-left: 1.5em;
	font-size: 14px;
	text-indent: -1.5em;
}

.hidden{
	display: none;
}

h1,h2{
	font-size: 16px;
}
</style>

</head>

<body>

<h1>【コンテンツの品質チェックシート】</h1>

<h2>●表示エラーのチェック</h2>
<ul class="check-list-keni">
	<li><label><input type="checkbox" /> HTMLの文法エラーはありませんか？<br />（公開後に <a href="https://validator.w3.org/" target="_blank">https://validator.w3.org/</a>にて文法チェックすることをオススメします）</label></li>
	<li><label><input type="checkbox" /> PCだけでなく、スマートフォンでも表示確認しましたか？</label></li>
</ul>
<h2>●SEO</h2>
<ul class="check-list-keni">
	<li><label><input type="checkbox" /> タイトルは34文字以内になっていますか？<br />もしくは、2フレーズの文章で構成され、前半と後半のタイトルがそれぞれ25文字以内になっていますか？<br />（よいタイトルの例：SEOのプロはみんな知っている！検索結果でクリックされやすいタイトルの付け方）</label></li>
	<li><label><input type="checkbox" /> 見出しタグ（h2～h5）の順番は適切ですか？</label></li>
	<li><label><input type="checkbox" /> 抜粋は100文字前後で入力されていますか？<br />（抜粋は検索結果で露出した際のクリック率に影響します）</label></li>
</ul>

<h2>●見やすさ＆読みやすさ</h2>
<ul class="check-list-keni">
	<li><label><input type="checkbox" /> 文章の行間が詰まり過ぎていませんか？<br />（必要に応じて「m60-t」などのclassを用いて、行間を調整しましょう）</label></li>
	<li><label><input type="checkbox" /> 文章の強調箇所の数は適切ですか？<br />（強調箇所が多すぎると見づらくなります）</label></li>
	<li><label><input type="checkbox" /> 箇条書きにできる箇所にはリストタグ（ul、ol）を使っていますか？</label></li>
</ul>

<h2>●著作権</h2>
<ul class="check-list-keni">
	<li><label><input type="checkbox" /> 他社のコンテンツの著作権を侵害していませんか？</label></li>
	<li><label><input type="checkbox" /> コンテンツを引用した場合には、&lt;blockquote&gt;というタグを使ったのち、引用元へきちんとリンクを張っていますか？</label></li>
</ul>

</body>
</html>