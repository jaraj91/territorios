<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        @vite('resources/css/app.css')
    </head>
    <body class="font-sans bg-white">
        <main style="width: 210mm" class="mx-auto border">
            <x-pdf.page></x-pdf.page>
        </main>
    </body>
</html>
