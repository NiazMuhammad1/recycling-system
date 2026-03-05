<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        color: #000;
        margin: 10px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    .section-header {
        background-color: #000;
        color: #fff;
        font-weight: bold;
        font-size: 11px;
        padding: 4px 6px;
        text-transform: uppercase;
    }
    .bordered {
        border: 1.5px solid #000;
    }
    .bordered td, .bordered th {
        border: 1px solid #000;
        padding: 3px 5px;
        vertical-align: top;
    }
    .bordered th {
        background-color: #e0e0e0;
        font-weight: bold;
        text-align: center;
        font-size: 10px;
    }
    .label {
        font-weight: bold;
        font-size: 10px;
    }
    .value {
        font-size: 11px;
        min-height: 16px;
    }
    .checkbox {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 1px solid #000;
        text-align: center;
        font-size: 10px;
        line-height: 12px;
        margin-right: 3px;
    }
    .checkbox-checked {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 1px solid #000;
        text-align: center;
        font-size: 10px;
        line-height: 12px;
        margin-right: 3px;
        background-color: #000;
        color: #fff;
    }
    .sig-line {
        border-bottom: 1px solid #000;
        min-width: 150px;
        display: inline-block;
        padding-bottom: 2px;
    }
    h1 {
        font-size: 18px;
        font-weight: bold;
        margin: 0;
    }
</style>
</head>
<body>

<!-- HEADER -->
<table style="width:100%; margin-bottom: 5px;">
    <tr>
        <td style="width:40%; vertical-align: middle;">
            <div style="font-size: 16px; font-weight: bold; letter-spacing: 1px;">COMPUTER IT</div>
            <div style="font-size: 7px; letter-spacing: 2px;">D I S P O S A L</div>
            <div style="font-size: 7px; color: #555;">www.computeritdisposals.co.uk</div>
        </td>
        <td style="width:35%; text-align: center; vertical-align: middle;">
            <h1>DUTY OF CARE</h1>
        </td>
        <td style="width:25%; text-align: right; vertical-align: middle;">
            <span style="font-size: 11px;">Ref: <strong>OLE954/17147</strong></span>
        </td>
    </tr>
</table>

<!-- SECTION A -->
<table class="bordered" style="margin-bottom: 6px;">
    <tr>
        <td colspan="5" class="section-header">SECTION A – DESCRIPTION OF WASTE</td>
        <td style="text-align: right; border: 1.5px solid #000; padding: 4px 6px;">
            <strong>Date:</strong> 19/02/2026
        </td>
    </tr>
    <tr>
        <td colspan="6" style="padding: 4px 6px;">
            <span class="label">A1: How is the waste contained: (loose, skip, sacks, container):</span>
            <span class="value">Loose</span>
        </td>
    </tr>
    <tr>
        <td colspan="6" style="padding: 4px 6px;">
            <span class="label">A2: How much waste (items and weight):</span>
            <span class="value">
                {{ $totalItems }} items, approx {{ number_format($totalWeight, 0) }} kg
            </span>
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center; font-weight: bold; padding: 4px; background: #f0f0f0;">
            YOUR COLLECTION DETAILS WERE:
        </td>
    </tr>
    <tr>
        <th style="width: 25%;">DESCRIPTION</th>
        <th style="width: 15%;">QUANTITY</th>
        <th style="width: 20%;">APPROX. WEIGHT</th>
        <th style="width: 15%;">PER ITEM (KG)</th>
        <th colspan="2" style="width: 25%;">EWC CODE</th>
    </tr>
    @foreach($rows as $r)
        <tr>
            <td>{{ $r->category->name }}</td>
            <td style="text-align:center;">{{ $r->qty }}</td>
            <td style="text-align:center;">{{ number_format($r->total_weight, 2) }}</td>
            <td style="text-align:center;">{{ number_format($r->per_item_weight, 2) }}</td>
            <td colspan="2" style="text-align:center;">{{ $r->category->ewc_code }}</td>
        </tr>
    @endforeach
</table>

