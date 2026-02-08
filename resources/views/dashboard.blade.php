@extends('layouts.app')
@section('content-title')
<div class="col-sm-6">
    <h3 class="mb-0">Dashboard</h3>
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

    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon text-bg-primary shadow-sm">
                <i class="fas fa-sync-alt"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">Total Merges</span>
                <span class="info-box-number">
                    {{ $summary['total'] }}
                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Recent</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-maximize">
                        <i data-lte-icon="maximize" class="fas fa-maximize"></i>
                        <i data-lte-icon="minimize" class="fas fa-minimize"></i>
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body"> 
                <div class="px-2">
                    @foreach($summary['recent'] as $r)
                    <div class="d-flex border-top py-2 px-1">
                        <div class="col-12">
                            <span href="javascript:void(0)" class="fw-bold">
                                {{ $r->user->email ?? 'n/a' }}
                                <span class="badge text-bg-primary float-end"> {{ $r->created_at }} </span>
                            </span>
                            <div>{{ $r->source_branch }} → {{ $r->dest_branch }}</div>
                        </div>
                    </div>
                    @endforeach
                    <!-- /.item -->
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>@endsection