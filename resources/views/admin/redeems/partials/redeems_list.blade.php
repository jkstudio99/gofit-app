@if($redeems->isEmpty())
   <div class="text-center py-5">
        <img src="{{ asset('images/empty-redeem.svg') }}" alt="ไม่มีรายการ" class="img-fluid mb-3" style="max-width: 200px;">
        <h5>ไม่พบรายการแลกรางวัล</h5>
        <p class="text-muted">ยังไม่มีรายการแลกรางวัลในระบบ</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ผู้ใช้</th>
                    <th>รางวัล</th>
                    <th>คะแนนที่ใช้</th>
                    <th>สถานะ</th>
                    <th>วันที่แลก</th>
                    <th>หมายเหตุ</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($redeems as $key => $redeem)
                <tr>
                    <td>{{ $redeems->firstItem() + $key }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($redeem->user->profile_image)
                                <img src="{{ asset('storage/' . $redeem->user->profile_image) }}"
                                     class="rounded-circle me-2" width="40" height="40"
                                     alt="{{ $redeem->user->username }}">
                            @else
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                     style="width: 40px; height: 40px; color: white;">
                                    {{ substr($redeem->user->username, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold">{{ $redeem->user->username }}</div>
                                <div class="small text-muted">{{ $redeem->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="reward-img-small me-2">
                                @if($redeem->reward->image_path)
                                    <img src="{{ asset('storage/' . $redeem->reward->image_path) }}"
                                         style="width: 40px; height: 40px; object-fit: contain;"
                                         alt="{{ $redeem->reward->name }}">
                                @else
                                    <i class="fas fa-gift fa-2x text-primary"></i>
                                @endif
                            </div>
                            <div>{{ $redeem->reward->name }}</div>
                        </div>
                    </td>
                    <td><span class="badge bg-warning text-dark">{{ $redeem->points_used ?? $redeem->reward->points_required }} คะแนน</span></td>
                    <td>
                        @if($redeem->status == 'pending')
                            <span class="badge bg-warning">
                                <span class="status-indicator status-pending"></span>รอดำเนินการ
                            </span>
                        @elseif($redeem->status == 'completed')
                            <span class="badge bg-success">
                                <span class="status-indicator status-completed"></span>เสร็จสิ้น
                            </span>
                        @elseif($redeem->status == 'cancelled')
                            <span class="badge bg-danger">
                                <span class="status-indicator status-cancelled"></span>ยกเลิก
                            </span>
                        @endif
                    </td>
                    <td>{{ $redeem->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $redeem->note ?? '-' }}</td>
                    <td>
                        <div class="d-flex">
                            @if($redeem->status == 'pending')
                                <button type="button"
                                        class="btn btn-success btn-sm me-1 btn-update-status"
                                        data-redeem-id="{{ $redeem->redeem_id }}"
                                        data-status="completed">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-danger btn-sm btn-update-status"
                                        data-redeem-id="{{ $redeem->redeem_id }}"
                                        data-status="cancelled">
                                    <i class="fas fa-times"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-lock"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
