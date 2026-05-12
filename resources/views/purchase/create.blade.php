@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Purchase Order Add</h4>
                <h6>Add / Update Purchase</h6>
            </div>
        </div>
        <form id="form">
            <div class="card" id="po-message">
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Purchase Order No</label>
                                <input type="text" name="po_number" id="po_number" value="{{ $newPoNo }}"
                                    readonly>
                            </div>

                        </div>

                        <div class="col-lg-4 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Supplier Name</label>
                                <div class="row">
                                    <div class="col-lg-12 col-sm-10 col-10">
                                        <select class="select" id="select_supplier" name="select_supplier">
                                            <option value="">Select Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Purchase Order Date</label>
                                <div class="input-groupicon">
                                    <input type="text" name="po_date" id="po_date" placeholder="DD-MM-YYYY"
                                        class="datetimepicker" required>
                                    <div class="addonset">
                                        <img src="/assets/admin/img/icons/calendars.svg" alt="img">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Reference</label>
                                <input type="text" name="reference" id="reference">
                            </div>
                        </div>

                        <div class="col-lg-5 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Product Name</label>
                                <div class="row">
                                    <div class="col-lg-12 col-sm-10 col-10">
                                        <select class="select" id="product" name="product">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->product_code }} - {{ $product->product_name }}
                                                    <!-- Separate code and name with a hyphen -->
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>

                    <div class="row">
                        <div class="table-responsive">
                            <table class="table" id="product-details-table">
                                <thead>
                                    <tr>
                                        <th style="display:none;">Id</th>
                                        <th style="font-weight: bold;">Product Code</th>
                                        <th style="font-weight: bold;">Product Name</th>
                                        <th style="font-weight: bold;">UoM</th>
                                        <th style="font-weight: bold;">Quantity</th>
                                        <th style="font-weight: bold;">Unit Price</th>
                                        <th style="text-align: right; font-weight: bold;">Total</th>
                                        <th style="text-align: center; font-weight: bold;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="font-weight: bold;" class="text-right ">Total:</td>
                                        <td colspan="2" style="text-align:right;font-weight: bold;" id="totalAmount">
                                            0</td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>


                    <div class="row ">
                        <div class="col-lg-12 float-md-right">
                            <div class="total-order">
                                <ul>

                                </ul>
                            </div>
                        </div>
                    </div>



                    <div class="col-lg-12">
                        <button type="button" id="submit-button" class="btn btn-submit me-2">Submit</button>
                    </div>
                </div>
            </div>
    </div>
</div>
</div>
</div>
</form>


<script>
    // get today's date
    var today = new Date();
    // get the day, month, and year
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    // combine into the desired date format
    var todayFormatted = dd + '-' + mm + '-' + yyyy;
    // set the input value to today's date
    document.getElementById("po_date").value = todayFormatted;
</script>

<script>
    $(function() {
        $("select").select2();
    });
</script>


