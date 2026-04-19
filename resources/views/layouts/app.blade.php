<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'SMEMOVE Style') }}</title>
  {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}

</head>

<body class="font-kanit bg-gray-50">
  <div class="flex h-screen bg-gray-100">
    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
      @include('layouts.navigation') <main class="flex-1 overflow-y-auto p-8">
        {{ $slot }}
      </main>
    </div>
  </div>
</body>

</html>
