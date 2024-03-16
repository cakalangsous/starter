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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Roles and Access List</h5>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                @foreach ($roles as $role)
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $loop->iteration == 1 ? 'active' : '' }}" id="tab_{{ $role->id }}" data-bs-toggle="tab" href="#tab_body_{{ $role->id }}" role="tab" aria-controls="home" aria-selected="true" data-role="{{ $role->id }}">{{ $role->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="row mt-5" id="loader">
                                    <div class="col-12 d-flex justify-content-center align-items-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                @foreach ($roles as $role)
                                    <div class="tab-pane fade {{ $loop->iteration == 1 ? 'active show' : '' }}" id="tab_body_{{ $role->id }}" role="tabpanel" aria-labelledby="home-tab">
                                        <form action="{{ route('admin.access.update', ['role' => $role->id]) }}" method="POST">
                                            @csrf
                                            @method('put')
                                            <table class="table data-table table-striped checkbox-table" id="role{{ $role->id }}_perms"></table>
                                            <div class="form-group mt-4 d-none submit_btn_wrapper">
                                                <button type="submit" id="submit_btn" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            let role_on_load = $('.nav-tabs a.active').data('role');
            getAccess(role_on_load);
    
            $('.nav-tabs a').click(function (e) {
                e.preventDefault();
                $('#loader').removeClass('d-none')
                let role = $(this).data('role')
    
                $('.checkbox-table').addClass('d-none')    
                getAccess(role)
            });
    
            function getAccess(role) {
                $('.submit_btn_wrapper').addClass('d-none')
                let html = '';
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.access.role', ['role' => ":role"]) }}",
                    data: {_token: token, role},
                    dataType: "JSON",
                    success: function (res) {
                        if (res.success==true) {
                            let group_name;
                            $.each(res.perms, function (index, value) {
    
                                group_name = index.split('_').join(' ');
                                group_name = group_name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                                    return letter.toUpperCase();
                                });
                                
                                html += `<tr>
                                            <td width="25%">
                                                <p>
                                                    <label>
                                                        <input type="checkbox" class="form-check-input form-check-primary form-check-glow parent ${index}-parent" data-target="${index}" />
                                                        <span>${group_name}</span>
                                                    </label>
                                                </p>
                                            </td>`;
                                $.each(value, function (index2, value2) { 
                                    let perm_check = '';
                                    $.each(res.role_perms, function (roleIndex, roleValue) { 
                                        if (roleValue.id == value2.id) {
                                            perm_check = `checked="checked"`;
                                        }
                                    });
    
                                    html += `<td class="${index}">
                                                <p>
                                                    <label>
                                                        <input type="checkbox" class="form-check-input form-check-primary form-check-glow" name="perms[]" value="${value2.name}" ${perm_check} data-parent="${index}" />
                                                        <span>${value2.name}</span>
                                                    </label>
                                                </p>
                                            </td>`;
                                });
    
                                html += `</tr>`;
                            });
                            
                            $('#loader').addClass('d-none');
                            $('.checkbox-table').html(html);
                            $('.checkbox-table').removeClass('d-none');
                            $('.submit_btn_wrapper').removeClass('d-none')

                            const parentCheckbox = $('.tab-pane.active input[type=checkbox].parent')
                            
                            $.each(parentCheckbox, function (i, v) { 
                                const check_class = $(this).data('target')
                                const table_id = $(this).parents('table').attr('id')
                                const total_checkbox = $(`table#${table_id} td.${check_class}`).length;
                                const checked_checkbox = $(`table#${table_id} td.${check_class} input:checked`).length
                                const checkbox_parent = $(`input:checkbox.${check_class}-parent`);
                                
                                if ((checked_checkbox < total_checkbox) && (checkbox_parent.attr('id')==undefined) ) {
                                    checkbox_parent.prop('checked', false);
                                    checkbox_parent.prop('indeterminate', true);
                                } else {
                                    checkbox_parent.prop('indeterminate', false);
                                    checkbox_parent.prop('checked', true);
                                }
                        
                                if (checked_checkbox == 0) {
                                    checkbox_parent.prop('indeterminate', false);
                                }
                            });
                        };
                    }
                });
            }
            $(document).on('click', 'input:checkbox', function(event) {
                check_group($(this))
            });
    
            function check_group(param_data) {
                const check_class = param_data.data('parent');
                const table_id = param_data.parents('table').attr('id');
                const total_checkbox = $(`table#${table_id} td.${check_class}`).length;
                const checked_checkbox = $(`table#${table_id} td.${check_class} input:checked`).length
                const checkbox_parent = $(`input:checkbox.${check_class}-parent`);

                if ((checked_checkbox < total_checkbox) && (checkbox_parent.attr('id')==undefined) ) {
                    checkbox_parent.prop('checked', false);
                    checkbox_parent.prop('indeterminate', true);
                } else {
                    checkbox_parent.prop('indeterminate', false);
                    checkbox_parent.prop('checked', true);
                }
        
                if (checked_checkbox == 0) {
                    checkbox_parent.prop('indeterminate', false);
                }
            }

            $(document).on('click', 'input:checkbox.parent', function(event) {
                let target = $(this).data('target');
                let state = $(this).prop('checked');

                $(`td.${target} input:checkbox`).prop('checked', state);
            });
        });
    </script>
@endsection