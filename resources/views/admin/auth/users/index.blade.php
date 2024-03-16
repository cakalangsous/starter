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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{ $title }} List
                            @can('users_create')
                                <a href="{{ route("admin.users.create") }}" class="btn btn-outline-primary ms-2">Add User</a>
                            @endcan
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Last Login</th>
                                        <th>Banned</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
        
                                </tbody>
                            </table>
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
                ajax: "{{ route('admin.users.data') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'role', name: 'role'},
                    {data: 'last_login', name: 'last_login', orderable: false, searchable: false},
                    {data: 'is_banned', name: 'is_banned', orderable: false, searchable: false, align: 'center'},
                    {data: "action", name: "action", orderable: false, searchable: false}
                ]
            })
        });
        
        $(document).on("change", ".banned", function(e) {
            var checkbox = $(this)
            var name = $(this).data("name")
            var id = $(this).data("id")

            var status = $(this).prop('checked');
        
            let ban_text = status == true ? 'Ban':'Unbanned'
            let ban_cap = status == true ? `${name} can't login anymore!` : `${name} can be logged in!`

            Swal.fire({
                title: `${ban_text} user?`,
                text: ban_cap,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, ${ban_text}!`
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: `{{ url('admin/users/ban') }}/${id}`,
                        data: {_token: "{{ csrf_token() }}", id, status},
                        dataType: "json",
                        success: function (response) {
                            if (response.success) {
                                Toastify({
                                    text: response.message,
                                    duration: 5000,
                                    newWindow: true,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    className: "bg-success",
                                    style: {
                                        background: "unset",
                                    },
                                }).showToast();
                                // $('.data-table').DataTable().ajax.reload()
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    response.message ?? 'Something went wrong. Please try again.',
                                    'error'
                                )
                                checkbox.prop('checked', false)
                            }
                        }
                    });
                } else {
                    checkbox.prop('checked', false)
                }
            })
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
                        url: `{{ url('admin/users') }}/${id}`,
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
                                    className: "bg-success",
                                    style: {
                                        background: "unset",
                                    },
                                }).showToast();
                                deleteBtn.parent("td").parent("tr").remove()
                                $('.data-table').DataTable().ajax.reload()
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    response.message ?? 'Something went wrong. Please try again.',
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