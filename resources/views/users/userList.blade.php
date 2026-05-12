@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>User List</h4>
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
                        <h5 class="modal-title" id="exampleModalLongTitle">User Add Form </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('user-create') }}" id="user-form" class="row g-3 needs-validation"
                            novalidate method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="select_role">Select User Role:</label>
                                <select class="select" id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach ($userRoles as $role)
                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                                <br><br>
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Name"
                                    required><br>
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Enter Email"
                                    required><br>
                                <label>Contact</label>
                                <input type="number" name="phone" class="form-control" placeholder="Enter Phone"
                                    required><br>
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password"
                                    required><br>
                                <label>Re Enter Password</label>
                                <input type="password" name="Confirm_password" class="form-control"
                                    placeholder=" Re Enter Password" required>
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



        <!--Edit  Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">User Edit Form </h5>
                        <button type="button" id="closeModalButton" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ url('/admin/users/update') }}" class="row g-3 needs-validation" novalidate
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label>Name</label>
                                <input type="hidden" name="userId" id="userId" value="">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter Name" required><br>
                                <label>Email</label>
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="Enter Email" required><br>
                                <label>Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control"
                                    placeholder="Enter phone" required>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit model End -->


        <!--password Edit  Modal -->
        <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Password Form </h5>
                        <button type="button" id="closeModalButton" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ url('/admin/users/password-update') }}" class="row g-3 needs-validation"
                            novalidate method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="hidden" name="userPassId" id="userPassId" value="">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Enter New Password" required>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Password model End -->

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>

                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th class="text-center">User Role</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>

                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $user->role_name }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm btnUserUpdate"
                                            data-id="{{ $user->id }}" data-toggle="modal"><i
                                                class="far fa-edit"></i></button>
                                        &nbsp;
                                        <button type="button" class="btn btn-info btn-sm passwordbtn"
                                            data-id="{{ $user->id }}" data-target="#passwordModal"
                                            data-toggle="modal">
                                            Change Password</button>
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

<script type="text/javascript">
    $(".passwordbtn").click(function() {
        var id = $(this).data("id");
        $('#userPassId').val(id);
    });
</script>


<script type="text/javascript">
    $(".btnUserUpdate").click(function() {
        var userId = $(this).data("id");
        $('#userId').val(userId);
        $.ajax({
            type: "get",
            url: "/admin/users/edit/" + userId,
            cache: false,
            success: function(data) {
                console.log(data);
                let d = data.data;
                $("#name").val(d.name);
                $("#email").val(d.email);
                $("#phone").val(d.phone);
                $("#editModal").modal("show");
            },
            error: function(e) {
                console.log(e);
            },
        });
    });
</script>


<script>
    const form = document.getElementById('user-form');

    function validateForm(event) {
        event.preventDefault(); // prevent form from submitting

        // validate select_role field
        const selectRoleField = document.getElementById('role_id');
        if (!selectRoleField.value) {
            alert('Please select a user role.');
            return;
        }

        // validate name field
        const nameField = document.getElementsByName('name')[0];
        if (!nameField.value) {
            alert('Please enter a name.');
            return;
        }

        // validate email field
        const emailField = document.getElementsByName('email')[0];
        if (!emailField.value) {
            alert('Please enter an email.');
            return;
        }

        // validate phone field
        const phoneField = document.getElementsByName('phone')[0];
        if (!phoneField.value) {
            alert('Please enter a phone.');
            return;
        }

        // validate password field
        const passwordField = document.getElementsByName('password')[0];
        if (!passwordField.value) {
            alert('Please enter a password.');
            return;
        }

        // validate confirm password field
        const confirmPasswordField = document.getElementsByName('Confirm_password')[0];
        if (!confirmPasswordField.value) {
            alert('Please enter the confirm password.');
            return;
        }

        // validate password and confirm password match
        if (passwordField.value !== confirmPasswordField.value) {
            alert('Password and confirm password do not match.');
            return;
        }

        // if all fields are valid, show confirmation alert
        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to add this form?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // if user confirms, submit the form
                form.submit();
            }
        });
    }

    form.addEventListener('submit', validateForm);
</script>


</body>

</html>
