@extends('layouts.admin')
@section('content')
<h3>Pending Comments</h3>
<table>
  <thead>
    <tr>
      <th>ID</th><th>Post</th><th>User</th><th>Content</th><th>Status</th>
    </tr>
  </thead>
  <tbody>
  @foreach($comments as $c)
    <tr>
      <td>{{ $c->id }}</td>
      <td>{{ $c->post->title }}</td>
      <td>{{ $c->user->name }}</td>
      <td>{{ $c->content }}</td>
      <td>{{ $c->status }}</td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $comments->links() }}
@endsection
