<?php
/**
 * Script tự động tạo 5 tài khoản đăng nhập (user1 - user5)
 * Dự án: TranCaoTrong_CMS
 * 
 * Hướng dẫn sử dụng:
 * 1. Chạy qua trình duyệt: http://localhost/TranCaoTrong_CMS/create_users.php
 * 2. Chạy qua CLI: php create_users.php
 */

require_once __DIR__ . '/wp-load.php';

// Danh sách 5 tài khoản cần tạo
$users_to_create = [
    [
        'username'     => 'user1',
        'password'     => 'User@123456',
        'email'        => 'user1@example.com',
        'display_name' => 'Người Dùng 1 (Admin)',
        'role'         => 'administrator', // Quản trị viên
        'role_name'    => 'Quản trị viên (Administrator)'
    ],
    [
        'username'     => 'user2',
        'password'     => 'User@123456',
        'email'        => 'user2@example.com',
        'display_name' => 'Người Dùng 2 (Editor)',
        'role'         => 'editor', // Biên tập viên
        'role_name'    => 'Biên tập viên (Editor)'
    ],
    [
        'username'     => 'user3',
        'password'     => 'User@123456',
        'email'        => 'user3@example.com',
        'display_name' => 'Người Dùng 3 (Author)',
        'role'         => 'author', // Tác giả
        'role_name'    => 'Tác giả (Author)'
    ],
    [
        'username'     => 'user4',
        'password'     => 'User@123456',
        'email'        => 'user4@example.com',
        'display_name' => 'Người Dùng 4 (Contributor)',
        'role'         => 'contributor', // Cộng tác viên
        'role_name'    => 'Cộng tác viên (Contributor)'
    ],
    [
        'username'     => 'user5',
        'password'     => 'User@123456',
        'email'        => 'user5@example.com',
        'display_name' => 'Người Dùng 5 (Subscriber)',
        'role'         => 'subscriber', // Thành viên đăng ký
        'role_name'    => 'Thành viên (Subscriber)'
    ],
];

$results = [];

foreach ($users_to_create as $index => $u) {
    $user_id = username_exists($u['username']);
    
    if (!$user_id) {
        // Tạo người dùng mới
        $user_id = wp_create_user($u['username'], $u['password'], $u['email']);
        
        if (!is_wp_error($user_id)) {
            // Cập nhật display name và role
            wp_update_user([
                'ID'           => $user_id,
                'display_name' => $u['display_name'],
                'nickname'     => $u['display_name'],
                'role'         => $u['role']
            ]);
            $action = 'Tạo mới thành công';
            $success = true;
        } else {
            $action = 'Lỗi: ' . $user_id->get_error_message();
            $success = false;
        }
    } else {
        // Nếu user đã tồn tại, cập nhật lại mật khẩu và vai trò để đảm bảo đăng nhập được
        wp_set_password($u['password'], $user_id);
        wp_update_user([
            'ID'           => $user_id,
            'user_email'   => $u['email'],
            'display_name' => $u['display_name'],
            'role'         => $u['role']
        ]);
        $action = 'Đã tồn tại (Đã cập nhật mật khẩu & quyền)';
        $success = true;
    }

    $results[] = [
        'stt'          => $index + 1,
        'username'     => $u['username'],
        'password'     => $u['password'],
        'email'        => $u['email'],
        'display_name' => $u['display_name'],
        'role'         => $u['role_name'],
        'user_id'      => is_numeric($user_id) ? $user_id : 'N/A',
        'action'       => $action,
        'success'      => $success,
    ];
}

// Kiểm tra môi trường chạy (CLI hay Web Browser)
$is_cli = (php_sapi_name() === 'cli' || defined('STDIN'));

echo "Đã tạo/cập nhật thành công 5 tài khoản (user1 - user5)!";

