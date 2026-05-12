@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Purchase Edit</h4>
                <!-- <h6>Add/Update Purchase</h6> -->
            </div>
        </div>
        <form>
            @csrf
            <div class="card" id="po-message">
                <div class="card-body">
                    <div class="row">

                        @foreach ($purchase_orders as $po)
                            <input type="hidden" name="purchase_order_id" id="purchase_order_id"
                                value="{{ $po->id }}">

                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Purchase Order No</label>
                                    <input type="text" name="purchase_order_number" id="purchase_order_number"
                                        value="{{ $po->purchase_order_number }}" readonly>
                                </div>

                            </div>

                            <div class="col-lg-4 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Supplier Name</label>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-10 col-10">
                                            <select class="select" id="select_supplier" name="select_supplier">
                                                <option value="{{ $po->supplier_id }}">{{ $po->supplier_name }}
                                                </option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}
                                                    </option>
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
                                        <input type="text" name="purchase_order_date" id="purchase_order_date"
                                            placeholder="DD-MM-YYYY" value="{{ $po->purchase_order_date }}"
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
                                    <input type="text" name="reference" id="reference" value="{{ $po->reference }}">
                                </div>
                            </div>
                        @endforeach
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
                                        <th style="font-weight: bold;">Product name</th>
                                        <th style="font-weight: bold;">UoM</th>
                                        <th style="font-weight: bold;">Quantity</th>
                                        <th style="font-weight: bold;">Unit Price</th>
                                        <th style="text-align: right; font-weight: bold;">Total</th>
                                        <th style="text-align: center; font-weight: bold;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">
                                    @foreach ($purchase_order_products as $product)
                                        <tr>
                                            <td style="display:none;">{{ $product->id }}</td>
                                            <td>{{ $product->product_code }}</td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>{{ $product->unit_of_measurement }}</td>
                                            <td>
                                                <input type="number" name="qty[]"
                                                    style="width:80px; text-align: center;"
                                                    value="{{ $product->quantity }}" class="qty">
                                            </td>
                                            <td style="float: left">
                                                <input type="number" name="price[]"
                                                    style="width:90px; text-align: center;"
                                                    value="{{ $product->unit_price }}" class="price">
                                            </td>
                                            <td style="text-align:right;">
                                                {{ $product->quantity * $product->unit_price }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm removeProducttable"
                                                    data-id="{{ $product->id }}">
                                                    <i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="5" style="font-weight: bold;" class="text-right">Total:</td>
                                        <td colspan="" style="text-align:right;font-weight: bold;" id="totalAmount">
                                            <!-- Calculate and display total amount here -->
                                            @php
                                                $totalAmount = 0;
                                                foreach ($purchase_order_products as $product) {
                                                    $totalAmount += $product->quantity * $product->unit_price;
                                                }
                                                echo $totalAmount;
                                            @endphp
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>


                    <div class="col-lg-12">
                        <button type="button" id="update-button" class="btn btn-submit me-2">Update</button>
                    </div>
                </div>
            </div>
    </div>
</div>
</div>
</div>
</form>

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
            // Check if the product with the same ID or product code already exists in the table
            var existingProduct = $('#product-details-table tbody tr').filter(function() {
                return $(this).find('td:first-child').text() == product.id || $(this).find(
                    'td:nth-child(2)').text() == product.product_code;
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
                    '<td style="float:left"><input type="number" name="price[]" style="width:90px; text-align:center;" value="' +
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


        $(document).on('click', '.removeProducttable', function() {
            var row = $(this).closest('tr');
            var po_product_id = $(this).data("id");

            if (confirm("Are you sure you want to delete this product?")) {
                $.ajax({
                    url: '/admin/purchase-order/delete-po-product/' + po_product_id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        row.remove();
                        alert("Product deleted successfully");
                        updateTotalAmount();
                    },
                    error: function(error) {
                        console.log(error);
                        alert("Error deleting product");
                    }
                });
            }
        });



        $(document).on('change', '.qty, .price', function() {
            var qty = parseInt($(this).val());
            if (qty <= 1) {
                qty = 1;
                $(this).val(qty);
            }
            updateTotalAmount();
        });

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


        //update button
        $('#update-button').click(function() {
            var purchase_order_id = $('#purchase_order_id').val();
            var purchase_order_date = $('#purchase_order_date').val();
            var supplier_id = $('#select_supplier').val();
            var reference = $('#reference').val();

            // console.log(purchase_order_id, purchase_order_date, supplier_id, reference);

            var products = [];

            $('#product-details-table tbody tr').each(function() {
                var quantity = $(this).find('.qty').val().trim();
                var unit_price = $(this).find('.price').val().trim();
                var po_product_id = $(this).find('td:first-child').text(); // Product ID

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
                    po_product_id: po_product_id, // Product ID
                    quantity: quantity,
                    unit_price: unit_price,
                };
                products.push(product);
            });

            console.log(products); // Log the products array

            if (products.length === 0) {
                return;
            }

            // Show confirmation message using SweetAlert and proceed if user confirms
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to Update this?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send the data to the API endpoint
                    $.ajax({
                        url: '{{ route('update-purchase-order') }}',
                        type: 'POST',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            purchase_order_id: purchase_order_id,
                            purchase_order_date: purchase_order_date,
                            supplier_id: supplier_id,
                            reference: reference,
                            products: products,
                        },
                        success: function(data) {
                            let d = data.data;
                            // console.log(d);
                            // alert('update');

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
                            $("#update-button").prop("disabled", false);

                            setTimeout(function() {
                                window.location.href =
                                    "{{ url('admin/purchase-order') }}";
                            }, 1500);


                        },
                        error: function(e) {
                            toastr.options = {
                                "closeButton": true,
                                "progressBar": true
                            }
                            toastr.error(e);
                            $("#update-button").prop("disabled", false);
                        }
                    });

                }
            });


        });


    });
</script>
