<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Laravel</title>
    <link rel="stylesheet" href="{{asset("bulma/css/bulma.css")}}">
</head>
<body>

@include("parts.navbar_html")

@yield("content")

</body>
</html>
