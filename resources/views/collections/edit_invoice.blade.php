@extends('adminlte::page')

@section('title', 'Edit Invoice')

@section('content_header')
    <h1>Edit Invoice</h1>
@stop

@section('content')
<div class="bg-white p-5 shadow-sm rounded">
        <h2>Edit Invoice Items for Collection #{{ $collection->id }}</h2>
        <hr>

        <form action="{{ route('collections.invoice.update', $collection->id) }}" method="POST">
            @csrf
            @method('PUT')

            <table class="table table-bordered align-middle" id="invoice-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">QTY</th>
                        <th style="width: 50%;">DESCRIPTION</th>
                        <th style="width: 20%;">PRICE (£)</th>
                        <th style="width: 20%;">TOTAL (£)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collection->invoiceItems as $index => $item)
                        <tr class="item-row">
                            <td><input type="number" name="items[{{ $index }}][qty]" class="form-control qty" value="{{ $item->qty }}" required min="1"></td>
                            <td><input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item->description }}" required></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][price]" class="form-control price" value="{{ $item->price }}" required></td>
                            <td><input type="text" class="form-control line-total" readonly value="0.00"></td>
                        </tr>
                    @empty
                        <tr class="item-row">
                            <td><input type="number" name="items[0][qty]" class="form-control qty" value="1" required min="1"></td>
                            <td><input type="text" name="items[0][description]" class="form-control" value="ADMINISTRATION FEE FOR IT COLLECTION MADE ON {{ $collection->collection_date }}" required></td>
                            <td><input type="number" step="0.01" name="items[0][price]" class="form-control price" value="120.00" required></td>
                            <td><input type="text" class="form-control line-total" readonly value="120.00"></td>
                        </tr>
                        <tr class="item-row">
                            <td><input type="number" name="items[1][qty]" class="form-control qty" value="2" required min="1"></td>
                            <td><input type="text" name="items[1][description]" class="form-control" value="Shredding of hard drives" required></td>
                            <td><input type="number" step="0.01" name="items[1][price]" class="form-control price" value="4.00" required></td>
                            <td><input type="text" class="form-control line-total" readonly value="8.00"></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="row justify-content-end mt-4">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong> <span id="subtotal">£0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>VAT (20%):</strong> <span id="vat">£0.00</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2">
                        <h4>Total:</h4> <h4 id="grand-total">£0.00</h4>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('collections.pdf.collection_invoice', $collection->id) }}" target="_blank" class="btn btn-secondary">View PDF</a>
                <button type="submit" class="btn btn-success">Save Invoice Data</button>
            </div>
        </form>
    </div>

    <script>
        function calculateTotals() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const total = qty * price;
                
                row.querySelector('.line-total').value = total.toFixed(2);
                subtotal += total;
            });

            let vat = subtotal * 0.20;
            let grandTotal = subtotal + vat;

            document.getElementById('subtotal').innerText = '£' + subtotal.toFixed(2);
            document.getElementById('vat').innerText = '£' + vat.toFixed(2);
            document.getElementById('grand-total').innerText = '£' + grandTotal.toFixed(2);
        }

        document.getElementById('invoice-table').addEventListener('input', calculateTotals);
        window.addEventListener('DOMContentLoaded', calculateTotals);
    </script>
@stop