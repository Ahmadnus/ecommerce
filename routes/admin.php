<?php

use AppHttpControllersAdminDashboardController;
use AppHttpControllersAdminUserController;
use AppHttpControllersAdminProductController;
use AppHttpControllersAdminOrderController;
use AppHttpControllersAdminThemeController;
use AppHttpControllersAdminSettingsController;
use AppHttpControllersAdminLogoController;
use AppHttpControllersAdminApiController;
use IlluminateSupportFacadesRoute;


--------------------------------------------------------------------------
 Admin Routes
--------------------------------------------------------------------------
 All admin routes are prefixed with admin and protected by auth + admin middleware.
 The structure is modular — add new resource groups as the project grows.


Routeprefix('admin')-name('admin.')-middleware(['auth', 'admin'])-group(function () {

     ── Dashboard ────────────────────────────────────────────────────────────
    Routeget('',          [DashboardControllerclass, 'index'])-name('dashboard');
    Routeget('analytics', [DashboardControllerclass, 'analytics'])-name('analytics');

     ── Users ────────────────────────────────────────────────────────────────
    Routeresource('users', UserControllerclass);
    Routepatch('users{user}toggle-status', [UserControllerclass, 'toggleStatus'])-name('users.toggle-status');
    Routepatch('users{user}role',          [UserControllerclass, 'updateRole'])-name('users.update-role');

     ── Products ─────────────────────────────────────────────────────────────
    Routeresource('products', ProductControllerclass);
    Routepatch('products{product}toggle-active', [ProductControllerclass, 'toggleActive'])-name('products.toggle-active');
    Routepost('productsbulk-action',              [ProductControllerclass, 'bulkAction'])-name('products.bulk-action');

     ── Orders ───────────────────────────────────────────────────────────────
    Routeresource('orders', OrderControllerclass)-only(['index', 'show', 'update', 'destroy']);
    Routepatch('orders{order}status',  [OrderControllerclass, 'updateStatus'])-name('orders.update-status');
    Routeget('ordersexportcsv',        [OrderControllerclass, 'exportCsv'])-name('orders.export');

     ── Theme ────────────────────────────────────────────────────────────────
    Routeget('theme',            [ThemeControllerclass, 'index'])-name('theme.index');
    Routepost('theme',           [ThemeControllerclass, 'update'])-name('theme.update');
    Routepost('themereset',     [ThemeControllerclass, 'reset'])-name('theme.reset');
    Routeget('themepreview',    [ThemeControllerclass, 'preview'])-name('theme.preview');

     ── Logo ──────────────────────────────────────────────────────────────────
    Routepost('logoupload',  [LogoControllerclass, 'upload'])-name('logo.upload');
    Routedelete('logo',       [LogoControllerclass, 'destroy'])-name('logo.destroy');
    Routeget('logocurrent',  [LogoControllerclass, 'current'])-name('logo.current');

     ── Settings ──────────────────────────────────────────────────────────────
    Routeget('settings',          [SettingsControllerclass, 'index'])-name('settings.index');
    Routepost('settingsgeneral', [SettingsControllerclass, 'updateGeneral'])-name('settings.general');
    Routepost('settingsmail',    [SettingsControllerclass, 'updateMail'])-name('settings.mail');
    Routepost('settingscache',   [SettingsControllerclass, 'clearCache'])-name('settings.cache');

     ── API Management (future expansion) ─────────────────────────────────────
    Routeprefix('api-management')-name('api.')-group(function () {
        Routeget('',                     [ApiControllerclass, 'index'])-name('index');
        Routepost('generate-key',        [ApiControllerclass, 'generateKey'])-name('generate-key');
        Routedelete('revoke{keyId}',    [ApiControllerclass, 'revokeKey'])-name('revoke');
    });
});