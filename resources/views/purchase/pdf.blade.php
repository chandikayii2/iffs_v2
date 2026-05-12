<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>IFFS</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;

        }

        th,
        td {
            text-align: left;
            padding: 5px;

        }

        /* th {
            background-color: black;
            color: white;
        } */

        /* tr:nth-child(even) {
            background-color: #f2f2f2;
        } */
    </style>
</head>

<body>

    <div>
        <div style="margin-bottom: 140px;">
            <div style="float: left;width: 50%;">

                <div style="">
                    <label for="" style="font-size: 23px;">Inter Freight Forwarding Service</label><br>
                    <label>No. 12, 2nd Floor, Keyzer Street, Colombo 11.</label><br>
                    <label for="">No-789
                        Inter Freight Forwarding Service Pvt Ltd
                        Samurdi Mawatha,
                        Heiyanthuduwa,
                        Sapugaskanda,
                    </label><br>
                    <label for="">website: https://www.iffs.idealsoft.us</label><br>
                </div>
            </div>
            <div style="float: right;">

                @if ($pdfData)
                    <?php
                    $purchase_order_number = $pdfData->purchase_order_number;
                    $purchase_order_date = $pdfData->purchase_order_date;
                    ?>
                    <span style="font-size: 23px;color: black;margin-top: 10px;">PURCHASE ORDER</span><br>
                    <label for="" style="margin-top: 10px;">Date <input type="text"
                            value="{{ $purchase_order_date }}" readonly></label><br>

                    <label for="" style="margin-top: 10px;">PO# <input type="text"
                            value="{{ $purchase_order_number }}" readonly></label>
                @else
                    <p>No data available</p>
                @endif
            </div>
        </div>
        <hr>

        <div style="margin-bottom: 130px;">
            <div style="float: left;width: 45%;">
                <div style="text-align: left;">
                    <h3>SUPPLIER DETAILS</h3>
                </div>
                <div style="">
                    @if ($pdfData)
                        <?php
                        $supplier_name = $pdfData->supplier_name;
                        $supplier_contact = $pdfData->supplier_contact;
                        $supplier_address = $pdfData->supplier_address;
                        
                        ?>
                        <label for="" style="font-size: 13px;">{{ $supplier_name }}.</label><br>
                        <label for="" style="font-size: 13px;">{{ $supplier_address }}</label><br>
                        <label for="" style="font-size: 13px;">{{ $supplier_contact }}</label>
                    @else
                        <p>No data available</p>
                    @endif
                </div>
            </div>

        </div>
        <table>
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th style="text-align:right;">Qty</th>
                    <th style="text-align:right;">Unit Price</th>
                    <th style="text-align:right;">Total</th>

                </tr>
            </thead>
            <tbody>
                <?php $sub_tot = 0; ?>
                @if ($pdfData && !empty($pdfData->purchase_order_products))
                    @foreach ($pdfData->purchase_order_products as $product)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td>{{ $product->product_code }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td style="text-align:right;">{{ $product->quantity }}</td>
                            <td style="text-align:right;">{{ $product->unit_price }}</td>
                            <td style="text-align:right;">{{ $product->quantity * $product->unit_price }}</td>
                        </tr>
                        <?php $sub_tot += $product->quantity * $product->unit_price; ?>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No products found</td>
                    </tr>
                @endif
                <tr style="border: none;background-color: white;">
                    <td style="text-align:right;font-weight: bold;" colspan="4">Sub Total</td>
                    <td style="text-align:right;font-weight: bold;">{{ $sub_tot }}</td>
                </tr>
                <tr style="border: none;background-color: white;">
                    <td style="text-align:right;font-weight: bold;" colspan="4">TAX</td>
                    <td style="text-align:right;font-weight: bold;">-</td>
                </tr>

                <tr style="background-color: #f2f2f2;">
                    <td style="text-align:right;font-weight: bold;" colspan="4">TOTAL</td>
                    <td style="text-align:right;font-weight: bold;border-top: 2px solid black;">
                        {{ $sub_tot }}
                    </td>
                </tr>
            </tbody>



        </table>
        {{-- <div style="border: none;background-color: white; font-size:16px; float: right;">
            <label style="text-align: center">For and onbehalf of</label><br>
            <label for="text-align: center"> Inter Freight Forwarding Service Pvt Ltd</label>
        </div> --}}
    </div>


</body>

</html>
