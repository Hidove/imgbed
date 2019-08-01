<!DOCTYPE html>
<html>
<head>
	<meta charset = "utf-8" />
	<title>Hidove图床 - Simple Free Image Hosting</title>
	<script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
	<link href="https://cdn.bootcss.com/twitter-bootstrap/3.4.1/css/bootstrap.css" rel="stylesheet">
	<script src="https://cdn.bootcss.com/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<link href="https://www.hidove.cn/favicon.ico" rel="shortcut icon" type="image/ico" />
	<link rel="stylesheet" href="./css/fileinput.css">
	<link rel="stylesheet" href="./css/style.css">
	<script src="./js/fileinput.js"></script>
	<script src="./js/fileinput_locale_zh.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
</head>
<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./">Hidove图床</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="./">Home</a></li>
                    <li ><a href="/about.php">About</a></li>
                    <li ><a href="https://blog.hidove.cn/">Blog</a></li>
                    <li ><a href="https://www.hidove.cn/">Contact</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li ><a href="https://api.abcyun.cc/">Change Log</a></li>
                    <li ><a href="https://www.hidove.cn">API</a></li>
                </ul>
            </div>
        </div>
    </nav>
	 <div class="container kv-main">
		<div class="page-header">
			<h1>Image Upload</h1>
			<p>5 MB max per file. 10 files max per request.</p>
		</div>

	<div class="post container">
			<form enctype="multipart/form-data">
				<div class="form-group">
					<input id="Hidove" type="file" multiple class="file" data-overwrite-initial="false" data-min-file-count="1" data-max-file-count="10" name="file" accept="image/*">
				</div>
			</form>
	</div>
		<div id="showurl" style="display: none;">
				<ul id="navTab" class="nav nav-tabs">
					<li class="active"><a href="#urlcodes" data-toggle="tab">URL</a></li>
					<li><a href="#htmlcodes" data-toggle="tab">HTML</a></li>
					<li><a href="#bbcodes" data-toggle="tab">BBCode</a></li>
					<li><a href="#markdowncodes" data-toggle="tab">Markdown</a></li>
					<li><a href="#markdowncodes2" data-toggle="tab">Markdown with Link</a></li>
				</ul>
				<div id="navTabContent" class="tab-content">
					<div class="tab-pane fade in active" id="urlcodes">
						<pre style="margin-top: 5px;"><code id="urlcode"></code></pre>
					</div>
					<div class="tab-pane fade" id="htmlcodes">
						<pre style="margin-top: 5px;"><code id="htmlcode"></code></pre>
					</div>
					<div class="tab-pane fade" id="bbcodes">
						<pre style="margin-top: 5px;"><code id="bbcode"></code></pre>
					</div>
					<div class="tab-pane fade" id="markdowncodes">
						<pre style="margin-top: 5px;"><code id="markdown"></code></pre>
					</div>
					<div class="tab-pane fade" id="markdowncodes2">
						<pre style="margin-top: 5px;"><code id="markdownlinks"></code></pre>
					</div>
				</div>
		</div>
		</div>
		<footer class="footer">
				<div class="container">
			<p class="text-muted">Copyright &#9400; 2019  <a href="www.hidove.cn">Hidove</a>. All rights reserved. 请勿上传违反中国大陆和香港法律的图片，违者后果自负. </p>
				</div>
		</footer>
		<script src="./js/upload.js"></script>
</body>
</html>