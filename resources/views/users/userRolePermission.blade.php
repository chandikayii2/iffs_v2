@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>User Role Permission</h4>
            </div>
            <div class="row">
                <div class="col-sm-12" style="float:right; margin-right:10px;">
                    <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#add">
                        Add Role Permission
                    </button>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <script>
                toastr.success('{{ session()->get('message') }}', '', {
                    timeOut: 1500
                });
            </script>
        @endif

        <!-- Add Modal -->
        <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">User Role Has Permission </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="addForm" class="row g-3 needs-validation" novalidate
                            action="{{ route('role-permission-create') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="select_role">Select User Role:</label>
                                <select class="select" id="select_role" name="select_role" required>
                                    <option value="">Select Role</option>
                                    @foreach ($user_roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                                <br><br>
                                <label>
                                    <input type="checkbox" id="select_all_permissions"> Select All
                                </label>
                                <br>
                                <label>Select Permissions</label>
                                @foreach ($user_permissions as $permission)
                                    <div>
                                        <input type="checkbox" class="individual_permission"
                                            id="permission_{{ $permission->id }}" name="permissions[]"
                                            value="{{ $permission->id }}" style="display: inline-block;">&nbsp;
                                        <label for="permission_{{ $permission->id }}"
                                            style="display: inline-block; vertical-align: middle;">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="submitBtn">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add model End -->


        <!--Edit  Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Permission Edit</h5>
                        <button type="button" id="closeModalButton" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ url('/admin/users/role_permission/update') }}" class="row g-3 needs-validation"
                            novalidate method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <strong><span style="font-size: 24px;" id="role_name"></span></strong>
                                <input type="hidden" name="role_id" id="role_id">
                                <br><br>
                                <label style="font-size: 16px; font-weight:bold;">Select Permissions</label>
                                @foreach ($user_permissions as $permission)
                                    <div>
                                        <input type="checkbox" id="permission_{{ $permission->id }}"
                                            name="permissions[]" class="check_edit" value="{{ $permission->id }}"
                                            style="display: inline-block;">&nbsp;
                                        <label for="permission_{{ $permission->id }}"
                                            style="display: inline-block; vertical-align: middle;">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit model End -->


        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Role Name</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user_role_permissions->groupBy('role_name') as $roleName => $permissions)
                                    <tr>
                                        <td>{{ $permissions->first()->role_id }}</td>
                                        <td>{{ $roleName }}</td>
                                        <td>
                                            @php
                                                $permissionNames = $permissions->pluck('name')->toArray();
                                                $permissionChunks = array_chunk($permissionNames, 5);
                                            @endphp

                                            @foreach ($permissionChunks as $chunk)
                                                {{ implode(' || ', $chunk) }} <br>
                                            @endforeach
                                        </td>

                                        <td>
                                            <button type="button"
                                                class="btn btn-success btn-sm btnRoleHasPermissionEdit"
                                                data-id="{{ $permissions->first()->role_id }}" data-toggle="modal"><i
                                                    class="far fa-edit"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>





    </div>
</div>


<script>
    // JavaScript to handle "Select All" functionality
    document.addEventListener('DOMContentLoaded', function() {
        var selectAllCheckbox = document.getElementById('select_all_permissions');
        var individualCheckboxes = document.querySelectorAll('.individual_permission');

        // Add event listener to the "Select All" checkbox
        selectAllCheckbox.addEventListener('change', function() {
            // Toggle the selection state of individual checkboxes based on the state of the "Select All" checkbox
            individualCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        // Add event listeners to individual checkboxes to update the state of the "Select All" checkbox
        individualCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // If any individual checkbox is unchecked, uncheck the "Select All" checkbox
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#editModal').modal({
            backdrop: 'static',
            keyboard: false
        });


        $('#closeModalButton').click(function() {
            $('#editModal').modal('hide');
        });
    });
</script>

<script>
    document.getElementById('submitBtn').addEventListener('click', function(event) {
        var roleSelect = document.getElementById('select_role');
        var permissionsCheckboxes = document.querySelectorAll('input[name="permissions[]"]:checked');

        if (roleSelect.value === '') {
            event.preventDefault();
            alert('Please select a role.');
        } else if (permissionsCheckboxes.length === 0) {
            event.preventDefault();
            alert('Please select at least one permission.');
        } else {
            // Ajax call to check if role permission already exists
            var formData = new FormData(document.getElementById('addForm'));
            $.ajax({
                url: '{{ route('check-role-exists') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.exists) {
                        event.preventDefault();
                        alert('Role permission already exists.');
                    } else {
                        // Submit the form if role permission does not exist
                        document.getElementById('addForm').submit();
                    }
                }
            });
        }
    });
</script>


<script>
    $(".btnRoleHasPermissionEdit").click(function() {
        var role_id = $(this).data("id");
        //  alert(role_permission_id);
        $.ajax({
            type: "get",
            url: "/admin/users/role_permission/edit/" + role_id,
            cache: false,
            success: function(data) {
                let d = data.data[0];
                console.log(d.role_name);
                $("#role_name").text(d.role_name);
                $("#role_id").val(d.role_id);


                // Loop through the checkboxes and check if they should be checked
                $('.check_edit').each(function() {
                    var checkboxValue = $(this).val();
                    if (data.data.some(permission => permission.permission_id ==
                            checkboxValue)) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
                $("#editModal").modal("show");
            },
            error: function(e) {
                console.log(e);
            },
        });
    });
</script>
