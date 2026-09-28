<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Cashier Apps</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<!-- Tambahkan class ios-bg di sini -->
<body class="ios-bg d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    
    <div class="container">
        @yield('content')
    </div>

</body>
</html>