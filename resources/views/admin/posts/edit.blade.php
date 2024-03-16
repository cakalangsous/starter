@extends("admin.layouts.master")
@section("style")
    <link rel="stylesheet" href="{{ asset("assets/extensions/filepond/filepond.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/extensions/toastify-js/src/toastify.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/pages/filepond.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/extensions/choices.js/public/assets/styles/choices.css") }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.1.1/css/froala_editor.min.css" integrity="sha512-N+DZfDEUyB3QFr7jBeB/RYdA2V8ND+21dPyZwkqLC/pxEmKy6NKsesVU4jlY2210Ee5z9bgOTRG1FkmmYThLmg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tui-image-editor@3.2.2/dist/tui-image-editor.css">
<link rel="stylesheet" href="https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.css">
    <link rel="stylesheet" href="{{ asset("assets/css/froala/plugins/quick_insert.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/froala/plugins/image.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/froala/plugins/image_manager.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/froala/third_party/image_tui.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/froala/plugins/files_manager.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/froala/plugins/char_counter.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/froala/plugins/emoticons.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/froala/plugins/code_view.min.css") }}">
@endsection
@section("content")
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>
    <div class="page-heading">
        @include('admin.partials.title')

        <section class="section">
            <form id="create_post_form" action="{{ route("admin.posts.update", ["post" => $post->id]) }}" class="form form-vertical" method="POST" enctype="multipart/form-data">
                @csrf
                @method("PATCH")
                <div class="row">
                    <div class="col-12 col-lg-8" id="post_body">
                        <div class="card">
                            <div class="card-header">
                                {{ $title }}
                            </div>
                            <div class="card-body">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="title">Title *</label>
                                        <input type="text" id="title" class="form-control" name="title" placeholder="Title *" value="{{ old("title", $post->title) }}" autofocus>
                                    </div>
                                </div>
        
                                <div class="col-12" style="min-height: 70vh;">
                                    <div class="form-group">
                                        <label for="body" class="form-label">Body *</label>
                                        <textarea name="body" id="body">{{ old("body", $post->body) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-12 col-lg-4" id="rightbar">
                        <div class="card">
                            <div class="card-header"></div>
                            <div class="card-body">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="slug">Slug *</label>
                                        <input type="text" id="slug" class="form-control" name="slug" placeholder="Slug *" value="{{ old("slug", $post->slug) }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="image">Image *</label>
                                        <input type="file" class="custom-input-file" name="filepond" id="image">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="category">Category *</label>
                                    <div class="form-group">
                                        <select name="category" id="category" class="choices">
                                            <option value="">-- Select Category --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $post->post_category_id == $category->id ? "selected" : "" }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="tags">Tags *</label>
                                    <div class="form-group">
                                        <select name="tags[]" id="tags" class="choices multiple-remove" multiple>
                                            @foreach ($tags as $tag)
                                                <option value="{{ $tag->id }}" {{ in_array($tag->id, $tags_selected) ? 'selected' : '' }}>{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 my-4">
                                    <div class="form-group">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="allow_comments" id="allow_comments" {{ $post->allow_comments ? "checked" : "" }}>
                                            <label class="form-check-label" for="allow_comments">Switch off to disable comments</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="excerpt">Excerpt</label>
                                        <textarea name="excerpt" id="excerpt" rows="3" class="form-control"> {{ old("excerpt", $post->excerpt) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="meta_description">Meta Description</label>
                                        <textarea name="meta_description" id="meta_description" rows="3" class="form-control"> {{ old("meta_description", $post->meta_description) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="meta_keywords">Meta Keywords</label>
                                        <input type="text" id="meta_keywords" class="form-control" name="meta_keywords" placeholder="Meta Keywords" value="{{ old("meta_keywords", $post->meta_keywords) }}">
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <label class="form-label" for="publish_status">Publish *</label>
                                    <div class="form-group">
                                        <select name="publish_status" id="publish_status" class="choices">
                                            <option value="0" {{ $post->publish_status == "Drafted" ? "selected" : "" }}>Drafted</option>
                                            <option value="1" {{ $post->publish_status == "Published" ? "selected" : "" }}>Published</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                    Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </form>
        </section>
    </div>
@endsection

@section("script")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.1.1/js/froala_editor.min.js" integrity="sha512-4PbQqbXCB4gvmgMRErcLr6J30W8L2Sf6p3vDuLG6w9d2L6FocyVWXqIHO5c86tGiFqcfhgeTVfB4LdzTvwIQHA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/paragraph_format.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/emoticons.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/fullscreen.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/image.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/image_manager.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/char_counter.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/quick_insert.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/link.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/lists.min.js") }}"></script>
    <script src="{{ asset("assets/js/extensions/froala/plugins/code_view.min.js") }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/1.6.7/fabric.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-code-snippet@1.4.0/dist/tui-code-snippet.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-image-editor@3.2.2/dist/tui-image-editor.min.js"></script>
    <script src="{{ asset("assets/js/extensions/froala/third_party/image_tui.min.js") }}"></script>
    <script src="{{ asset("assets/extensions/filepond/filepond.js") }}"></script>
    <script src="{{ asset("assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js") }}"></script>
    <script src="{{ asset("assets/extensions/toastify-js/src/toastify.js") }}"></script>
    <script src="{{ asset("assets/js/pages/filepond.js") }}"></script>
    <script src="{{ asset("assets/extensions/choices.js/public/assets/scripts/choices.js") }}"></script>
    <script src="{{ asset("assets/js/pages/form-element-select.js") }}"></script>

    <script>
        $("#title").on("keyup", function () {
            var title = $(this).val()
            title = title.replace(/\s+/g, '-').toLowerCase();
            $("#slug").val(title)
        });

        $("#rightbar_toggle").click(function() {
            $("#rightbar").toggle();
            if ($("#post_body").hasClass("col-lg-8")) {
                $("#post_body").animate("slow").removeClass("col-lg-8");
            } else {
                $("#post_body").addClass("col-lg-8");
            }
        })

        $("#create_post_form").submit(function(e) {
            e.preventDefault()
            var publish_status = $("#publish_status").val() == 0 ? "Draft" : "Publish"
            Swal.fire({
                title: `Save post as ${publish_status}?`,
                text: "This post will marked as " + publish_status,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#435ebe',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#create_post_form").off("submit").submit()
                }
            })
        })

        $(document).ready(function () {
            var editor = new FroalaEditor("#body", {
                toolbarVisibleWithoutSelection: true,
                toolbarInline: true,
                imageDefaultWidth: 500,
                imageUploadMethod: 'POST',
                // Set the image upload URL.
                imageUploadURL:'{{ route("admin.editor_upload") }}',

                imageManagerLoadURL: '{{ route("admin.editor_image_manager") }}',
                // Set the image delete URL.
                imageManagerDeleteURL: '{{ route("admin.editor_delete") }}',
                imageManagerDeleteMethod: "DELETE",
                imageManagerDeleteParams: {_token: "{{ csrf_token() }}"},

                //Validation                           
                imageAllowedTypes: ['jpeg', 'jpg', 'png'],
                // Set max image size to 10MB.
                imageMaxSize: 1024 * 1024 * 10,
                imageUploadParams: {_token: "{{ csrf_token() }}"},
            })

        });
        FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.create(document.querySelector(".custom-input-file"), {
            credits: null,
            allowImagePreview: true,
            allowImageFilter: false,
            allowImageExifOrientation: false,
            allowImageCrop: false,
            acceptedFileTypes: ["image/png", "image/jpg", "image/jpeg"],
            server: {
                url: `${base_url}/admin/temp-upload`,
                headers: {
                    "X-CSRF-TOKEN": token,
                },
                load: (source, load, error, progress, abort, headers) => {

                    const myRequest = new Request(base_url + "/" + source);
                    fetch(myRequest).then(function(response) {
                        response.blob().then(function(myBlob) {
                            load(myBlob)
                        });
                    });

                },
            },
            fileValidateTypeDetectType: (source, type) =>
                new Promise((resolve, reject) => {
                    // Do custom type detection here and return with promise
                    resolve(type);
                }),
            files: [{
                source: '{{ $post->image }}',
                options: {type: 'local'},
            }],
        });
    </script>
@endsection