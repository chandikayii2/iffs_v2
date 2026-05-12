@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Issue Note List</h4>
                <h6>Manage your Issue Note</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('create-issue-note-view') }}" class="btn btn-added">
                    <img src="/assets/admin/img/icons/plus.svg" alt="img">Add New
                </a>
            </div>
        </div>
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


                <!--products  Modal -->
                <div class="modal fade" id="issueViewModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="static"
                    data-keyboard="false">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Issue Note Products </h5>
                                <button type="button" id="closeModalButton" class="close" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body mb-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><strong>Product Code</strong></th>
                                            <th><strong>Product Name</strong></th>
                                            <th class="text-center"><strong>Issued Quantity</strong></th>
                                            <th class="text-center"><strong>Serial Number</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody class="details_body">

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- products model End -->

                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th style="display:none">
                                    <label class="checkboxs">
                                        <input type="checkbox" id="select-all">
                                        <span class="checkmarks"></span>
                                    </label>
                                </th>
                                <th>Issue No</th>
                                <th class="text-right"> Issue Date</th>
                                <th class="text-center">Driver Name</th>
                                <th class="text-center">Lorry No</th>
                                <th class="text-center">Remark</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($issue_notes as $issue_note)
                                <tr>
                                    <td style="display:none">
                                        <!-- Your checkbox code here -->
                                    </td>
                                    <td>{{ $issue_note->issue_note_number }}</td>
                                    <td class="text-right">{{ $issue_note->issue_note_date }}</td>
                                    <td class="text-center">{{ $issue_note->driver_name }}</td>
                                    <td class="text-center">{{ $issue_note->lorry_number }}</td>
                                    <td class="text-center">{{ $issue_note->remarks }}</td>
                                    <td class="text-center">

                                        <a type="button" class="issue_note_products" data-id="{{ $issue_note->id }}"
                                            data-toggle="modal" data-target="#issueViewModal">
                                            <img src="/assets/admin/img/icons/eye.svg" alt="img">
                                        </a>

                                        &nbsp; &nbsp;
                                        <a type="button"
                                            href="{{ url('admin/issue-note/generate-pdf/' . $issue_note->id) }}"
                                            class="pdf_products">
                                            <img src="/assets/admin/img/icons/pdf.svg" alt="img">
                                        </a>
                                        &nbsp; &nbsp;
                                       @if(Auth::check() && in_array(Auth::user()->role_id, [1, 2]))
    &nbsp; &nbsp;
    <a type="button" class="issue_note_delete" data-id="{{ $issue_note->id }}"
        data-toggle="modal" style="cursor: pointer;">
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
    $('#closeModalButton').click(function() {
        $('#editModal').modal('hide');
    });
</script>

<script>
    $(".issue_note_delete").click(function() {
    var issue_note_id = $(this).data("id");

    Swal.fire({
        title: 'Confirmation',
        text: 'Are you sure you want to Delete this Issue Note? This will restore the stock quantities.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: 'DELETE',
                url: "/admin/issue-note/delete-issue-note/" + issue_note_id,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                success: function(data) {
                    if (data.status == 200) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.success(data.message);
                        
                        // Reload the page after successful deletion
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else if (data.status == 400) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.error(data.message);
                    }
                },
                error: function(e) {
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true
                    }
                    toastr.error('An error occurred while deleting the issue note.');
                    console.error(e);
                }
            });
        }
    });
});
</script>


<script>
    $(".issue_note_products").click(function() {
        var issue_note_id = $(this).data("id");
        // alert(issue_note_id);
        $.ajax({
            method: 'GET',
            dataType: "json",
            url: "/admin/issue-note/issue_note_products_view/" + issue_note_id,
            cache: false,
            success: function(data) {
                let dataArray = data.data;
                console.log(dataArray);

                // Clear previous data in the modal body
                $('.details_body').empty();

                // Loop through the retrieved grnProducts
                dataArray.forEach(function(product) {
                    var row = '<tr>' +
                        '<td>' + product.product_code + '</td>' +
                        '<td>' + product.product_name + '</td>' +
                        '<td class="text-center">' + product.issued_quantity + '</td>' +
                        '<td class="text-center">';

                    // Check if grnSerialNumbers exist
                    if (product.issue_serial_numbers && product.issue_serial_numbers
                        .length >
                        0) {
                        // Loop through grnSerialNumbers
                        product.issue_serial_numbers.forEach(function(serial) {
                            row += serial.serial_number + '<br>';
                        });
                    } else {
                        row += 'No serial numbers';
                    }

                    row += '</td></tr>';

                    // Append row to the modal table
                    $('.details_body').append(row);
                });

            },
            error: function(e) {
                console.log(e);
            },
        });
    });
</script>

</body>

</html>