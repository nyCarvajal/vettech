<li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="me-auto">
        <div class="fw-semibold">{{ $title }}</div>
        @isset($subtitle)
            <small class="text-muted">{{ $subtitle }}</small>
        @endisset
    </div>
    @isset($meta)
        <span class="badge bg-light text-dark">{{ $meta }}</span>
    @endisset
</li>