<!-- SECTION B & C side by side -->
<table style="width: 100%; margin-bottom: 6px;">
    <tr>
        <td style="width: 48%; vertical-align: top; padding-right: 4px;">
            <table class="bordered" style="width: 100%;">
                <tr>
                    <td class="section-header">SECTION B – CURRENT HOLDER OF WASTE</td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-size: 10px; line-height: 1.6;">
                        By signing in section D below I confirm that I have fulfilled my
                        duty to apply the waste hierarchy as required by Regulations
                        12 of the Waste (England and Wales) Regulations 2011
                        <br/><br/>
                        YES <span class="checkbox">✓</span>
                        <br/><br/>
                        <span class="label">Full Name:</span> <span class="sig-line">{{ $collection->client_print_name }}</span>
                        <br/><br/>
                        <span class="label">Company Name &amp; Address:</span><br/>
                        <span class="value">{{ optional($collection->client)->name }}
                        {{ optional($collection->client)->address_line_1 }}
                        {{ optional($collection->client)->postcode }}</span>
                        <br/><br/>
                        <span class="label">What are you:</span><br/>
                        <span style="font-size: 9px;">(Producers of waste / importer of waste / local authority / holder of environmental permit.)</span>
                    </td>
                </tr>
            </table>
        </td>
        <td style="width: 52%; vertical-align: top; padding-left: 4px;">
            <table class="bordered" style="width: 100%;">
                <tr>
                    <td class="section-header">SECTION C – PERSON COLLECTING THE WASTE</td>
                </tr>
                <tr>
                    <td style="padding: 5px;">
                        <table style="width: 100%; font-size: 10px; line-height: 1.8;">
                            <tr>
                                <td style="width: 40%;"><span class="label">Full Name:</span></td>
                                <td>{{ $collection->driver_print_name }}</td>
                            </tr>
                            <tr>
                                <td><span class="label">Company And Address:</span></td>
                                <td>
                                    Commercial IT Recycling LTD<br/>
                                    Unit 3, 3 Gibbons Street<br/>
                                    Dunkirk Industrial Estate<br/>
                                    Nottingham<br/>
                                    NG7 2SB
                                </td>
                            </tr>
                            <tr>
                                <td><span class="label">Name Of Local Council:</span></td>
                                <td>Nottingham City Council</td>
                            </tr>
                            <tr>
                                <td><span class="label">What Are You:</span></td>
                                <td>Commercial IT Recycling LTD</td>
                            </tr>
                            <tr>
                                <td><span class="label">Registered:</span></td>
                                <td>
                                    Carrier / Broker<br/>
                                    Dealer of Waste<br/>
                                    TIER 11 Exemption
                                </td>
                            </tr>
                            <tr>
                                <td><span class="label">Registration Number:</span></td>
                                <td>
                                    CBDU457511<br/>
                                    EXP/MP3646YY
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- SECTION D -->
<table class="bordered" style="width: 100%;">
    <tr>
        <td colspan="2" class="section-header">SECTION D – THE TRANSFER</td>
    </tr>
    <tr>
        <td style="width: 50%; padding: 5px; font-size: 10px; line-height: 1.8; vertical-align: top;">
            <span class="label">Transfer Address Or Collection Point:</span><br/>
            <span class="value"></span>
            <br/><br/>
            <span class="label">Who arranged the transfer:</span> Commercial IT Recycling LTD<br/>
            <span style="margin-left: 140px;">Registration number: CBDU457511</span>
            <br/><br/>
            <span class="label">Data to be processed?</span>
            <span class="checkbox-checked">✓</span> Yes
            <span class="checkbox"> </span> No
            <br/><br/>
            <span class="label">Data Destruction Method (If required):</span><br/>
            <span class="checkbox"> </span> Data Wipe HMG level 1
            &nbsp;&nbsp;
            <span class="checkbox"> </span> Data Wipe HMG level 3<br/>
            <span class="checkbox"> </span> Crush (Charges May Apply)
            <br/><br/>
            <span class="label">Would you like an audit report?</span>
            <span class="checkbox"> </span> Yes (Charges Will Apply)
            <br/><br/>
            <span class="label">Email address for audit report (If required):</span><br/>
            <span class="sig-line" style="min-width: 250px;"></span>
        </td>
        <td style="width: 50%; vertical-align: top; padding: 0;">
            <table class="bordered" style="width: 100%; margin: 0;">
                <tr>
                    <td style="padding: 8px; line-height: 1.8; font-size: 10px;">
                        <span class="label">Transferee's Signature:</span><br/>
                        <div style="height: 30px; font-style: italic; font-size: 14px; color: #333;">
                            @if($collection->driver_signature)
                                <img src="{{ $collection->driver_signature }}" style="height:70px;">
                            @else
                                <span class="muted">No signature</span>
                            @endif
                        </div>
                        <span class="label">Name:</span> <span class="sig-line">{{ $collection->driver_print_name }}</span><br/><br/>
                        <span class="label">Representing:</span> <span class="sig-line">Computer IT Disposal</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px; line-height: 1.8; font-size: 10px; border-top: 1.5px solid #000;">
                        <span class="label">Transferors Signature:</span><br/>
                        <div style="height: 30px; font-style: italic; font-size: 14px; color: #333;">
                            @if($collection->client_signature)
                                <img src="{{ $collection->client_signature }}" style="height:70px;">
                            @else
                               
                            @endif
                        </div>
                        <span class="label">Name:</span> <span class="sig-line">{{  $collection->client_print_name ? $collection->client_print_name: $collection->client?->name }}</span><br/><br/>
                        <span class="label">Representing:</span> <span class="sig-line">Company Name</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
