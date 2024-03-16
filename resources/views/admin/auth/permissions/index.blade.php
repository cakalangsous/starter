@extends("admin.layouts.master")
@section('style')
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
                                        <th>Name</th>
                                        <th>Table Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @can('permissions_create')
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                Add {{ $title }}
                            </div>
                            <div class="card-body">
                                <form id="add_form" class="form form-vertical" action="{{ route("admin.permissions.store") }}" method="POST">
                                    <div class="form-body">
                                        <div class="row">
                                            @csrf
                                            <div id="httpMethod"></div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="permission_name">Permission Name *</label>
                                                    <input type="text" id="permission_name" class="form-control" name="name" placeholder="Permission Name *">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="permission_name">Table Name *</label>
                                                    <select name="table_name" id="table_name" class="choices">
                                                        <option value="">-- Select Table --</option>
                                                        @foreach ($tables as $table)
                                                            <option value="{{ $table }}" @if (old('table_name') == $table) selected @endif>{{ $table }}</option>
                                                        @endforeach
                                                    </select>
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
                @endcan
            </div>
        </section>
    </div>
@endsection

@section("script")
    <script src="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.js"></script>
    <script src="{{ asset("assets/js/pages/datatables.js") }}"></script>
    <script src="{{ asset("assets/extensions/choices.js/public/assets/scripts/choices.js") }}"></script>
    <script src="{{ asset("assets/js/pages/form-element-select.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.permissions.data') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'table_name', name: 'table_name'},
                    {data: "action", name: "action", orderable: false, searchable: false}
                ]
            })
            
            $("#permission_name").keyup(function() {
                var str = $(this).val()
                str = str.replace(/\s+/g, '-').toLowerCase();
                
                $("#slug").val(str)
            })

        });

        $(document).on("click", ".edit-data", function(e) {
            e.preventDefault()
            var id = $(this).data("id")
            var table = $(this).data("table")

            $("#permission_name").val($(this).data("name"))
            initChoice.setChoiceByValue([table])

            $("#permission_name").focus()
            
            $("#add_form").attr("action", `{{ url('admin/permissions/') }}/${id}`)
            $("#httpMethod").html(`@method('PUT')`)
        })

        $(document).on("click", ".delete-data", function(e) {
            var name = $(this).data("name")
            var id = $(this).data("id")
            var deleteBtn = $(this)
            Swal.fire({
                title: `Delete ${name}?`,
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
                        url: `{{ url('admin/permissions') }}/${id}`,
                        data: {_token: "{{ csrf_token() }}"},
                        dataType: "json",
                        success: function (response) {
                            if (response.success) {
                                Toastify({
                                    text: "Data has been deleted",
                                    duration: 5000,
                                    newWindow: true,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    stopOnFocus: true,
                                    className: "bg-success",
                                    style: {
                                        background: "unset",
                                    },
                                }).showToast();
                                deleteBtn.parent("td").parent("tr").remove()
                                $('.data-table').DataTable().ajax.reload()
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong. Please try again.',
                                    'error'
                                )
                            }
                        },
                        error: function(err) {
                            if (err.responseJSON.success != undefined) {
                                Swal.fire(
                                    'Sorry!',
                                    err.responseJSON.message,
                                    'error'
                                )
                            }
                        }
                    });
                }
            })
        })

      </script>
@endsection