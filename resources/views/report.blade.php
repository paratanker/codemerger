@extends('layouts.app')
@section('content-title')
<div class="col-sm-6">
    <h3 class="mb-0">Logs</h3>
</div>
<div class="col-sm-6">
    <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Unfixed Layout</li>
    </ol>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <table class="table table-bordered" id="datatables">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>PR</th>
                    <th>Merge</th>
                    <th>Deploy</th>
                    <th>Output</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $l)
                <tr>
                    <td>{{ $l->id }}</td>
                    <td>{{ $l->user->email ?? 'n/a' }}</td>
                    <td>[{{ $l->direction }}] {{ $l->source_branch }} → <br>{{ $l->dest_branch }}</td>
                    <td>@if($l->pr_link)<a href="{{ $l->pr_link }}" target="_blank">PR</a>@endif</td>
                    <td>{{ $l->merge_status }}</td>
                    <td>{{ $l->deploy_status }}</td>
                    <td>
                        <pre>{{ $l->deploy_output }}</pre>
                    </td>
                    <td>{{ $l->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $logs->links() }}
@endsection