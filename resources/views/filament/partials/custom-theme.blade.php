<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    /* ==========================================
       ELITE LOCADORA — Modern Theme
       Palette: Blue #2563eb · Orange #f97316 · White
       ========================================== */

    :root {
        --elite-blue-50: #eff6ff;
        --elite-blue-100: #dbeafe;
        --elite-blue-200: #bfdbfe;
        --elite-blue-300: #93c5fd;
        --elite-blue-400: #60a5fa;
        --elite-blue-500: #3b82f6;
        --elite-blue-600: #2563eb;
        --elite-blue-700: #1d4ed8;
        --elite-blue-800: #1e40af;
        --elite-blue-900: #1e3a8a;
        --elite-blue-950: #172554;

        --elite-orange-50: #fff7ed;
        --elite-orange-100: #ffedd5;
        --elite-orange-200: #fed7aa;
        --elite-orange-300: #fdba74;
        --elite-orange-400: #fb923c;
        --elite-orange-500: #f97316;
        --elite-orange-600: #ea580c;
        --elite-orange-700: #c2410c;

        --elite-navy-900: #0b1120;
        --elite-navy-800: #0f172a;
        --elite-navy-700: #1e293b;
        --elite-navy-600: #334155;
    }

    /* === GLOBAL FONT === */
    body, .fi-body {
        font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* === SIDEBAR === */
    .fi-sidebar {
        background: linear-gradient(180deg, var(--elite-navy-900) 0%, var(--elite-navy-800) 100%) !important;
        border-right: 1px solid rgba(59, 130, 246, 0.1) !important;
    }

    .fi-sidebar-header {
        border-bottom: 1px solid rgba(59, 130, 246, 0.08) !important;
        padding: 1rem 1.25rem !important;
    }

    /* Brand name styling */
    .fi-sidebar-header a,
    .fi-sidebar-header span {
        font-weight: 800 !important;
        letter-spacing: -0.025em !important;
    }

    /* Sidebar navigation items */
    .fi-sidebar-item a {
        border-radius: 0.625rem !important;
        margin: 0.125rem 0.5rem !important;
        padding: 0.5rem 0.75rem !important;
        transition: all 0.15s ease !important;
        font-weight: 500 !important;
        font-size: 0.8125rem !important;
    }

    .fi-sidebar-item a:hover {
        background: rgba(59, 130, 246, 0.08) !important;
    }

    .fi-sidebar-item-active > a,
    .fi-sidebar-item a[aria-current="page"] {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(59, 130, 246, 0.08) 100%) !important;
        color: var(--elite-blue-400) !important;
        border-left: 3px solid var(--elite-blue-500) !important;
        font-weight: 600 !important;
    }

    /* Sidebar group headers */
    .fi-sidebar-group-label {
        font-size: 0.6875rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.08em !important;
        color: rgba(148, 163, 184, 0.6) !important;
        padding: 0.75rem 1.25rem 0.375rem !important;
    }

    /* Sidebar group icons */
    .fi-sidebar-group-label .fi-icon {
        color: var(--elite-orange-500) !important;
    }

    /* === TOPBAR === */
    .fi-topbar {
        background: var(--elite-navy-900) !important;
        border-bottom: 1px solid rgba(59, 130, 246, 0.08) !important;
        backdrop-filter: blur(12px) !important;
    }

    /* === MAIN CONTENT AREA === */
    .fi-main {
        background: var(--elite-navy-800) !important;
    }

    .fi-page {
        background: transparent !important;
    }

    /* === PAGE HEADERS === */
    .fi-header-heading {
        font-weight: 800 !important;
        letter-spacing: -0.025em !important;
        color: #f8fafc !important;
    }

    .fi-header-subheading {
        color: #94a3b8 !important;
    }

    /* === CARDS & SECTIONS === */
    .fi-section,
    .fi-card {
        background: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid rgba(59, 130, 246, 0.08) !important;
        border-radius: 0.875rem !important;
        backdrop-filter: blur(8px) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }

    .fi-section-header {
        border-bottom: 1px solid rgba(59, 130, 246, 0.06) !important;
    }

    /* === TABLES === */
    .fi-ta-header-cell {
        font-size: 0.6875rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: #94a3b8 !important;
    }

    .fi-ta-row:hover {
        background: rgba(59, 130, 246, 0.04) !important;
    }

    .fi-ta-row {
        border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
        transition: background 0.15s ease !important;
    }

    /* === FORM INPUTS === */
    .fi-input,
    .fi-select,
    .fi-textarea {
        border-radius: 0.625rem !important;
        border: 1px solid rgba(59, 130, 246, 0.15) !important;
        background: rgba(15, 23, 42, 0.5) !important;
        transition: all 0.2s ease !important;
    }

    .fi-input:focus,
    .fi-select:focus,
    .fi-textarea:focus {
        border-color: var(--elite-blue-500) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    /* === BUTTONS === */
    .fi-btn-primary {
        background: linear-gradient(135deg, var(--elite-blue-600) 0%, var(--elite-blue-500) 100%) !important;
        border: none !important;
        border-radius: 0.625rem !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25) !important;
    }

    .fi-btn-primary:hover {
        background: linear-gradient(135deg, var(--elite-blue-500) 0%, var(--elite-blue-400) 100%) !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35) !important;
        transform: translateY(-1px) !important;
    }

    /* === STATS WIDGETS === */
    .fi-wi-stats-overview-stat {
        background: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid rgba(59, 130, 246, 0.08) !important;
        border-radius: 0.875rem !important;
        padding: 1.25rem !important;
        transition: all 0.2s ease !important;
    }

    .fi-wi-stats-overview-stat:hover {
        border-color: rgba(59, 130, 246, 0.15) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    }

    .fi-wi-stats-overview-stat-value {
        font-weight: 800 !important;
        font-size: 1.875rem !important;
        letter-spacing: -0.025em !important;
    }

    .fi-wi-stats-overview-stat-label {
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: #94a3b8 !important;
    }

    /* === BADGES === */
    .fi-badge {
        border-radius: 0.5rem !important;
        font-weight: 600 !important;
        font-size: 0.6875rem !important;
        letter-spacing: 0.02em !important;
    }

    /* === MODAL / DIALOG === */
    .fi-modal-window {
        background: var(--elite-navy-800) !important;
        border: 1px solid rgba(59, 130, 246, 0.1) !important;
        border-radius: 1rem !important;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4) !important;
    }

    /* === NOTIFICATIONS === */
    .fi-no {
        border-radius: 0.75rem !important;
        border: 1px solid rgba(59, 130, 246, 0.08) !important;
    }

    /* === TABS === */
    .fi-tabs-tab {
        font-weight: 600 !important;
        font-size: 0.8125rem !important;
        transition: all 0.2s ease !important;
    }

    .fi-tabs-tab[aria-selected="true"] {
        color: var(--elite-blue-400) !important;
        border-color: var(--elite-blue-500) !important;
    }

    /* === GLOBAL SEARCH === */
    .fi-global-search-input {
        border-radius: 0.75rem !important;
        background: rgba(15, 23, 42, 0.5) !important;
        border: 1px solid rgba(59, 130, 246, 0.1) !important;
    }

    .fi-global-search-input:focus {
        border-color: var(--elite-blue-500) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    /* === PAGINATION === */
    .fi-pagination-item-btn {
        border-radius: 0.5rem !important;
    }

    /* === SCROLLBAR CUSTOMIZATION === */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.2);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.35);
    }

    /* === ORANGE ACCENT FOR KEY ELEMENTS === */
    .fi-sidebar-item .fi-icon,
    .fi-sidebar-item svg {
        color: var(--elite-orange-400) !important;
        width: 1.125rem !important;
        height: 1.125rem !important;
    }

    .fi-sidebar-item-active .fi-icon,
    .fi-sidebar-item-active svg,
    .fi-sidebar-item a[aria-current="page"] .fi-icon,
    .fi-sidebar-item a[aria-current="page"] svg {
        color: var(--elite-blue-400) !important;
    }

    /* === BREADCRUMBS === */
    .fi-breadcrumbs {
        font-size: 0.8125rem !important;
    }

    .fi-breadcrumbs a {
        color: #94a3b8 !important;
        transition: color 0.15s ease !important;
    }

    .fi-breadcrumbs a:hover {
        color: var(--elite-blue-400) !important;
    }

    /* === LOADING INDICATOR === */
    .fi-loading-indicator {
        background: var(--elite-blue-600) !important;
    }

    /* === ACTION BUTTONS WITH ORANGE ACCENT === */
    .fi-ta-row-action {
        color: var(--elite-blue-400) !important;
        transition: color 0.15s ease !important;
    }

    .fi-ta-row-action:hover {
        color: var(--elite-orange-400) !important;
    }

    /* === EMPTY STATE === */
    .fi-ta-empty-state-icon {
        color: rgba(59, 130, 246, 0.2) !important;
    }

    /* === SMOOTH TRANSITIONS === */
    * {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    /* ==========================================
       CUSTOM REPORT PAGES — Shared Elite Theme
       ========================================== */
    .rpt-grid { display: grid; gap: 1rem; }
    .rpt-grid-6 { grid-template-columns: repeat(6, 1fr); }
    .rpt-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .rpt-grid-2 { grid-template-columns: 1fr 1fr; }

    .rpt-card {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(59, 130, 246, 0.08);
        border-radius: 0.875rem;
        padding: 1.25rem;
        backdrop-filter: blur(8px);
        transition: all 0.2s ease;
    }
    .rpt-card:hover {
        border-color: rgba(59, 130, 246, 0.15);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }
    .rpt-card-label {
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .rpt-card-value {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.025em;
    }
    .rpt-card-sub {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .rpt-section {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(59, 130, 246, 0.08);
        border-radius: 0.875rem;
        overflow: hidden;
        backdrop-filter: blur(8px);
    }
    .rpt-section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(59, 130, 246, 0.06);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .rpt-section-header h3 {
        font-weight: 700;
        font-size: 0.95rem;
        color: #f1f5f9;
        margin: 0;
    }

    .rpt-table { width: 100%; font-size: 0.8125rem; border-collapse: collapse; }
    .rpt-table thead tr {
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .rpt-table th, .rpt-table td { padding: 0.625rem 1.25rem; text-align: left; }
    .rpt-table tbody tr { border-bottom: 1px solid rgba(59, 130, 246, 0.04); transition: background 0.15s ease; }
    .rpt-table tbody tr:hover { background: rgba(59, 130, 246, 0.04); }

    .rpt-link { color: var(--elite-blue-400); text-decoration: none; font-weight: 600; transition: color 0.15s ease; }
    .rpt-link:hover { color: var(--elite-orange-400); text-decoration: underline; }

    .rpt-badge {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 0.6875rem;
        font-weight: 700;
    }
    .rpt-badge-danger { background: rgba(244, 63, 94, 0.12); color: #fb7185; }
    .rpt-badge-warning { background: rgba(249, 115, 22, 0.12); color: #fb923c; }
    .rpt-badge-success { background: rgba(16, 185, 129, 0.12); color: #34d399; }
    .rpt-badge-info { background: rgba(59, 130, 246, 0.12); color: #60a5fa; }
    .rpt-badge-plate {
        padding: 0.15rem 0.45rem;
        border-radius: 0.375rem;
        font-size: 0.6875rem;
        font-weight: 700;
        background: rgba(59, 130, 246, 0.1);
        color: #93c5fd;
        font-family: 'SF Mono', 'Fira Code', monospace;
        letter-spacing: 0.05em;
    }

    .rpt-filter-section {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(59, 130, 246, 0.08);
        border-radius: 0.875rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
        backdrop-filter: blur(8px);
    }
    .rpt-filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; align-items: end; }
    .rpt-filter-label {
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 0.35rem;
        display: block;
    }
    .rpt-filter-input, .rpt-filter-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(59, 130, 246, 0.12);
        border-radius: 0.625rem;
        color: #e2e8f0;
        font-size: 0.8125rem;
        outline: none;
        transition: all 0.2s ease;
    }
    .rpt-filter-input:focus, .rpt-filter-select:focus {
        border-color: var(--elite-blue-500);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .rpt-filter-select option { background: #0f172a; color: #e2e8f0; }

    .rpt-btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.625rem;
        font-weight: 600;
        font-size: 0.8125rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .rpt-btn:hover { opacity: 0.9; transform: translateY(-1px); }
    .rpt-btn-primary {
        background: linear-gradient(135deg, var(--elite-blue-600), var(--elite-blue-500));
        color: #fff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }
    .rpt-btn-secondary { background: rgba(59, 130, 246, 0.08); color: #94a3b8; }
    .rpt-btn-pdf { background: rgba(244, 63, 94, 0.1); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.15); }
    .rpt-btn-excel { background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.15); }
    .rpt-btn-orange {
        background: linear-gradient(135deg, var(--elite-orange-600), var(--elite-orange-500));
        color: #fff;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.25);
    }

    .rpt-empty { padding: 2rem; text-align: center; color: #64748b; font-size: 0.8125rem; }
    .rpt-text-right { text-align: right; }
    .rpt-chart-container { padding: 1.25rem; }
    .rpt-error {
        background: rgba(244, 63, 94, 0.06);
        border: 1px solid rgba(244, 63, 94, 0.15);
        color: #fb7185;
        padding: 0.75rem 1rem;
        border-radius: 0.625rem;
        margin-bottom: 1rem;
        font-size: 0.8125rem;
    }

    .rpt-utilization-bar {
        height: 0.5rem;
        border-radius: 9999px;
        background: rgba(59, 130, 246, 0.08);
        overflow: hidden;
        margin-top: 0.75rem;
    }
    .rpt-utilization-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.5s ease;
    }

    /* Toggle de colunas */
    .rpt-toggle-bar { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
    .rpt-toggle {
        display: inline-flex; align-items: center; gap: 0.3rem;
        font-size: 0.6875rem; color: #94a3b8;
        cursor: pointer; user-select: none;
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid rgba(59, 130, 246, 0.08);
        transition: all 0.15s;
    }
    .rpt-toggle:hover { border-color: rgba(59, 130, 246, 0.2); color: var(--elite-blue-400); }
    .rpt-toggle input { display: none; }
    .rpt-toggle.active { background: rgba(59, 130, 246, 0.08); border-color: rgba(59, 130, 246, 0.2); color: var(--elite-blue-400); }
    .col-hidden { display: none !important; }

    @media (max-width: 1024px) {
        .rpt-grid-6 { grid-template-columns: repeat(3, 1fr); }
        .rpt-grid-2 { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .rpt-grid-6 { grid-template-columns: repeat(2, 1fr); }
        .rpt-grid-3 { grid-template-columns: 1fr; }
    }

    /* ==========================================
       EXECUTIVE DASHBOARD — exec-* classes
       ========================================== */
    .exec-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        padding: 1.75rem 2rem;
        background:
            radial-gradient(1200px 300px at -10% -50%, rgba(37, 99, 235, 0.25), transparent 60%),
            radial-gradient(800px 260px at 110% 150%, rgba(249, 115, 22, 0.18), transparent 60%),
            linear-gradient(135deg, #0f172a 0%, #0b1120 100%);
        border: 1px solid rgba(59, 130, 246, 0.12);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
    }
    .exec-hero::before {
        content: "";
        position: absolute; inset: 0;
        background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'><path d='M0 59.5h60M59.5 0v60' stroke='rgba(148,163,184,0.05)' stroke-width='1' fill='none'/></svg>");
        opacity: 0.8;
        pointer-events: none;
    }
    .exec-hero-inner { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
    .exec-hero-title { font-size: 1.625rem; font-weight: 800; letter-spacing: -0.025em; color: #f8fafc; line-height: 1.1; }
    .exec-hero-sub { font-size: 0.875rem; color: #94a3b8; margin-top: 0.35rem; }
    .exec-hero-chip {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.35rem 0.75rem; margin-top: 0.75rem;
        border-radius: 9999px;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.25);
        color: #34d399; font-size: 0.75rem; font-weight: 600;
    }
    .exec-hero-chip .dot { width: 6px; height: 6px; border-radius: 50%; background: #34d399; box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.8); animation: exec-pulse 2s infinite; }
    @keyframes exec-pulse { 0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.6);} 70% { box-shadow: 0 0 0 8px rgba(52, 211, 153, 0);} 100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0);} }

    .exec-quick-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .exec-action {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 1rem; border-radius: 0.75rem;
        font-size: 0.8125rem; font-weight: 600;
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: #bfdbfe; text-decoration: none;
        transition: all 0.2s ease;
    }
    .exec-action:hover { background: rgba(59, 130, 246, 0.18); border-color: rgba(59, 130, 246, 0.35); transform: translateY(-1px); color: #fff; }
    .exec-action.exec-action-orange { background: rgba(249, 115, 22, 0.1); border-color: rgba(249, 115, 22, 0.25); color: #fed7aa; }
    .exec-action.exec-action-orange:hover { background: rgba(249, 115, 22, 0.2); border-color: rgba(249, 115, 22, 0.4); }

    /* KPI cards grandes */
    .exec-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .exec-kpi {
        position: relative; overflow: hidden;
        border-radius: 0.875rem; padding: 1.25rem;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.6));
        border: 1px solid rgba(59, 130, 246, 0.08);
        transition: all 0.25s ease;
    }
    .exec-kpi:hover { transform: translateY(-2px); border-color: rgba(59, 130, 246, 0.2); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25); }
    .exec-kpi-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; }
    .exec-kpi-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; }
    .exec-kpi-icon {
        width: 2.25rem; height: 2.25rem; border-radius: 0.625rem;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(59, 130, 246, 0.12); color: #60a5fa;
    }
    .exec-kpi-icon svg { width: 1.15rem; height: 1.15rem; }
    .exec-kpi-value { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; color: #f8fafc; margin-top: 0.5rem; line-height: 1.1; }
    .exec-kpi-value.sm { font-size: 1.375rem; }
    .exec-kpi-sub { font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; }
    .exec-kpi-trend { font-size: 0.7rem; font-weight: 700; padding: 0.1rem 0.45rem; border-radius: 0.4rem; }
    .exec-kpi.accent-green .exec-kpi-icon { background: rgba(16, 185, 129, 0.14); color: #34d399; }
    .exec-kpi.accent-blue  .exec-kpi-icon { background: rgba(59, 130, 246, 0.14); color: #60a5fa; }
    .exec-kpi.accent-orange .exec-kpi-icon { background: rgba(249, 115, 22, 0.14); color: #fb923c; }
    .exec-kpi.accent-red   .exec-kpi-icon { background: rgba(244, 63, 94, 0.14); color: #fb7185; }
    .exec-kpi.accent-purple .exec-kpi-icon { background: rgba(139, 92, 246, 0.14); color: #a78bfa; }

    /* Mini cards de frota */
    .exec-mini-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.75rem; }
    .exec-mini {
        padding: 0.9rem 1rem;
        border-radius: 0.75rem;
        background: rgba(15, 23, 42, 0.55);
        border: 1px solid rgba(59, 130, 246, 0.08);
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    .exec-mini::before {
        content: ""; position: absolute; left: 0; top: 0; bottom: 0;
        width: 3px; background: currentColor; opacity: 0.7;
    }
    .exec-mini:hover { transform: translateY(-1px); border-color: rgba(59, 130, 246, 0.22); }
    .exec-mini-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; opacity: 0.85; }
    .exec-mini-value { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; margin-top: 0.2rem; }
    .exec-mini-sub { font-size: 0.65rem; color: #64748b; margin-top: 0.15rem; }

    /* Layout 2:1 cards + chart */
    .exec-split { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; }
    .exec-chart-card {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(59, 130, 246, 0.08);
        border-radius: 0.875rem;
        padding: 1.25rem;
        backdrop-filter: blur(8px);
    }
    .exec-chart-card h3 {
        font-size: 0.95rem; font-weight: 700; color: #f1f5f9;
        margin: 0 0 0.15rem 0;
    }
    .exec-chart-card .exec-chart-sub { font-size: 0.75rem; color: #64748b; margin-bottom: 0.9rem; }
    .exec-chart-wrap { position: relative; height: 280px; }
    .exec-chart-wrap.sm { height: 230px; }

    /* Lista de atividade recente */
    .exec-activity-row {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.65rem 0; border-bottom: 1px solid rgba(59, 130, 246, 0.05);
    }
    .exec-activity-row:last-child { border-bottom: none; }
    .exec-avatar {
        width: 2.1rem; height: 2.1rem; border-radius: 0.6rem;
        display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.2), rgba(249, 115, 22, 0.15));
        color: #bfdbfe; font-size: 0.75rem; font-weight: 700;
        flex-shrink: 0;
    }
    .exec-activity-main { flex: 1; min-width: 0; }
    .exec-activity-title { font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .exec-activity-sub { font-size: 0.7rem; color: #64748b; margin-top: 0.1rem; }
    .exec-activity-meta { font-size: 0.7rem; color: #94a3b8; text-align: right; flex-shrink: 0; }

    @media (max-width: 1280px) {
        .exec-kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .exec-mini-grid { grid-template-columns: repeat(3, 1fr); }
        .exec-split { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .exec-kpi-grid { grid-template-columns: 1fr; }
        .exec-mini-grid { grid-template-columns: repeat(2, 1fr); }
        .exec-hero { padding: 1.25rem; }
        .exec-hero-title { font-size: 1.25rem; }
    }

    /* ==========================================
       VEHICLE DASHBOARD — vd-* classes
       ========================================== */

    /* Grids responsivos */
    .vd-grid { display: grid; gap: 1rem; }
    .vd-g2 { grid-template-columns: repeat(2, 1fr); }
    .vd-g3 { grid-template-columns: repeat(3, 1fr); }
    .vd-g4 { grid-template-columns: repeat(4, 1fr); }
    .vd-g6 { grid-template-columns: repeat(6, 1fr); }
    @media (max-width: 1280px) {
        .vd-g4 { grid-template-columns: repeat(2, 1fr); }
        .vd-g6 { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .vd-g2, .vd-g3, .vd-g4 { grid-template-columns: 1fr; }
        .vd-g6 { grid-template-columns: repeat(2, 1fr); }
    }

    /* HEADER do veículo */
    .vd-header {
        display: flex; align-items: stretch; gap: 1.25rem;
        padding: 1.25rem;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(11, 17, 32, 0.75) 100%) !important;
        border: 1px solid rgba(59, 130, 246, 0.1) !important;
        border-radius: 1rem !important;
        outline: none !important;
        backdrop-filter: blur(12px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    .vd-header-photo {
        width: 180px; height: 130px;
        object-fit: cover;
        border-radius: 0.75rem;
        border: 1px solid rgba(59, 130, 246, 0.15);
        flex-shrink: 0;
    }
    .vd-header-placeholder {
        width: 180px; height: 130px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(59, 130, 246, 0.05);
        border: 1px dashed rgba(59, 130, 246, 0.2);
        border-radius: 0.75rem;
        flex-shrink: 0;
    }
    .vd-header-title {
        font-size: 1.5rem; font-weight: 800; letter-spacing: -0.025em;
        color: #f8fafc; margin: 0 0 0.5rem 0; line-height: 1.15;
    }
    .vd-header-meta {
        display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;
        font-size: 0.8125rem; color: #cbd5e1; margin-bottom: 0.35rem;
    }
    .vd-header-meta > span:not(.vd-plate) {
        padding: 0.15rem 0.55rem;
        background: rgba(59, 130, 246, 0.08);
        border-radius: 0.4rem; font-size: 0.75rem;
        color: #cbd5e1;
    }
    .vd-header-details {
        display: flex; flex-wrap: wrap; gap: 0.75rem;
        font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;
    }
    .vd-header-details > span { display: inline-flex; align-items: center; gap: 0.25rem; }
    .vd-plate {
        display: inline-block !important;
        padding: 0.2rem 0.65rem !important;
        background: linear-gradient(135deg, var(--elite-blue-600), var(--elite-blue-500)) !important;
        color: #fff !important;
        font-family: 'SF Mono', 'Fira Code', ui-monospace, monospace !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.08em !important;
        border-radius: 0.4rem !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }
    .vd-status-area {
        display: flex; flex-direction: column; align-items: flex-end;
        gap: 0.35rem; min-width: 140px; flex-shrink: 0;
    }
    .vd-status-label {
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.08em; color: #64748b;
    }
    .vd-status-badge {
        padding: 0.3rem 0.85rem; border-radius: 9999px;
        font-size: 0.75rem; font-weight: 700; letter-spacing: 0.02em;
        display: inline-block; text-align: center;
    }

    /* TABS */
    .vd-tabs {
        display: flex; gap: 0; flex-wrap: wrap;
        border-bottom: 1px solid rgba(59, 130, 246, 0.1);
        padding: 0 0.25rem;
    }
    .vd-tab {
        padding: 0.75rem 1.15rem;
        background: transparent; border: none;
        color: #94a3b8;
        font-size: 0.8125rem; font-weight: 600;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
        transition: color 0.15s, border-color 0.15s, background 0.15s;
        display: inline-flex; align-items: center; gap: 0.4rem;
    }
    .vd-tab:hover { color: #e2e8f0; background: rgba(59, 130, 246, 0.04); }
    .vd-tab-active {
        color: var(--elite-blue-400) !important;
        border-bottom: 2px solid var(--elite-blue-500) !important;
        background: rgba(59, 130, 246, 0.06) !important;
    }
    .vd-tab-badge {
        padding: 0.1rem 0.45rem;
        border-radius: 9999px;
        font-size: 0.65rem; font-weight: 700;
        line-height: 1.4;
    }

    /* Info rows dentro de seções */
    .vd-section-info { display: flex; flex-direction: column; gap: 0.5rem; padding: 0.25rem 0; }
    .vd-section-info > div {
        display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
        font-size: 0.8125rem;
    }
    .vd-lbl { color: #94a3b8; font-weight: 600; font-size: 0.75rem; }
    .vd-val { color: #e2e8f0; font-weight: 500; }
    .vd-val-green { color: #34d399; font-weight: 700; }
    .vd-empty { color: #64748b; font-size: 0.8125rem; text-align: center; margin: 0; }

    /* Ajuste de espaçamento dos cards KPI dentro de vd-grid */
    .vd-grid .fi-wi-stats-overview-stat {
        margin: 0 !important;
        padding: 1.1rem 1.25rem !important;
    }

    /* Tabelas (histórico contratos, reservas, serviços, financeiro) */
    .vd-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.8125rem;
    }
    .vd-table thead th {
        padding: 0.65rem 0.85rem;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        background: rgba(59, 130, 246, 0.04);
        border-bottom: 1px solid rgba(59, 130, 246, 0.12);
        white-space: nowrap;
    }
    .vd-table tbody td {
        padding: 0.75rem 0.85rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        color: #e2e8f0;
        vertical-align: middle;
    }
    .vd-table tbody tr:hover td {
        background: rgba(59, 130, 246, 0.04);
    }
    .vd-table tbody tr:last-child td {
        border-bottom: none;
    }
    .vd-table td.vd-empty,
    .vd-table .vd-empty {
        padding: 1.5rem 0.85rem;
        text-align: center;
        color: #64748b;
    }

    /* Badges/pills genéricos dentro do dashboard do veículo */
    .vd-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        line-height: 1.4;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .vd-header { flex-direction: column; align-items: stretch; }
        .vd-header-photo, .vd-header-placeholder { width: 100%; height: 180px; }
        .vd-status-area { align-items: flex-start; }
    }

    /* ==========================================
       LOGIN PAGE
       ========================================== */
    .fi-simple-layout {
        background: linear-gradient(135deg, var(--elite-navy-900) 0%, #0c1929 50%, var(--elite-navy-800) 100%) !important;
        min-height: 100vh;
    }

    .fi-simple-main {
        background: rgba(15, 23, 42, 0.7) !important;
        border: 1px solid rgba(59, 130, 246, 0.1) !important;
        border-radius: 1.25rem !important;
        backdrop-filter: blur(20px) !important;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4), 0 0 80px rgba(37, 99, 235, 0.05) !important;
    }
</style>
