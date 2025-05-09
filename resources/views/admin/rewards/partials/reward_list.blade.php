@if($rewards->isEmpty())
   <div class="text-center py-5">
        <div class="text-muted mb-3">
            <i class="fas fa-gift fa-4x"></i>
        </div>
        <h5>ไม่พบข้อมูลรางวัล</h5>
        <p class="text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</p>
        <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary mt-3">
            <i class="fas fa-plus me-1"></i> เพิ่มรางวัลใหม่
        </a>
    </div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($rewards as $reward)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm reward-card">
                <div class="reward-status">
                    @if($reward->is_enabled)
                        <span class="badge bg-success px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i> เปิดใช้งาน
                        </span>
                    @else
                        <span class="badge bg-danger px-3 py-2">
                            <i class="fas fa-ban me-1"></i> ปิดใช้งาน
                        </span>
                    @endif
                </div>

                <div class="reward-stock">
                    @if($reward->quantity > 10)
                        <span class="badge bg-success px-3 py-2">
                            <i class="fas fa-cubes me-1"></i> คงเหลือ {{ $reward->quantity }}
                        </span>
                    @elseif($reward->quantity > 0)
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> เหลือน้อย {{ $reward->quantity }}
                        </span>
                    @else
                        <span class="badge bg-danger px-3 py-2">
                            <i class="fas fa-times-circle me-1"></i> หมด
                        </span>
                    @endif
                </div>

                <div class="reward-img-container">
                    @if($reward->image_path)
                        <img src="{{ asset('storage/' . $reward->image_path) }}" class="reward-img" alt="{{ $reward->name }}">
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-gift fa-3x"></i>
                            <p class="small mt-2">ไม่มีรูปภาพ</p>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <h5 class="card-title">{{ $reward->name }}</h5>
                    <p class="card-text small text-muted">{{ Str::limit($reward->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="fas fa-coins me-1"></i> {{ number_format($reward->points_required) }} คะแนน
                        </span>
                    </div>
                </div>

                <div class="card-footer bg-white py-2">
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('admin.rewards.show', $reward) }}" class="btn btn-sm btn-info text-white reward-action-btn me-2" title="ดูรายละเอียด">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('admin.rewards.edit', $reward) }}" class="btn btn-sm btn-warning reward-action-btn me-2" title="แก้ไข">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('admin.rewards.toggle-active', $reward) }}" method="POST" class="d-inline me-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $reward->is_enabled ? 'btn-success' : 'btn-dark' }} reward-action-btn"
                                    title="{{ $reward->is_enabled ? 'ปิดการใช้งาน' : 'เปิดการใช้งาน' }}">
                                <i class="fas {{ $reward->is_enabled ? 'fa-toggle-on' : 'fa-toggle-off' }} text-white"></i>
                            </button>
                        </form>

                        <button type="button" class="btn btn-sm btn-danger reward-action-btn delete-reward"
                                data-reward-id="{{ $reward->reward_id }}"
                                data-reward-name="{{ $reward->name }}"
                                title="ลบ">
                            <i class="fas fa-trash text-white"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $rewards->links() }}
    </div>
@endif
