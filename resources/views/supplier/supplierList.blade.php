@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Supplier List</h4>
                <h6>Manage your Supplier</h6>
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
                        timeOut: 1500
                    });
                </script>
            @endif
        @endif

        <!-- Add Supplier Modal -->
        <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Add Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('supplier-create') }}" id="supplier-form" class="row g-3 needs-validation"
                        novalidate method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Name"
                                    required><br>

                                <label>Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Enter Email"
                                    required><br>

                                <label>Contact</label>
                                <input type="text" name="contact" class="form-control" placeholder="Enter Contact"
                                    required><br>

                                <label>Address</label>
                                <textarea name="address" class="form-control" placeholder="Enter Address" required></textarea>
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


        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Edit Supplier</h5>
                        <button type="button" id="closeModalButton" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/admin/supplier/update') }}" class="row g-3 needs-validation" novalidate
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label>Name</label>
                                <input type="hidden" name="supplierId" id="supplierId" value="">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter Name" required><br>
                                <label>Email</label>
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="Enter Email" required>
                                <label>Contact</label>
                                <input type="text" name="contact" id="contact" class="form-control"
                                    placeholder="Enter Contact" required><br>

                                <label>Address</label>
                                <textarea name="address" id="address" class="form-control" placeholder="Enter Address" required></textarea>

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



        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-path">
                            <a class="btn btn-filter" id="filter_search">
                                <img src="/assets/admin/img/icons/filter.svg" alt="img">
                            </a>
                        </div>
                        <div class="search-input">
                            <a class="btn btn-searchset"><img src="/assets/admin/img/icons/search-white.svg"
                                    alt="img"></a>
                        </div>
                    </div>

                </div>

                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table datanew">
                    <thead>
                        <tr>
                            <th style="display: none">
                                <label class="checkboxs">
                                    <input type="checkbox" id="select-all">
                                    <span class="checkmarks"></span>
                                </label>
                            </th>
                            <th>Supplier Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td style="display: none">
                                    <label class="checkboxs">
                                        <input type="checkbox" class="select-single">
                                        <span class="checkmarks"></span>
                                    </label>
                                </td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->contact }}</td>
                                <td>{{ $supplier->address }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-success btn-sm btnSupplierEdit"
                                        data-id="{{ $supplier->id }}" data-toggle="modal"><i
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
    $(".btnSupplierEdit").click(function() {
        var supplierId = $(this).data("id");
        $('#supplierId').val(supplierId);
        // alert(id);
        $.ajax({
            type: "get",
            url: "/admin/supplier/edit/" + supplierId,
            cache: false,
            success: function(data) {
                console.log(data);
                let d = data.data;
                $("#name").val(d.name);
                $("#email").val(d.email);
                $("#contact").val(d.contact);
                $("#address").val(d.address);
                $("#editModal").modal("show");
            },
            error: function(e) {
                console.log(e);
            },
        });
    });
</script>

<script>
    const form = document.getElementById('supplier-form');
    const submitBtn = document.getElementById('submit-btn');

    function validateForm(event) {
        event.preventDefault(); // prevent form from submitting

        // validate name field
        const nameField = document.getElementsByName('name')[0];
        if (!nameField.value) {
            alert('Please enter a name.');
            nameField.focus(); // Focus on the name field
            return;
        }

        // validate email field
        const emailField = document.getElementsByName('email')[0];
        if (!emailField.value) {
            alert('Please enter an email.');
            emailField.focus(); // Focus on the email field
            return;
        }

        // validate contact field
        const contactField = document.getElementsByName('contact')[0];
        if (!contactField.value) {
            alert('Please enter a contact.');
            contactField.focus(); // Focus on the contact field
            return;
        }

        // validate address field
        const addressField = document.getElementsByName('address')[0];
        if (!addressField.value) {
            alert('Please enter the address.');
            addressField.focus(); // Focus on the address field
            return;
        }

        // if all fields are valid, submit the form
        form.submit();
    }

    submitBtn.addEventListener('click', validateForm);
</script>



</body>

</html>
