@extends('admin.layouts.master')

@section('content')
<div class="page-breadcrumb">
    <h4 class="page-title">Edit Blog</h4>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form
                action="{{ route('admin.blogs.update', $blog->id) }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">Title <span class="text-danger">*</span></label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $blog->title) }}"
                        required
                    >
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    @if ($blog->image)
                        <div class="mb-2">
                            <img
                                src="{{ asset('storage/' . $blog->image) }}"
                                alt="{{ $blog->title }}"
                                width="120"
                            >
                        </div>
                    @endif

                    <input
                        id="image"
                        type="file"
                        name="image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/*"
                    >
                    <small>Để trống nếu muốn giữ ảnh hiện tại.</small>
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="form-control @error('description') is-invalid @enderror"
                    >{{ old('description', $blog->description) }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="10"
                        class="form-control @error('content') is-invalid @enderror"
                    >{{ old('content', $blog->content) }}</textarea>
                    @error('content')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content');
    </script>
@endpush