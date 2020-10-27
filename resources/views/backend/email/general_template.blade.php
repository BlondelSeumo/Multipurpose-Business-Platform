<!DOCTYPE html>
<html>
<head>
    <title>{{ $content->subject }}</title>
	<style type="text/css">
	   .g-container{
		   padding: 15px 30px;
	   }
	</style>
</head>
<body>
	<div class="g-container">
		{!! xss_clean($content->body)  !!}
	</div>
</body>
</html>