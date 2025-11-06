<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin</title>
  <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@2/css/pico.min.css">
</head>
<body class="container">
  <nav>
    <ul><li><strong>Admin</strong></li></ul>
    <ul>
      <li><a href="/admin/posts">Posts</a></li>
      <li><a href="/admin/comments/pending">Pending Comments</a></li>
    </ul>
  </nav>
  <main>@yield('content')</main>
</body>
</html>
