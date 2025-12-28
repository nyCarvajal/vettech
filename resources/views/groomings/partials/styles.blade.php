@push('styles')
<style>
    :root {
        --grooming-purple-50: #f5f3ff;
        --grooming-purple-100: #ede9fe;
        --grooming-purple-400: #c084fc;
        --grooming-purple-500: #a855f7;
        --grooming-mint-50: #ecfdf3;
        --grooming-mint-100: #d1fae5;
        --grooming-mint-400: #34d399;
        --grooming-mint-500: #10b981;
    }

    .grooming-surface {
        background: linear-gradient(135deg, var(--grooming-purple-50), #ffffff 40%, var(--grooming-mint-50));
        border: 1px solid var(--grooming-purple-100);
    }

    .grooming-card {
        border: 1px solid var(--grooming-purple-100);
        box-shadow: 0 12px 32px -12px rgba(168, 85, 247, 0.2);
    }

    .grooming-card-accent {
        border-left: 5px solid var(--grooming-mint-400);
        background: linear-gradient(145deg, rgba(168, 85, 247, 0.05), rgba(16, 185, 129, 0.06));
    }

    .grooming-btn-primary {
        background: linear-gradient(135deg, var(--grooming-purple-500), var(--grooming-mint-400));
        color: white;
        border: none;
        border-radius: 0.85rem;
        padding: 0.85rem 1.6rem;
        font-weight: 600;
        box-shadow: 0 10px 25px -10px rgba(168, 85, 247, 0.55);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .grooming-btn-primary:hover {
        filter: brightness(1.03);
        transform: translateY(-1px);
        box-shadow: 0 12px 28px -10px rgba(16, 185, 129, 0.45);
    }

    .grooming-btn-secondary {
        background: linear-gradient(135deg, var(--grooming-mint-400), var(--grooming-mint-500));
        color: white;
        border: none;
        border-radius: 0.75rem;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        box-shadow: 0 8px 18px -10px rgba(16, 185, 129, 0.5);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .grooming-btn-secondary:hover { filter: brightness(1.05); transform: translateY(-1px); }

    .grooming-chip { background: var(--grooming-purple-50); border: 1px solid var(--grooming-purple-100); color: #5b21b6; }
</style>
@endpush
