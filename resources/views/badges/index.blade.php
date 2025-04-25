@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Your Badges</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Earned Badges ({{ count($userBadgeIds) }})</h5>
                            <div class="row mt-3">
                                @forelse($allBadges->whereIn('badge_id', $userBadgeIds) as $badge)
                                <div class="col-md-3 mb-4">
                                    <div class="badge-item earned">
                                        <div class="badge-icon">
                                            <i class="fa {{ $badge->badge_icon ?? 'fa-medal' }}"></i>
                                        </div>
                                        <div class="badge-details">
                                            <h5>{{ $badge->badge_name }}</h5>
                                            <p>{{ $badge->badge_description }}</p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-md-12">
                                    <p class="text-center">You haven't earned any badges yet. Start running to earn your first badge!</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Available Badges</h5>
                            <div class="row mt-3">
                                @foreach($allBadges->whereNotIn('badge_id', $userBadgeIds) as $badge)
                                <div class="col-md-3 mb-4">
                                    <div class="badge-item locked">
                                        <div class="badge-icon">
                                            <i class="fa {{ $badge->badge_icon ?? 'fa-medal' }}"></i>
                                        </div>
                                        <div class="badge-details">
                                            <h5>{{ $badge->badge_name }}</h5>
                                            <p>{{ $badge->badge_description }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
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
    .badge-item {
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .badge-item.earned {
        background-color: #f0f7ff;
        border: 2px solid #4a89dc;
    }

    .badge-item.locked {
        background-color: #f5f5f5;
        border: 2px dashed #aaa;
        opacity: 0.8;
    }

    .badge-icon {
        font-size: 3rem;
        margin-bottom: 10px;
        color: #4a89dc;
    }

    .badge-item.locked .badge-icon {
        color: #888;
    }

    .badge-details h5 {
        font-size: 1.1rem;
        margin-bottom: 5px;
    }

    .badge-details p {
        font-size: 0.85rem;
        color: #666;
    }
</style>
@endsection
