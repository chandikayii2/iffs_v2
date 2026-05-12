@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Product List</h4>
                <h6>Manage your Products</h6>
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


        <!-- Add product Modal -->
        <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Add Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" id="product-form" class="row g-3 needs-validation" novalidate method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Product Code</label>
                                <input type="text" name="product_code" class="form-control"
                                    placeholder="Enter Product Code" required>
                            </div>
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="product_name" class="form-control"
                                    placeholder="Enter Product Name" required>
                            </div>
                            {{-- <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" placeholder="Enter Product Description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Stock Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control"
                                    placeholder="Enter Stock Quantity" required>
                            </div>
                            <div class="form-group">
                                <label>Unit Price</label>
                                <input type="number" name="unit_price" class="form-control"
                                    placeholder="Enter Unit Price" required>
                            </div> --}}
                            <div class="form-group">
                                <label>Unit of Measurement</label>
                                <select name="unit_of_measurement" class="form-control" required>
                                    <option value="">Select Unit</option>
                                    <option value="pieces">Pieces</option>
                                    <option value="units">Units</option>
                                    <option value="sets">Sets</option>
                                    <option value="g">Grams (g)</option>
                                    <option value="kg">Kilograms (Kg)</option>
                                    <option value="ml">Milliliters (ml)</option>
                                    <option value="l">Liters (L)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Serial Number</label>
                                <select name="serial_number" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
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


        <!--Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Product Edit Form </h5>
                        <button type="button" id="closeModalButton" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="product-edit-form" action="{{ url('/admin/products/update') }}"
                            class="row g-3 needs-validation" novalidate method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label>Product Code</label>
                                <input type="text" name="product_code" id="product_code" class="form-control"
                                    placeholder="Enter Code" required><br>
                                <label>Product Name</label>
                                <input type="hidden" name="productId" id="productId" value="">
                                <input type="text" name="product_name" id="product_name" class="form-control"
                                    placeholder="Enter Name" required><br>
                                <div class="form-group">
                                    <label>Unit of Measurement</label>
                                    <select name="unit_of_measurement" id="unit_of_measurement" class="form-control"
                                        required>
                                        <option value="">Select Unit</option>
                                        <option value="pieces">Pieces</option>
                                        <option value="units">Units</option>
                                        <option value="sets">Sets</option>
                                        <option value="g">Grams (g)</option>
                                        <option value="kg">Kilograms (Kg)</option>
                                        <option value="ml">Milliliters (ml)</option>
                                        <option value="l">Liters (L)</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" onclick="return validateForm()"
                            class="btn btn-primary btn-sm">Update</button>
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

            <div class="table-responsive container">
                <table class="table datanew ">
                    <thead>
                        <tr>
                            <th style="display: none">
                                <label class="checkboxs">
                                    <input type="checkbox" id="select-all">
                                    <span class="checkmarks"></span>
                                </label>
                            </th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            {{-- <th class="text-center">Quantity</th> --}}
                            {{-- <th>Unit Price</th> --}}
                            <th>Unit Of Measurment</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td style="display: none">
                                    <label class="checkboxs">
                                        <input type="checkbox" class="select-single">
                                        <span class="checkmarks"></span>
                                    </label>
                                </td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->product_name }}</td>
                                {{-- <td class="text-center">{{ $product->stock_quantity }}</td> --}}
                                {{-- <td>{{ $product->unit_price }}</td> --}}
                                <td>{{ $product->unit_of_measurement }}</td>

                                @php
                                    $has_product_id = DB::table('purchase_order_products')
                                        ->where('product_id', $product->id)
                                        ->exists();
                                @endphp

                                <td class="text-center">
                                    <button type="button" class="btn btn-success btn-sm btnProductEdit"
                                        data-id="{{ $product->id }}" data-toggle="modal">
                                        <i class="far fa-edit"></i>
                                    </button>&nbsp;

                                    {{-- Display delete icon if product ID does not exist in purchase_order_products --}}
                                    @if (!$has_product_id)
                                        <a type="button" class="product_delete" data-id="{{ $product->id }}"
                                            data-toggle="modal" style="vertical-align: middle;">
                                            <img src="/assets/admin/img/icons/delete.svg" alt="img">
                                        </a>
                                    @endif
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
    $(".btnProductEdit").click(function() {
        var productId = $(this).data("id");
        $('#productId').val(productId);

        $.ajax({
            type: "get",
            url: "/admin/products/edit/" + productId,
            cache: false,
            success: function(data) {
                console.log(data);
                let d = data.data;
                $("#product_code").val(d.product_code);
                $("#product_name").val(d.product_name);
                $("#unit_of_measurement").val(d.unit_of_measurement);
                $("#editModal").modal("show");
            },
            error: function(e) {
                console.log(e);
            },
        });
    });
</script>



<script type="text/javascript">
    $(".product_delete").click(function() {
        var productId = $(this).data("id");
        $('#productId').val(productId);
        // alert(productId);

        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to Delete this?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "DELETE",
                    url: "/admin/products/delete/" + productId,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    cache: false,
                    success: function(data) {
                        // Handle success response
                        if (data.status == 200) {
                            toastr.options = {
                                "closeButton": true,
                                "progressBar": true
                            }
                            toastr.success(data.message);
                        } else if (data.status == 400) {
                            toastr.options = {
                                "closeButton": true,
                                "progressBar": true
                            }
                            toastr.error(data.message);
                        }
                        $(".product_delete").prop("disabled", false);
                        setTimeout(function() {
                            window.location.href =
                                "{{ url('admin/products') }}";
                        }, 1500);
                    },
                    error: function(e) {
                        // Handle error response
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.error(e);
                        $(".product_delete").prop("disabled", false);
                    }
                });
            }
        });
    });
</script>

<script>
    const form = document.getElementById('product-form');

    function validateForm(event) {
        event.preventDefault(); // prevent form from submitting

        // validate each form field
        const fields = form.querySelectorAll('input, select, textarea');
        for (const field of fields) {
            if (!field.value) {
                alert(`Please enter ${field.name.replace('_', ' ')}.`);
                field.focus();
                return;
            }
        }

        // Gather form data
        const formData = new FormData(form);

        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to Add this?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request
                $.ajax({
                    url: '{{ route('product-create') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Close the modal
                        $('#add').modal('hide');
                        // Show success message using Toastr
                        toastr.success(response.message);

                        setTimeout(function() {
                            window.location.href = "{{ url('admin/products') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const key in errors) {
                                errorMessage = `${errors[key][0]}\n`;
                            }
                            alert(errorMessage);
                        } else {
                            // Handle other errors
                            console.error(xhr.responseText);
                            // Optionally, you can display an error message to the user
                        }
                    }
                });
            }
        });


    }

    form.addEventListener('submit', validateForm);
</script>

<script>
    function validateForm() {
        var productCode = document.getElementById("product_code").value;
        var productName = document.getElementById("product_name").value;
        var unitOfMeasurement = document.getElementById("unit_of_measurement").value;

        if (productCode.trim() == '') {
            alert("Please fill out the Product Code field.");
            document.getElementById("product_code").focus();
            return false;
        } else if (productName.trim() == '') {
            alert("Please fill out the Product Name field.");
            document.getElementById("product_name").focus();
            return false;
        } else if (unitOfMeasurement == '') {
            alert("Please select a Unit of Measurement.");
            document.getElementById("unit_of_measurement").focus();
            return false;
        }

        return true;
    }
</script>

</body>

</html>
