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
                    $issue_note_number = $pdfData->issue_note_number;
                    $issue_date = $pdfData->issue_note_date;
                    ?>
                    <span style="font-size: 23px;color: black;margin-top: 10px;">ISSUE NOTE</span><br>
                    <label for="" style="margin-top: 7px;">Date : {{ $issue_date }}</label><br>
                    <label for="" style="margin-top: 7px;">Issue No : {{ $issue_note_number }}</label><br>
                @else
                    <p>No data available</p>
                @endif
            </div>

        </div>


        <table>
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>UoM</th>
                    <th style="text-align:right;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalQty = 0;
                @endphp
                @if ($pdfData && !empty($pdfData->issue_note_products))
                    @foreach ($pdfData->issue_note_products as $product)
                        <tr style=" border-bottom: 1px solid #ddd;">
                            <td style="font-size:14px;">{{ $product->product_code }}</td>
                            <td style="font-size:14px;">{{ $product->product_name }}</td>
                            <td style="font-size:14px;">{{ $product->unit_of_measurement }}</td>
                            <td style="text-align:right; font-size:14px;">{{ $product->issued_quantity }}</td>
                        </tr>
                        @php
                            $totalQty += $product->issued_quantity;
                        @endphp
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">No products found</td>
                    </tr>
                @endif

                <tr style="background-color: #f2f2f2;">
                    <td style="text-align:right;font-weight: bold; " colspan="3">TOTAL</td>
                    <td style="text-align:right;font-weight: bold;border-top: 2px solid black;">{{ $totalQty }}</td>
                </tr>
            </tbody>

        </table>
        @if ($pdfData)
            <?php
            $driver_name = $pdfData->driver_name;
            $lorry_number = $pdfData->lorry_number;
            $remarks = $pdfData->remarks;
            ?>
            <div style="border: none;background-color: white; font-size:16px; margin-top:10px; float: left;">
                <label style="margin-top: 5px; font-size:14px;">Remarks : {{ $remarks }}</label><br>
                <label style="margin-top: 5px; font-size:14px;">Lorry No : {{ $lorry_number }}</label><br>
                <label style="margin-top: 5px; font-size:14px;">Driver : {{ $driver_name }}</label><br>
            </div>
        @endif
        <div style="border: none;background-color: white; font-size:14px; margin-top:45px; float: right;">
            <label style="margin-top: 5px;">...........................................</label><br>
            <label style="margin-top: 5px;">Manager/Stores Executive</label><br>
            <label style="margin-top: 35px;">...........................................</label><br>
            <label style="margin-top: 5px;">Driver Signature</label><br>
        </div>
    </div>
</body>



</html>
