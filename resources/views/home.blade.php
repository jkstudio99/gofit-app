@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="mb-4">Your Running Dashboard</h3>

            <div class="row">
                <!-- Stats Summary Cards -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-road fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Total Distance</h5>
                            <h2 class="display-5 fw-bold text-primary">{{ number_format($totalDistance, 1) }}</h2>
                            <p class="card-text">kilometers</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-fire-flame-simple fa-3x text-danger"></i>
                            </div>
                            <h5 class="card-title">Calories Burned</h5>
                            <h2 class="display-5 fw-bold text-danger">{{ number_format($totalCalories, 0) }}</h2>
                            <p class="card-text">kcal</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-3">
                                <i class="fas fa-running fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Total Activities</h5>
                            <h2 class="display-5 fw-bold text-success">{{ $totalActivities }}</h2>
                            <p class="card-text">runs completed</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Weekly Progress -->
                <div class="col-md-8 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Weekly Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Weekly Goal ({{ $weeklyGoal }} km)</span>
                                        <span>{{ number_format($weeklyDistance, 1) }} km</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $weeklyGoalProgress }}%" aria-valuenow="{{ $weeklyGoalProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="weekly-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Distance This Week:</span>
                                            <span class="stat-value">{{ number_format($weeklyDistance, 1) }} km</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Calories This Week:</span>
                                            <span class="stat-value">{{ number_format($weeklyCalories, 0) }} kcal</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('run.index') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Start New Run
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Badges</h5>
                            <a href="{{ route('badges.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            @if($badges->count() > 0)
                                <div class="row">
                                    @foreach($badges->take(4) as $badge)
                                        <div class="col-6 text-center mb-3">
                                            <div class="badge-icon-small">
                                                <i class="fa {{ $badge->badge_icon ?? 'fa-medal' }}"></i>
                                            </div>
                                            <div class="badge-name-small">{{ $badge->badge_name }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center my-4">You haven't earned any badges yet. Start running to earn your first badge!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Activities</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Distance</th>
                                            <th>Duration</th>
                                            <th>Avg. Speed</th>
                                            <th>Calories</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentActivities as $activity)
                                            <tr>
                                                <td>{{ $activity->start_time->format('d M Y, H:i') }}</td>
                                                <td>{{ number_format($activity->distance, 2) }} km</td>
                                                <td>
                                                    @if($activity->end_time)
                                                        {{ gmdate('H:i:s', $activity->end_time->diffInSeconds($activity->start_time)) }}
                                                    @else
                                                        In Progress
                                                    @endif
                                                </td>
                                                <td>{{ number_format($activity->average_speed, 1) }} km/h</td>
                                                <td>{{ number_format($activity->calories_burned, 0) }} kcal</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">No activities recorded yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .stat-icon {
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .weekly-stats {
        margin-top: 20px;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .stat-label {
        color: #6c757d;
    }

    .stat-value {
        font-weight: bold;
        color: #343a40;
    }

    .badge-icon-small {
        font-size: 2rem;
        color: #4a89dc;
        margin-bottom: 5px;
    }

    .badge-name-small {
        font-size: 0.8rem;
        color: #343a40;
    }
</style>
@endsection
