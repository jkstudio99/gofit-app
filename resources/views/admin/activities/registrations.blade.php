@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Activity Registrations</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.activities') }}">Activities</a></li>
        <li class="breadcrumb-item active">{{ $activity->title }} - Registrations</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Registrations for "{{ $activity->title }}"
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('admin.activities') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Activities
                </a>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Activity Details</h5>
                            <p><strong>Date:</strong> {{ $activity->start_time->format('M d, Y') }}</p>
                            <p><strong>Time:</strong> {{ $activity->start_time->format('h:i A') }} - {{ $activity->end_time->format('h:i A') }}</p>
                            <p><strong>Location:</strong> {{ $activity->location }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge {{ $activity->status == 'active' ? 'bg-success' : ($activity->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </p>
                            <p><strong>Registrations:</strong> {{ $registrations->total() }} {{ $activity->max_participants ? 'of ' . $activity->max_participants : '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($registrations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                                <tr>
                                    <td>{{ $registration->user->name }}</td>
                                    <td>{{ $registration->user->email }}</td>
                                    <td>{{ $registration->user->phone ?? 'N/A' }}</td>
                                    <td>{{ $registration->registration_date->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <span class="badge {{ $registration->status == 'registered' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($registration->status == 'registered')
                                            <form method="POST" action="{{ route('admin.activities.registrations.cancel', [$activity->id, $registration->id]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this registration?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.activities.registrations.restore', [$activity->id, $registration->id]) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i> Restore
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.activities.registrations.delete', [$activity->id, $registration->id]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this registration?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $registrations->links() }}
            @else
                <div class="alert alert-info">No registrations found for this activity.</div>
            @endif
        </div>
    </div>
</div>
@endsection
