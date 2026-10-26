@extends('frontend.layouts.master')

@section('title', 'Danh Sách Bài Viết')

@section('content')
    <div class="blog-post-area">
        <h2 class="title text-center">Latest From our Blog</h2>

        @forelse ($blogs as $blog)
            <div class="single-blog-post">
                <h3>
                    <a href="{{ route('frontend.blogs.show', $blog->id) }}">{{ $blog->title }}</a>
                </h3>

                <div class="post-meta">
                    <ul>
                        <li><i class="fa fa-calendar"></i> {{ $blog->created_at->format('M d, Y') }}</li>
                    </ul>
                </div>

                @if ($blog->image)
                    <a href="{{ route('frontend.blogs.show', $blog->id) }}">
                        <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="img-responsive" style="width:50%">
                    </a>
                @endif

                @if ($blog->description)
                    <p>{{ \Illuminate\Support\Str::limit($blog->description) }}</p>
                @endif

                <a class="btn btn-primary" href="{{ route('frontend.blogs.show', $blog->id) }}">Read More</a>
            </div>
        @empty
            <p class="text-center">No blog posts found.</p>
        @endforelse

        <div class="pagination-area">
            {{ $blogs->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection