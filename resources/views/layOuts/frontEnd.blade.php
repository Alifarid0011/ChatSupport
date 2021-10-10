<!DOCTYPE html >
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;1,300&display=swap"
          rel="stylesheet">
{{--    <link rel="stylesheet" href="{{ asset("assets/Chat/css/chat.css")}}">--}}
{{--    <link rel="stylesheet" href="{{ asset("assets/Chat/css/style-chat.css")}}">--}}
{{--    <link rel="stylesheet" href="{{ asset("assets/Chat/css/typing.css")}}">--}}
{{--    <link rel="stylesheet" href="{{("css/index.css")}}">--}}
    <title>{{ $header ?? "نرم افزار حساب داري"}}</title>
</head>
<body>
@include('Parts.chat')
<div id="app">
</div>
{{ $slot ?? "" }}

</body>
<script src="{{ asset('js/app.js') }}">
</script>

{{ $script ?? "" }}
</html>