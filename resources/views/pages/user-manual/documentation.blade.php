<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manual - MEEDO Water Billing System</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ================================================================
           BASE & RESET
           ================================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-width: 300px;
            --sidebar-bg: #1a1f2e;
            --sidebar-hover: #252b3d;
            --sidebar-active: #2563eb;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --sidebar-border: #2d3548;
            --sidebar-header-bg: #141825;
            --content-bg: #f8f9fc;
            --content-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent: #2563eb;
            --accent-light: #dbeafe;
            --border-color: #e2e8f0;
            --code-bg: #f1f5f9;
            --success: #16a34a;
            --warning: #f59e0b;
            --info: #0ea5e9;
        }

        html { font-size: 16px; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--content-bg);
            color: var(--text-primary);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ================================================================
           LAYOUT
           ================================================================ */
        .doc-layout {
            display: flex;
            min-height: 100vh;
        }

        /* ================================================================
           SIDEBAR
           ================================================================ */
        .doc-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .doc-sidebar-header {
            background: var(--sidebar-header-bg);
            padding: 20px 24px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .doc-sidebar-header .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .doc-sidebar-header .logo-area img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
        }

        .doc-sidebar-header .brand {
            display: flex;
            flex-direction: column;
        }

        .doc-sidebar-header .brand-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .doc-sidebar-header .brand-subtitle {
            font-size: 0.7rem;
            color: var(--sidebar-text);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-top: 2px;
        }

        .doc-sidebar-search {
            padding: 16px 20px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .doc-sidebar-search input {
            width: 100%;
            padding: 9px 14px 9px 36px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--sidebar-border);
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 0.8rem;
            outline: none;
            transition: all 0.2s;
        }

        .doc-sidebar-search input::placeholder { color: #64748b; }
        .doc-sidebar-search input:focus {
            border-color: var(--accent);
            background: rgba(255,255,255,0.1);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .doc-sidebar-search .search-wrapper {
            position: relative;
        }

        .doc-sidebar-search .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 0.75rem;
        }

        .doc-sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 0;
            scrollbar-width: thin;
            scrollbar-color: #374151 transparent;
        }

        .doc-sidebar-nav::-webkit-scrollbar { width: 4px; }
        .doc-sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .doc-sidebar-nav::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }

        /* Sidebar section label */
        .sidebar-section-label {
            padding: 16px 24px 6px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
        }

        /* Sidebar nav item */
        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 24px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            border-left: 3px solid transparent;
            user-select: none;
        }

        .sidebar-nav-item:hover {
            color: var(--sidebar-text-active);
            background: var(--sidebar-hover);
        }

        .sidebar-nav-item.active {
            color: var(--sidebar-text-active);
            background: linear-gradient(90deg, rgba(37,99,235,0.15), transparent);
            border-left-color: var(--accent);
        }

        .sidebar-nav-item .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 0.8rem;
            flex-shrink: 0;
            opacity: 0.7;
        }

        .sidebar-nav-item.active .nav-icon { opacity: 1; color: var(--accent); }

        .sidebar-nav-item .nav-chevron {
            margin-left: auto;
            font-size: 0.6rem;
            transition: transform 0.2s;
            opacity: 0.5;
        }

        .sidebar-nav-item .nav-chevron.open { transform: rotate(180deg); }

        /* Sidebar subnav */
        .sidebar-subnav {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease;
        }

        .sidebar-subnav.open { max-height: 1000px; }

        .sidebar-subnav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 24px 7px 60px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.15s;
            position: relative;
        }

        .sidebar-subnav-item::before {
            content: '';
            position: absolute;
            left: 40px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #475569;
            transition: background 0.15s;
        }

        .sidebar-subnav-item:hover {
            color: var(--sidebar-text-active);
            background: var(--sidebar-hover);
        }

        .sidebar-subnav-item.active {
            color: var(--accent);
            font-weight: 600;
        }

        .sidebar-subnav-item.active::before {
            background: var(--accent);
            box-shadow: 0 0 6px rgba(37, 99, 235, 0.5);
        }

        /* Sidebar footer */
        .doc-sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .doc-sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: rgba(37, 99, 235, 0.1);
            color: #93c5fd;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .doc-sidebar-footer a:hover {
            background: rgba(37, 99, 235, 0.2);
            color: #bfdbfe;
        }

        /* ================================================================
           MAIN CONTENT
           ================================================================ */
        .doc-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top bar */
        .doc-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 48px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .doc-topbar .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .doc-topbar .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.15s;
        }

        .doc-topbar .breadcrumb a:hover { color: var(--accent); }
        .doc-topbar .breadcrumb .separator { font-size: 0.6rem; }
        .doc-topbar .breadcrumb .current { color: var(--text-primary); font-weight: 600; }

        .doc-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .doc-topbar-actions .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-secondary);
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s;
        }

        .doc-topbar-actions .btn-back:hover {
            color: var(--accent);
            border-color: var(--accent);
        }

        /* Content wrapper */
        .doc-content {
            flex: 1;
            padding: 40px 48px 80px;
            max-width: 900px;
            margin: 0 auto;
            margin: 0 auto;
        }

        /* ================================================================
           TYPOGRAPHY (PDF-style documentation)
           ================================================================ */
        .doc-content h1 {
            font-family: 'Merriweather', 'Georgia', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.3;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .doc-content h2 {
            font-family: 'Merriweather', 'Georgia', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.35;
            margin-top: 48px;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            letter-spacing: -0.01em;
        }

        .doc-content h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.4;
            margin-top: 36px;
            margin-bottom: 12px;
        }

        .doc-content h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 28px;
            margin-bottom: 10px;
        }

        .doc-content p {
            font-size: 0.94rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 16px;
        }

        .doc-content .subtitle {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 32px;
        }

        /* Section separator */
        .doc-content hr {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 40px 0;
        }

        /* ================================================================
           CONTENT COMPONENTS
           ================================================================ */

        /* Step blocks */
        .doc-step {
            display: flex;
            gap: 20px;
            margin-bottom: 28px;
            padding: 24px;
            background: var(--content-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: box-shadow 0.2s;
        }

        .doc-step:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }

        .doc-step-number {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            background: var(--accent);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .doc-step-content h4 {
            margin-top: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
            text-transform: none;
            letter-spacing: 0;
        }

        .doc-step-content p {
            font-size: 0.88rem;
            margin-bottom: 0;
        }

        /* Callout / tip boxes */
        .doc-callout {
            padding: 16px 20px;
            border-radius: 10px;
            margin: 20px 0;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            font-size: 0.88rem;
            line-height: 1.7;
        }

        .doc-callout-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            margin-top: 2px;
        }

        .doc-callout.info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .doc-callout.info .doc-callout-icon {
            background: #2563eb;
            color: white;
        }

        .doc-callout.warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .doc-callout.warning .doc-callout-icon {
            background: #f59e0b;
            color: white;
        }

        .doc-callout.success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .doc-callout.success .doc-callout-icon {
            background: #16a34a;
            color: white;
        }

        /* Image placeholder */
        .doc-image-placeholder {
            background: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 48px 24px;
            text-align: center;
            color: #94a3b8;
            margin: 24px 0;
        }

        .doc-image-placeholder i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
            opacity: 0.5;
        }

        .doc-image-placeholder span {
            font-size: 0.82rem;
            font-weight: 500;
        }

        /* Video placeholder */
        .doc-video-placeholder {
            background: #0f172a;
            border-radius: 12px;
            padding: 64px 24px;
            text-align: center;
            color: #64748b;
            margin: 24px 0;
            position: relative;
        }

        .doc-video-placeholder .play-btn {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .doc-video-placeholder .play-btn i {
            font-size: 1.4rem;
            color: rgba(255,255,255,0.6);
            margin-left: 3px;
        }

        .doc-video-placeholder span {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
        }

        /* List styles */
        .doc-content ul, .doc-content ol {
            padding-left: 24px;
            margin-bottom: 16px;
        }

        .doc-content li {
            font-size: 0.92rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 6px;
        }

        /* Table of contents mini */
        .doc-toc {
            background: var(--content-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 36px;
        }

        .doc-toc-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .doc-toc a {
            display: block;
            padding: 5px 0;
            font-size: 0.86rem;
            color: var(--accent);
            text-decoration: none;
            transition: color 0.15s;
        }

        .doc-toc a:hover { color: #1d4ed8; text-decoration: underline; }

        /* Mobile toggle */
        .doc-sidebar-toggle {
            display: none;
            position: fixed;
            bottom: 24px;
            left: 24px;
            z-index: 200;
            width: 48px;
            height: 48px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(37,99,235,0.3);
            transition: transform 0.2s;
        }

        .doc-sidebar-toggle:hover { transform: scale(1.05); }

        .doc-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 90;
        }

        /* ================================================================
           RESPONSIVE
           ================================================================ */
        @media (max-width: 1024px) {
            .doc-sidebar {
                transform: translateX(-100%);
            }

            .doc-sidebar.open {
                transform: translateX(0);
            }

            .doc-main {
                margin-left: 0;
            }

            .doc-content {
                padding: 24px 20px 60px;
            }

            .doc-topbar {
                padding: 0 20px;
            }

            .doc-sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .doc-sidebar-overlay.open {
                display: block;
            }
        }

        @media (max-width: 640px) {
            .doc-content h1 { font-size: 1.5rem; }
            .doc-content h2 { font-size: 1.25rem; }
        }

        /* Print styles */
        @media print {
            .doc-sidebar, .doc-topbar, .doc-sidebar-toggle { display: none !important; }
            .doc-main { margin-left: 0 !important; }
            .doc-content { max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>

<div class="doc-layout" id="docApp">

    <!-- Mobile Overlay -->
    <div class="doc-sidebar-overlay" id="sidebarOverlay" onclick="toggleDocSidebar()"></div>

    <!-- ================================================================
         SIDEBAR
         ================================================================ -->
    <aside class="doc-sidebar" id="docSidebar">

        <!-- Header -->
        <div class="doc-sidebar-header">
            <a href="{{ route('dashboard') }}" class="logo-area">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <div class="brand">
                    <span class="brand-title">MEEDO</span>
                    <span class="brand-subtitle">User Manual</span>
                </div>
            </a>
        </div>

        <!-- Search -->
        <div class="doc-sidebar-search">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search documentation..." id="docSearch" onkeyup="filterSidebar(this.value)">
            </div>
        </div>

        <!-- Navigation -->
        <nav class="doc-sidebar-nav" id="sidebarNav">

            <div class="sidebar-section-label">Getting Started</div>

            <a href="#introduction" class="sidebar-nav-item active" onclick="navigateTo(event, 'introduction')">
                <span class="nav-icon"><i class="fas fa-home"></i></span>
                Introduction
            </a>
            <a href="#system-requirements" class="sidebar-nav-item" onclick="navigateTo(event, 'system-requirements')">
                <span class="nav-icon"><i class="fas fa-server"></i></span>
                System Requirements
            </a>
            <a href="#logging-in" class="sidebar-nav-item" onclick="navigateTo(event, 'logging-in')">
                <span class="nav-icon"><i class="fas fa-sign-in-alt"></i></span>
                Logging In
            </a>
            <a href="#dashboard-overview" class="sidebar-nav-item" onclick="navigateTo(event, 'dashboard-overview')">
                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                Dashboard Overview
            </a>

            <div class="sidebar-section-label">Core Modules</div>

            <!-- User Management -->
            <div class="sidebar-nav-group" data-group="user-management">
                <div class="sidebar-nav-item" onclick="toggleDocSubmenu('user-management')">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    User Management
                    <i class="fas fa-chevron-down nav-chevron" id="chevron-user-management"></i>
                </div>
                <div class="sidebar-subnav" id="subnav-user-management">
                    <a href="#user-add" class="sidebar-subnav-item" onclick="navigateTo(event, 'user-add')">Adding Users</a>
                    <a href="#user-list" class="sidebar-subnav-item" onclick="navigateTo(event, 'user-list')">User List</a>
                    <a href="#user-roles" class="sidebar-subnav-item" onclick="navigateTo(event, 'user-roles')">Roles & Permissions</a>
                </div>
            </div>

            <!-- Connection Management -->
            <div class="sidebar-nav-group" data-group="connection-management">
                <div class="sidebar-nav-item" onclick="toggleDocSubmenu('connection-management')">
                    <span class="nav-icon"><i class="fas fa-plug"></i></span>
                    Connection Management
                    <i class="fas fa-chevron-down nav-chevron" id="chevron-connection-management"></i>
                </div>
                <div class="sidebar-subnav" id="subnav-connection-management">
                    <a href="#connection-new-application" class="sidebar-subnav-item" onclick="navigateTo(event, 'connection-new-application')">New Application</a>
                    <a href="#connection-applications" class="sidebar-subnav-item" onclick="navigateTo(event, 'connection-applications')">Applications List</a>
                    <a href="#connection-active" class="sidebar-subnav-item" onclick="navigateTo(event, 'connection-active')">Active Connections</a>
                </div>
            </div>

            <!-- Customer Management -->
            <div class="sidebar-nav-group" data-group="customer-management">
                <div class="sidebar-nav-item" onclick="toggleDocSubmenu('customer-management')">
                    <span class="nav-icon"><i class="fas fa-user-tie"></i></span>
                    Customer Management
                    <i class="fas fa-chevron-down nav-chevron" id="chevron-customer-management"></i>
                </div>
                <div class="sidebar-subnav" id="subnav-customer-management">
                    <a href="#customer-list" class="sidebar-subnav-item" onclick="navigateTo(event, 'customer-list')">Customer List</a>
                    <a href="#customer-details" class="sidebar-subnav-item" onclick="navigateTo(event, 'customer-details')">Customer Details</a>
                    <a href="#customer-approval" class="sidebar-subnav-item" onclick="navigateTo(event, 'customer-approval')">Customer Approval</a>
                </div>
            </div>

            <!-- Payment Management -->
            <div class="sidebar-nav-group" data-group="payment-management">
                <div class="sidebar-nav-item" onclick="toggleDocSubmenu('payment-management')">
                    <span class="nav-icon"><i class="fas fa-credit-card"></i></span>
                    Payment Management
                    <i class="fas fa-chevron-down nav-chevron" id="chevron-payment-management"></i>
                </div>
                <div class="sidebar-subnav" id="subnav-payment-management">
                    <a href="#payment-processing" class="sidebar-subnav-item" onclick="navigateTo(event, 'payment-processing')">Processing Payments</a>
                    <a href="#payment-history" class="sidebar-subnav-item" onclick="navigateTo(event, 'payment-history')">Payment History</a>
                </div>
            </div>

            <!-- Billing Management -->
            <div class="sidebar-nav-group" data-group="billing-management">
                <div class="sidebar-nav-item" onclick="toggleDocSubmenu('billing-management')">
                    <span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                    Billing Management
                    <i class="fas fa-chevron-down nav-chevron" id="chevron-billing-management"></i>
                </div>
                <div class="sidebar-subnav" id="subnav-billing-management">
                    <a href="#billing-generate" class="sidebar-subnav-item" onclick="navigateTo(event, 'billing-generate')">Generating Bills</a>
                    <a href="#billing-uploaded-readings" class="sidebar-subnav-item" onclick="navigateTo(event, 'billing-uploaded-readings')">Uploaded Readings</a>
                    <a href="#billing-consumer-view" class="sidebar-subnav-item" onclick="navigateTo(event, 'billing-consumer-view')">Consumer Billing View</a>
                </div>
            </div>

            <!-- Meter Management -->
            <a href="#meter-management" class="sidebar-nav-item" onclick="navigateTo(event, 'meter-management')">
                <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                Meter Management
            </a>

            <!-- Rate Management -->
            <a href="#rate-management" class="sidebar-nav-item" onclick="navigateTo(event, 'rate-management')">
                <span class="nav-icon"><i class="fas fa-percentage"></i></span>
                Rate Management
            </a>

            <!-- Ledger -->
            <a href="#ledger-management" class="sidebar-nav-item" onclick="navigateTo(event, 'ledger-management')">
                <span class="nav-icon"><i class="fas fa-book"></i></span>
                Ledger
            </a>

            <!-- Reports -->
            <div class="sidebar-nav-group" data-group="reports">
                <div class="sidebar-nav-item" onclick="toggleDocSubmenu('reports')">
                    <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                    Reports
                    <i class="fas fa-chevron-down nav-chevron" id="chevron-reports"></i>
                </div>
                <div class="sidebar-subnav" id="subnav-reports">
                    <a href="#reports-billing-summary" class="sidebar-subnav-item" onclick="navigateTo(event, 'reports-billing-summary')">Monthly Billing Summary</a>
                    <a href="#reports-collection-summary" class="sidebar-subnav-item" onclick="navigateTo(event, 'reports-collection-summary')">Monthly Collection Summary</a>
                    <a href="#reports-aging" class="sidebar-subnav-item" onclick="navigateTo(event, 'reports-aging')">Aging of Accounts</a>
                    <a href="#reports-masterlist" class="sidebar-subnav-item" onclick="navigateTo(event, 'reports-masterlist')">Consumer Master List</a>
                    <a href="#reports-bill-history" class="sidebar-subnav-item" onclick="navigateTo(event, 'reports-bill-history')">Bill History</a>
                    <a href="#reports-statement" class="sidebar-subnav-item" onclick="navigateTo(event, 'reports-statement')">Statement of Account</a>
                </div>
            </div>

            <div class="sidebar-section-label">Administration</div>

            <!-- Admin Configuration -->
            <div class="sidebar-nav-group" data-group="admin-config">
                <div class="sidebar-nav-item" onclick="toggleDocSubmenu('admin-config')">
                    <span class="nav-icon"><i class="fas fa-cogs"></i></span>
                    Admin Configuration
                    <i class="fas fa-chevron-down nav-chevron" id="chevron-admin-config"></i>
                </div>
                <div class="sidebar-subnav" id="subnav-admin-config">
                    <a href="#config-geographic" class="sidebar-subnav-item" onclick="navigateTo(event, 'config-geographic')">Geographic Settings</a>
                    <a href="#config-water-rates" class="sidebar-subnav-item" onclick="navigateTo(event, 'config-water-rates')">Water Rates</a>
                    <a href="#config-account-types" class="sidebar-subnav-item" onclick="navigateTo(event, 'config-account-types')">Account Types</a>
                    <a href="#config-access-control" class="sidebar-subnav-item" onclick="navigateTo(event, 'config-access-control')">Access Control</a>
                </div>
            </div>

            <!-- Activity Log -->
            <a href="#activity-log" class="sidebar-nav-item" onclick="navigateTo(event, 'activity-log')">
                <span class="nav-icon"><i class="fas fa-history"></i></span>
                Activity Log
            </a>

        </nav>

        <!-- Sidebar footer removed for cleaner UI -->

    </aside>

    <!-- ================================================================
         MAIN CONTENT
         ================================================================ -->
    <main class="doc-main">

        <!-- Top Bar -->
        <div class="doc-topbar">
            <div class="breadcrumb">
                <a href="{{ route('dashboard') }}">Home</a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current" id="breadcrumbCurrent">Introduction</span>
            </div>
            <div class="doc-topbar-actions">
                <a href="{{ route('dashboard') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to App
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="doc-content">

            {{-- ============================================================
                 INTRODUCTION
                 ============================================================ --}}
            <section id="introduction">
                <h1>MEEDO Water Billing System</h1>
                <p class="subtitle">User Manual & Documentation Guide for the Initao Municipal Water System</p>

                <div class="doc-toc">
                    <div class="doc-toc-title">In This Section</div>
                    <a href="#system-requirements">System Requirements</a>
                    <a href="#logging-in">Logging In</a>
                    <a href="#dashboard-overview">Dashboard Overview</a>
                </div>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    The MEEDO Water Billing System is a comprehensive web-based platform designed for the Municipal Government of Initao to manage
                    water service connections, billing, meter reading, payment processing, and customer management.
                </p>
                <p>
                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    This user manual provides step-by-step guidance to help administrators, billing clerks, meter readers, and other
                    authorized personnel navigate and operate the system effectively.
                </p>

                <div class="doc-callout info">
                    <div class="doc-callout-icon"><i class="fas fa-info"></i></div>
                    <div>
                        <strong>Note:</strong> This documentation is a living document and will be updated as new features are
                        added to the system. Content marked with placeholder text will be replaced with actual instructions.
                    </div>
                </div>

                <!-- Image placeholder -->
                <div class="doc-image-placeholder">
                    <i class="fas fa-image"></i>
                    <span>Screenshot: System Overview / Landing Page</span>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 SYSTEM REQUIREMENTS
                 ============================================================ --}}
            <section id="system-requirements">
                <h2>System Requirements</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Before using the MEEDO Water Billing System,
                    ensure your computer meets the following minimum requirements.
                </p>

                <h3>Browser Compatibility</h3>
                <ul>
                    <li>Google Chrome (version 90 or later) — <strong>Recommended</strong></li>
                    <li>Mozilla Firefox (version 88 or later)</li>
                    <li>Microsoft Edge (Chromium-based)</li>
                    <li>Safari (version 14 or later)</li>
                </ul>

                <h3>Network Requirements</h3>
                <p>
                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                    A stable internet connection is required for accessing the system. Minimum recommended bandwidth is 2 Mbps.
                </p>

                <div class="doc-callout warning">
                    <div class="doc-callout-icon"><i class="fas fa-exclamation"></i></div>
                    <div>
                        <strong>Important:</strong> Internet Explorer is not supported. Please use one of the recommended browsers listed above.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 LOGGING IN
                 ============================================================ --}}
            <section id="logging-in">
                <h2>Logging In</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Follow the steps below to log in to the system.
                </p>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Open Your Browser</h4>
                        <p>Launch your preferred web browser and navigate to the MEEDO Water Billing System URL provided by your administrator.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Enter Credentials</h4>
                        <p>Enter your username (email address) and password in the login form. These credentials are provided by the system administrator.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">3</div>
                    <div class="doc-step-content">
                        <h4>Click "Log In"</h4>
                        <p>Click the Log In button to authenticate. Upon successful login, you will be redirected to the Dashboard.</p>
                    </div>
                </div>

                <div class="doc-image-placeholder">
                    <i class="fas fa-image"></i>
                    <span>Screenshot: Login Page</span>
                </div>

                <div class="doc-callout info">
                    <div class="doc-callout-icon"><i class="fas fa-info"></i></div>
                    <div>
                        If you forget your password, contact your system administrator to have it reset.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 DASHBOARD OVERVIEW
                 ============================================================ --}}
            <section id="dashboard-overview">
                <h2>Dashboard Overview</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Dashboard is the first screen you see after logging in.
                    It provides a high-level summary of the system's key metrics and recent activity.
                </p>

                <h3>Key Dashboard Widgets</h3>
                <ul>
                    <li><strong>Total Customers</strong> — Total number of registered customers in the system.</li>
                    <li><strong>Active Connections</strong> — Number of currently active service connections.</li>
                    <li><strong>Pending Bills</strong> — Count of unpaid water bills awaiting payment.</li>
                    <li><strong>Revenue Summary</strong> — Overview of monthly collections and billing amounts.</li>
                </ul>

                <div class="doc-image-placeholder">
                    <i class="fas fa-image"></i>
                    <span>Screenshot: Dashboard Page</span>
                </div>

                <!-- Video placeholder -->
                <div class="doc-video-placeholder">
                    <div class="play-btn"><i class="fas fa-play"></i></div>
                    <span>Video Tutorial: Navigating the Dashboard</span>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 USER MANAGEMENT
                 ============================================================ --}}
            <section id="user-management">
                <h2>User Management</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The User Management module allows administrators to
                    create, view, edit, and manage user accounts. Access to this module requires the appropriate permissions.
                </p>

                <div id="user-add">
                    <h3>Adding Users</h3>
                    <p>
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.
                        To create a new user account, follow these steps.
                    </p>

                    <div class="doc-step">
                        <div class="doc-step-number">1</div>
                        <div class="doc-step-content">
                            <h4>Navigate to User Management</h4>
                            <p>Click on "User Management" in the sidebar, then select "Add User" from the submenu.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">2</div>
                        <div class="doc-step-content">
                            <h4>Fill in User Information</h4>
                            <p>Enter the new user's name, email address, and assign a role. Set a temporary password for the account.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">3</div>
                        <div class="doc-step-content">
                            <h4>Save the User</h4>
                            <p>Review the entered information and click the "Save" button to create the user account.</p>
                        </div>
                    </div>

                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Add User Form</span>
                    </div>
                </div>

                <div id="user-list">
                    <h3>User List</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. The User List page displays all registered users
                        in a searchable and sortable table. From here, administrators can view user details, edit accounts, or deactivate users.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: User List Page</span>
                    </div>
                </div>

                <div id="user-roles">
                    <h3>Roles & Permissions</h3>
                    <p>
                        Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit. The system uses a role-based
                        access control (RBAC) system. Each role is assigned specific permissions that determine what actions a user can perform.
                    </p>

                    <div class="doc-callout success">
                        <div class="doc-callout-icon"><i class="fas fa-check"></i></div>
                        <div>
                            <strong>Available Roles:</strong> Super Admin, Admin, Billing Clerk, Meter Reader, and Cashier.
                            Each role has a predefined set of permissions that can be customized through the Admin Configuration panel.
                        </div>
                    </div>

                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Permission Matrix</span>
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 CONNECTION MANAGEMENT
                 ============================================================ --}}
            <section id="connection-management">
                <h2>Connection Management</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Connection Management module handles
                    service applications, approvals, and active water connections.
                </p>

                <div id="connection-new-application">
                    <h3>New Application</h3>
                    <p>
                        At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis. To process a new water
                        service connection application, follow the step-by-step guide below.
                    </p>

                    <div class="doc-step">
                        <div class="doc-step-number">1</div>
                        <div class="doc-step-content">
                            <h4>Open New Application Form</h4>
                            <p>Navigate to Connection Management → New Application to open the service application form.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">2</div>
                        <div class="doc-step-content">
                            <h4>Fill in Applicant Details</h4>
                            <p>Enter the applicant's personal information, address details, and desired connection type.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">3</div>
                        <div class="doc-step-content">
                            <h4>Submit Application</h4>
                            <p>Review the details and submit the application for processing and approval.</p>
                        </div>
                    </div>

                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: New Application Form</span>
                    </div>
                </div>

                <div id="connection-applications">
                    <h3>Applications List</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Applications List shows all submitted service applications
                        along with their current status (Pending, Verified, Paid, Scheduled, Connected, etc.).
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Applications List</span>
                    </div>
                </div>

                <div id="connection-active">
                    <h3>Active Connections</h3>
                    <p>
                        Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam. The Active Connections page
                        shows all currently active service connections with their assigned meters, areas, and customer details.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Active Connections</span>
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 CUSTOMER MANAGEMENT
                 ============================================================ --}}
            <section id="customer-management">
                <h2>Customer Management</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Customer Management module is the central hub
                    for managing customer records, viewing customer details, and performing account-level operations.
                </p>

                <div id="customer-list">
                    <h3>Customer List</h3>
                    <p>
                        Excepteur sint occaecat cupidatat non proident. The Customer List displays all customers in a searchable table
                        with key information such as name, account number, connection status, and outstanding balance.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Customer List</span>
                    </div>
                </div>

                <div id="customer-details">
                    <h3>Customer Details</h3>
                    <p>
                        Sunt in culpa qui officia deserunt mollit anim id est laborum. The Customer Details page provides
                        a comprehensive view of a single customer, including personal information, service connections,
                        billing history, payment records, and ledger entries.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Customer Details Page</span>
                    </div>
                    <div class="doc-video-placeholder">
                        <div class="play-btn"><i class="fas fa-play"></i></div>
                        <span>Video Tutorial: Navigating Customer Details</span>
                    </div>
                </div>

                <div id="customer-approval">
                    <h3>Customer Approval</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Customer Approval page allows authorized personnel
                        to review and approve or reject pending customer applications.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Customer Approval Page</span>
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 PAYMENT MANAGEMENT
                 ============================================================ --}}
            <section id="payment-management">
                <h2>Payment Management</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Payment Management module handles all payment
                    processing operations including water bill payments, service connection fees, and miscellaneous charges.
                </p>

                <div id="payment-processing">
                    <h3>Processing Payments</h3>
                    <p>
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem. Follow these steps to process a payment.
                    </p>

                    <div class="doc-step">
                        <div class="doc-step-number">1</div>
                        <div class="doc-step-content">
                            <h4>Search for Customer</h4>
                            <p>Navigate to Payment Management and search for the customer by name or account number.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">2</div>
                        <div class="doc-step-content">
                            <h4>Select Outstanding Bills</h4>
                            <p>Review the list of outstanding bills and select the ones to be paid.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">3</div>
                        <div class="doc-step-content">
                            <h4>Enter Payment Amount</h4>
                            <p>Enter the amount received from the customer. The system will automatically calculate the change.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">4</div>
                        <div class="doc-step-content">
                            <h4>Confirm & Print Receipt</h4>
                            <p>Review the payment summary and confirm. A receipt will be generated for printing.</p>
                        </div>
                    </div>

                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Payment Processing Screen</span>
                    </div>
                </div>

                <div id="payment-history">
                    <h3>Payment History</h3>
                    <p>
                        Lorem ipsum dolor sit amet. The Payment History section provides a searchable record of all payments processed,
                        filterable by date range, customer, or payment method.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Payment History</span>
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 BILLING MANAGEMENT
                 ============================================================ --}}
            <section id="billing-management">
                <h2>Billing Management</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Billing Management module is responsible for
                    generating water bills based on meter readings, managing billing periods, and tracking bill statuses.
                </p>

                <div id="billing-generate">
                    <h3>Generating Bills</h3>
                    <p>
                        At vero eos et accusamus et iusto odio dignissimos. Water bills are generated based on the difference
                        between previous and current meter readings, multiplied by the applicable water rate.
                    </p>

                    <div class="doc-step">
                        <div class="doc-step-number">1</div>
                        <div class="doc-step-content">
                            <h4>Select Connection</h4>
                            <p>Search and select the service connection for which you want to generate a bill.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">2</div>
                        <div class="doc-step-content">
                            <h4>Enter Meter Reading</h4>
                            <p>Enter the current meter reading. The system will display the previous reading and calculated consumption.</p>
                        </div>
                    </div>

                    <div class="doc-step">
                        <div class="doc-step-number">3</div>
                        <div class="doc-step-content">
                            <h4>Preview & Generate</h4>
                            <p>Preview the bill breakdown, verify the amount, and click Generate to create the water bill.</p>
                        </div>
                    </div>

                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Bill Generation Modal</span>
                    </div>
                </div>

                <div id="billing-uploaded-readings">
                    <h3>Uploaded Readings</h3>
                    <p>
                        Lorem ipsum dolor sit amet. Meter readings can be uploaded in bulk via CSV to speed up the billing process.
                        The Uploaded Readings tab shows the status of each uploaded batch.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Uploaded Readings Tab</span>
                    </div>
                </div>

                <div id="billing-consumer-view">
                    <h3>Consumer Billing View</h3>
                    <p>
                        Nemo enim ipsam voluptatem quia voluptas sit aspernatur. The consumer billing view provides a detailed look
                        at an individual connection's billing history, current balance, and payment status.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Consumer Billing Details</span>
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 METER MANAGEMENT
                 ============================================================ --}}
            <section id="meter-management">
                <h2>Meter Management</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Meter Management module provides tools to
                    register meters, assign them to service connections, track meter installations, and manage meter replacements.
                </p>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Register a Meter</h4>
                        <p>Add a new meter to the system with its serial number, brand, and specifications.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Assign to Connection</h4>
                        <p>Link the meter to an active service connection with the installation reading.</p>
                    </div>
                </div>

                <div class="doc-image-placeholder">
                    <i class="fas fa-image"></i>
                    <span>Screenshot: Meter Management Page</span>
                </div>

                <div class="doc-video-placeholder">
                    <div class="play-btn"><i class="fas fa-play"></i></div>
                    <span>Video Tutorial: Managing Meters</span>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 RATE MANAGEMENT
                 ============================================================ --}}
            <section id="rate-management">
                <h2>Rate Management</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Rate Management module allows administrators to
                    configure water rates, billing periods, and rate structures used for bill calculation.
                </p>

                <h3>Billing Periods</h3>
                <p>
                    Ut enim ad minima veniam. Billing periods define the time frames for which bills are generated.
                    Each period has a start date, end date, and can be marked as open or closed.
                </p>

                <h3>Water Rate Configuration</h3>
                <p>
                    Quis autem vel eum iure reprehenderit. Water rates are configured by account type (e.g., Residential, Commercial)
                    and define the pricing tiers based on consumption volume.
                </p>

                <div class="doc-image-placeholder">
                    <i class="fas fa-image"></i>
                    <span>Screenshot: Rate Configuration Page</span>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 LEDGER
                 ============================================================ --}}
            <section id="ledger-management">
                <h2>Ledger</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Ledger module provides a detailed financial record
                    for each customer, showing all debits (bills, charges) and credits (payments) in chronological order.
                </p>

                <p>
                    Sed ut perspiciatis unde omnis iste natus error. The running balance is calculated automatically, helping
                    the billing department quickly identify outstanding amounts per customer.
                </p>

                <div class="doc-image-placeholder">
                    <i class="fas fa-image"></i>
                    <span>Screenshot: Customer Ledger View</span>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 REPORTS
                 ============================================================ --}}
            <section id="reports">
                <h2>Reports</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Reports module provides a suite of printable
                    and exportable reports that aid in operational management and decision-making.
                </p>

                <div id="reports-billing-summary">
                    <h3>Monthly Billing Summary</h3>
                    <p>
                        At vero eos et accusamus. This report summarizes all water bills generated during a specific month,
                        grouped by area. It includes consumption volumes, billing amounts, and paid/unpaid counts.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Monthly Billing Summary Report</span>
                    </div>
                </div>

                <div id="reports-collection-summary">
                    <h3>Monthly Collection Summary</h3>
                    <p>
                        Lorem ipsum dolor sit amet. This report summarizes all payments collected during a specific month,
                        providing a breakdown by collection type and payment method.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Monthly Collection Summary Report</span>
                    </div>
                </div>

                <div id="reports-aging">
                    <h3>Aging of Accounts</h3>
                    <p>
                        Sed ut perspiciatis unde omnis iste. The Aging of Accounts report shows all customers with unpaid
                        balances, categorized by how long the balance has been outstanding (Current, 1–30 days, 31–60 days, etc.).
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Aging of Accounts Report</span>
                    </div>
                </div>

                <div id="reports-masterlist">
                    <h3>Consumer Master List</h3>
                    <p>
                        Nemo enim ipsam voluptatem. The Consumer Master List is a comprehensive listing of all consumers/customers
                        in the system with their account numbers, addresses, and connection statuses.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Consumer Master List Report</span>
                    </div>
                </div>

                <div id="reports-bill-history">
                    <h3>Bill History</h3>
                    <p>
                        Quis autem vel eum iure. The Bill History report provides a searchable record of all bills generated by the system,
                        including billing period, amount, and payment status.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Bill History Report</span>
                    </div>
                </div>

                <div id="reports-statement">
                    <h3>Statement of Account</h3>
                    <p>
                        At vero eos et accusamus. The Statement of Account generates a per-customer financial statement
                        showing all transactions and the current balance due.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Statement of Account</span>
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 ADMIN CONFIGURATION
                 ============================================================ --}}
            <section id="admin-configuration">
                <h2>Admin Configuration</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Admin Configuration section is accessible to
                    users with administrative privileges and allows fine-tuning of system parameters.
                </p>

                <div id="config-geographic">
                    <h3>Geographic Settings</h3>
                    <p>
                        Sed do eiusmod tempor incididunt. Configure the geographic hierarchy used in the system:
                        Barangays, Areas (Zones), and Puroks. These are used for address management and routing meter readers.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Geographic Configuration</span>
                    </div>
                </div>

                <div id="config-water-rates">
                    <h3>Water Rates</h3>
                    <p>
                        Ut enim ad minim veniam. Configure the water rate structure including base amounts, consumption tiers,
                        and rate increments per account type.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Water Rate Configuration</span>
                    </div>
                </div>

                <div id="config-account-types">
                    <h3>Account Types</h3>
                    <p>
                        Quis nostrud exercitation ullamco. Manage account type classifications (e.g., Residential, Commercial, Industrial)
                        that determine billing rates and reporting categories.
                    </p>
                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Account Types</span>
                    </div>
                </div>

                <div id="config-access-control">
                    <h3>Access Control</h3>
                    <p>
                        Duis aute irure dolor in reprehenderit. Manage roles, permissions, and the permission matrix that
                        controls which features each user role can access.
                    </p>

                    <div class="doc-callout warning">
                        <div class="doc-callout-icon"><i class="fas fa-exclamation"></i></div>
                        <div>
                            <strong>Caution:</strong> Changes to access control settings take effect immediately and may impact
                            what users can see and do in the system. Exercise care when modifying permissions.
                        </div>
                    </div>

                    <div class="doc-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Screenshot: Role & Permission Matrix</span>
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 ACTIVITY LOG
                 ============================================================ --}}
            <section id="activity-log">
                <h2>Activity Log</h2>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. The Activity Log is a super-admin-only feature that
                    records all significant actions performed in the system for audit and accountability purposes.
                </p>

                <p>
                    Ut enim ad minim veniam, quis nostrud exercitation. Each log entry records the user, action type, timestamp,
                    and details of what was changed.
                </p>

                <div class="doc-image-placeholder">
                    <i class="fas fa-image"></i>
                    <span>Screenshot: Activity Log Page</span>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 FOOTER
                 ============================================================ --}}
            <footer style="background: var(--bg-secondary); border-top: 1px solid var(--border-color); margin-top: 60px; padding: 32px 48px; color: var(--text-muted); font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; max-width: 900px; margin: 0 auto;">
                    <div>
                        <p style="margin: 0; font-weight: 600; color: var(--text-primary);">MEEDO Water Billing System</p>
                        <p style="margin: 4px 0 0 0;">Municipal Government of Initao, Misamis Oriental</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0;">Document Version 1.0</p>
                        <p style="margin: 4px 0 0 0;">Last Updated: {{ now()->format('F j, Y') }}</p>
                    </div>
                </div>
            </footer>

        </div>
    </main>
