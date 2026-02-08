@extends('layouts.app')
@section('content-title')
<div class="col-sm-6">
    <h3 class="mb-0">Merge</h3>
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
    <div class="col-12 d-flex justify-content-between mb-3">
        <div>
            @can('manage-users')
            <a href="{{ route('matches.index') }}" class="btn btn-outline-primary"><i class="fa fa-link"></i> Match Branches</a>
            @endcan
        </div>
        <div>
            <a href="{{ route('merge.form', ['refresh' => 1]) }}" class="btn btn-outline-secondary">
                <i class="fa fa-rotate"></i> Refresh Branches
            </a>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">Available Pairs</div>
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead><tr><th>{{ config('services.bitbucket.repo_a') }} (A)</th><th>{{ config('services.bitbucket.repo_b') }} (B)</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        @php($pairs = \App\Models\RepoBranchMatch::with(['branchA','branchB'])->orderBy('id','desc')->get())
                        @forelse($pairs as $p)
                        <tr>
                            <td>{{ $p->branchA->branch ?? '' }}</td>
                            <td>{{ $p->branchB->branch ?? '' }}</td>
                            <td class="text-end">
                                <form method="post" action="{{ route('merge') }}" class="d-inline ajax-merge">@csrf
                                    <input type="hidden" name="branchA" value="{{ $p->branchA->branch ?? '' }}"/>
                                    <input type="hidden" name="branchB" value="{{ $p->branchB->branch ?? '' }}"/>
                                    <button name="direction" value="AtoB" class="btn btn-sm btn-primary">Sync A → B</button>
                                </form>
                                <form method="post" action="{{ route('merge') }}" class="d-inline ajax-merge">@csrf
                                    <input type="hidden" name="branchA" value="{{ $p->branchA->branch ?? '' }}"/>
                                    <input type="hidden" name="branchB" value="{{ $p->branchB->branch ?? '' }}"/>
                                    <button name="direction" value="BtoA" class="btn btn-sm btn-secondary">Sync B → A</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">No pairs matched yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection