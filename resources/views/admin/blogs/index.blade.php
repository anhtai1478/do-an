@extends('admin.layouts.master')

@section('content')
<div class="page-breadcrumb">
    <h4 class="page-title">Blog Management</h4>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Danh sách bài viết</h4>
            <a href="{{ route('admin.blogs.create') }}" class="btn btn-success mb-3">
                Add Blog
            </a>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        
                        <tr>
                            <th>Action</th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($blog as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }}</td>
                                <td>
                                    @if ($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}"
                                             alt="{{ $item->title }}"
                                             width="100">
                                    @else
                                        Chưa có ảnh
                                    @endif
                                </td>
                                <td>{{ $item->description }}</td>
                                <td>
                                    <a href="{{ route('admin.blogs.edit', $item->id) }}"
                                       class="btn btn-primary btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.blogs.destroy', $item->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Chưa có bài viết nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection