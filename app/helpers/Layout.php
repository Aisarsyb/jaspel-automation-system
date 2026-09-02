<?php
declare(strict_types=1);

class Layout {
    /**
     * Render the opening HTML tags and sidebar/header.
     */
    public static function start(string $title, string $activeMenu = 'dashboard'): void {
        Session::requireLogin();
        $username = Session::getUsername() ?? 'admin';
        $appName = defined('APP_NAME') ? APP_NAME : 'RSGM Jaspel';
        $companyName = defined('COMPANY_NAME') ? COMPANY_NAME : 'RSGM Universitas Jember';
        
        // Define base url path dynamically
        $baseUrl = '/Excel_Automation_System/';

        // Calculate active menu state classes
        $actDashboard = $activeMenu === 'dashboard' ? 'active' : '';
        $actDepts     = $activeMenu === 'departments' ? 'active' : '';
        $actDpjp      = $activeMenu === 'master-dpjp' ? 'active' : '';
        $actImport    = $activeMenu === 'import' ? 'active' : '';
        $actHistory   = $activeMenu === 'history' ? 'active' : '';
        $actSettings  = $activeMenu === 'settings' ? 'active' : '';

        echo <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} | {$appName}</title>
    <link rel="stylesheet" href="{$baseUrl}assets/css/style.css">
    <script src="{$baseUrl}assets/js/app.js" defer></script>
</head>
<body>
<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-logo">J</div>
            <div class="sidebar-brand-name">{$appName}</div>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item {$actDashboard}">
                <a href="{$baseUrl}modules/dashboard/index.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                    Dashboard
                </a>
            </li>
            <li class="sidebar-menu-item {$actDepts}">
                <a href="{$baseUrl}modules/departments/index.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                    Departemen
                </a>
            </li>
            <li class="sidebar-menu-item {$actDpjp}">
                <a href="{$baseUrl}modules/master-dpjp/index.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Master DPJP
                </a>
            </li>
            <li class="sidebar-menu-item {$actImport}">
                <a href="{$baseUrl}modules/import/index.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Import Excel
                </a>
            </li>
            <li class="sidebar-menu-item {$actHistory}">
                <a href="{$baseUrl}modules/history/index.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Riwayat Import
                </a>
            </li>
            <li class="sidebar-menu-item {$actSettings}">
                <a href="{$baseUrl}modules/settings/index.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Settings & Health
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="{$baseUrl}logout.php" class="btn btn-secondary" style="width: 100%;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Keluar
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Header -->
        <header class="header">
            <div class="header-title">{$title}</div>
            <div class="header-user">
                <span style="font-size: 13.5px; color: var(--text-secondary);">{$companyName}</span>
                <div class="user-profile">
                    <div class="user-avatar">A</div>
                    <span class="user-name">{$username}</span>
                </div>
            </div>
        </header>
        <!-- Content -->
        <main class="content-container">
HTML;
    }

    /**
     * Render the closing HTML tags and import JS.
     */
    public static function end(): void {
        $baseUrl = '/Excel_Automation_System/';
        echo <<<HTML
        </main>
    </div>
</div>
</body>
</html>
HTML;
    }
}
