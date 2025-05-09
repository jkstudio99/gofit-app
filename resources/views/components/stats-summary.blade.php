<!-- Stats Summary Cards in 3-column layout for Mobile -->
<div class="stats-row mb-4">
    <div class="stat-col">
        <div class="stat-icon">
            <i class="fas fa-road" style="color: #2ecc71;"></i>
        </div>
        <div class="stat-value">{{ number_format($totalDistance ?? 0, 1) }}</div>
        <div class="stat-label">กิโลเมตรสะสม</div>
    </div>

    <div class="stat-col">
        <div class="stat-icon">
            <i class="fas fa-fire" style="color: #ff5e57;"></i>
        </div>
        <div class="stat-value">{{ number_format($totalCalories ?? 0) }}</div>
        <div class="stat-label">แคลอรี่ที่เผาผลาญ</div>
    </div>

    <div class="stat-col">
        <div class="stat-icon">
            <i class="fas fa-calendar-check" style="color: #2ecc71;"></i>
        </div>
        <div class="stat-value">{{ $userRegisteredEvents ?? 0 }}</div>
        <div class="stat-label">กิจกรรมที่เข้าร่วม</div>
    </div>
</div>
