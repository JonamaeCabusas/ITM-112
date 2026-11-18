<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Directory</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            font-family: 'Segoe UI', sans-serif;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            /* Background image with a dark gradient overlay for readability */
            background: linear-gradient(rgba(14, 15, 13, 0.75), rgba(14, 15, 13, 0.75)), 
                        url('{{ asset("bg.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #e0e0e0;
        }

        /* Glassmorphism Cards & Containers */
        .glass-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            color: #ffffff;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.05) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }

        /* Tables inside Glass */
        .table {
            color: #e0e0e0;
            background: transparent;
        }

        .table-light {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }

        .table td, .table th {
            border-color: rgba(255, 255, 255, 0.1);
            background: transparent !important;
        }

        /* Form Controls */
        .form-control, .form-check-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #4facfe;
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.25);
        }

        .form-check-input:checked {
            background-color: #4facfe;
            border-color: #4facfe;
        }

        .form-label, .form-check-label {
            color: #d0d0d0;
        }

        /* Text Utilities Override */
        .text-muted {
            color: #a0a0a0 !important;
        }

        hr {
            border-color: rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body>
    <main class="container py-5">
        <header class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-5">
            <div class="text-center text-sm-start">
                <h1 class="fw-bold text-white">Student Directory</h1>
                <p class="text-muted mb-0">Enroll students and manage their course selections.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light">Log out</button>
            </form>
        </header>

        @if (session('status'))
            <div class="alert alert-success bg-success text-white border-0" role="status">{{ session('status') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-md-4">
                {{-- Add Student Section --}}
                <section class="card glass-card" aria-labelledby="add-student-heading">
                    <h2 id="add-student-heading" class="card-header glass-header fs-6 fw-semibold py-3">
                        Add New Student
                    </h2>
                    <div class="card-body">
                        @if ($courses->isEmpty())
                            <p class="text-muted mb-0">There are no courses to select. Add courses first, then return to enroll students.</p>
                        @else
                            <form method="POST" action="{{ route('students.store') }}">
                                @csrf
                                <input type="hidden" name="_submission_token" value="{{ \Illuminate\Support\Str::uuid() }}">
                                <x-form-errors/>
                                <div class="mb-3">
                                    <label for="studentName" class="form-label">Student Name</label>
                                    <input
                                        type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        id="studentName"
                                        name="name"
                                        value="{{ old('name') }}"
                                        maxlength="255"
                                        placeholder="Enter full name"
                                        required
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <fieldset class="mb-4">
                                    <legend class="form-label fs-6">Select Courses</legend>
                                    @foreach ($courses as $course)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input @error('course_ids') is-invalid @enderror"
                                                type="checkbox"
                                                id="course-{{ $course->id }}"
                                                name="course_ids[]"
                                                value="{{ $course->id }}"
                                                @checked(in_array($course->id, old('course_ids', [])))
                                            >
                                            <label class="form-check-label" for="course-{{ $course->id }}">
                                                {{ $course->title }}
                                            </label>
                                        </div>
                                    @endforeach
                                    @error('course_ids')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('course_ids.*')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </fieldset>

                                <button type="submit" class="btn btn-primary w-100">+ Add student</button>
                            </form>
                        @endif
                    </div>
                </section>
            </div>

            <div class="col-md-8">
                {{-- Enrolled Students Table --}}
                <section class="card glass-card" aria-labelledby="enrolled-students-heading">
                    <div class="card-header glass-header fw-semibold d-flex justify-content-between align-items-center py-3">
                        <h2 id="enrolled-students-heading" class="fs-6 mb-0 text-white">Enrolled Students</h2>
                        <span class="badge bg-secondary">{{ $students->count() }} Total</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-white">Student Name</th>
                                    <th scope="col" class="text-white">Courses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)
                                    <tr>
                                        <td class="fw-medium text-white">{{ $student->name }}</td>
                                        <td>
                                            @forelse ($student->courses as $course)
                                                <span @class([
                                                    'badge',
                                                    'bg-info text-dark' => $course->title === 'BSIT',
                                                    'bg-warning text-dark' => $course->title === 'HM',
                                                    'bg-primary' => ! in_array($course->title, ['BSIT', 'HM']),
                                                ])>{{ $course->title }}</span>
                                            @empty
                                                <span class="text-muted">No courses enrolled</span>
                                            @endforelse
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted py-4" colspan="2">
                                            No students yet. Add one above to create a course enrollment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        @auth
            {{-- Phone Directory --}}
            <section class="mt-5" aria-labelledby="phone-directory-heading">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 id="phone-directory-heading" class="h4 mb-0 text-white">Phone Directory</h2>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary">{{ $userPhones->count() }} Total</span>
                        <button
                            class="btn btn-primary btn-sm"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#phoneContactForm"
                            aria-expanded="{{ $errors->has('owner_name') || $errors->has('phone_number') ? 'true' : 'false' }}"
                            aria-controls="phoneContactForm"
                        >Add Contact</button>
                    </div>
                </div>

                <div
                    class="collapse {{ $errors->has('owner_name') || $errors->has('phone_number') ? 'show' : '' }} mb-3"
                    id="phoneContactForm"
                >
                    <div class="card glass-card card-body">
                        <form method="POST" action="{{ route('phone-directory.store') }}">
                            @csrf
                            <input type="hidden" name="_submission_token" value="{{ \Illuminate\Support\Str::uuid() }}">
                            <x-form-errors/>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="ownerName" class="form-label">User name</label>
                                    <input
                                        type="text"
                                        class="form-control @error('owner_name') is-invalid @enderror"
                                        id="ownerName"
                                        name="owner_name"
                                        value="{{ old('owner_name') }}"
                                        maxlength="255"
                                        required
                                    >
                                    @error('owner_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label for="phoneNumber" class="form-label">Phone number</label>
                                    <input
                                        type="tel"
                                        class="form-control @error('phone_number') is-invalid @enderror"
                                        id="phoneNumber"
                                        name="phone_number"
                                        value="{{ old('phone_number') }}"
                                        maxlength="50"
                                        required
                                    >
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">Add Contact</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card glass-card">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-white">User</th>
                                    <th scope="col" class="text-white">Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($userPhones as $userPhone)
                                    <tr>
                                        <td class="fw-medium text-white">{{ $userPhone->name }}</td>
                                        <td class="text-light">{{ $userPhone->phone?->phone_number ?? 'No phone on file' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted py-4" colspan="2">
                                            No phone records yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- Posts & Comments --}}
            <section class="mt-5" aria-labelledby="posts-heading">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 id="posts-heading" class="h4 mb-0 text-white">Posts &amp; Comments</h2>
                    <div class="d-flex align-items-center gap-2">
               <span class="badge bg-secondary">{{ $posts->count() }} Posts</span>
                        <button
                            class="btn btn-primary btn-sm"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#newPostForm"
                            aria-expanded="{{ $errors->has('title') || $errors->has('body') ? 'true' : 'false' }}"
                            aria-controls="newPostForm"
                        >New Post</button>
                    </div>
                </div>

                <div
                    class="collapse {{ $errors->has('title') || $errors->has('body') ? 'show' : '' }} mb-3"
                    id="newPostForm"
                >
                    <div class="card glass-card card-body">
                        <form method="POST" action="{{ route('posts.store') }}">
                            @csrf
                            <input type="hidden" name="_submission_token" value="{{ \Illuminate\Support\Str::uuid() }}">
                            <x-form-errors/>
                            <div class="mb-3">
                                <label for="postTitle" class="form-label">Title</label>
                                <input
                                    type="text"
                                    class="form-control @error('title') is-invalid @enderror"
                                    id="postTitle"
                                    name="title"
                                    value="{{ old('title') }}"
                                    maxlength="255"
                                    required
                                >
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="postBody" class="form-label">Post</label>
                                <textarea
                                    class="form-control @error('body') is-invalid @enderror"
                                    id="postBody"
                                    name="body"
                                    rows="3"
                                    required
                                >{{ old('body') }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Publish Post</button>
                        </form>
                    </div>
                </div>

                <div class="row g-4">
                    @forelse ($posts as $post)
                        <div class="col-lg-6">
                            <article class="card glass-card h-100">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-white">{{ $post->title }}</h3>
                                    <p class="small text-muted mb-2">
                                        By {{ $post->user?->name ?? 'Unknown user' }}
                                    </p>
                                    <p class="card-text text-light">{{ $post->body }}</p>

                                    <hr>
                                    <h4 class="h6 text-white">Comments ({{ $post->comments->count() }})</h4>
                                    @forelse ($post->comments as $comment)
                                        <p class="mb-2 text-light">{{ $comment->body }}</p>
                                    @empty
                                        <p class="text-muted mb-0">No comments yet.</p>
                                    @endforelse

                                    <form
                                        method="POST"
                                        action="{{ route('posts.comments.store', $post) }}"
                                        class="mt-3"
                                    >
                                        @csrf
                                        <input type="hidden" name="_submission_token" value="{{ \Illuminate\Support\Str::uuid() }}">
                                        <label for="comment-{{ $post->id }}" class="form-label">Add a comment</label>
                                        <textarea
                                            class="form-control @if (old('comment_post_id') == $post->id && $errors->has('comment_body')) is-invalid @endif"
                                            id="comment-{{ $post->id }}"
                                            name="comment_body"
                                            rows="2"
                                            required
                                        >{{ old('comment_post_id') == $post->id ? old('comment_body') : '' }}</textarea>
                                        <input type="hidden" name="comment_post_id" value="{{ $post->id }}">
                                        @if (old('comment_post_id') == $post->id)
                                            @error('comment_body')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                        <button type="submit" class="btn btn-outline-light btn-sm mt-2">Add Comment</button>
                                    </form>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card glass-card">
                                <p class="text-muted text-center py-4 mb-0">No posts yet.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        @endauth
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('form[method="POST"]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.submitting === 'true') {
                    event.preventDefault();
                    return;
                }

                form.dataset.submitting = 'true';
                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
                    button.disabled = true;
                });
            });
        });
    </script>
</body>
</html>