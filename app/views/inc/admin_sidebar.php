<?php if(!isset($_SESSION)) session_start();
// Determine current route (uses rewrite to pass via ?url=controller/method)
$raw = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$segments = $raw === '' ? [] : explode('/', $raw);
$controller = isset($segments[0]) ? $segments[0] : '';
$method = isset($segments[1]) ? $segments[1] : '';
// Helper to mark active
function _is_active($c, $m = null) {
    global $controller, $method;
    if ($m === null) {
        return $controller === $c;
    }
    return $controller === $c && $method === $m;
}
?>
<nav class="admin-sidebar" aria-label="Administration sidebar">
    <div class="sidebar-header">
        <div class="sidebar-top">
            <h4><?php echo SITENAME; ?></h4>
            <button id="sidebarToggle" class="btn btn-sm btn-outline-light ms-2" aria-label="Toggle sidebar"></button>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li class="<?php echo (_is_active('admin','dashboard') || (_is_active('admin') && $method==='')) ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/admin/dashboard"><span class="sidebar-icon"></span><span class="menu-text">Dashboard</span></a>
        </li>
        <li class="<?php echo (_is_active('admin','beneficiaries')) ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/admin/beneficiaries"><span class="sidebar-icon"></span><span class="menu-text">Beneficiaries</span></a>
        </li>
        <li class="<?php echo (_is_active('admin','importJobs')) ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/admin/importJobs"><span class="sidebar-icon"></span><span class="menu-text">Import Jobs</span></a>
        </li>
        <li class="<?php echo (_is_active('admin','users')) ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/admin/users"><span class="sidebar-icon"></span><span class="menu-text">Users</span></a>
        </li>
        <li class="<?php echo (_is_active('admin','settings')) ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/admin/settings"><span class="sidebar-icon"></span><span class="menu-text">Settings</span></a>
        </li>
        <li class="<?php echo (_is_active('admin','reports')) ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/admin/reports"><span class="sidebar-icon"></span><span class="menu-text">Reports</span></a>
        </li>
        <li>
            <a href="<?php echo URLROOT; ?>/users/logout"><span class="sidebar-icon"></span><span class="menu-text">Logout</span></a>
        </li>
    </ul>
</nav>