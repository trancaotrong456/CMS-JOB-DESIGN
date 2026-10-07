<?php
/**
 * Tự động tạo 3 User chuẩn tên theo yêu cầu bài thi/bài tập:
 * 1. cms_read  - Thành viên (Subscriber)
 * 2. cms_write - Biên tập viên (Editor)
 * 3. cms_admin - Quản trị viên (Administrator)
 */

require_once __DIR__ . '/wp-load.php';

$required_users = [
    [
        'username'     => 'cms_read',
        'password'     => 'User@123456',
        'email'        => 'cms_read@example.com',
        'display_name' => 'cms_read',
        'role'         => 'subscriber',
        'role_title'   => 'Thành viên (Subscriber)'
    ],
    [
        'username'     => 'cms_write',
        'password'     => 'User@123456',
        'email'        => 'cms_write@example.com',
        'display_name' => 'cms_write',
        'role'         => 'editor',
        'role_title'   => 'Biên tập viên (Editor)'
    ],
    [
        'username'     => 'cms_admin',
        'password'     => 'User@123456',
        'email'        => 'cms_admin@example.com',
        'display_name' => 'cms_admin',
        'role'         => 'administrator',
        'role_title'   => 'Quản trị viên (Administrator)'
    ]
];

echo "<h2>Kết quả khởi tạo 3 tài khoản chuẩn:</h2><ul>";

foreach ($required_users as $u) {
    $user_id = username_exists($u['username']);
    
    if (!$user_id) {
        $user_id = wp_create_user($u['username'], $u['password'], $u['email']);
        if (!is_wp_error($user_id)) {
            wp_update_user([
                'ID'           => $user_id,
                'display_name' => $u['display_name'],
                'role'         => $u['role']
            ]);
            echo "<li><strong>" . htmlspecialchars($u['username']) . "</strong>: Đã tạo mới thành công - Vai trò: " . htmlspecialchars($u['role_title']) . "</li>";
        } else {
            echo "<li><strong>" . htmlspecialchars($u['username']) . "</strong>: Lỗi khi tạo - " . htmlspecialchars($user_id->get_error_message()) . "</li>";
        }
    } else {
        // Cập nhật lại mật khẩu và quyền đúng chuẩn
        wp_set_password($u['password'], $user_id);
        wp_update_user([
            'ID'           => $user_id,
            'user_email'   => $u['email'],
            'display_name' => $u['display_name'],
            'role'         => $u['role']
        ]);
        echo "<li><strong>" . htmlspecialchars($u['username']) . "</strong>: Đã tồn tại (đã cập nhật đúng vai trò: " . htmlspecialchars($u['role_title']) . ")</li>";
    }
}

echo "</ul>";
echo "<p>Mật khẩu mặc định cho cả 3 user là: <code>User@123456</code></p>";
