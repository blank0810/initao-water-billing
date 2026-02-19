<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manual - MEEDO Water Billing System</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
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
        }

        /* ================================================================
           TYPOGRAPHY
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

        /* Code blocks */
        .doc-content code {
            background: var(--code-bg);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.85em;
            font-family: 'Courier New', monospace;
        }

        .doc-content pre {
            background: var(--code-bg);
            padding: 16px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 16px 0;
        }

        .doc-content pre code {
            background: transparent;
            padding: 0;
        }

        /* Table styles */
        .doc-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.88rem;
        }

        .doc-content table th {
            background: var(--code-bg);
            padding: 10px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }

        .doc-content table td {
            padding: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .doc-content table tr:hover {
            background: var(--code-bg);
        }

        /* Blockquote */
        .doc-content blockquote {
            border-left: 4px solid var(--accent);
            padding: 12px 20px;
            margin: 20px 0;
            background: var(--code-bg);
            font-style: italic;
            color: var(--text-secondary);
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
            <a href="#test-accounts" class="sidebar-nav-item" onclick="navigateTo(event, 'test-accounts')">
                <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                Test Accounts
            </a>

            <div class="sidebar-section-label">Core Features</div>

            <a href="#dashboard-overview" class="sidebar-nav-item" onclick="navigateTo(event, 'dashboard-overview')">
                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                Dashboard Overview
            </a>

            <a href="#initial-setup" class="sidebar-nav-item" onclick="navigateTo(event, 'initial-setup')">
                <span class="nav-icon"><i class="fas fa-cogs"></i></span>
                Initial Setup
            </a>

            <a href="#customer-registration" class="sidebar-nav-item" onclick="navigateTo(event, 'customer-registration')">
                <span class="nav-icon"><i class="fas fa-user-plus"></i></span>
                Customer Registration
            </a>

            <a href="#service-application" class="sidebar-nav-item" onclick="navigateTo(event, 'service-application')">
                <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                Service Application
            </a>

            <a href="#connection-meter" class="sidebar-nav-item" onclick="navigateTo(event, 'connection-meter')">
                <span class="nav-icon"><i class="fas fa-plug"></i></span>
                Connection & Meters
            </a>

            <a href="#meter-reading" class="sidebar-nav-item" onclick="navigateTo(event, 'meter-reading')">
                <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                Meter Reading
            </a>

            <a href="#billing-generation" class="sidebar-nav-item" onclick="navigateTo(event, 'billing-generation')">
                <span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                Billing & Adjustments
            </a>

            <a href="#payment-processing" class="sidebar-nav-item" onclick="navigateTo(event, 'payment-processing')">
                <span class="nav-icon"><i class="fas fa-credit-card"></i></span>
                Payment Processing
            </a>

            <a href="#ledger-statements" class="sidebar-nav-item" onclick="navigateTo(event, 'ledger-statements')">
                <span class="nav-icon"><i class="fas fa-book"></i></span>
                Ledger & Statements
            </a>

            <a href="#reports" class="sidebar-nav-item" onclick="navigateTo(event, 'reports')">
                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                Reports & Exports
            </a>

            <div class="sidebar-section-label">Administration</div>

            <a href="#user-management" class="sidebar-nav-item" onclick="navigateTo(event, 'user-management')">
                <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
                User Management
            </a>

            <a href="#admin-configuration" class="sidebar-nav-item" onclick="navigateTo(event, 'admin-configuration')">
                <span class="nav-icon"><i class="fas fa-sliders-h"></i></span>
                Admin Configuration
            </a>

        </nav>

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
                <h1>Initao Water Billing System</h1>
                <p class="subtitle">Complete User Manual & Feature Guide for the MEEDO Water Billing System</p>

                <div class="doc-toc">
                    <div class="doc-toc-title">Quick Navigation</div>
                    <a href="#test-accounts">Test Accounts</a>
                    <a href="#dashboard-overview">Dashboard Overview</a>
                    <a href="#initial-setup">Initial Setup & Configuration</a>
                    <a href="#service-application">Service Application Workflow</a>
                </div>

                <p>
                    The <strong>MEEDO Water Billing System</strong> is a comprehensive web-based platform designed for the
                    Municipal Government of Initao to manage water service connections, billing, meter reading, payment
                    processing, and customer management.
                </p>
                <p>
                    This user manual provides complete guidance to help administrators, billing officers, meter readers,
                    cashiers, and other authorized personnel navigate and operate the system effectively.
                </p>

                <div class="doc-callout info">
                    <div class="doc-callout-icon"><i class="fas fa-info"></i></div>
                    <div>
                        <strong>System URL:</strong> Access the system at <code>http://localhost:8000</code> (local)
                        or <code>http://localhost:9000</code> (Docker). Login credentials are provided in the Test Accounts section below.
                    </div>
                </div>

                <h3>What's Included in This System</h3>
                <ul>
                    <li><strong>Customer Management</strong> - Register and manage customer records with address tracking</li>
                    <li><strong>Service Application Workflow</strong> - Complete lifecycle from application to connection</li>
                    <li><strong>Meter Management</strong> - Track meters, assignments, and readings</li>
                    <li><strong>Billing Engine</strong> - Tiered rate calculation with adjustments</li>
                    <li><strong>Payment Processing</strong> - Payment allocation and receipt generation</li>
                    <li><strong>Double-Entry Ledger</strong> - Complete financial tracking per customer</li>
                    <li><strong>Comprehensive Reports</strong> - 8+ operational and financial reports</li>
                    <li><strong>Role-Based Access Control</strong> - 6 roles with granular permissions</li>
                </ul>
            </section>

            <hr>

            {{-- ============================================================
                 TEST ACCOUNTS
                 ============================================================ --}}
            <section id="test-accounts">
                <h2>Test Accounts Quick Reference</h2>

                <p>
                    The system comes pre-configured with test accounts for each role. Use these credentials to login
                    and explore different permission levels.
                </p>

                <table>
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>What They Can Do</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Super Admin</strong></td>
                            <td>super_admin</td>
                            <td>password</td>
                            <td>Full access to everything</td>
                        </tr>
                        <tr>
                            <td><strong>Admin</strong></td>
                            <td>admin_user</td>
                            <td>password</td>
                            <td>User management + all features</td>
                        </tr>
                        <tr>
                            <td><strong>Billing Officer</strong></td>
                            <td>billing_officer</td>
                            <td>password</td>
                            <td>Billing, payments, meter readings</td>
                        </tr>
                        <tr>
                            <td><strong>Meter Reader</strong></td>
                            <td>meter_reader</td>
                            <td>password</td>
                            <td>Meter reading entry only</td>
                        </tr>
                        <tr>
                            <td><strong>Cashier</strong></td>
                            <td>cashier</td>
                            <td>password</td>
                            <td>Payment processing only</td>
                        </tr>
                        <tr>
                            <td><strong>Viewer</strong></td>
                            <td>viewer</td>
                            <td>password</td>
                            <td>Read-only access</td>
                        </tr>
                    </tbody>
                </table>

                <div class="doc-callout success">
                    <div class="doc-callout-icon"><i class="fas fa-check"></i></div>
                    <div>
                        <strong>Demo Tip:</strong> Login as Super Admin for the full demo. Switch to other roles to
                        see how the sidebar navigation and permissions change based on role.
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
                    The Dashboard is your landing page after login, providing a high-level overview of the system's
                    key metrics and recent activity.
                </p>

                <h3>Key Features</h3>
                <ul>
                    <li><strong>Clean, Modern UI</strong> - Intuitive interface with dark mode support</li>
                    <li><strong>Role-Based Navigation</strong> - Sidebar menu adapts based on user permissions</li>
                    <li><strong>Quick Stats</strong> - Overview cards showing key system metrics</li>
                    <li><strong>Theme Toggle</strong> - Switch between light and dark mode</li>
                    <li><strong>Notification Bell</strong> - Stay updated on important system events</li>
                    <li><strong>User Dropdown</strong> - Quick access to common actions</li>
                </ul>

                <div class="doc-callout info">
                    <div class="doc-callout-icon"><i class="fas fa-lightbulb"></i></div>
                    <div>
                        <strong>Navigation Tip:</strong> The sidebar menu shows different options based on your role.
                        Super Admins see all modules, while other roles see only what they have permission to access.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 INITIAL SETUP & CONFIGURATION
                 ============================================================ --}}
            <section id="initial-setup">
                <h2>Initial Setup & Configuration</h2>

                <p>
                    Before daily operations begin, administrators need to configure foundational data.
                    The system comes pre-seeded with data for Initao, but you can customize it as needed.
                </p>

                <h3>Geographic Configuration</h3>
                <p>
                    Navigate to <strong>Admin Configuration → Geographic</strong> to manage:
                </p>
                <ul>
                    <li><strong>Barangays</strong> - The 16 barangays of Initao (pre-seeded)</li>
                    <li><strong>Areas</strong> - Service zones for meter reader assignment</li>
                    <li><strong>Puroks</strong> - Sub-village zones (24 per barangay)</li>
                </ul>

                <h3>Water Rates Configuration</h3>
                <p>
                    Navigate to <strong>Admin Configuration → Water Rates</strong> to configure tiered pricing:
                </p>
                <ul>
                    <li><strong>Residential Rates</strong> - 0-10 cu.m = ₱100 flat, 11-20 = ₱11/cu.m, etc.</li>
                    <li><strong>Commercial Rates</strong> - Typically double residential rates</li>
                    <li><strong>Period-Based</strong> - Rates are copied for each billing period</li>
                </ul>

                <h3>Account Types</h3>
                <p>
                    Navigate to <strong>Admin Configuration → Billing Configuration → Account Types</strong>.
                    Pre-configured types include Residential and Commercial. You can add Government, Non-Profit, etc.
                </p>

                <h3>Charge Item Templates</h3>
                <p>
                    Navigate to <strong>Admin Configuration → Billing Configuration → Application Fee Templates</strong>.
                    These are fee templates applied when customers apply for service:
                </p>
                <ul>
                    <li>Connection Fee (₱500)</li>
                    <li>Service Deposit (₱300)</li>
                    <li>Meter Deposit (₱200)</li>
                    <li>Application Fee (₱50)</li>
                    <li>Installation (₱800)</li>
                    <li>Reconnection (₱300)</li>
                    <li>Late Payment (₱50)</li>
                </ul>

                <div class="doc-callout success">
                    <div class="doc-callout-icon"><i class="fas fa-check"></i></div>
                    <div>
                        <strong>Pre-Seeded Data:</strong> The system comes with all reference data already configured
                        for Initao. You can modify amounts and add new items as needed.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 CUSTOMER REGISTRATION
                 ============================================================ --}}
            <section id="customer-registration">
                <h2>Customer Registration</h2>

                <p>
                    Register new water service customers into the system with complete personal and address information.
                </p>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Navigate to Customer List</h4>
                        <p>Go to <strong>Customer Management → Customer List</strong> to view all registered customers.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Add New Customer</h4>
                        <p>The easiest way is through <strong>Connection Management → New Application</strong>,
                        which creates both the customer and their service application in one step.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">3</div>
                    <div class="doc-step-content">
                        <h4>Enter Customer Details</h4>
                        <p>Fill in: Full name (UPPERCASE), contact information, and complete address
                        (Province → Town → Barangay → Purok). The system auto-generates a resolution number.</p>
                    </div>
                </div>

                <h3>Key Features</h3>
                <ul>
                    <li><strong>Auto-Generated Resolution Number</strong> - Format: <code>INITAO-ABC-1234567890</code></li>
                    <li><strong>Address Hierarchy</strong> - Province → Town → Barangay → Purok</li>
                    <li><strong>Multiple Connections</strong> - One customer can have multiple service connections</li>
                    <li><strong>Name Validation</strong> - Customer names are automatically stored in UPPERCASE</li>
                </ul>

                <div class="doc-callout warning">
                    <div class="doc-callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div>
                        <strong>Important:</strong> Ensure customer information is accurate before creating service
                        applications, as this data will be used for billing and official documents.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 SERVICE APPLICATION WORKFLOW
                 ============================================================ --}}
            <section id="service-application">
                <h2>Service Application Workflow</h2>

                <p>
                    The complete lifecycle from customer application to active water connection. This is the core workflow
                    of the system.
                </p>

                <h3>Application Lifecycle</h3>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Create New Application</h4>
                        <p>Navigate to <strong>Connection Management → New Application</strong>. Fill in customer
                        information, select address, and choose account type. System auto-generates application charges.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Verify Application</h4>
                        <p>From the Applications List, open a PENDING application and click <strong>Verify</strong>.
                        This moves the application to VERIFIED status.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">3</div>
                    <div class="doc-step-content">
                        <h4>Process Payment</h4>
                        <p>Click <strong>Process Payment</strong> to record the application fee payment.
                        System creates receipt and allocates payment to application charges.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">4</div>
                    <div class="doc-step-content">
                        <h4>Schedule Installation</h4>
                        <p>Click <strong>Schedule</strong> and set the installation date.
                        Application moves to SCHEDULED status.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">5</div>
                    <div class="doc-step-content">
                        <h4>Complete Connection</h4>
                        <p>After installation, click <strong>Complete Connection</strong>. System creates the
                        ServiceConnection record and assigns a meter. Connection becomes ACTIVE.</p>
                    </div>
                </div>

                <h3>Application Status Flow</h3>
                <p><code>PENDING → VERIFIED → PAID → SCHEDULED → CONNECTED</code></p>

                <h3>Additional Actions</h3>
                <ul>
                    <li><strong>Print Application</strong> - Printable application form</li>
                    <li><strong>Print Contract</strong> - Printable service contract</li>
                    <li><strong>Reject</strong> - Reject application with reason tracking</li>
                    <li><strong>Cancel</strong> - Cancel the application</li>
                    <li><strong>Timeline</strong> - View complete audit trail</li>
                </ul>

                <div class="doc-callout success">
                    <div class="doc-callout-icon"><i class="fas fa-check"></i></div>
                    <div>
                        <strong>Workflow Tracking:</strong> Every action is logged with who did it and when,
                        creating a complete audit trail for accountability.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 CONNECTION & METER MANAGEMENT
                 ============================================================ --}}
            <section id="connection-meter">
                <h2>Service Connection & Meter Assignment</h2>

                <p>
                    Manage active water service connections and their assigned meters.
                </p>

                <h3>Viewing Active Connections</h3>
                <p>
                    Navigate to <strong>Connection Management → Active Connections</strong> to see all active
                    connections with their account numbers and status.
                </p>

                <h3>Connection Details</h3>
                <ul>
                    <li><strong>Account Number</strong> - Auto-generated (e.g., <code>RES-202602-00001</code>)</li>
                    <li><strong>Account Type</strong> - Residential or Commercial</li>
                    <li><strong>Current Meter</strong> - Assigned meter information</li>
                    <li><strong>Billing History</strong> - Past bills and payments</li>
                    <li><strong>Outstanding Balance</strong> - Current amount due</li>
                </ul>

                <h3>Meter Assignment</h3>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Assign Meter to Connection</h4>
                        <p>From connection details, click <strong>Assign Meter</strong>. Select from available
                        meters and set installation date.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Change Meter</h4>
                        <p>To change a meter, first <strong>Remove</strong> the current meter (set removal date),
                        then <strong>Assign</strong> a new meter. System tracks meter change history.</p>
                    </div>
                </div>

                <h3>Connection Lifecycle Actions</h3>
                <ul>
                    <li><strong>Suspend</strong> - Temporarily cut off service (e.g., non-payment)</li>
                    <li><strong>Disconnect</strong> - Permanent disconnection</li>
                    <li><strong>Reconnect</strong> - Restore previously suspended/disconnected service</li>
                    <li><strong>Print Statement</strong> - Generate statement of account</li>
                </ul>

                <div class="doc-callout info">
                    <div class="doc-callout-icon"><i class="fas fa-info"></i></div>
                    <div>
                        <strong>Meter Change Handling:</strong> Bills during meter change periods handle split
                        consumption (old meter + new meter) to maintain billing accuracy.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 METER READING
                 ============================================================ --}}
            <section id="meter-reading">
                <h2>Meter Reading Management</h2>

                <p>
                    Record periodic water meter readings and manage reading schedules for organized field operations.
                </p>

                <h3>Meter Inventory</h3>
                <p>
                    Navigate to <strong>Meter</strong> to view:
                </p>
                <ul>
                    <li><strong>Total Meters</strong> - 25 pre-seeded meters across 5 brands</li>
                    <li><strong>Assigned Meters</strong> - Currently in use</li>
                    <li><strong>Available Meters</strong> - Ready for assignment</li>
                    <li><strong>Faulty Meters</strong> - Marked as needing repair/replacement</li>
                </ul>

                <h3>Recording Readings</h3>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Select Connection</h4>
                        <p>From billing management, select the connection you want to read.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Enter Current Reading</h4>
                        <p>Input the current meter reading. System automatically calculates consumption
                        (Current Reading - Previous Reading).</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">3</div>
                    <div class="doc-step-content">
                        <h4>Preview Bill</h4>
                        <p>Review the calculated bill before generating to ensure accuracy.</p>
                    </div>
                </div>

                <h3>Reading Schedules (For Organized Operations)</h3>
                <p>
                    Create reading schedules to organize meter reader routes:
                </p>
                <ul>
                    <li>Select billing period</li>
                    <li>Select area/zone</li>
                    <li>Assign meter reader</li>
                    <li>Set reading date</li>
                    <li>Download schedule template for field use</li>
                </ul>

                <h3>Schedule Lifecycle</h3>
                <p><code>PENDING → STARTED → COMPLETED</code> (or <code>DELAYED</code>)</p>

                <div class="doc-callout success">
                    <div class="doc-callout-icon"><i class="fas fa-mobile-alt"></i></div>
                    <div>
                        <strong>Mobile Integration:</strong> Meter readers can upload readings via mobile app
                        with bulk upload support (JSON/CSV) and processing workflow with validation.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 BILLING GENERATION & ADJUSTMENTS
                 ============================================================ --}}
            <section id="billing-generation">
                <h2>Billing Generation & Adjustments</h2>

                <p>
                    Generate monthly water bills based on meter readings and make adjustments when needed.
                </p>

                <h3>Bill Generation Process</h3>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Select Billing Period</h4>
                        <p>Navigate to <strong>Billing</strong> and select the current billing period.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Preview Bill</h4>
                        <p>Select a connection and click <strong>Preview Bill</strong> to see consumption,
                        rate tier applied, and computed amount.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">3</div>
                    <div class="doc-step-content">
                        <h4>Generate Bill</h4>
                        <p>Click <strong>Generate Bill</strong> to create the official bill with due date.</p>
                    </div>
                </div>

                <h3>Bill Adjustments (Two Types)</h3>

                <h4>1. Consumption Adjustment</h4>
                <p>
                    Changes the consumption reading and recalculates the entire bill amount based on rate tiers.
                    Use for meter reading errors.
                </p>

                <h4>2. Amount Adjustment</h4>
                <p>
                    Adds credit or debit to the bill. Select adjustment type:
                </p>
                <ul>
                    <li><strong>Meter Error</strong> - Credit adjustment</li>
                    <li><strong>Penalty Waiver</strong> - Credit adjustment</li>
                    <li><strong>Discount</strong> - Credit adjustment</li>
                    <li><strong>Penalty</strong> - Debit adjustment</li>
                    <li><strong>Correction</strong> - Credit or debit</li>
                </ul>

                <h3>Adjustment Actions</h3>
                <ul>
                    <li><strong>Void Adjustment</strong> - Undo a previous adjustment</li>
                    <li><strong>Recompute Bill</strong> - Recalculate a single bill (open periods only)</li>
                    <li><strong>Recompute Period</strong> - Recalculate all bills in a period</li>
                </ul>

                <div class="doc-callout warning">
                    <div class="doc-callout-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div>
                        <strong>Important Distinction:</strong><br>
                        <strong>Recalculate</strong> - Re-runs bill computation on existing bills (OPEN periods only). Fixes base amount.<br>
                        <strong>Adjustment</strong> - Adds credit/debit entry (works on OPEN and CLOSED periods). Shows as separate line item.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 PAYMENT PROCESSING
                 ============================================================ --}}
            <section id="payment-processing">
                <h2>Payment Processing & Allocation</h2>

                <p>
                    Process customer payments and automatically allocate across outstanding bills and charges.
                </p>

                <h3>Processing a Payment</h3>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Navigate to Payment Management</h4>
                        <p>Go to <strong>Payment Management</strong> to access payment processing.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Select Customer/Bill</h4>
                        <p>Search for customer or navigate from billing details and click <strong>Pay</strong>.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">3</div>
                    <div class="doc-step-content">
                        <h4>Enter Payment Amount</h4>
                        <p>Enter amount received. System auto-generates receipt number and allocates payment
                        across outstanding bills (oldest first).</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">4</div>
                    <div class="doc-step-content">
                        <h4>View/Print Receipt</h4>
                        <p>After processing, view receipt with payment details and allocation breakdown.</p>
                    </div>
                </div>

                <h3>Payment Features</h3>
                <ul>
                    <li><strong>Automatic Allocation</strong> - Distributes payment across multiple bills</li>
                    <li><strong>Double-Entry Ledger</strong> - Every payment creates credit entries</li>
                    <li><strong>Receipt Generation</strong> - Unique receipt numbers for all payments</li>
                    <li><strong>Transaction Export</strong> - Export as CSV or PDF</li>
                </ul>

                <h3>Payment Cancellation/Void</h3>
                <p>
                    To cancel a payment (requires <code>payments.void</code> permission):
                </p>
                <ol>
                    <li>Find payment in payment list</li>
                    <li>Click <strong>Cancel Payment</strong></li>
                    <li>Enter cancellation reason</li>
                    <li>System marks as CANCELLED, reverses ledger entries, restores balances</li>
                </ol>

                <div class="doc-callout info">
                    <div class="doc-callout-icon"><i class="fas fa-info"></i></div>
                    <div>
                        <strong>Cancellation Tracking:</strong> All cancellations are fully reversible with
                        complete audit trail showing who cancelled, when, and why.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 LEDGER & STATEMENTS
                 ============================================================ --}}
            <section id="ledger-statements">
                <h2>Customer Ledger & Statements</h2>

                <p>
                    Double-entry accounting ledger tracking all financial transactions per customer.
                </p>

                <h3>Viewing the Ledger</h3>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Navigate to Ledger</h4>
                        <p>Go to <strong>Ledger</strong> to view customer transactions.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Select Customer/Connection</h4>
                        <p>Choose the customer or specific connection to view ledger entries.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">3</div>
                    <div class="doc-step-content">
                        <h4>View Transaction History</h4>
                        <p>See all entries with: Date, Period, Type (BILL/CHARGE/PAYMENT), Debit, Credit,
                        Running Balance, and Source Reference.</p>
                    </div>
                </div>

                <h3>Ledger Features</h3>
                <ul>
                    <li><strong>Polymorphic Source Tracking</strong> - Each entry links to Bill, Charge, or Payment</li>
                    <li><strong>Per-Connection Ledger</strong> - Separate ledgers for multiple connections</li>
                    <li><strong>Running Balance</strong> - Automatic balance calculation</li>
                    <li><strong>Export Capability</strong> - Download as PDF or CSV</li>
                    <li><strong>Audit Trail</strong> - User tracking for all entries</li>
                </ul>

                <h3>Statement of Account</h3>
                <p>
                    From Customer Details or Ledger page, click <strong>Print Statement</strong> to generate
                    a printable statement showing:
                </p>
                <ul>
                    <li>Customer information</li>
                    <li>Connection details</li>
                    <li>Transaction history</li>
                    <li>Current balance due</li>
                </ul>

                <div class="doc-callout success">
                    <div class="doc-callout-icon"><i class="fas fa-check"></i></div>
                    <div>
                        <strong>Financial Accuracy:</strong> The ledger system ensures complete transparency
                        with every transaction tracked and auditable.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 REPORTS & EXPORTS
                 ============================================================ --}}
            <section id="reports">
                <h2>Reports & Exports</h2>

                <p>
                    Generate operational and financial reports for management decision-making.
                    Navigate to <strong>Reports</strong> to access all available reports.
                </p>

                <h3>Available Reports</h3>

                <table>
                    <thead>
                        <tr>
                            <th>Report</th>
                            <th>What It Shows</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Aging of Accounts</strong></td>
                            <td>Overdue bills grouped by age (30/60/90+ days)</td>
                        </tr>
                        <tr>
                            <td><strong>Consumer Master List</strong></td>
                            <td>Complete customer listing with addresses and status</td>
                        </tr>
                        <tr>
                            <td><strong>Monthly Billing Summary</strong></td>
                            <td>Total bills, amounts, consumption for a period</td>
                        </tr>
                        <tr>
                            <td><strong>Monthly Collection Summary</strong></td>
                            <td>Daily/weekly payment totals</td>
                        </tr>
                        <tr>
                            <td><strong>Summary Status Report</strong></td>
                            <td>Distribution of customer/connection statuses</td>
                        </tr>
                        <tr>
                            <td><strong>Abstract of Collection</strong></td>
                            <td>Detailed collection transactions (printable)</td>
                        </tr>
                        <tr>
                            <td><strong>Water Bill History</strong></td>
                            <td>Complete bill listing with all details</td>
                        </tr>
                        <tr>
                            <td><strong>Billing Statement</strong></td>
                            <td>Individual customer statement of account</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Report Features</h3>
                <ul>
                    <li><strong>Filtering</strong> - By period, area, status</li>
                    <li><strong>Export Formats</strong> - PDF and CSV</li>
                    <li><strong>Print-Optimized</strong> - Layouts designed for printing</li>
                    <li><strong>DataTables</strong> - Sortable and paginated views</li>
                </ul>

                <div class="doc-callout info">
                    <div class="doc-callout-icon"><i class="fas fa-chart-bar"></i></div>
                    <div>
                        <strong>Comprehensive Coverage:</strong> All 8 reports cover complete operational needs
                        from collections to aging accounts to customer master lists.
                    </div>
                </div>
            </section>

            <hr>

            {{-- ============================================================
                 USER MANAGEMENT
                 ============================================================ --}}
            <section id="user-management">
                <h2>User Management (Admin)</h2>

                <p>
                    Manage system users and control access through role-based permissions.
                    Navigate to <strong>User Management</strong> to add and manage users.
                </p>

                <h3>Adding a New User</h3>

                <div class="doc-step">
                    <div class="doc-step-number">1</div>
                    <div class="doc-step-content">
                        <h4>Navigate to Add User</h4>
                        <p>Go to <strong>User Management → Add User</strong>.</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">2</div>
                    <div class="doc-step-content">
                        <h4>Fill in User Details</h4>
                        <p>Enter: Name, Email, Password, and select a Role. Optionally assign an Area (for meter readers).</p>
                    </div>
                </div>

                <div class="doc-step">
                    <div class="doc-step-number">3</div>
                    <div class="doc-step-content">
                        <h4>Save User</h4>
                        <p>Review details and click <strong>Save</strong> to create the account.</p>
                    </div>
                </div>

                <h3>Available Roles</h3>
                <ul>
                    <li><strong>Super Admin</strong> - Full system access, bypasses all permission checks</li>
                    <li><strong>Admin</strong> - User management + all features</li>
                    <li><strong>Billing Officer</strong> - Billing, payments, meter readings</li>
                    <li><strong>Meter Reader</strong> - Meter reading entry only</li>
                    <li><strong>Cashier</strong> - Payment processing only</li>
                    <li><strong>Viewer</strong> - Read-only access everywhere</li>
                </ul>

                <h3>Permission System</h3>
                <p>
                    Navigate to <strong>Admin Configuration → Access Control</strong> to manage:
                </p>
                <ul>
                    <li><strong>Roles</strong> - View and edit role configurations</li>
                    <li><strong>Permissions</strong> - 18 permissions across 8 modules</li>
                    <li><strong>Permission Matrix</strong> - Visual grid for bulk permission assignment</li>
                </ul>

                <div class="doc-callout warning">
                    <div class="doc-callout-icon"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <strong>Security Note:</strong> Permission changes take effect immediately and affect what
                        users can see and do. Exercise care when modifying access control settings.
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
                    Configure system parameters and settings. Accessible to users with administrative privileges.
                    Navigate to <strong>Admin Configuration</strong> for all settings.
                </p>

                <h3>Geographic Settings</h3>
                <p>
                    Configure the geographic hierarchy:
                </p>
                <ul>
                    <li><strong>Barangays</strong> - The 16 barangays of Initao (pre-configured)</li>
                    <li><strong>Areas (Zones)</strong> - Service zones for meter reader routing</li>
                    <li><strong>Puroks</strong> - Sub-village zones for detailed addressing</li>
                </ul>

                <h3>Billing Configuration</h3>
                <p>
                    Manage billing-related settings:
                </p>
                <ul>
                    <li><strong>Water Rates</strong> - Tiered pricing per account type</li>
                    <li><strong>Account Types</strong> - Residential, Commercial, etc.</li>
                    <li><strong>Charge Items</strong> - Fee templates for applications</li>
                    <li><strong>Bill Adjustment Types</strong> - Categories for adjustments</li>
                </ul>

                <h3>Rate Management</h3>
                <p>
                    Navigate to <strong>Rate</strong> to:
                </p>
                <ul>
                    <li>Create new billing periods (monthly)</li>
                    <li>Copy rates from previous period</li>
                    <li>Upload rates via CSV</li>
                    <li>Close periods (prevents changes)</li>
                    <li>Open periods (allows corrections)</li>
                </ul>

                <h3>Activity Log (Super Admin Only)</h3>
                <p>
                    Navigate to <strong>Activity Log</strong> to view audit trail of all significant system actions:
                </p>
                <ul>
                    <li>Who performed the action</li>
                    <li>What was changed</li>
                    <li>When it occurred</li>
                    <li>Complete details of changes</li>
                </ul>

                <div class="doc-callout info">
                    <div class="doc-callout-icon"><i class="fas fa-cog"></i></div>
                    <div>
                        <strong>Configuration Flexibility:</strong> All settings are admin-managed without
                        requiring developer intervention. Changes take effect immediately.
                    </div>
                </div>
            </section>

            <hr>

            <footer style="background: #f8f9fc; border-top: 1px solid #e2e8f0; margin-top: 60px; padding: 32px 0; color: #64748b; font-size: 0.85rem;">
                <div style="max-width: 900px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="margin: 0; font-weight: 600; color: #1e293b;">MEEDO Water Billing System</p>
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
