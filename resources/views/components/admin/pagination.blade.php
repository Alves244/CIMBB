@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $paginator->appends(request()->query());
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $currentPage + 2);
    @endphp
    <div class="card-footer px-3 border-0">
        <div class="admin-pagination">
            <p class="admin-pagination__info">
                A mostrar {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} de {{ $paginator->total() }} registos
            </p>
            <div class="admin-pagination__buttons">
                <a href="{{ $paginator->previousPageUrl() ?? '#' }}" class="admin-page-btn admin-page-btn-icon {{ $paginator->onFirstPage() ? 'disabled' : '' }}" aria-label="Página anterior">
                    &laquo;
                </a>
                @for ($page = $startPage; $page <= $endPage; $page++)
                    <a href="{{ $paginator->url($page) }}" class="admin-page-btn {{ $currentPage === $page ? 'active' : '' }}">{{ $page }}</a>
                @endfor
                <a href="{{ $paginator->nextPageUrl() ?? '#' }}" class="admin-page-btn admin-page-btn-icon {{ $currentPage === $lastPage ? 'disabled' : '' }}" aria-label="Próxima página">
                    &raquo;
                </a>
            </div>
        </div>
    </div>
@endif

@once
    @push('css')
        <style>
            .admin-pagination {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.65rem;
            }

            .admin-pagination__info {
                margin: 0;
                font-size: 0.88rem;
                color: #7b809a;
            }

            .admin-pagination__buttons {
                display: flex;
                gap: 0.4rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .admin-page-btn {
                min-width: 44px;
                height: 40px;
                border-radius: 12px;
                border: 1px solid #e9ecef;
                background-color: #fff;
                color: #344767;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                box-shadow: 0 2px 6px rgba(50, 50, 93, 0.12);
                transition: all 0.2s ease;
            }

            .admin-page-btn:hover {
                color: #17ad37;
                border-color: #17ad37;
            }

            .admin-page-btn.active {
                background: linear-gradient(310deg, #17ad37, #84dc61);
                border-color: transparent;
                color: #fff;
                box-shadow: 0 8px 16px rgba(23, 173, 55, 0.35);
            }

            .admin-page-btn.disabled {
                pointer-events: none;
                opacity: 0.35;
            }

            .admin-page-btn-icon {
                font-size: 1.1rem;
                line-height: 1;
            }
        </style>
    @endpush
@endonce
