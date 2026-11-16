<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gauge Hub - Sistem Peminjaman Alat Ukur Presisi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 64px;
            
            /* Industrial Precision Palette (Slate & Steel Blue) */
            --primary: #1e3a5f;          /* Deep Steel Blue */
            --primary-hover: #152943;    /* Deep Steel Blue Hover */
            --primary-light: #2b4c7e;
            --primary-subtle: #f0f4f9;
            --primary-border: #cbd5e1;
            
            --accent: #b45309;           /* Metrology Amber / Copper Accent */
            --accent-subtle: #fef3c7;
            
            --sidebar-bg: #0f172a;       /* Slate 900 Shell */
            --sidebar-border: #1e293b;   /* Slate 800 */
            --sidebar-text: #94a3b8;     /* Slate 400 */
            --sidebar-text-hover: #f1f5f9;
            --sidebar-active-bg: #1e293b;
            --sidebar-active-indicator: #38bdf8;
            
            --body-bg: #f8fafc;          /* Slate 50 */
            --card-bg: #ffffff;
            --text-primary: #0f172a;     /* Slate 900 - High Contrast WCAG AAA */
            --text-secondary: #475569;   /* Slate 600 - WCAG AA compliant */
            --text-muted: #64748b;
            --border-color: #e2e8f0;     /* Slate 200 */
            --border-dark: #cbd5e1;      /* Slate 300 */
            
            /* Functional Status Palette (Non-rainbow, purpose-driven) */
            --status-ready: #059669;
            --status-ready-bg: #ecfdf5;
            --status-ready-border: #a7f3d0;
            
            --status-borrowed: #d97706;
            --status-borrowed-bg: #fffbeb;
            --status-borrowed-border: #fde68a;
            
            --status-queue: #0284c7;
            --status-queue-bg: #f0f9ff;
            --status-queue-border: #bae6fd;
            
            --status-repair: #4f46e5;
            --status-repair-bg: #eef2ff;
            --status-repair-border: #c7d2fe;
            
            --status-danger: #dc2626;
            --status-danger-bg: #fef2f2;
            --status-danger-border: #fecaca;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-primary);
            min-height: 100vh;
            margin: 0;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Focus visibility for keyboard navigation (R-32) */
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* ===================== SIDEBAR ===================== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 18px 20px 16px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .sidebar-brand .brand-logo {
            width: 38px;
            height: 38px;
            background: var(--primary);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            color: #ffffff;
        }

        .sidebar-brand .brand-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #f8fafc;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.7rem;
            color: var(--sidebar-text);
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.12) transparent;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

        .nav-section-label {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            padding: 12px 10px 6px;
            margin-top: 4px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--sidebar-text);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            margin-bottom: 2px;
            text-decoration: none;
            position: relative;
            min-height: 40px;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--sidebar-text-hover);
        }

        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active-bg);
            color: #ffffff;
            font-weight: 600;
            border-left: 3px solid var(--sidebar-active-indicator);
            padding-left: 9px;
        }

        .sidebar-nav .nav-link .nav-icon {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            color: currentColor;
        }

        .sidebar-nav .nav-link .nav-badge {
            margin-left: auto;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
        }

        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid var(--sidebar-border);
            background: #0b1120;
            flex-shrink: 0;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--primary);
            border: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #ffffff;
            flex-shrink: 0;
        }

        .sidebar-user-info .user-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #f1f5f9;
            line-height: 1.25;
        }

        .sidebar-user-info .user-role {
            font-size: 0.72rem;
            color: var(--sidebar-text);
        }

        /* ===================== TOPBAR ===================== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 1030;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sidebar-toggle {
            display: none;
            background: transparent;
            border: 1px solid var(--border-color);
            font-size: 1.25rem;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.15s;
            min-height: 40px;
            min-width: 40px;
        }

        .sidebar-toggle:hover {
            background: var(--body-bg);
            color: var(--text-primary);
            border-color: var(--border-dark);
        }

        .topbar-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            color: var(--text-primary);
            min-height: 40px;
        }

        .topbar-user-btn:hover {
            border-color: var(--primary);
            background: var(--primary-subtle);
            color: var(--primary);
        }

        .topbar-avatar {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #ffffff;
        }

        .topbar-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            position: relative;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            padding: 0;
        }

        .topbar-icon-btn:hover {
            border-color: var(--primary);
            background: var(--primary-subtle);
            color: var(--primary);
        }

        .topbar-badge-pulse {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 9px;
            font-size: 0.65rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            color: #ffffff;
            line-height: 1;
        }

        .badge-pulse-amber {
            background-color: #d97706;
        }

        .badge-pulse-danger {
            background-color: #dc2626;
        }

        /* ===================== MAIN CONTENT ===================== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            padding: 24px 28px 28px;
            flex: 1;
        }

        /* ===================== CARDS ===================== */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        /* Purposeful Metric Cards (No rainbow gradients, no giant floating background icons) */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-top: 3px solid var(--primary);
            border-radius: 10px;
            padding: 20px;
            position: relative;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            height: 100%;
        }

        .stat-card:hover {
            border-color: var(--border-dark);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        .stat-card-primary { border-top-color: var(--primary); }
        .stat-card-info    { border-top-color: var(--status-queue); }
        .stat-card-success { border-top-color: var(--status-ready); }
        .stat-card-warning { border-top-color: var(--status-borrowed); }
        .stat-card-danger  { border-top-color: var(--status-danger); }
        .stat-card-purple  { border-top-color: var(--status-repair); }

        .stat-card .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 14px;
        }

        .stat-card-primary .stat-icon { background: var(--primary-subtle); color: var(--primary); }
        .stat-card-info .stat-icon    { background: var(--status-queue-bg); color: var(--status-queue); }
        .stat-card-success .stat-icon { background: var(--status-ready-bg); color: var(--status-ready); }
        .stat-card-warning .stat-icon { background: var(--status-borrowed-bg); color: var(--status-borrowed); }
        .stat-card-danger .stat-icon  { background: var(--status-danger-bg); color: var(--status-danger); }
        .stat-card-purple .stat-icon  { background: var(--status-repair-bg); color: var(--status-repair); }

        .stat-card .stat-value {
            font-size: 1.875rem;
            font-weight: 700;
            line-height: 1.2;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .stat-card .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card .stat-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* Background icons hidden as required by anti-slop R-04 / R-14 */
        .stat-card .stat-bg-icon {
            display: none !important;
        }

        /* ===================== TABLES ===================== */
        .table-modern {
            font-size: 0.875rem;
            width: 100%;
            margin-bottom: 0;
        }

        .table-modern thead th {
            background: #f8fafc;
            color: var(--text-secondary);
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid var(--border-color);
            padding: 12px 16px;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .table-modern tbody tr:hover td {
            background-color: #f8fafc;
        }

        .table-modern tbody tr:last-child td { border-bottom: none; }

        /* ===================== BADGES ===================== */
        .badge-status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            line-height: 1.3;
        }

        .badge-dipinjam {
            background: var(--status-borrowed-bg);
            color: var(--status-borrowed);
            border: 1px solid var(--status-borrowed-border);
        }
        .badge-menunggu {
            background: var(--status-queue-bg);
            color: var(--status-queue);
            border: 1px solid var(--status-queue-border);
        }
        .badge-selesai {
            background: var(--status-ready-bg);
            color: var(--status-ready);
            border: 1px solid var(--status-ready-border);
        }
        .badge-rusak {
            background: var(--status-danger-bg);
            color: var(--status-danger);
            border: 1px solid var(--status-danger-border);
        }
        .badge-perbaikan {
            background: var(--status-repair-bg);
            color: var(--status-repair);
            border: 1px solid var(--status-repair-border);
        }
        .badge-konfirmasi {
            background: var(--status-danger-bg);
            color: var(--status-danger);
            border: 1px solid var(--status-danger);
            font-weight: 700;
        }
        .badge-dikonfirmasi {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid var(--border-dark);
        }
        .badge-user {
            background: var(--primary-subtle);
            color: var(--primary);
            border: 1px solid var(--primary-border);
        }
        .badge-admin {
            background: var(--accent-subtle);
            color: var(--accent);
            border: 1px solid #fde68a;
        }
        .badge-teknisi {
            background: var(--status-ready-bg);
            color: var(--status-ready);
            border: 1px solid var(--status-ready-border);
        }

        /* ===================== BUTTONS ===================== */
        .btn-primary-modern {
            background: var(--primary);
            border: 1px solid var(--primary);
            color: #ffffff !important;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: background 0.15s ease, border-color 0.15s ease;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-primary-modern:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #ffffff !important;
        }

        .btn-ghost {
            background: #ffffff;
            border: 1px solid var(--border-dark);
            color: var(--text-secondary) !important;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-ghost:hover {
            background: var(--body-bg);
            border-color: #94a3b8;
            color: var(--text-primary) !important;
        }

        /* ===================== ALERTS ===================== */
        .alert-modern {
            border: 1px solid var(--border-color);
            border-left-width: 4px;
            border-radius: 8px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            background: #ffffff;
        }

        .alert-modern-success {
            border-left-color: var(--status-ready);
            background: var(--status-ready-bg);
            color: #064e3b;
        }
        .alert-modern-danger {
            border-left-color: var(--status-danger);
            background: var(--status-danger-bg);
            color: #7f1d1d;
        }
        .alert-modern-warning {
            border-left-color: var(--status-borrowed);
            background: var(--status-borrowed-bg);
            color: #78350f;
        }

        .alert-icon {
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        /* ===================== PAGE HEADER ===================== */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin: 0;
        }

        /* ===================== FOOTER ===================== */
        .main-footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 14px 28px;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* ===================== FORM CONTROLS ===================== */
        .form-control-modern, .form-select-modern {
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            padding: 9px 13px;
            font-size: 0.875rem;
            color: var(--text-primary);
            background: #ffffff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control-modern:focus, .form-select-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.15);
            outline: none;
        }

        /* ===================== EMPTY STATE ===================== */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
        }

        .empty-state-icon {
            width: 60px;
            height: 60px;
            background: var(--body-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: var(--text-secondary);
            margin: 0 auto 16px;
        }

        /* ===================== PAGINATION ===================== */
        nav svg,
        .pagination svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 1rem !important;
            max-height: 1rem !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
        .pagination {
            margin-bottom: 0;
            gap: 4px;
            display: flex;
            align-items: center;
        }
        .page-link {
            border-radius: 6px !important;
            border-color: var(--border-color);
            color: var(--text-primary);
            font-size: 0.85rem;
            font-weight: 500;
            padding: 6px 12px;
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }
        .page-link:hover {
            background-color: var(--primary-subtle);
            color: var(--primary);
            border-color: var(--primary-border);
        }
        .page-item.active .page-link {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #ffffff !important;
        }
        .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: var(--border-color);
        }

        /* ===================== OVERLAY ===================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 1039;
        }

        /* ===================== RESPONSIVE (antislop-layoutmobile) ===================== */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .topbar {
                left: 0;
                padding: 0 16px;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .main-content {
                padding: 20px 16px;
            }

            .sidebar-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }

        /* ===================== INTERACTIVE ANIMATIONS & MICRO-INTERACTIONS ===================== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
            }
            50% {
                transform: scale(1.04);
                box-shadow: 0 0 0 6px rgba(220, 38, 38, 0);
            }
        }

        @keyframes pulseAmber {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4);
            }
            50% {
                transform: scale(1.04);
                box-shadow: 0 0 0 6px rgba(217, 119, 6, 0);
            }
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Page entrance */
        .main-content {
            animation: fadeInSimple 0.25s ease-out;
        }

        @keyframes fadeInSimple {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Stat card interactive hover */
        .stat-card {
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease !important;
            will-change: transform, box-shadow;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.08), 0 4px 8px -2px rgba(15, 23, 42, 0.04) !important;
            border-color: var(--border-dark) !important;
        }

        .stat-card .stat-icon {
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.14) rotate(-3deg);
        }

        /* General card interactive hover */
        .card {
            transition: box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
        }

        /* Buttons micro-interaction & tactile press */
        .btn-primary-modern, .btn-ghost, .btn {
            transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, color 0.18s ease !important;
            position: relative;
            overflow: hidden;
        }

        .btn-primary-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(30, 58, 95, 0.24);
        }

        .btn-primary-modern:active, .btn-ghost:active, .btn:active {
            transform: scale(0.97) translateY(0) !important;
        }

        .btn-ghost:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.08);
        }

        /* Ripple effect circle */
        .btn-ripple {
            position: absolute;
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.55s linear;
            background-color: rgba(255, 255, 255, 0.38);
            pointer-events: none;
        }

        .btn-ghost .btn-ripple, .btn-outline-primary .btn-ripple, .btn-outline-secondary .btn-ripple {
            background-color: rgba(30, 58, 95, 0.18);
        }

        /* Table rows interactive highlight */
        .table-modern tbody tr {
            transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.15s ease, box-shadow 0.15s ease;
        }

        .table-modern tbody tr:hover {
            background-color: #f1f5f9 !important;
            box-shadow: inset 3px 0 0 var(--primary);
        }

        /* Interactive Sidebar Nav links */
        .sidebar-nav .nav-link {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .sidebar-nav .nav-link .nav-icon {
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }

        .sidebar-nav .nav-link:hover .nav-icon {
            transform: translateX(3px) scale(1.15);
        }

        .sidebar-nav .nav-link.active .nav-icon {
            transform: scale(1.1);
        }

        /* Modal Smooth Entrance & Clean Non-Blur Backdrop */
        .modal {
            z-index: 1060 !important;
        }

        .modal.fade .modal-dialog {
            transform: scale(0.96) translateY(-10px);
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.18s ease;
        }

        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }

        .modal-backdrop.show {
            background-color: rgba(15, 23, 42, 0.55) !important;
            opacity: 1 !important;
        }

        /* Pulse animation on badges */
        .badge-pulse-danger, .badge.bg-danger {
            animation: pulseGlow 2.5s infinite ease-in-out;
        }

        .badge-pulse-amber {
            animation: pulseAmber 2.5s infinite ease-in-out;
        }

        /* Form input smooth focus */
        .form-control-modern, .form-select-modern, .form-control, .form-select {
            transition: border-color 0.2s ease, box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s ease !important;
        }

        .form-control-modern:focus, .form-select-modern:focus, .form-control:focus, .form-select:focus {
            transform: translateY(-1px);
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.16), 0 2px 4px rgba(15, 23, 42, 0.04) !important;
        }

        /* Accessibility: Respect Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* ===================== PRINT ===================== */
        @media print {
            .sidebar, .topbar, .main-footer, .btn, .no-print, form.no-print { display: none !important; }
            .main-wrapper { margin: 0; padding: 0; }
            .main-content { padding: 0; }
            body { background: white; }
        }
    </style>
    @stack('styles')
