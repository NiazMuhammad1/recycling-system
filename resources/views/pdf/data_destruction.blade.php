<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
        margin: 20px;
    }
    .header {
        text-align: center;
        margin-bottom: 10px;
    }
    .logo {
        font-size: 42px;
        font-weight: bold;
        color: #6cba3d;
        letter-spacing: -1px;
        line-height: 1;
    }
    .logo-sub {
        font-size: 10px;
        color: #6cba3d;
        letter-spacing: 4px;
        margin-top: 2px;
    }
    h1 {
        color: #6cba3d;
        font-size: 26px;
        font-weight: bold;
        margin: 15px 0 20px 0;
        text-align: center;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    .main-table {
        border: 1px solid #000;
    }
    .main-table td, .main-table th {
        border: 1px solid #000;
        padding: 6px 8px;
        vertical-align: top;
        font-size: 12px;
    }
    .label {
        font-weight: bold;
    }
    .items-header {
        font-weight: bold;
        background-color: #fff;
    }
    .items-table th {
        text-align: left;
        font-weight: bold;
        font-size: 12px;
        background-color: #fff;
    }
    .signature-cell {
        height: 120px;
    }
    .sig-text {
        font-family: 'Brush Script MT', cursive;
        font-size: 28px;
        color: #222;
        margin-top: 30px;
        margin-left: 40%;
    }
    .footer {
        margin-top: 25px;
        text-align: center;
        font-size: 11px;
        color: #333;
        border-top: 4px solid #d4ecc4;
        padding-top: 10px;
    }
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">ecogreen</div>
    <div class="logo-sub">IT RECYCLING</div>
</div>

<h1>CERTIFICATE OF DATA DESTRUCTION</h1>

<!-- DETAILS TABLE -->
<table class="main-table" style="margin-bottom: 0;">
    <tr>
        <td style="width: 25%;"><span class="label">Issued by:</span></td>
        <td colspan="3">{{ $collection->user?->name }}</td>
    </tr>
    <tr>
        <td><span class="label">Ref No:</span></td>
        <td colspan="3">{{ $collection->collection_code }}</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 50%;">
            <span class="label">Client:</span><br/><br/>
            <strong>{{ optional($collection->client)->name }}</strong><br/>
            {{ optional($collection->client)->address_line_1 }}
                        <br/>{{ optional($collection->client)->postcode }}
        </td>
        <td colspan="2" style="width: 50%;">
            <span class="label">Delivery Address:</span><br/><br/>
            <strong>Commercial IT Recycling LTD</strong><br/>
            Unit 3 William Isaac Building<br/>
            3 Gibbons Street<br/>
            Nottingham<br/>
            NG7 2SB
        </td>
    </tr>
    <tr>
        <td><span class="label">Date:</span></td>
        <td colspan="3">{{ $collection->collection_date}}</td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 18px 12px; text-align: center; line-height: 1.6;">
            EcoGreen IT Recycling hereby certifies that all data on the following data bearing devices have been destroyed by the indicated process in accordance with the Data Protection Act 2018.
        </td>
    </tr>
    <tr>
        <td colspan="4" class="items-header">Items Destroyed:</td>
    </tr>
</table>

<!-- ITEMS TABLE -->
<table class="main-table items-table" style="border-top: none; margin-bottom: 0;">
    <tr>
        <th style="width: 33%;">Item</th>
        <th style="width: 33%;">Quantity</th>
        <th style="width: 34%;">Method of Destruction</th>
    </tr>

    @forelse($erasureItems as $row)
        <tr>
            <td class="signature-cell" style="vertical-align: top;">
                {{ $row->item }}
            </td>
            <td class="signature-cell" style="vertical-align: top;">
                X {{ $row->quantity }}
            </td>
            <td class="signature-cell" style="vertical-align: top;">
                {{ $row->method ?: 'N/A' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="signature-cell" style="text-align: center;">
                No erasure-required items found
            </td>
        </tr>
    @endforelse
</table>

<!-- SIGNATURE TABLE -->
<table class="main-table" style="border-top: none;">
    <tr>
        <td colspan="3" style="padding: 8px;">
            <span class="label">On behalf of Commercial IT Recycling LTD:</span>
            <div class="sig-text">{{ $collection->user?->name }}</div>
        </td>
    </tr>
</table>

<!-- FOOTER -->
<div class="footer">
    ECOGREEN IT RECYCLING IS TRADING NAME OF COMMERCIAL IT RECYCLING LTD
</div>

</body>
</html>