<?php
// Banner locations configuration
// Mỗi vị trí có code và tên hiển thị

define('BANNER_LOCATIONS', [
    'trang_chu' => 'Trang Chủ - Banner Chính',
]);

/**
 * Get location name by code
 */
function getBannerLocationName($code) {
    $locations = BANNER_LOCATIONS;
    return $locations[$code] ?? $code;
}

/**
 * Get all banner locations
 */
function getAllBannerLocations() {
    return BANNER_LOCATIONS;
}
