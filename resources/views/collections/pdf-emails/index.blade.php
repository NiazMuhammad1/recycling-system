@extends('adminlte::page')
@section('title','Edit Collection')
@section('plugins.Select2', true)
@section('content_header')
<h1>Edit Collection {{ $collection->collection_code }}</h1>
@stop

@section('content')
<div class="container">

    <h2>
        Collection PDF Emails
    </h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">

        <div class="card-header">
            Send PDFs
        </div>

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('collections.pdf-emails.send', $collection) }}"
            >

                @csrf

                <h5>Emails</h5>

                <div class="mb-2">

                    @if($collection->client?->contact_email)

                        <label>

                            <input
                                type="checkbox"
                                name="emails[]"
                                value="{{ $collection->client->contact_email }}"
                                checked
                            >

                            {{ $collection->client->contact_email }}

                        </label>

                        <br>

                    @endif

                    @if($collection->client?->sec_contact_email)

                        <label>

                            <input
                                type="checkbox"
                                name="emails[]"
                                value="{{ $collection->client->sec_contact_email }}"
                            >

                            {{ $collection->client->sec_contact_email }}

                        </label>

                    @endif

                </div>

                <hr>

                <h5>PDFs</h5>

                <div class="mb-3">

                    <label>
                        <input type="checkbox" name="pdfs[]" value="weee" checked>
                        WEEE Disposal
                    </label>

                    <br>

                    <label>
                        <input type="checkbox" name="pdfs[]" value="duty" checked>
                        Duty Of Care
                    </label>

                    <br>

                    <label>
                        <input type="checkbox" name="pdfs[]" value="hazard">
                        Hazardous
                    </label>

                    <br>

                    <label>
                        <input type="checkbox" name="pdfs[]" value="audit">
                        Audit Report
                    </label>

                    <br>

                    <label>
                        <input type="checkbox" name="pdfs[]" value="data_destruction">
                        Data Destruction
                    </label>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Send PDFs
                </button>

            </form>

        </div>
    </div>

    <div class="card">

        <div class="card-header">
            Previous Emails
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Email</th>

                        <th>PDFs</th>

                        <th>Status</th>

                        <th>Sent At</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($pdfEmails as $item)

                        <tr>

                            <td>
                                {{ $item->id }}
                            </td>

                            <td>
                                {{ $item->email }}
                            </td>

                            <td>

                                @foreach($item->pdfs as $pdf)

                                    <span class="badge bg-info">
                                        {{ $pdf }}
                                    </span>

                                @endforeach

                            </td>

                            <td>

                                @if($item->status == 'sent')

                                    <span class="badge bg-success">
                                        Sent
                                    </span>

                                @elseif($item->status == 'failed')

                                    <span class="badge bg-danger">
                                        Failed
                                    </span>

                                @else

                                    <span class="badge bg-warning">
                                        Pending
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ optional($item->sent_at)->format('d M Y h:i A') }}
                            </td>

                            <td>

                                <a
                                    href="{{ route('collections.pdf-emails.show', $item) }}"
                                    class="btn btn-sm btn-info"
                                >
                                    View
                                </a>

                                <form
                                    action="{{ route('collections.pdf-emails.resend', $item) }}"
                                    method="POST"
                                    style="display:inline-block;"
                                >

                                    @csrf

                                    <button
                                        class="btn btn-sm btn-warning"
                                        onclick="return confirm('Resend email?')"
                                    >
                                        Resend
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6">
                                No emails found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

            {{ $pdfEmails->links() }}

        </div>

    </div>

</div>

@stop