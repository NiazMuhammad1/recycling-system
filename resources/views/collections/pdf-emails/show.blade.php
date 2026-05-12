@extends('adminlte::page')
@section('title','Edit Collection')
@section('plugins.Select2', true)
@section('content_header')
<h1></h1>
@stop

@section('content')
<div class="container">

    <div class="card">

        <div class="card-header">
            PDF Email Details
        </div>

        <div class="card-body">

            <p>
                <strong>ID:</strong>
                {{ $pdfEmail->id }}
            </p>

            <p>
                <strong>Email:</strong>
                {{ $pdfEmail->email }}
            </p>

            <p>
                <strong>Status:</strong>
                {{ $pdfEmail->status }}
            </p>

            <p>
                <strong>PDFs:</strong>
            </p>

            <ul>

                @foreach($pdfEmail->pdfs as $pdf)

                    <li>
                        {{ $pdf }}
                    </li>

                @endforeach

            </ul>

            <p>
                <strong>Sent At:</strong>

                {{ optional($pdfEmail->sent_at)->format('d M Y h:i A') }}
            </p>

            <p>
                <strong>Error:</strong>

                {{ $pdfEmail->error }}
            </p>

            <form
                method="POST"
                action="{{ route('collections.pdf-emails.resend', $pdfEmail) }}"
            >

                @csrf

                <button class="btn btn-warning">

                    Resend Email

                </button>

            </form>

        </div>

    </div>

</div>

@stop