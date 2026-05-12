@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>User Role List</h4>
            </div>
            <div class="page-btn">
                <a href="add#" data-toggle="modal" data-target="#add" class="btn btn-added">
                    <img src="/assets/admin/img/icons/plus.svg" alt="img">Add New
                </a>
            </div>
        </div>


        @if (session()->has('status') && session()->has('message'))
            @if (session()->get('status') === 200)
                <script>
                    toastr.success('{{ session()->get('message') }}', '', {
                        timeOut: 1500
                    });
                </script>
            @elseif (session()->get('status') === 500)
                <script>
                    toastr.error('{{ session()->get('message') }}', '', {
                        timeOut: 2000
                    });
                </script>
            @endif
        @endif


        <!--Add  Modal -->
        <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">User Role Add </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('user-role-create') }}" id="user-role-form"
                            class="row g-3 needs-validation" novalidate method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text" name="role_name" class="form-control" placeholder="Enter Role "
                                    required>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" id="submit-btn" class="btn btn-primary btn-sm">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add model End -->

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>

                                <th>ID</th>
                                <th>Role Name</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user_role as $role)
                                <tr>

                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->role_name }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm btnUserRoleEdit"
                                            data-id="{{ $role->id }}" data-toggle="modal"><i
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
</div>


<script>
    const form = document.getElementById('user-role-form');
    const submitBtn = document.getElementById('submit-btn');

    function validateForm(event) {
        event.preventDefault();

        // validate name field
        const nameField = document.getElementsByName('role_name')[0];
        if (!nameField.value) {
            alert('Please enter a role name.');
            return;
        }
        // if all fields are valid, submit the form
        form.submit();
    }

    submitBtn.addEventListener('click', validateForm);
</script>


</body>

</html>
