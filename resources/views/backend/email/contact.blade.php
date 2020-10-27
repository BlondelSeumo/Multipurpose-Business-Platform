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
		<p>{{ _lang('You have received new contact message from') }} : {{ $content->name }}</p>
		<p>{{ _lang('Message details are bellow') }}:</p>

		<p>{{ _lang('Name') }}: {{ $content->name }}<br>
		{{ _lang('Email') }}: {{ $content->email }}<br>
		{{ _lang('Message') }}: {{ $content->message }}</p>

		<br>
		<p>{{ _lang('Thank You') }}</p>
	</div>
</body>
</html>
