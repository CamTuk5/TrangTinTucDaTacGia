@extends('layouts.admin')
@section('content')
<h3>Posts</h3>
<table>
  <thead>
    <tr>
      <th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Status</th><th>Published At</th>
    </tr>
  </thead>
  <tbody>
  @foreach($posts as $p)
    <tr>
      <td>{{ $p->id }}</td>
      <td>{{ $p->title }}</td>
      <td>{{ $p->user->name }}</td>
      <td>{{ $p->category->name ?? '-' }}</td>
      <td>{{ $p->status }}</td>
      <td>{{ $p->published_at }}</td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $posts->links() }}
@endsection
