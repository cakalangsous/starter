@extends("admin.layouts.master")
@section('style')
    <link rel="stylesheet" href="{{ asset("assets/css/pages/filepond.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/extensions/choices.js/public/assets/styles/choices.css") }}">
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
            <form action="{{ route('admin.users.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                                </div>
                                <div class="form-group">
                                    <label for="avatar" class="form-label">Avatar</label>
                                    <input class="form-control custom-input-file" name="filepond" type="file" id="avatar">
                                </div>
                                <div class="form-group">
                                    <label for="roles" class="form-label">Roles *</label>
                                    <select name="roles[]" id="roles" class="choices multiple-remove" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                </div>
                                <div class="form-group mt-4">
                                    <button type="submit" id="submit_btn" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('script')
<script src="{{ asset("assets/extensions/filepond/filepond.js") }}"></script>
<script src="{{ asset("assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js") }}"></script>
<script src="{{ asset("assets/js/pages/filepond.js") }}"></script>
<script src="{{ asset("assets/extensions/choices.js/public/assets/scripts/choices.js") }}"></script>
<script src="{{ asset("assets/js/pages/form-element-select.js") }}"></script>

<script>
    var submitBtn = document.getElementById('submit_btn')
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
        },
        fileValidateTypeDetectType: (source, type) =>
            new Promise((resolve, reject) => {
                // Do custom type detection here and return with promise
                resolve(type);
            }),
        onprocessfilestart: (file, progress) => {
            submitBtn.setAttribute('disabled', true)
            submitBtn.classList.add('disabled')
        },
        onprocessfile: (err, fileItem) => {
            submitBtn.removeAttribute('disabled')
            submitBtn.classList.remove('disabled')
        },
    });
</script>
@endsection