<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <nav class="bg-dark py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <div>
        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none fs-5 fw-bold me-4">Dashboard</a>
        @if(Auth::user()->role == 'admin')
          <a href="{{ route('index.user') }}" class="text-white text-decoration-none me-3">Users</a>
        @endif
        <a href="{{ route('profile', ['id' => Auth::user()->id]) }}" class="text-white text-decoration-none">Profile</a>
      </div>
      <div>
        <a href="{{ route('logout') }}" class="text-white text-decoration-none">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    @section('content')
    @show
  </div>

</body>
</html>
