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
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            Tags List
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Category Name</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            Add Tag
                        </div>
                        <div class="card-body">
                            <form id="post_category_form" class="form form-vertical" action="{{ route("admin.tags.store") }}" method="POST">
                                <div class="form-body">
                                    <div class="row">
                                        @csrf
                                        <div id="httpMethod"></div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="tag_name">Tag Name *</label>
                                                <input type="text" id="tag_name" class="form-control" name="name" placeholder="Tag Name *" autofocus>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="slug">Slug *</label>
                                                <input type="text" id="slug" class="form-control" name="slug" placeholder="Slug *">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea type="text" id="description" class="form-control" name="description" placeholder="Description"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">
                                            Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
                ajax: "{{ route('admin.tags.data') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'slug', name: 'slug'},
                    {data: 'description', name: 'description', orderable: false, searchable: false},
                    {data: "action", name: "action", orderable: false, searchable: false}
                ]
            })
            
            $("#tag_name").keyup(function() {
                var str = $(this).val()
                str = str.replace(/\s+/g, '-').toLowerCase();
                
                $("#slug").val(str)
            })
        });

        $(document).on("click", ".edit-post-category", function(e) {
            e.preventDefault()
            var id = $(this).data("id")

            $("#tag_name").val($(this).data("name"))
            $("#slug").val($(this).data("slug"))
            $("#description").val($(this).data("description"))

            $("#tag_name").focus()
            
            $("#post_category_form").attr("action", `{{ url('admin/tags/') }}/${id}`)
            $("#httpMethod").html(`@method('PUT')`)
        })

        $(document).on("click", ".delete-post-category", function(e) {
            var name = $(this).data("name")
            var id = $(this).data("id")
            var deleteBtn = $(this)
            Swal.fire({
                title: `Delete Post Category ${name}?`,
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "delete",
                        url: `{{ url('admin/tags') }}/${id}`,
                        data: {_token: "{{ csrf_token() }}"},
                        dataType: "json",
                        success: function (response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )
                                deleteBtn.parent("td").parent("tr").remove()
                                $('.data-table').DataTable().ajax.reload()
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong. Please try again.',
                                    'danger'
                                )
                            }
                        }
                    });
                }
            })
        })

      </script>
@endsection