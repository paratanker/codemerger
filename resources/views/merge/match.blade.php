@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header">Create Branch Match</div>
            <div class="card-body">
                <form method="post" action="{{ route('matches.store') }}">@csrf
                <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Repo A Branch ({{ config('services.bitbucket.repo_a') }})</label>
                            <select name="branch_a" class="form-control select2">
                                @foreach($branchesA as $b)
                                <option value="{{ $b->branch }}">{{ $b->branch }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Repo B Branch ({{ config('services.bitbucket.repo_b') }}})</label>
                            <select name="branch_b" class="form-control select2">
                                @foreach($branchesB as $b)
                                <option value="{{ $b->branch }}">{{ $b->branch }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary">Save Match</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Existing Matches</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead><tr><th>{{ config('services.bitbucket.repo_a') }} (A)</th><th>{{ config('services.bitbucket.repo_b') }} (B)</th><th></th></tr></thead>
                    <tbody>
                        @foreach($pairs as $p)
                        <tr>
                            <td>{{ $p->branchA->repo ?? '' }}: {{ $p->branchA->branch ?? '' }}</td>
                            <td>{{ $p->branchB->repo ?? '' }}: {{ $p->branchB->branch ?? '' }}</td>
                            <td>
                                <form method="post" action="{{ route('matches.delete', $p->id) }}" onsubmit="return confirm('Delete this match?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

