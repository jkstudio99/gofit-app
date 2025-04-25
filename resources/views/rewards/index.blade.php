@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Rewards</h4>
                    <div class="user-points">
                        <span class="badge bg-primary p-2">
                            <i class="fa fa-star mr-1"></i> Your Points: {{ $userPoints }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h5>Available Rewards</h5>
                    <div class="row mt-3">
                        @forelse($rewards as $reward)
                            <div class="col-md-4 mb-4">
                                <div class="reward-card {{ $userPoints >= $reward->points_required ? 'available' : 'unavailable' }}">
                                    <div class="reward-image">
                                        @if($reward->reward_image)
                                            <img src="{{ asset('storage/' . $reward->reward_image) }}" alt="{{ $reward->reward_name }}">
                                        @else
                                            <div class="default-image">
                                                <i class="fa fa-gift"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="reward-details">
                                        <h5>{{ $reward->reward_name }}</h5>
                                        <p class="description">{{ $reward->reward_description }}</p>
                                        <div class="points-required">
                                            <span><i class="fa fa-star"></i> {{ $reward->points_required }} points</span>
                                        </div>
                                        <div class="quantity">
                                            Available: {{ $reward->quantity > 0 ? $reward->quantity : 'Out of stock' }}
                                        </div>

                                        <form action="{{ route('rewards.redeem') }}" method="POST" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="reward_id" value="{{ $reward->reward_id }}">
                                            <button type="submit" class="btn btn-primary btn-sm w-100"
                                                {{ ($userPoints < $reward->points_required || $reward->quantity <= 0) ? 'disabled' : '' }}>
                                                Redeem
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center">No rewards available at the moment.</p>
                            </div>
                        @endforelse
                    </div>

                    <hr>

                    <h5 class="mt-4">Your Redemption History</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reward</th>
                                    <th>Points Used</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($redeemedRewards as $redeem)
                                    <tr>
                                        <td>{{ $redeem->created_at->format('d M Y, H:i') }}</td>
                                        <td>{{ $redeem->reward->reward_name }}</td>
                                        <td>{{ $redeem->points_used }}</td>
                                        <td>
                                            @if($redeem->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($redeem->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($redeem->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No redemption history</td>
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
@endsection

@section('styles')
<style>
    .reward-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        height: 100%;
        overflow: hidden;
        transition: transform 0.3s;
    }

    .reward-card:hover {
        transform: translateY(-5px);
    }

    .reward-card.available {
        border: 2px solid #28a745;
    }

    .reward-card.unavailable {
        border: 2px solid #dee2e6;
        opacity: 0.7;
    }

    .reward-image {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        overflow: hidden;
    }

    .reward-image img {
        max-width: 100%;
        max-height: 150px;
        object-fit: cover;
    }

    .default-image {
        font-size: 4rem;
        color: #6c757d;
    }

    .reward-details {
        padding: 15px;
    }

    .reward-details h5 {
        margin-bottom: 10px;
        font-weight: bold;
    }

    .description {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 10px;
        min-height: 60px;
    }

    .points-required {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 5px;
    }

    .quantity {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .user-points {
        font-size: 1.1rem;
    }
</style>
@endsection
