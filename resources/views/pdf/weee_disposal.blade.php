<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Certificate of WEEE Disposal</title>

<style>
    

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10.5pt;
        color: #111;
        margin: 0;
        padding: 0;

        /* ✅ Background image for mPDF */
        background-image: url('{{ public_path("storage/images/ecogreen_certificate_background.png") }}');
        background-repeat: no-repeat;
        background-position: center center;
        background-image-resize: 6;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    td {
        vertical-align: top;
    }

    .header {
        text-align: center;
        margin-top: 2mm;
        margin-bottom: 5mm;
    }

    .logo-img {
        width: 86mm;
        height: auto;
    }

    h1 {
        font-size: 20pt;
        font-weight: bold;
        text-align: center;
        margin: 0 0 8mm 0;
        padding: 0;
        color: #000;
    }

    .main-table {
        border: 0.45mm solid #000;
        table-layout: fixed;
        background-color: transparent;
    }

    .main-table td {
        border: 0.25mm solid #000;
        padding: 2mm;
        line-height: 1.38;
    }

    .label {
        font-weight: bold;
    }

    .small-label {
        width: 36mm;
        font-weight: bold;
    }

    .half {
        width: 50%;
    }

    .intro-cell {
        height: 38mm;
        padding-top: 10mm !important;
        font-size: 10.5pt;
    }

    .license-cell {
        height: 9mm;
        vertical-align: middle !important;
        font-size: 10pt;
        font-weight: bold;
    }

    .signature-cell {
        height: 31mm;
        padding: 1.5mm 2mm !important;
    }

    .signature-img {
        width: 30mm;
        margin-left: 74mm;
        margin-top: 3mm;
    }

    .footer {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 5mm;
        text-align: center;
        font-size: 8pt;
        line-height: 1.35;
        color: #111;
    }
</style>
</head>

<body>

<div class="header">
    <!-- ✅ Logo image -->
    <img class="logo-img"
         src="{{ public_path('storage/images/ecogreen_logo.png') }}"
         alt="ecogreen IT Recycling">
</div>

<h1>CERTIFICATE OF WEEE Disposal</h1>

<table class="main-table">

    <tr>
        <td colspan="2">
            <span class="label">Issued by:</span> Ray Shah
        </td>
    </tr>

    <tr>
        <td class="small-label">Ref No:</td>
        <td>OLE954/1</td>
    </tr>

    <tr>
        <td class="half">
            <span class="label">Client:</span><br>
            Spryker Systems GmbH<br>
            Heidestrasse 9-10<br>
            10557 Berlin<br>
            Germany
        </td>

        <td class="half">
            <span class="label">Delivery Address:</span><br>

            <span class="label">Commercial IT Recycling LTD</span><br>

            Unit 3 William Isaac Building<br>
            3 Gibbons Street<br>
            Nottingham<br>
            NG7 2SB
        </td>
    </tr>

    <tr>
        <td class="small-label">Collection Date:</td>
        <td>08-04-2026</td>
    </tr>

    <tr>
        <td colspan="2" class="intro-cell">
            In accordance with The Waste Electrical and Electronic Equipment Regulations (WEEE) 2013,
            this document certifies that all the reusable equipment in this consignment has been
            processed and disposable material has been correctly consigned to a duly licensed operator
            and that the customer&rsquo;s obligations in this regard have now been successfully discharged.
        </td>
    </tr>

    <tr>
        <td colspan="2" class="license-cell">

            <table>
                <tr>
                    <td style="width:58%; border:0; padding:0;">
                        <span class="label">Waste carrier License No:</span>
                        CBDU457511
                    </td>

                    <td style="width:42%; border:0; padding:0; text-align:right;">
                        <span class="label">T11 Exemption certificate:</span>
                        EXP/MP3646YY
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    <tr>
        <td colspan="2" class="signature-cell">

            <span class="label">
                On behalf of Commercial IT Recycling LTD:
            </span>
            <br>

            <!-- ✅ Signature image -->
            <img class="signature-img"
                 src="{{ public_path('storage/images/signature_ray_shah_transparent.png') }}"
                 alt="Signature">

        </td>
    </tr>

</table>

<div class="footer">
    ECOGREEN IT RECYCLING IS TRADING NAME OF COMMERCIAL IT RECYCLING LTD<br>
    Company Registration Number: 10675077
</div>

</body>
</html>