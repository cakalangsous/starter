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
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <div class="avatar avatar-2xl">
                                    <img src="{{ $user->avatar ? asset($user->avatar) : asset('assets/images/faces/2.jpg') }}" alt="{{ $user->name }}">
                                </div>
    
                                <h3 class="mt-3">{{ $user->name }}</h3>
                                <p class="text-small">{{ $user->email }}</p>
                                <small class="text-small">Last login: {{ $user->last_login ? date('M j, Y H:i', strtotime($user->last_login)) : ''; }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.users.update', ['user' => $user->id]) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $user->name }}">
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ $user->email }}">
                                </div>
                                <div class="form-group">
                                    <label for="avatar" class="form-label">Avatar <small>(Leave empty if there are no changes)</small></label>
                                    <input class="form-control custom-input-file" name="filepond" type="file" id="avatar">
                                </div>
                                <div class="form-group">
                                    <label for="password" class="form-label">Password <small>(Leave empty if there are no changes)</small></label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirm Password <small>(Leave empty if there are no changes for password)</small></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="roles" class="form-label">Roles *</label>
                                    <select name="roles[]" id="roles" class="choices multiple-remove" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" {{ in_array($role->id, $user_roles) ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-4">
                                    <button type="submit" id="submit_btn" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
            @if ($user->avatar)
                load: (source, load, error, progress, abort, headers) => {
                    const myRequest = new Request(base_url + "/" + source);
                    fetch(myRequest).then(function(response) {
                        response.blob().then(function(myBlob) {
                            load(myBlob)
                        });
                    });

                },
            @endif
        },
        fileValidateTypeDetectType: (source, type) =>
            new Promise((resolve, reject) => {
                // Do custom type detection here and return with promise
                resolve(type);
            }),
        @if ($user->avatar)
            files: [{
                source: '{{ $user->avatar }}',
                options: {type: 'local'},
            }],
        @endif
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