<script>
    $(document).ready(function() {
        // Bind change event to quantity and price input fields
        $('#product-details-table').on('change', '.qty, .price', function() {
            updateTotalAmount();
        });

        // Bind click event to remove buttons
        $('#product-details-table').on('click', '.remove-product', function() {
            // Get the closest row to the clicked button
            var row = $(this).closest('tr');

            // Display a confirmation dialog
            swal.fire({
                title: 'Are you sure?',
                text: 'You are about to delete this product.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, remove the row
                    row.remove();
                    updateTotalAmount(); // Update the total amount after removing the row
                }
            });
        });

        $('#product').change(function() {
            var productId = $(this).val();
            if (productId != '') {
                var url = '/admin/purchase-order/get-product-data/' + productId;
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let product = data.data;
                        populateProductTable(product);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        function populateProductTable(product) {
            // Check if the product with the same ID already exists in the table
            var existingProduct = $('#product-details-table tbody').find('td:first-child').filter(function() {
                return $(this).text() == product.id;
            }).closest('tr');


            $(document).on('change', '.qty, .price', function() {
                var value = parseFloat($(this).val());
                if (value < 1) {
                    $(this).val(1);
                }
                updateTotalAmount();
            });


            if (existingProduct.length === 0) { // Product not found in the table, add it
                // Create a new table row with the product details and input fields for quantity and price
                var newRow = '<tr>' +
                    '<td style="display:none;">' + product.id + '</td>' +
                    '<td>' + product.product_code + '</td>' +
                    '<td>' + product.product_name + '</td>' +
                    '<td>' + product.unit_of_measurement + '</td>' +
                    '<td><input type="number" name="qty[]" style="width:80px; text-align:center;" value="1" class="qty" data-price="' +
                    product.unit_price + '"></td>' +
                    '<td style="float:left"><input type="number" name="price[]" style="width:90px; text-align:right;" value="' +
                    product.unit_price + '" class="price"></td>' +
                    '<td style="text-align:right;">' + (product.stock_quantity * product.unit_price) +
                    '</td>' +
                    '<td style="text-align:center;"><button type="button" class="btn btn-sm btn-danger remove-product"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
                // Append the new row to the table body
                $('#productTableBody').append(newRow);
            } else {
                // Product already exists in the table, display a SweetAlert alert
                swal.fire({
                    title: 'Product already exists',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }

            // Update the total amount
            updateTotalAmount();
        }

        function updateTotalAmount() {
            var totalAmount = 0;
            $('#product-details-table tbody tr').each(function() {
                var qty = parseFloat($(this).find('.qty').val());
                var price = parseFloat($(this).find('.price').val());
                var totalPrice = qty * price;
                totalAmount += totalPrice;
                $(this).find('td:nth-child(7)').text(totalPrice.toFixed(2));
            });
            $('#totalAmount').text(totalAmount.toFixed(2));
        }


        //save function

        $('#submit-button').click(function() {
            var purchase_order_date = $('#po_date').val();
            var supplier_id = $('#select_supplier').val();
            var purchase_order_number = $('#po_number').val();
            var reference = $('#reference').val();
            var products = [];

            if (purchase_order_date.trim() === '' || supplier_id.trim() === '') {
                swal.fire({
                    title: 'Please fill all fields',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if ($('#product-details-table tbody tr').length === 0) {
                swal.fire({
                    title: 'Please select at least one product',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $('#product-details-table tbody tr').each(function() {
                var quantity = $(this).find('.qty').val().trim();
                var unit_price = $(this).find('.price').val().trim();
                var product_id = $(this).find('td:first-child').text(); // Product ID

                if (quantity === '' || unit_price === '') {
                    swal.fire({
                        title: 'Please fill quantity and unit price',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    products = [];
                    return false;
                }

                var product = {
                    product_id: product_id, // Product ID
                    quantity: quantity,
                    unit_price: unit_price,
                };
                products.push(product);
            });

            if (products.length === 0) {
                return;
            }

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
                    $.ajax({
                        url: '{{ route('save-purchase-order') }}',
                        type: 'POST',
                        data: {
                            purchase_order_date: purchase_order_date,
                            supplier_id: supplier_id,
                            purchase_order_number: purchase_order_number,
                            reference: reference,
                            products: products
                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                          success: function(data) {
                    if (data.status == 200) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.success(data.message);
                        
                        setTimeout(function() {
                            window.location.href = "{{ url('admin/purchase-order/create') }}";
                        }, 1500);
                    } else if (data.status == 400) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.error(data.message); // This should now show the error message
                    }
                    $("#submit-button").prop("disabled", false);
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors (network issues, server errors, etc.)
                    console.log(xhr.responseText); // Check console for detailed error
                    
                    let errorMessage = 'An error occurred while processing your request.';
                    
                    // Try to parse the response if it's JSON
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // If not JSON, use the status text
                        errorMessage = xhr.statusText || errorMessage;
                    }
                    
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true
                    }
                    toastr.error(errorMessage);
                    $("#submit-button").prop("disabled", false);
                }
                    });
                }
            });
        });




    });
</script>
