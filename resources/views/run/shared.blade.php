@extends('layouts.app')

@section('title', 'การวิ่งที่แชร์กับฉัน')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card gofit-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">การวิ่งที่แชร์กับฉัน</h5>
                    <div>
                        <a href="{{ route('run.index') }}" class="btn btn-sm btn-primary rounded-pill">
                            <i class="fas fa-running me-1"></i> กลับไปหน้าวิ่ง
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($sharedRuns->count() > 0)
                        <div class="row">
                            @foreach($sharedRuns as $share)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-primary">{{ $share->run->distance }} กม.</span>
                                                @php
                                                    $hours = floor($share->run->duration / 3600);
                                                    $minutes = floor(($share->run->duration % 3600) / 60);
                                                    $seconds = $share->run->duration % 60;
                                                    $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                                @endphp
                                                <span class="badge bg-secondary ms-1">{{ $duration }}</span>
                                            </div>
                                            <div class="text-muted small">
                                                {{ $share->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex mb-3">
                                            <div class="me-3">
                                                <div class="avatar">
                                                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $share->user->username }}</h6>
                                                <p class="text-muted mb-0 small">{{ $share->user->firstname }} {{ $share->user->lastname }}</p>
                                            </div>
                                        </div>

                                        @if($share->share_message)
                                        <div class="alert alert-light mb-3">
                                            {{ $share->share_message }}
                                        </div>
                                        @endif

                                        <div class="mb-3" id="sharedRunMap-{{ $share->share_id }}" style="height: 200px; width: 100%; border-radius: var(--radius-md);"></div>

                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fs-5 fw-bold text-primary">{{ number_format($share->run->distance, 2) }}</div>
                                                <div class="small text-muted">กิโลเมตร</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="fs-5 fw-bold text-primary">{{ $duration }}</div>
                                                <div class="small text-muted">เวลา</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="fs-5 fw-bold text-primary">{{ number_format($share->run->calories_burned) }}</div>
                                                <div class="small text-muted">แคลอรี่</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-top-0">
                                        <a href="{{ route('run.show', $share->run->run_id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                        </a>
                                        @if(!$share->is_viewed)
                                        <a href="#" class="btn btn-sm btn-success mark-as-viewed float-end" data-id="{{ $share->share_id }}">
                                            <i class="fas fa-check me-1"></i> ทำเครื่องหมายว่าดูแล้ว
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $sharedRuns->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-share-alt fa-3x text-muted mb-3"></i>
                            <h5>ยังไม่มีการวิ่งที่แชร์กับคุณ</h5>
                            <p class="text-muted">เมื่อเพื่อนแชร์การวิ่งกับคุณ จะแสดงที่นี่</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin="" />
<style>
    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .location-pin {
        transition: all 0.3s ease;
    }

    .location-pin:hover {
        transform: scale(1.2);
    }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // แสดงแผนที่สำหรับแต่ละการแชร์
        const sharedRuns = @json($sharedRuns);

        sharedRuns.forEach(share => {
            setTimeout(() => {
                const mapId = 'sharedRunMap-' + share.share_id;
                const mapElement = document.getElementById(mapId);

                if (mapElement) {
                    const map = L.map(mapId).setView([13.736717, 100.523186], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    if (share.run.route_data && share.run.route_data.length > 0) {
                        const points = share.run.route_data.map(point => [point.lat, point.lng]);

                        const polyline = L.polyline(points, {
                            color: 'blue',
                            weight: 5,
                            opacity: 0.7
                        }).addTo(map);

                        map.fitBounds(polyline.getBounds());

                        // เพิ่มมาร์กเกอร์จุดเริ่มต้นและจุดสิ้นสุด
                        L.marker(points[0], {
                            icon: L.divIcon({
                                className: 'location-pin',
                                html: '<i class="fas fa-play-circle text-success" style="font-size: 20px;"></i>',
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            })
                        }).addTo(map);

                        L.marker(points[points.length - 1], {
                            icon: L.divIcon({
                                className: 'location-pin',
                                html: '<i class="fas fa-flag-checkered text-danger" style="font-size: 20px;"></i>',
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            })
                        }).addTo(map);
                    }
                }
            }, 500);
        });

        // ทำเครื่องหมายว่าดูแล้ว
        document.querySelectorAll('.mark-as-viewed').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const shareId = this.getAttribute('data-id');

                fetch('{{ route("run.mark-as-viewed", ":id") }}'.replace(':id', shareId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.remove();
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
@endsection
