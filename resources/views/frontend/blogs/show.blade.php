@extends('frontend.layouts.master')
@section('title', $blog->title)
@section('css')
    <link rel="stylesheet" href="{{ asset('rate/css/rate.css') }}">
@endsection
@section('content')


    <div class="blog-navigation">
        @if ($previousBlog)
            <a href="{{ route('frontend.blogs.show', $previousBlog->id) }}" class="btn btn-default">
                Bài mới hơn
            </a>
        @endif

        @if ($nextBlog)
            <a href="{{ route('frontend.blogs.show', $nextBlog->id) }}" class="btn btn-default">
                Bài cũ hơn
            </a>
        @endif
    </div>

    <div class="blog-post-area">
        <h2 class="title text-center">{{ $blog->title }}</h2>

        <div class="single-blog-post">
            <div class="post-meta">
                <ul>
                    <li>
                        <i class="fa fa-calendar"></i>
                        {{ $blog->created_at->format('M d, Y') }}
                    </li>
                </ul>
            </div>

            @if ($blog->image)
                <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="img-responsive"
                    style="width: 100%;">
            @endif

            @if ($blog->description)
                {{ $blog->description }}
            @endif

            <div class="blog-content">
                {!! nl2br(e(strip_tags($blog->content))) !!}
            </div>
        </div>
        <div class="blog-rating" data-blog-id="{{ $blog->id }}">
            <p>Đánh giá bài viết:</p>
            <div class="rate">
                <div class="vote">
                    @for ($star = 1; $star <= 5; $star++)
                        <div class="star_{{ $star }} ratings_stars {{ ($userRating ?? round($averageRating ?? 0)) >= $star ? 'ratings_over' : '' }}">
                            <input value="{{ $star }}" type="hidden">
                        </div>
                    @endfor
                    <span class="rate-np">{{ number_format($averageRating ?? 0, 1) }}</span>
                </div>
            </div>
            <p id="rating-message">
                @if ($ratingCount > 0)
                    {{ $ratingCount }} lượt đánh giá
                @else
                    Chưa có lượt đánh giá
                @endif
            </p>
        </div>

        <section class="blog-comments" data-blog-id="{{ $blog->id }}">
            <h3>Bình luận ({{ $comments->count() }})</h3>

            @auth
                <form id="comment-form" class="comment-form">
                    <textarea name="content" rows="4" maxlength="2000" required
                        placeholder="Viết bình luận của bạn..."></textarea>
                    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
                </form>
            @else
                <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận.</p>
            @endauth

            <p id="comment-message" role="status"></p>
            <div id="comment-list">
                @foreach ($comments as $comment)
                    <article class="blog-comment" data-comment-id="{{ $comment->id }}">
                        <strong>{{ $comment->user->name }}</strong>
                        <time>{{ $comment->created_at->format('d/m/Y H:i') }}</time>
                        <p>{{ $comment->content }}</p>
                        @auth
                            <button type="button" class="reply-toggle">Trả lời</button>
                            <form class="reply-form" hidden>
                                <textarea name="content" rows="2" maxlength="2000" required
                                    placeholder="Viết câu trả lời..."></textarea>
                                <button type="submit" class="btn btn-primary">Gửi trả lời</button>
                            </form>
                        @endauth
                        <div class="comment-replies">
                            @foreach ($comment->replies as $reply)
                                <article class="blog-comment blog-comment-reply" data-comment-id="{{ $reply->id }}">
                                    <strong>{{ $reply->user->name }}</strong>
                                    <time>{{ $reply->created_at->format('d/m/Y H:i') }}</time>
                                    <p>{{ $reply->content }}</p>
                                </article>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <a href="{{ route('frontend.blogs.index') }}" class="btn btn-primary">
            Quay lại danh sách bài viết
        </a>
    </div>



