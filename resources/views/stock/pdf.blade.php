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

                <span style="font-size: 23px; color: black; margin-top: 10px;">STOCK</span><br>
                <label style="margin-top: 10px;">
                    Date <input type="text" value="{{ $date }}" readonly>
                </label><br>
            </div>

        </div>
        <hr>

        <div style="margin-bottom: 30px;">
            <div style="text-align: center;">
                <div>
                    <h3>STOCK DETAILS</h3>
                </div>
            </div>


        </div>
        <table>
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Unit Of Measurement</th>
                    <th style="text-align:right;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pdfData as $data)
                    <tr>
                        <td>{{ $data->product_code }}</td>
                        <td>{{ $data->product_name }}</td>
                        <td>{{ $data->unit_of_measurement }}</td>
                        <td style="text-align:right;">{{ $data->stock_quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                    <td style="text-align:right;">
                        {{ $pdfData->sum('stock_quantity') }}
                    </td>
                </tr>
            </tfoot>
        </table>
        {{-- <div style="border: none;background-color: white; font-size:16px; float: right;">
            <label style="text-align: center">For and onbehalf of</label><br>
            <label for="text-align: center"> Inter Freight Forwarding Service Pvt Ltd</label>
        </div> --}}
    </div>


</body>

</html>
