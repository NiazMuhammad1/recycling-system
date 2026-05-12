@extends('adminlte::page')

@section('title', 'Edit Collection')

@section('plugins.Select2', true)

@section('content_header')
    <h1>
        Edit Collection {{ $collection->collection_code }}
    </h1>
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

    @if ($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    {{-- SEND PDF CARD --}}
    <div class="card mb-4">

        <div class="card-header">

            <strong>
                Send PDFs
            </strong>

        </div>

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('collections.pdf-emails.send', $collection) }}"
            >

                @csrf

                {{-- EMAILS --}}
                <h5 class="mb-3">

                    Select Emails

                </h5>

                <div class="mb-4">

                    @if($collection->contact_email)

                        <div class="mb-2">

                            <label>

                                <input
                                    type="checkbox"
                                    name="emails[]"
                                    value="{{ $collection->contact_email }}"
                                    checked
                                >

                                {{ $collection->contact_email }}

                            </label>

                        </div>

                    @endif

                    @if($collection->sec_contact_email)

                        <div class="mb-2">

                            <label>

                                <input
                                    type="checkbox"
                                    name="emails[]"
                                    value="{{ $collection->sec_contact_email }}"
                                >

                                {{ $collection->sec_contact_email }}

                            </label>

                        </div>

                    @endif

                </div>

                <hr>

                {{-- PDFS --}}
                <h5 class="mb-3">

                    Select PDFs

                </h5>

                <div class="mb-4">

                    <div class="mb-2">

                        <label>

                            <input
                                type="checkbox"
                                name="pdfs[]"
                                value="weee"
                                checked
                            >

                            WEEE Disposal

                        </label>

                    </div>

                    <div class="mb-2">

                        <label>

                            <input
                                type="checkbox"
                                name="pdfs[]"
                                value="duty"
                                checked
                            >

                            Duty Of Care

                        </label>

                    </div>

                    <div class="mb-2">

                        <label>

                            <input
                                type="checkbox"
                                name="pdfs[]"
                                value="hazard"
                            >

                            Hazardous

                        </label>

                    </div>

                    <div class="mb-2">

                        <label>

                            <input
                                type="checkbox"
                                name="pdfs[]"
                                value="audit"
                            >

                            Audit Report

                        </label>

                    </div>

                    <div class="mb-2">

                        <label>

                            <input
                                type="checkbox"
                                name="pdfs[]"
                                value="data_destruction"
                            >

                            Data Destruction

                        </label>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="fas fa-paper-plane"></i>

                    Send PDFs

                </button>

            </form>

        </div>

    </div>

    {{-- HISTORY CARD --}}
    <div class="card">

        <div class="card-header">

            <strong>
                Previous Emails
            </strong>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped">

                <thead>

                    <tr>

                        <th width="80">
                            ID
                        </th>

                        <th width="120">
                            Type
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            PDFs
                        </th>

                        <th width="150">
                            Status
                        </th>

                        <th width="180">
                            Sent At
                        </th>

                        <th width="200">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($pdfEmails as $item)

                        <tr>

                            {{-- ID --}}
                            <td>

                                {{ $item->id }}

                            </td>

                            {{-- TYPE --}}
                            <td>

                                @if($item->parent_id)

                                    <span class="badge bg-warning">

                                        Resend

                                    </span>

                                    <br>

                                    <small class="text-muted">

                                        From #{{ $item->parent_id }}

                                    </small>

                                @else

                                    <span class="badge bg-primary">

                                        Original

                                    </span>

                                @endif

                            </td>

                            {{-- EMAIL --}}
                            <td>

                                {{ $item->email }}

                            </td>

                            {{-- PDFS --}}
                            <td>

                                @foreach($item->pdfs as $pdf)

                                    <span class="badge bg-info text-dark mb-1">

                                        {{ ucfirst(str_replace('_', ' ', $pdf)) }}

                                    </span>

                                @endforeach

                            </td>

                            {{-- STATUS --}}
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

                                @if($item->error)

                                    <br>

                                    <small class="text-danger">

                                        {{ Str::limit($item->error, 80) }}

                                    </small>

                                @endif

                            </td>

                            {{-- SENT AT --}}
                            <td>

                                @if($item->sent_at)

                                    {{ $item->sent_at->format('d M Y') }}

                                    <br>

                                    <small class="text-muted">

                                        {{ $item->sent_at->format('h:i A') }}

                                    </small>

                                @else

                                    -

                                @endif

                            </td>

                            {{-- ACTIONS --}}
                            <td>

                                <a
                                    href="{{ route('collections.pdf-emails.show', $item) }}"
                                    class="btn btn-sm btn-info mb-1"
                                >

                                    <i class="fas fa-eye"></i>

                                    View

                                </a>

                                <form
                                    action="{{ route('collections.pdf-emails.resend', $item) }}"
                                    method="POST"
                                    style="display:inline-block;"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-warning mb-1"
                                        onclick="return confirm('Resend this email?')"
                                    >

                                        <i class="fas fa-redo"></i>

                                        Resend

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="text-center">

                                No PDF emails found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="mt-3">

                {{ $pdfEmails->links() }}

            </div>

        </div>

    </div>

</div>

@stop