@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ratingContainer = document.querySelector('.blog-rating');
            var stars = Array.from(document.querySelectorAll('.ratings_stars'));
            var message = document.getElementById('rating-message');
            var isLoggedIn = @json(auth()->check());
            var currentRating = {{ (int) ($userRating ?? round($averageRating ?? 0)) }};
            var commentsSection = document.querySelector('.blog-comments');
            var commentList = document.getElementById('comment-list');
            var commentMessage = document.getElementById('comment-message');
            var commentsCount = document.querySelector('.blog-comments h3');
            var commentCount = {{ $comments->count() }};
            var commentUrl = '{{ route('frontend.blogs.comment.ajax') }}';
            var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            function showRating(rating, className) {
                stars.forEach(function (star, index) {
                    star.classList.toggle(className, index < rating);
                });
            }

            stars.forEach(function (star) {
                star.addEventListener('mouseenter', function () {
                    showRating(Number(star.querySelector('input').value), 'ratings_hover');
                });

                star.addEventListener('mouseleave', function () {
                    showRating(0, 'ratings_hover');
                });

                star.addEventListener('click', async function () {
                    if (!isLoggedIn) {
                        alert('Vui lòng đăng nhập để đánh giá.');
                        return;
                    }

                    var rating = Number(star.querySelector('input').value);
                    showRating(rating, 'ratings_over');

                    try {
                        var response = await fetch('{{ route('frontend.blogs.rate.ajax') }}', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                rating: rating,
                                id_blog: ratingContainer.dataset.blogId
                            })
                        });
                        var data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Không thể lưu đánh giá.');
                        }

                        currentRating = rating;
                        document.querySelector('.rate-np').textContent = data.averageRating;
                        message.textContent = data.ratingCount + ' lượt đánh giá';
                    } catch (error) {
                        showRating(currentRating, 'ratings_over');
                        message.textContent = error.message;
                    }
                });
            });

            if (commentsSection) {
                function appendComment(comment, parentId) {
                    var article = document.createElement('article');
                    article.className = parentId
                        ? 'blog-comment blog-comment-reply'
                        : 'blog-comment';
                    article.dataset.commentId = comment.id;

                    var author = document.createElement('strong');
                    author.textContent = comment.user.name;
                    article.appendChild(author);

                    var time = document.createElement('time');
                    time.textContent = new Date(comment.created_at).toLocaleString();
                    article.appendChild(time);

                    var content = document.createElement('p');
                    content.textContent = comment.content;
                    article.appendChild(content);

                    if (!parentId) {
                        var replyButton = document.createElement('button');
                        replyButton.type = 'button';
                        replyButton.className = 'reply-toggle';
                        replyButton.textContent = 'Trả lời';
                        article.appendChild(replyButton);

                        var replyForm = document.createElement('form');
                        replyForm.className = 'reply-form';
                        replyForm.hidden = true;
                        replyForm.innerHTML =
                            '<textarea name="content" rows="2" maxlength="2000" required placeholder="Viết câu trả lời..."></textarea>' +
                            '<button type="submit" class="btn btn-primary">Gửi trả lời</button>';
                        article.appendChild(replyForm);

                        var replies = document.createElement('div');
                        replies.className = 'comment-replies';
                        article.appendChild(replies);
                    }

                    if (parentId) {
                        commentsSection.querySelector(
                            '[data-comment-id="' + parentId + '"] .comment-replies'
                        ).appendChild(article);
                    } else {
                        commentList.prepend(article);
                        commentCount++;
                        commentsCount.textContent = 'Bình luận (' + commentCount + ')';
                    }
                }

                commentsSection.addEventListener('click', function (event) {
                    if (event.target.matches('.reply-toggle')) {
                        var form = event.target.nextElementSibling;
                        form.hidden = !form.hidden;
                    }
                });

                commentsSection.addEventListener('submit', async function (event) {
                    if (!event.target.matches('#comment-form, .reply-form')) {
                        return;
                    }

                    event.preventDefault();

                    if (!isLoggedIn) {
                        alert('Vui lòng đăng nhập để bình luận.');
                        return;
                    }

                    var form = event.target;
                    var content = form.elements.content.value.trim();
                    var parentComment = form.closest('[data-comment-id]');

                    if (!content) {
                        commentMessage.textContent = 'Vui lòng nhập nội dung bình luận.';
                        return;
                    }

                    try {
                        var response = await fetch(commentUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                id_blog: commentsSection.dataset.blogId,
                                parent_id: parentComment ? parentComment.dataset.commentId : null,
                                content: content
                            })
                        });
                        var result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Không thể gửi bình luận.');
                        }

                        appendComment(result.data, parentComment ? parentComment.dataset.commentId : null);
                        form.reset();
                        if (form.classList.contains('reply-form')) {
                            form.hidden = true;
                        }
                        commentMessage.textContent = 'Bình luận đã được gửi.';
                    } catch (error) {
                        commentMessage.textContent = error.message;
                    }
                });
            }
        });
    </script>
@endsection