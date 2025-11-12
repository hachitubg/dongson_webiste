<?php
// Banner locations configuration
// Mỗi vị trí có code và tên hiển thị

define('BANNER_LOCATIONS', [
    'trang_chu' => 'Trang Chủ - Slider',
    'trang_chu_esg' => 'Trang Chủ - ESG Banner',
    'gioi_thieu' => 'Giới Thiệu - Banner',
    'gioi_thieu_1' => 'Giới Thiệu - Hình ảnh 1',
    'gioi_thieu_2' => 'Giới Thiệu - Hình ảnh 2',
    'gioi_thieu_icon' => 'Giới Thiệu - Icon',
    'san_pham' => 'Sản Phẩm - Banner',
    'tin_tuc' => 'Tin Tức - Banner',
    'lien_he' => 'Liên hệ - Banner',
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
