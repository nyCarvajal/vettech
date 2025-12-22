<div class="card shadow-sm h-100">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0 text-uppercase text-muted small">{{ $title }}</h6>
            @isset($badge)
                <span class="badge bg-primary">{{ $badge }}</span>
            @endisset
        </div>
        <h3 class="fw-bold mb-1">{{ $value }}</h3>
        @isset($subtitle)
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        @endisset
        @isset($footer)
            <div class="mt-3 text-end">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