</head>

<body>
    {{-- Sidebar Overlay (mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    @auth
    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="sidebar" id="sidebar">
        {{-- Brand --}}
        <div class="sidebar-brand">
            @php
                $brandRoute = auth()->user()->role === 'admin' ? route('admin.dashboard')
                    : (auth()->user()->role === 'teknisi' ? route('teknisi.dashboard') : route('user.dashboard'));
            @endphp
            <a href="{{ $brandRoute }}" class="d-flex align-items-center gap-3 text-decoration-none">
                <div class="brand-logo">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div>
                    <div class="brand-name">Gauge<span style="color: #f59e0b;">Hub</span></div>
                    <div class="brand-sub">Precision Tools System</div>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            @if(auth()->user()->role === 'user')
                <div class="nav-section-label">Menu Utama</div>
                @php
                    $sidebarUserActionCount = \App\Models\Peminjaman::where('user_id', auth()->id())
                        ->where('status', 'menunggu_konfirmasi')
                        ->whereHas('pengembalian', fn($q) => $q->where('konfirmasi_user', false))
                        ->count();
                @endphp
                <a href="{{ route('user.dashboard') }}"
                   class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2 nav-icon"></i>
                    <span>Dashboard</span>
                    @if($sidebarUserActionCount > 0)
                        <span class="nav-badge bg-danger text-white" title="Butuh konfirmasi kerusakan">{{ $sidebarUserActionCount }}</span>
                    @endif
                </a>
                <a href="{{ route('user.pinjam.create') }}"
                   class="nav-link {{ request()->routeIs('user.pinjam.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle nav-icon"></i>
                    <span>Pinjam Alat Baru</span>
                </a>

                <div class="nav-section-label" style="margin-top: 8px;">Akun</div>
                <a href="{{ route('user.profile') }}"
                   class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                    <i class="bi bi-person-gear nav-icon"></i>
                    <span>Profil & PIC</span>
                </a>

            @elseif(auth()->user()->role === 'teknisi')
                <div class="nav-section-label">Menu Utama</div>
                <a href="{{ route('teknisi.dashboard') }}"
                   class="nav-link {{ request()->routeIs('teknisi.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2 nav-icon"></i>
                    <span>Dashboard</span>
                </a>
                @php
                    $sidebarTeknisiCount = \App\Models\Peminjaman::whereIn('status', ['menunggu_cek_teknisi', 'dalam_perbaikan'])->count();
                @endphp
                <a href="{{ route('teknisi.antrian') }}"
                   class="nav-link {{ request()->routeIs('teknisi.antrian') || request()->routeIs('teknisi.cek') ? 'active' : '' }}">
                    <i class="bi bi-inbox-fill nav-icon"></i>
                    <span>Antrian & Repair</span>
                    @if($sidebarTeknisiCount > 0)
                        <span class="nav-badge bg-warning text-dark">{{ $sidebarTeknisiCount }}</span>
                    @endif
                </a>
                <a href="{{ route('teknisi.history') }}"
                   class="nav-link {{ request()->routeIs('teknisi.history') ? 'active' : '' }}">
                    <i class="bi bi-clock-history nav-icon"></i>
                    <span>Riwayat Inspeksi</span>
                </a>

            @elseif(auth()->user()->role === 'admin')
                <div class="nav-section-label">Menu Utama</div>
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2 nav-icon"></i>
                    <span>Dashboard</span>
                </a>

                <div class="nav-section-label" style="margin-top: 8px;">Inventaris</div>
                <a href="{{ route('admin.tools.index') }}"
                   class="nav-link {{ request()->routeIs('admin.tools.*') ? 'active' : '' }}">
                    <i class="bi bi-tools nav-icon"></i>
                    <span>Stok Alat Ukur</span>
                </a>
                @php
                    $sidebarPendingConfCount = \App\Models\Peminjaman::where('status', 'menunggu_konfirmasi')->count();
                @endphp
                <a href="{{ route('admin.peminjaman.index') }}"
                   class="nav-link {{ request()->routeIs('admin.peminjaman.*') ? 'active' : '' }}">
                    <i class="bi bi-card-checklist nav-icon"></i>
                    <span>Monitoring Peminjaman</span>
                    @if($sidebarPendingConfCount > 0)
                        <span class="nav-badge bg-danger text-white" title="Menunggu konfirmasi kerusakan">{{ $sidebarPendingConfCount }}</span>
                    @endif
                </a>

                <div class="nav-section-label" style="margin-top: 8px;">Manajemen</div>
                <a href="{{ route('admin.users.index') }}"
                   class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people nav-icon"></i>
                    <span>Pengguna & Hak Akses</span>
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph nav-icon"></i>
                    <span>Laporan Bulanan</span>
                </a>
            @endif
        </nav>

        {{-- Sidebar Footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="sidebar-user-info" style="min-width: 0;">
                    <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="user-role">
                        @if(auth()->user()->role === 'admin')
                            <i class="bi bi-shield-fill-check me-1" style="color: #f59e0b;"></i>Administrator
                        @elseif(auth()->user()->role === 'teknisi')
                            <i class="bi bi-wrench-adjustable me-1" style="color: #10b981;"></i>Teknisi
                        @else
                            <i class="bi bi-person-fill me-1" style="color: #6366f1;"></i>User
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </aside>
    @endauth

    {{-- ===================== MAIN WRAPPER ===================== --}}
    <div class="{{ auth()->check() ? 'main-wrapper' : '' }}">

        @auth
        {{-- ===================== TOPBAR ===================== --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title no-print">
                    @yield('page_title', 'Gauge Hub')
                </div>
            </div>

            <div class="topbar-right d-flex align-items-center gap-2">
                @if(auth()->user()->role === 'user')
                    @php
                        $navUserLoans = \App\Models\Peminjaman::where('user_id', auth()->id())
                            ->where('status', 'dipinjam')
                            ->with(['details.tool'])
                            ->get();
                        $navDueToday = $navUserLoans->filter(fn($item) => $item->isJatuhTempoHariIni());
                        $navOverdue = $navUserLoans->filter(fn($item) => $item->isTerlambat());
                        $navReminderCount = $navDueToday->count() + $navOverdue->count();
                    @endphp

                    {{-- Notification Bell Dropdown --}}
                    <div class="dropdown">
                        <button class="topbar-icon-btn border-0 dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false" title="Pengingat Pengembalian Alat">
                            <i class="bi bi-bell-fill" style="font-size: 1rem;"></i>
                            @if($navReminderCount > 0)
                                <span class="topbar-badge-pulse {{ $navOverdue->isNotEmpty() ? 'badge-pulse-danger' : 'badge-pulse-amber' }}">
                                    {{ $navReminderCount }}
                                </span>
                            @endif
                        </button>

                        <div class="dropdown-menu dropdown-menu-end shadow border p-0 mt-2" style="border-radius: 10px; width: 330px; max-width: 90vw; border-color: var(--border-color);">
                            {{-- Dropdown Header --}}
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                                <div>
                                    <div class="fw-bold" style="font-size: 0.85rem; color: var(--text-primary);">
                                        <i class="bi bi-clock-history me-1 text-primary"></i>Pengingat Pengembalian
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        {{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}
                                    </div>
                                </div>
                                @if($navReminderCount > 0)
                                    <span class="badge {{ $navOverdue->isNotEmpty() ? 'bg-danger' : 'bg-warning text-dark' }}" style="font-size: 0.68rem; font-weight: 600;">
                                        {{ $navReminderCount }} Tindakan
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">
                                        Jadwal Aman
                                    </span>
                                @endif
                            </div>

                            {{-- Dropdown Body --}}
                            <div style="max-height: 320px; overflow-y: auto;">
                                @if($navReminderCount === 0)
                                    <div class="p-4 text-center text-muted" style="font-size: 0.82rem;">
                                        <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                                        <strong>Semua Jadwal Aman</strong>
                                        <div class="mt-1 small text-secondary">Tidak ada alat ukur yang jatuh tempo hari ini atau terlambat dikembalikan.</div>
                                    </div>
                                @else
                                    @if($navOverdue->isNotEmpty())
                                        <div class="px-3 pt-2 pb-1 bg-danger-subtle text-danger fw-bold border-bottom" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>Terlambat Pengembalian ({{ $navOverdue->count() }})
                                        </div>
                                        @foreach($navOverdue as $no)
                                            <div class="p-3 border-bottom bg-white">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold text-danger font-monospace" style="font-size: 0.82rem;">
                                                        #{{ str_pad($no->id, 5, '0', STR_PAD_LEFT) }}
                                                    </span>
                                                    <span class="badge bg-danger text-white" style="font-size: 0.65rem;">
                                                        Terlambat {{ abs($no->sisaHariPinjam()) }} Hari
                                                    </span>
                                                </div>
                                                <div class="text-secondary small mb-2" style="font-size: 0.75rem; line-height: 1.4;">
                                                    @foreach($no->details as $d)
                                                        <div>&bull; {{ $d->tool->nama_alat ?? 'Alat' }} ({{ $d->qty }} pcs)</div>
                                                    @endforeach
                                                </div>
                                                <a href="{{ route('user.dashboard') }}#reminder-panel" class="btn btn-sm btn-danger py-1 px-2 fw-semibold w-100 text-center" style="font-size: 0.75rem; border-radius: 6px;">
                                                    <i class="bi bi-box-arrow-in-down me-1"></i>Kumpulkan & Kembalikan
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($navDueToday->isNotEmpty())
                                        <div class="px-3 pt-2 pb-1 bg-warning-subtle text-warning-emphasis fw-bold border-bottom" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                            <i class="bi bi-alarm-fill me-1 text-warning"></i>Jatuh Tempo Hari Ini ({{ $navDueToday->count() }})
                                        </div>
                                        @foreach($navDueToday as $nt)
                                            <div class="p-3 border-bottom bg-white">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold text-dark font-monospace" style="font-size: 0.82rem;">
                                                        #{{ str_pad($nt->id, 5, '0', STR_PAD_LEFT) }}
                                                    </span>
                                                    <span class="badge bg-warning text-dark border border-warning" style="font-size: 0.65rem;">
                                                        Hari Ini s/d 16:30 WIB
                                                    </span>
                                                </div>
                                                <div class="text-secondary small mb-2" style="font-size: 0.75rem; line-height: 1.4;">
                                                    @foreach($nt->details as $d)
                                                        <div>&bull; {{ $d->tool->nama_alat ?? 'Alat' }} ({{ $d->qty }} pcs)</div>
                                                    @endforeach
                                                </div>
                                                <a href="{{ route('user.dashboard') }}#reminder-panel" class="btn btn-sm btn-primary-modern py-1 px-2 fw-semibold w-100 text-center" style="font-size: 0.75rem; border-radius: 6px;">
                                                    <i class="bi bi-list-check me-1"></i>Buka Tangga Pengingat
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            </div>

                            {{-- Dropdown Footer --}}
                            <div class="p-2 bg-light text-center border-top">
                                <a href="{{ route('user.dashboard') }}" class="text-decoration-none text-primary fw-semibold" style="font-size: 0.75rem;">
                                    Buka Dashboard Peminjaman &raquo;
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Logout quick / Profile dropdown --}}
                <div class="dropdown">
                    <button class="topbar-user-btn dropdown-toggle border-0 bg-transparent" data-bs-toggle="dropdown" type="button">
                        <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                        <span class="d-none d-sm-inline text-sm fw-medium" style="font-size: 0.85rem;">
                            {{ Str::words(auth()->user()->name, 1, '') }}
                        </span>
                        <i class="bi bi-chevron-down" style="font-size: 0.65rem; color: var(--text-secondary);"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; min-width: 220px; padding: 8px;">
                        <li>
                            <div class="px-3 py-2">
                                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">{{ auth()->user()->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ auth()->user()->email }}</div>
                                <div class="mt-1">
                                    @if(auth()->user()->role === 'admin')
                                        <span class="badge-status badge-admin">Administrator</span>
                                    @elseif(auth()->user()->role === 'teknisi')
                                        <span class="badge-status badge-teknisi">Teknisi</span>
                                    @else
                                        <span class="badge-status badge-user">User</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item rounded-2 py-2" href="{{ route('user.profile') }}" style="font-size: 0.875rem;">
                                <i class="bi bi-person-gear me-2 text-primary"></i>Edit Profil & PIC
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger" style="font-size: 0.875rem;">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        @endauth

        {{-- ===================== CONTENT ===================== --}}
        <main class="{{ auth()->check() ? 'main-content' : '' }}">
            {{-- Flash Alerts --}}
            @if(session('success'))
                <div class="alert-modern alert-modern-success mb-4 no-print">
                    <i class="bi bi-check-circle-fill alert-icon" style="color: #16a34a;"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="ms-auto btn btn-sm p-0" data-bs-dismiss="alert" style="color: inherit; opacity: 0.6;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-modern alert-modern-danger mb-4 no-print">
                    <i class="bi bi-exclamation-triangle-fill alert-icon" style="color: #dc2626;"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="ms-auto btn btn-sm p-0" data-bs-dismiss="alert" style="color: inherit; opacity: 0.6;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-modern alert-modern-warning mb-4 no-print">
                    <i class="bi bi-exclamation-circle-fill alert-icon" style="color: #d97706;"></i>
                    <div>
                        <div class="fw-semibold mb-1">Terdapat kesalahan pada input Anda:</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        @auth
        {{-- Footer --}}
        <footer class="main-footer d-flex justify-content-between align-items-center no-print flex-wrap gap-2">
            <div>&copy; {{ date('Y') }} <strong>Gauge Hub</strong> : Sistem Peminjaman Alat Ukur Presisi</div>
            <div class="text-secondary small">Dial Gauge &bull; Bore Gauge &bull; Micrometer &bull; Vernier Caliper</div>
        </footer>
        @endauth
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.alert-modern');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-8px)';
                    alert.style.transition = 'all 0.3s ease';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });

            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // 1. Interactive Animated Counter for Stat Values
            if (!prefersReducedMotion) {
                const statValues = document.querySelectorAll('.stat-value');
                statValues.forEach(el => {
                    const rawText = el.textContent.trim();
                    const target = parseInt(rawText, 10);
                    if (!isNaN(target) && target >= 0 && rawText === target.toString()) {
                        el.textContent = '0';
                        const duration = Math.min(800, Math.max(400, target * 20));
                        const startTime = performance.now();

                        function updateCount(currentTime) {
                            const elapsed = currentTime - startTime;
                            const progress = Math.min(elapsed / duration, 1);
                            // Ease-out cubic
                            const easeOut = 1 - Math.pow(1 - progress, 3);
                            const current = Math.floor(easeOut * target);
                            el.textContent = current;

                            if (progress < 1) {
                                requestAnimationFrame(updateCount);
                            } else {
                                el.textContent = target;
                            }
                        }

                        requestAnimationFrame(updateCount);
                    }
                });
            }

            // 2. Interactive Click Ripple Effect for Buttons
            document.addEventListener('click', function (e) {
                const targetBtn = e.target.closest('.btn-primary-modern, .btn-ghost, .btn:not(.btn-link)');
                if (!targetBtn) return;

                const rect = targetBtn.getBoundingClientRect();
                const circle = document.createElement('span');
                const diameter = Math.max(rect.width, rect.height);
                const radius = diameter / 2;

                circle.style.width = circle.style.height = `${diameter}px`;
                circle.style.left = `${e.clientX - rect.left - radius}px`;
                circle.style.top = `${e.clientY - rect.top - radius}px`;
                circle.classList.add('btn-ripple');

                const existingRipple = targetBtn.querySelector('.btn-ripple');
                if (existingRipple) {
                    existingRipple.remove();
                }

                targetBtn.appendChild(circle);
                setTimeout(() => circle.remove(), 600);
            });

            // 3. Pindahkan semua .modal ke document.body agar tidak terperangkap stacking context container
            document.querySelectorAll('.modal').forEach(function (modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>