<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        color: #000;
        margin: 20px;
    }
    table { border-collapse: collapse; width: 100%; }
    .logo {
        font-size: 26px;
        font-weight: bold;
        font-style: italic;
        color: #6cba3d;
    }
    .logo-sub {
        font-size: 9px;
        letter-spacing: 3px;
        color: #444;
    }
    h1 {
        font-size: 28px;
        color: #6cba3d;
        text-align: right;
        margin: 0;
        letter-spacing: 2px;
    }
    .meta-table td { padding: 4px 8px; vertical-align: top; }
    .label { font-weight: bold; color: #6cba3d; text-transform: uppercase; font-size: 10px; }
    .items { border: 1.5px solid #6cba3d; margin-top: 14px; }
    .items th {
        background-color: #6cba3d;
        color: #fff;
        padding: 8px;
        font-size: 11px;
        text-align: left;
    }
    .items th.center { text-align: center; }
    .items th.right { text-align: right; }
    .items td {
        border-bottom: 1px solid #d4ecc4;
        padding: 8px;
        vertical-align: top;
    }
    .items td.center { text-align: center; }
    .items td.right { text-align: right; }
    .totals td {
        padding: 6px 8px;
        font-size: 11px;
    }
    .totals .grand {
        background-color: #6cba3d;
        color: #fff;
        font-weight: bold;
        font-size: 13px;
    }
    .notes {
        margin-top: 20px;
        border-top: 1.5px solid #6cba3d;
        padding-top: 10px;
        line-height: 1.6;
    }
    .footer {
        text-align: center;
        font-size: 9px;
        color: #555;
        margin-top: 30px;
        border-top: 1px solid #6cba3d;
        padding-top: 6px;
    }
</style>
</head>
<body>

<!-- HEADER -->
<table style="margin-bottom: 10px;">
    <tr>
        <td style="width: 60%; vertical-align: middle;">
            <div class="logo">ecogreen</div>
            <div class="logo-sub">IT RECYCLING</div>
        </td>
        <td style="width: 40%; text-align: right; vertical-align: middle;">
            <h1>INVOICE</h1>
        </td>
    </tr>
</table>

<!-- META -->
<table class="meta-table" style="margin-top: 10px;">
    <tr>
        <td style="width: 50%;">
            <div class="label">From</div>
            <strong>Commercial IT Recycling Ltd</strong><br/>
            Unit 3 William Isaac Building<br/>
            3 Gibbons Street<br/>
            Nottingham<br/>
            NG7 2SB
        </td>
        <td style="width: 50%;">
            <div class="label">To</div>
            <strong>Sandfords</strong><br/>
            213-215 Gloucester Place<br/>
            London<br/>
            NW1 6BU
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 12px;">
            <span class="label">Invoice Number:</span> <strong>80925</strong>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="label">Date:</span> <strong>06/05/2026</strong>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="label">Payment Terms:</span> &mdash;
        </td>
    </tr>
</table>

<!-- ITEMS -->
<table class="items">
    <tr>
        <th class="center" style="width: 10%;">QTY</th>
        <th style="width: 55%;">DESCRIPTION</th>
        <th class="right" style="width: 17%;">PRICE</th>
        <th class="right" style="width: 18%;">TOTAL</th>
    </tr>
    <tr>
        <td class="center">1</td>
        <td>ADMINISTRATION FEE FOR IT COLLECTION MADE ON 29/04/2026</td>
        <td class="right">&pound;120.00</td>
        <td class="right">&pound;120.00</td>
    </tr>
    <tr>
        <td class="center">2</td>
        <td>Shredding of hard drives</td>
        <td class="right">&pound;4.00</td>
        <td class="right">&pound;8.00</td>
    </tr>
    <tr>
        <td class="center"></td>
        <td>OLE954/17273</td>
        <td class="right"></td>
        <td class="right"></td>
    </tr>
</table>

<!-- TOTALS -->
<table class="totals" style="margin-top: 10px;">
    <tr>
        <td style="width: 70%;"></td>
        <td style="width: 18%; text-align: right;">Subtotal</td>
        <td style="width: 12%; text-align: right;">&pound;128.00</td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align: right;">VAT @ 20%</td>
        <td style="text-align: right;">&pound;25.60</td>
    </tr>
    <tr>
        <td></td>
        <td class="grand" style="text-align: right;">TOTAL</td>
        <td class="grand" style="text-align: right;">&pound;153.60</td>
    </tr>
</table>

<!-- NOTES -->
<div class="notes">
    <strong>Please Note:</strong> Please quote the invoice number on all payments.<br/><br/>
    <strong>Payment Methods:</strong><br/>
    Online Transfer &mdash; Our bank details are:<br/>
    <span class="label">Account Name:</span> Commercial IT Recycling LTD<br/>
    <span class="label">Account Number:</span> 84877217<br/>
    <span class="label">Sort Code:</span> 40-35-18<br/>
    <span class="label">Bank:</span> HSBC
</div>

<div class="footer">
    ECOGREEN IT RECYCLING IS TRADING NAME OF COMMERCIAL IT RECYCLING LTD<br/>
    Company Registration Number: 10675077 &nbsp;|&nbsp; VAT No: 338 4575 72
</div>

</body>
</html>
