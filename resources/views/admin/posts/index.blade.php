@extends("admin.layouts.master")
@section("content")
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>
    <div class="page-heading">
        @include('admin.partials.title')
        <section class="section">
            <div class="card">
                <div class="card-header">
                    All Posts
                    @can('posts_create')
                        <a href="{{ route("admin.posts.create") }}" class="btn btn-outline-primary ms-2">Add New</a>
                    @endcan

                </div>
                <div class="card-body table-responsive">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>title</th>
                                <th>Category</th>
                                <th>Tags</th>
                                <th>Publish Status</th>
                                <th>Published At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

@section("script")
    <script src="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.js"></script>
    <script src="{{ asset("assets/js/pages/datatables.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.posts.data') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'title', name: 'title'},
                    {data: 'category', name: 'category'},
                    {data: 'tags', name: 'tags'},
                    {data: 'publish_status', name: 'publish_status'},
                    {data: 'published_at', name: 'published_at'},
                    {data: "action", name: "action", orderable: false, searchable: false}
                ]
            });

            $(document).on("click", ".delete-btn", function(e) {
                e.preventDefault()
    
                var form = $(this).parent("form")
                var title = form.data("title")
                var form_id = form.data("form_id")
                Swal.fire({
                    title: `Delete Post?`,
                    text: `"${title}" post will be deleted. You can recover it later.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#435ebe',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#${form_id}`).submit()
                    }
                })
            })
        });

      </script>
@endsection