</div>

<!-- Mobile Sidebar Toggle -->
<button class="doc-sidebar-toggle" onclick="toggleDocSidebar()" title="Toggle Navigation">
    <i class="fas fa-bars"></i>
</button>

<script>
    // ================================================================
    // SIDEBAR TOGGLE (Mobile)
    // ================================================================
    function toggleDocSidebar() {
        const sidebar = document.getElementById('docSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    }

    // ================================================================
    // SUBMENU TOGGLE
    // ================================================================
    function toggleDocSubmenu(group) {
        const subnav = document.getElementById('subnav-' + group);
        const chevron = document.getElementById('chevron-' + group);

        if (subnav) {
            subnav.classList.toggle('open');
        }
        if (chevron) {
            chevron.classList.toggle('open');
        }
    }

    // ================================================================
    // NAVIGATION / SCROLL TO SECTION
    // ================================================================
    function navigateTo(event, sectionId) {
        event.preventDefault();

        // Update active state in sidebar
        document.querySelectorAll('.sidebar-nav-item, .sidebar-subnav-item').forEach(el => {
            el.classList.remove('active');
        });
        event.currentTarget.classList.add('active');

        // Scroll to section
        const target = document.getElementById(sectionId);
        if (target) {
            const offset = 80; // top bar height + padding
            const y = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top: y, behavior: 'smooth' });
        }

        // Update breadcrumb
        const text = event.currentTarget.textContent.trim();
        document.getElementById('breadcrumbCurrent').textContent = text;

        // Close mobile sidebar
        if (window.innerWidth <= 1024) {
            toggleDocSidebar();
        }
    }

    // ================================================================
    // SEARCH FILTER
    // ================================================================
    function filterSidebar(query) {
        const items = document.querySelectorAll('.sidebar-nav-item, .sidebar-subnav-item');
        const groups = document.querySelectorAll('.sidebar-nav-group');
        const q = query.toLowerCase().trim();

        if (!q) {
            items.forEach(el => el.style.display = '');
            groups.forEach(el => el.style.display = '');
            document.querySelectorAll('.sidebar-section-label').forEach(el => el.style.display = '');
            return;
        }

        items.forEach(el => {
            const text = el.textContent.toLowerCase();
            el.style.display = text.includes(q) ? '' : 'none';
        });

        // Show groups that have visible children
        groups.forEach(group => {
            const visibleChildren = group.querySelectorAll('.sidebar-subnav-item:not([style*="display: none"])');
            const parentItem = group.querySelector('.sidebar-nav-item');
            const parentText = parentItem?.textContent.toLowerCase() || '';

            if (visibleChildren.length > 0 || parentText.includes(q)) {
                group.style.display = '';
                if (parentItem) parentItem.style.display = '';
                // Open subnav to show matches
                const subnav = group.querySelector('.sidebar-subnav');
                if (subnav && visibleChildren.length > 0) subnav.classList.add('open');
            } else {
                group.style.display = 'none';
            }
        });
    }

    // ================================================================
    // SCROLL SPY - Highlight sidebar item on scroll
    // ================================================================
    window.addEventListener('scroll', () => {
        const sections = document.querySelectorAll('section[id], div[id]');
        let currentSection = '';

        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            if (rect.top <= 120) {
                currentSection = section.id;
            }
        });

        if (currentSection) {
            const activeLink = document.querySelector(`.sidebar-nav-item[href="#${currentSection}"], .sidebar-subnav-item[href="#${currentSection}"]`);
            if (activeLink) {
                document.querySelectorAll('.sidebar-nav-item, .sidebar-subnav-item').forEach(el => el.classList.remove('active'));
                activeLink.classList.add('active');
                document.getElementById('breadcrumbCurrent').textContent = activeLink.textContent.trim();
            }
        }
    });
</script>

</body>
</html>
