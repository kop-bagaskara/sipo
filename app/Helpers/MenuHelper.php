<?php

if (!function_exists('canAccessMenu')) {
    /**
     * Check if user can access a menu by menu_key
     */
    function canAccessMenu($menuKey, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        $menuSetting = \App\Models\MenuNavigationSetting::where('menu_key', $menuKey)
            ->where('is_active', true)
            ->first();

        if (!$menuSetting) {
            // Jika tidak ada setting, default allow (backward compatibility)
            return true;
        }

        return $menuSetting->canAccess($user);
    }
}

if (!function_exists('canAccessTrainingMenu')) {
    /**
     * Check if user can access training admin/coordinator menu
     * Based on master setting 'access_menu_training'
     * Returns true if user ID is in the allowed list, false otherwise
     */
    function canAccessTrainingMenu($user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        // Get setting value (comma-separated user IDs)
        $allowedUserIds = \App\Models\Setting::getValue('access_menu_training', '');

        if (empty($allowedUserIds)) {
            // Jika setting tidak ada, default deny (hanya user yang explicit di-set yang bisa akses)
            return false;
        }

        // Convert comma-separated string to array
        $allowedIds = array_map('trim', explode(',', $allowedUserIds));

        // Check if current user ID is in the allowed list
        return in_array($user->id, $allowedIds);
    }
}

