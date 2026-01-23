{{-- SLA Status Badge Component --}}
@props(['task', 'size' => 'md'])

@php
    $isOverdue = $task->overdue_flag ?? false;
    $hoursRemaining = $task->getHoursUntilSLAExpiry();
    $expectedAt = $task->expected_completion_at;

    $badgeClasses = match(true) {
        $isOverdue => 'badge-danger',
        $hoursRemaining === null => 'badge-secondary',
        $hoursRemaining < 4 => 'badge-warning',
        $hoursRemaining < 24 => 'badge-info',
        default => 'badge-success'
    };

    $badgeText = match(true) {
        $isOverdue => '🔴 OVERDUE',
        $hoursRemaining === null => '⏳ No SLA Set',
        $hoursRemaining < 1 => '⚠️ < 1 hour',
        $hoursRemaining < 4 => '⏰ ' . intval($hoursRemaining) . 'h',
        $hoursRemaining < 24 => '🕐 ' . intval($hoursRemaining / 24) . 'd ' . ($hoursRemaining % 24) . 'h',
        default => '✓ On Track'
    };

    $sizeClass = match($size) {
        'sm' => 'font-size-80',
        'lg' => 'font-size-120',
        default => ''
    };
@endphp

<span class="badge {{ $badgeClasses }} {{ $sizeClass }}"
      title="{{ $expectedAt ? 'Expected: ' . $expectedAt->format('d M Y H:i') : 'No SLA deadline' }}"
      data-toggle="tooltip">
    {{ $badgeText }}
</span>
