<?php
/**
 * Plugin Name: Doctor Khaste (دکتر خسته)
 * Description: سیستم مدیریت تسک، تقویم شمسی، یادداشت تیمی ابسیدین و چت دو نفره با هماهنگی کامل تلگرام برای وردپرس.
 * Version: 2.0.0
 * Author: Jules
 * Text Domain: doctor-khaste
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'telegram.php';

// --- Activation & Database Table Creation ---
register_activation_hook(__FILE__, 'doctor_khaste_activate');

function doctor_khaste_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // 1. Tasks Table
    $table_tasks = $wpdb->prefix . 'doctor_khaste_tasks';
    $sql_tasks = "CREATE TABLE $table_tasks (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        description text,
        due_date date DEFAULT NULL,
        status varchar(50) DEFAULT 'pending' NOT NULL,
        priority varchar(50) DEFAULT 'normal' NOT NULL,
        created_by bigint(20) NOT NULL,
        assigned_to bigint(20) DEFAULT NULL,
        notified_1day tinyint(1) DEFAULT 0 NOT NULL,
        notified_due tinyint(1) DEFAULT 0 NOT NULL,
        notified_overdue tinyint(1) DEFAULT 0 NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY due_date (due_date)
    ) $charset_collate;";

    // 2. Notes Table
    $table_notes = $wpdb->prefix . 'doctor_khaste_notes';
    $sql_notes = "CREATE TABLE $table_notes (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        content longtext,
        color varchar(50) DEFAULT '#4fd1c5' NOT NULL,
        folder varchar(100) DEFAULT 'عمومی' NOT NULL,
        created_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY created_by (created_by)
    ) $charset_collate;";

    // 3. Messages Table
    $table_messages = $wpdb->prefix . 'doctor_khaste_messages';
    $sql_messages = "CREATE TABLE $table_messages (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        admin_id bigint(20) NOT NULL,
        content text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY created_at (created_at)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_tasks);
    dbDelta($sql_notes);
    dbDelta($sql_messages);

    // Register Daily Cron Job
    if (!wp_next_scheduled('doctor_khaste_daily_cron')) {
        wp_schedule_event(time(), 'daily', 'doctor_khaste_daily_cron');
    }

    // Flush rewrites to ensure standalone frontend is mapped properly instantly
    doctor_khaste_rewrite_rules();
    flush_rewrite_rules();
}

// Deactivation: Clean up scheduler & Flush rewrites
register_deactivation_hook(__FILE__, 'doctor_khaste_deactivate');
function doctor_khaste_deactivate() {
    wp_clear_scheduled_hook('doctor_khaste_daily_cron');
    flush_rewrite_rules();
}

// --- Check due tasks & Notify Daily ---
add_action('doctor_khaste_daily_cron', 'doctor_khaste_check_due_tasks_cron');
function doctor_khaste_check_due_tasks_cron() {
    global $wpdb;
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    $table_tasks = $wpdb->prefix . 'doctor_khaste_tasks';
    $tasks = $wpdb->get_results("SELECT * FROM $table_tasks WHERE status != 'done' AND due_date IS NOT NULL");

    foreach ($tasks as $t) {
        $recipients = array();

        if (!empty($t->assigned_to)) {
            $chat_id = get_user_meta($t->assigned_to, '_doctor_khaste_telegram_chat_id', true);
            if (!empty($chat_id)) $recipients[] = $chat_id;
        } else {
            $admins = get_users(array('role' => 'administrator'));
            foreach ($admins as $admin) {
                $chat_id = get_user_meta($admin->ID, '_doctor_khaste_telegram_chat_id', true);
                if (!empty($chat_id)) $recipients[] = $chat_id;
            }
        }

        if ($t->due_date === $tomorrow && !$t->notified_1day) {
            foreach ($recipients as $chat) doctor_khaste_tg_send($chat, "⏰ یادآوری: فردا موعد تسک «{$t->title}» است.");
            $wpdb->update($table_tasks, array('notified_1day' => 1), array('id' => $t->id));
        } elseif ($t->due_date === $today && !$t->notified_due) {
            foreach ($recipients as $chat) doctor_khaste_tg_send($chat, "📌 امروز موعد تسک «{$t->title}» است!");
            $wpdb->update($table_tasks, array('notified_due' => 1), array('id' => $t->id));
        } elseif ($t->due_date < $today && !$t->notified_overdue) {
            foreach ($recipients as $chat) doctor_khaste_tg_send($chat, "⚠️ تسک «{$t->title}» از موعد ({$t->due_date}) گذشته و هنوز انجام نشده.");
            $wpdb->update($table_tasks, array('notified_overdue' => 1), array('id' => $t->id));
        }
    }
}

// --- Routing & Page Serve (standalone frontend) ---
add_action('init', 'doctor_khaste_rewrite_rules');
function doctor_khaste_rewrite_rules() {
    add_rewrite_rule('^doctor-khaste/?$', 'index.php?doctor_khaste_trigger=1', 'top');
}

add_filter('query_vars', 'doctor_khaste_query_vars');
function doctor_khaste_query_vars($vars) {
    $vars[] = 'doctor_khaste_trigger';
    return $vars;
}

add_action('template_redirect', 'doctor_khaste_serve_frontend');
function doctor_khaste_serve_frontend() {
    if (get_query_var('doctor_khaste_trigger') == 1) {
        // Must be logged in
        if (!is_user_logged_in()) {
            auth_redirect();
            exit;
        }

        // Must be an administrator
        if (!current_user_can('administrator')) {
            wp_die(
                '<div style="text-align:center; padding: 50px; font-family: Tahoma, Arial, sans-serif; background:#070a13; color:#f43f5e; min-height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                    <h2 style="font-size:24px; margin-bottom:10px;">⚡ دسترسی غیر مجاز ⚡</h2>
                    <p style="color:#94a3b8; font-size:16px;">فقط کاربران با نقش مدیر کل (Administrator) اجازه دسترسی به این سیستم مدیریتی را دارند.</p>
                    <a href="' . wp_logout_url(home_url('/doctor-khaste')) . '" style="margin-top:20px; padding:10px 20px; background:#f43f5e; color:#fff; text-decoration:none; border-radius:8px; font-weight:bold;">خروج از حساب فعلی</a>
                 </div>',
                'دسترسی غیر مجاز'
            );
            exit;
        }

        // Serve beautiful standalone dashboard
        doctor_khaste_include_frontend_html();
        exit;
    }
}

// --- Add WordPress Admin Menu page ---
add_action('admin_menu', 'doctor_khaste_add_admin_menu');
function doctor_khaste_add_admin_menu() {
    add_menu_page(
        'دکتر خسته', // Page title
        'دکتر خسته 🩺', // Menu title
        'administrator', // Capability
        'doctor-khaste-dashboard', // Menu slug
        'doctor_khaste_render_admin_menu_page', // Callback function
        'dashicons-clipboard', // Icon url
        2 // Position
    );
}

function doctor_khaste_render_admin_menu_page() {
    ?>
    <div class="wrap" style="max-width: 800px; margin: 30px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-family: Tahoma, Arial, sans-serif; text-align: center; direction: rtl;">
        <span style="font-size: 52px; display: block; margin-bottom: 20px;">🩺</span>
        <h1 style="font-size: 28px; font-weight: 900; color: #1e293b; margin-bottom: 12px;">سیستم مدیریت خلاقانه «دکتر خسته»</h1>
        <p style="font-size: 15px; color: #64748b; line-height: 1.8; margin-bottom: 30px;">
            به بخش مدیریت همکاران و ادمین‌های پروژه خوش آمدید! هم‌اکنون می‌توانید اپلیکیشن مدیریت تسک، تقویم شمسی، یادداشت‌های تیمی ابسیدین و چت لحظه‌ای اختصاصی را به صورت ۱۰۰٪ مجزا و تمام‌صفحه باز کنید.
        </p>

        <div style="margin-bottom: 40px;">
            <a href="<?php echo esc_url(home_url('/doctor-khaste')); ?>" target="_blank" style="display: inline-block; padding: 14px 32px; background: #f59e0b; color: #111; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 16px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); transition: transform 0.2s;">
                🚀 ورود به اپلیکیشن اختصاصی (تمام صفحه)
            </a>
        </div>

        <div style="border-top: 1px solid #e2e8f0; padding-top: 30px; text-align: right;">
            <h3 style="font-size: 16px; color: #0f172a; margin-bottom: 12px; font-weight: bold;">💡 راهنمای سریع اتصال ربات تلگرام:</h3>
            <ul style="color: #475569; font-size: 14px; line-height: 2; list-style-type: decimal; padding-right: 20px;">
                <li>ابتدا یک ربات تلگرام از طریق <a href="https://t.me/BotFather" target="_blank" style="color:#f59e0b; font-weight:bold;">BotFather@</a> ایجاد کنید.</li>
                <li>وارد اپلیکیشن اختصاصی بالا شوید، به تب «تنظیمات» بروید و در بخش «تنظیمات پیشرفته»، توکن ربات و آیدی آن را ذخیره کنید.</li>
                <li>برای متصل شدن، ربات را استارت کنید تا آیدی عددی شما را نمایش دهد؛ سپس آن را کپی کرده و در بخش «ادمین‌ها -> ویرایش» برای خود ذخیره کنید!</li>
            </ul>
        </div>
    </div>
    <?php
}

// --- Setup REST API Routes ---
add_action('rest_api_init', 'doctor_khaste_register_routes');
function doctor_khaste_register_routes() {
    $namespace = 'doctor-khaste/v1';

    // Public / Tokenless telegram webhook route
    register_rest_route($namespace, '/telegram/webhook/(?P<secret>[a-zA-Z0-9_-]+)', array(
        'methods'             => 'POST',
        'callback'            => 'doctor_khaste_handle_tg_webhook_api',
        'permission_callback' => '__return_true',
    ));

    // Protected API endpoints
    $protected_routes = array(
        '/me' => array('GET' => 'doctor_khaste_get_me'),
        '/settings' => array(
            'GET'  => 'doctor_khaste_get_settings',
            'POST' => 'doctor_khaste_save_settings'
        ),
        '/messages' => array(
            'GET'  => 'doctor_khaste_get_messages',
            'POST' => 'doctor_khaste_save_message'
        ),
        '/admins' => array(
            'GET'  => 'doctor_khaste_get_admins',
            'POST' => 'doctor_khaste_create_admin'
        ),
        '/admins/(?P<id>\d+)' => array(
            'PUT'    => 'doctor_khaste_update_admin',
            'DELETE' => 'doctor_khaste_delete_admin'
        ),
        '/tasks' => array(
            'GET'  => 'doctor_khaste_get_tasks',
            'POST' => 'doctor_khaste_create_task'
        ),
        '/tasks/(?P<id>\d+)' => array(
            'PUT'    => 'doctor_khaste_update_task',
            'DELETE' => 'doctor_khaste_delete_task'
        ),
        '/notes' => array(
            'GET'  => 'doctor_khaste_get_notes',
            'POST' => 'doctor_khaste_create_note'
        ),
        '/notes/(?P<id>\d+)' => array(
            'PUT'    => 'doctor_khaste_update_note',
            'DELETE' => 'doctor_khaste_delete_note'
        ),
        '/upload' => array(
            'POST' => 'doctor_khaste_upload_file'
        )
    );

    foreach ($protected_routes as $route => $config) {
        $methods = array_keys($config);
        foreach ($methods as $m) {
            register_rest_route($namespace, $route, array(
                'methods'             => $m,
                'callback'            => $config[$m],
                'permission_callback' => 'doctor_khaste_api_permission',
            ));
        }
    }
}

// Permission verification callback
function doctor_khaste_api_permission() {
    return current_user_can('administrator');
}

// Webhook handling
function doctor_khaste_handle_tg_webhook_api($request) {
    $secret = $request->get_param('secret');
    $expected = get_option('doctor_khaste_TELEGRAM_WEBHOOK_SECRET');
    if (empty($expected) || $secret !== $expected) {
        return new WP_Error('unauthorized', 'Invalid webhook secret', array('status' => 401));
    }

    $body = $request->get_json_params();
    if (!empty($body)) {
        doctor_khaste_process_telegram_update($body);
    }
    return rest_ensure_response(array('ok' => true));
}

// 1. Get Me details
function doctor_khaste_get_me() {
    $current = wp_get_current_user();
    $color = get_user_meta($current->ID, '_doctor_khaste_color', true);
    if (empty($color)) $color = '#f2a154';

    $telegram_chat_id = get_user_meta($current->ID, '_doctor_khaste_telegram_chat_id', true);

    return rest_ensure_response(array(
        'admin' => array(
            'id'              => $current->ID,
            'username'        => $current->user_login,
            'name'            => $current->display_name ? $current->display_name : $current->user_login,
            'color'           => $color,
            'is_super'        => user_can($current->ID, 'administrator'),
            'telegram_linked' => !empty($telegram_chat_id),
            'telegram_chat_id'=> $telegram_chat_id ? $telegram_chat_id : null
        )
    ));
}

// 2. Settings Operations
function doctor_khaste_get_settings() {
    $keys = array(
        'TELEGRAM_BOT_TOKEN',
        'TELEGRAM_WEBHOOK_SECRET',
        'TELEGRAM_BOT_USERNAME'
    );
    $settings = array();
    foreach ($keys as $key) {
        $settings[$key] = get_option('doctor_khaste_' . $key, '');
    }
    return rest_ensure_response(array('settings' => $settings));
}

function doctor_khaste_save_settings($request) {
    $body = $request->get_json_params();
    $keys = array(
        'TELEGRAM_BOT_TOKEN',
        'TELEGRAM_WEBHOOK_SECRET',
        'TELEGRAM_BOT_USERNAME'
    );
    foreach ($keys as $key) {
        if (isset($body[$key])) {
            update_option('doctor_khaste_' . $key, trim($body[$key]));
        }
    }
    return rest_ensure_response(array('ok' => true));
}

// 3. Messages/Chat Operations
function doctor_khaste_get_messages() {
    global $wpdb;
    $table_messages = $wpdb->prefix . 'doctor_khaste_messages';

    $results = $wpdb->get_results("SELECT * FROM $table_messages ORDER BY created_at ASC LIMIT 100");
    $messages = array();
    foreach ($results as $m) {
        $user = get_userdata($m->admin_id);
        $name = $user ? ($user->display_name ? $user->display_name : $user->user_login) : 'نامشخص';
        $color = $user ? get_user_meta($user->ID, '_doctor_khaste_color', true) : '#f2a154';
        if (empty($color)) $color = '#f2a154';

        $messages[] = array(
            'id'           => (int)$m->id,
            'admin_id'     => (int)$m->admin_id,
            'content'      => $m->content,
            'created_at'   => $m->created_at,
            'sender_name'  => $name,
            'sender_color' => $color
        );
    }
    return rest_ensure_response(array('messages' => $messages));
}

function doctor_khaste_save_message($request) {
    global $wpdb;
    $body = $request->get_json_params();
    $content = isset($body['content']) ? trim($body['content']) : '';
    if (empty($content)) {
        return new WP_Error('bad_request', 'متن پیام نمی‌تواند خالی باشد.', array('status' => 400));
    }

    $table_messages = $wpdb->prefix . 'doctor_khaste_messages';
    $wpdb->insert($table_messages, array(
        'admin_id'   => get_current_user_id(),
        'content'    => $content,
        'created_at' => current_time('mysql')
    ));
    return rest_ensure_response(array('ok' => true));
}

// 4. Admins / WordPress Administrator Integration
function doctor_khaste_get_admins() {
    $admins = get_users(array('role' => 'administrator'));
    $list = array();
    foreach ($admins as $a) {
        $color = get_user_meta($a->ID, '_doctor_khaste_color', true);
        if (empty($color)) $color = '#4fd1c5';
        $telegram_chat_id = get_user_meta($a->ID, '_doctor_khaste_telegram_chat_id', true);

        $list[] = array(
            'id'               => $a->ID,
            'username'         => $a->user_login,
            'name'             => $a->display_name ? $a->display_name : $a->user_login,
            'color'            => $color,
            'is_super'         => true,
            'telegram_chat_id' => $telegram_chat_id ? $telegram_chat_id : null,
            'telegram_linked'  => !empty($telegram_chat_id),
            'created_at'       => $a->user_registered
        );
    }
    return rest_ensure_response(array('admins' => $list));
}

function doctor_khaste_create_admin($request) {
    $body = $request->get_json_params();
    $username = isset($body['username']) ? trim($body['username']) : '';
    $password = isset($body['password']) ? trim($body['password']) : '';
    $name = isset($body['name']) ? trim($body['name']) : '';
    $color = isset($body['color']) ? trim($body['color']) : '#4fd1c5';
    $telegram_chat_id = isset($body['telegram_chat_id']) ? trim($body['telegram_chat_id']) : '';

    if (empty($username) || empty($password) || empty($name)) {
        return new WP_Error('bad_request', 'نام کاربری، رمز عبور و نام الزامی است.', array('status' => 400));
    }

    if (username_exists($username)) {
        return new WP_Error('username_exists', 'این نام کاربری قبلاً استفاده شده است.', array('status' => 400));
    }

    $user_id = wp_create_user($username, $password);
    if (is_wp_error($user_id)) {
        return new WP_Error('creation_failed', $user_id->get_error_message(), array('status' => 500));
    }

    wp_update_user(array(
        'ID'           => $user_id,
        'display_name' => $name,
        'role'         => 'administrator'
    ));

    update_user_meta($user_id, '_doctor_khaste_color', $color);
    if (!empty($telegram_chat_id)) {
        update_user_meta($user_id, '_doctor_khaste_telegram_chat_id', $telegram_chat_id);
    }

    $user = get_userdata($user_id);
    return rest_ensure_response(array(
        'admin' => array(
            'id'               => $user_id,
            'username'         => $username,
            'name'             => $name,
            'color'            => $color,
            'is_super'         => true,
            'telegram_chat_id' => $telegram_chat_id ? $telegram_chat_id : null,
            'telegram_linked'  => !empty($telegram_chat_id),
            'created_at'       => $user->user_registered
        )
    ));
}

function doctor_khaste_update_admin($request) {
    $id = (int)$request->get_param('id');
    $body = $request->get_json_params();

    // Check if target user exists and has administrator role
    $user = get_userdata($id);
    if (!$user || !user_can($id, 'administrator')) {
        return new WP_Error('not_found', 'کاربر یافت نشد یا دسترسی مدیر ندارد.', array('status' => 404));
    }

    $update_data = array('ID' => $id);
    if (isset($body['name'])) {
        $update_data['display_name'] = trim($body['name']);
    }
    if (isset($body['password']) && !empty($body['password'])) {
        $update_data['user_pass'] = trim($body['password']);
    }

    wp_update_user($update_data);

    if (isset($body['color'])) {
        update_user_meta($id, '_doctor_khaste_color', trim($body['color']));
    }
    if (isset($body['telegram_chat_id'])) {
        update_user_meta($id, '_doctor_khaste_telegram_chat_id', trim($body['telegram_chat_id']));
    }

    $updated_user = get_userdata($id);
    $color = get_user_meta($id, '_doctor_khaste_color', true);
    if (empty($color)) $color = '#4fd1c5';
    $telegram_chat_id = get_user_meta($id, '_doctor_khaste_telegram_chat_id', true);

    return rest_ensure_response(array(
        'admin' => array(
            'id'               => $id,
            'username'         => $updated_user->user_login,
            'name'             => $updated_user->display_name ? $updated_user->display_name : $updated_user->user_login,
            'color'            => $color,
            'is_super'         => true,
            'telegram_chat_id' => $telegram_chat_id ? $telegram_chat_id : null,
            'telegram_linked'  => !empty($telegram_chat_id),
            'created_at'       => $updated_user->user_registered
        )
    ));
}

function doctor_khaste_delete_admin($request) {
    $id = (int)$request->get_param('id');
    if ($id === get_current_user_id()) {
        return new WP_Error('bad_request', 'نمی‌توانید خودتان را حذف کنید.', array('status' => 400));
    }

    require_once ABSPATH . 'wp-admin/includes/user.php';
    wp_delete_user($id);
    return rest_ensure_response(array('ok' => true));
}

// 5. Tasks Operations
function doctor_khaste_get_tasks() {
    global $wpdb;
    $table_tasks = $wpdb->prefix . 'doctor_khaste_tasks';
    $results = $wpdb->get_results("SELECT * FROM $table_tasks ORDER BY (due_date IS NULL), due_date ASC, created_at DESC");

    $tasks = array();
    foreach ($results as $t) {
        $creator = get_userdata($t->created_by);
        $creator_name = $creator ? ($creator->display_name ? $creator->display_name : $creator->user_login) : 'نامشخص';
        $creator_color = $creator ? get_user_meta($creator->ID, '_doctor_khaste_color', true) : '#f2a154';
        if (empty($creator_color)) $creator_color = '#f2a154';

        $assignee_name = '';
        $assignee_color = '';
        if ($t->assigned_to) {
            $assignee = get_userdata($t->assigned_to);
            if ($assignee) {
                $assignee_name = $assignee->display_name ? $assignee->display_name : $assignee->user_login;
                $assignee_color = get_user_meta($assignee->ID, '_doctor_khaste_color', true);
            }
        }
        if (empty($assignee_color)) $assignee_color = '#4fd1c5';

        $tasks[] = array(
            'id'             => (int)$t->id,
            'title'          => $t->title,
            'description'    => $t->description,
            'due_date'       => $t->due_date ? $t->due_date : null,
            'status'         => $t->status,
            'priority'       => $t->priority,
            'created_by'     => (int)$t->created_by,
            'assigned_to'    => $t->assigned_to ? (int)$t->assigned_to : null,
            'created_at'     => $t->created_at,
            'updated_at'     => $t->updated_at,
            'creator_name'   => $creator_name,
            'creator_color'  => $creator_color,
            'assignee_name'  => $assignee_name,
            'assignee_color' => $assignee_color
        );
    }
    return rest_ensure_response(array('tasks' => $tasks));
}

function doctor_khaste_create_task($request) {
    global $wpdb;
    $body = $request->get_json_params();
    $title = isset($body['title']) ? trim($body['title']) : '';
    if (empty($title)) {
        return new WP_Error('bad_request', 'عنوان تسک الزامی است.', array('status' => 400));
    }

    $description = isset($body['description']) ? trim($body['description']) : '';
    $due_date = isset($body['due_date']) && !empty($body['due_date']) ? trim($body['due_date']) : null;
    $priority = isset($body['priority']) ? trim($body['priority']) : 'normal';
    $assigned_to = isset($body['assigned_to']) && !empty($body['assigned_to']) ? (int)$body['assigned_to'] : null;

    $table_tasks = $wpdb->prefix . 'doctor_khaste_tasks';
    $current_user_id = get_current_user_id();

    $wpdb->insert($table_tasks, array(
        'title'       => $title,
        'description' => $description,
        'due_date'    => $due_date,
        'priority'    => $priority,
        'created_by'  => $current_user_id,
        'assigned_to' => $assigned_to,
        'created_at'  => current_time('mysql'),
        'updated_at'  => current_time('mysql')
    ));

    $taskId = $wpdb->insert_id;
    $me = wp_get_current_user();
    $me_name = $me->display_name ? $me->display_name : $me->user_login;

    // Send Interactive Telegram Keyboard Alert
    $shamsiDateText = $due_date ? doctor_khaste_gregorian_to_shamsi($due_date) : '';
    $notifyText = "🆕 <b>تسک جدید توسط {$me_name}:</b>\n«{$title}»" . ($shamsiDateText ? "\nموعد: " . $shamsiDateText : '');
    $replyMarkup = array(
        'inline_keyboard' => array(
            array(
                array('text' => '✅ تکمیل تسک', 'callback_data' => "complete:{$taskId}"),
                array('text' => '🔄 در حال انجام', 'callback_data' => "inprogress:{$taskId}")
            ),
            array(
                array('text' => '🙋‍♂️ واگذاری به من', 'callback_data' => "assign:{$taskId}")
            )
        )
    );

    $chatTarget = null;
    if ($assigned_to) {
        $chatTarget = get_user_meta($assigned_to, '_doctor_khaste_telegram_chat_id', true);
    }
    if (!empty($chatTarget)) {
        doctor_khaste_tg_send_inline($chatTarget, $notifyText, $replyMarkup);
    } else {
        doctor_khaste_notify_all_admins_inline($notifyText, $replyMarkup, $current_user_id);
    }

    // Register Chat Log Alert
    $shamsiDateLabel = $due_date ? doctor_khaste_gregorian_to_shamsi($due_date) : 'بدون موعد';
    $priorityLabelMap = array('urgent' => 'فوری', 'high' => 'بالا', 'normal' => 'عادی', 'low' => 'کم');
    $pLabel = isset($priorityLabelMap[$priority]) ? $priorityLabelMap[$priority] : 'عادی';
    $activityContent = "🆕 <b>تسک جدید ایجاد شد:</b>\n«{$title}»\n📅 موعد: {$shamsiDateLabel} | ⚡ اولویت: {$pLabel}";

    $table_messages = $wpdb->prefix . 'doctor_khaste_messages';
    $wpdb->insert($table_messages, array(
        'admin_id'   => $current_user_id,
        'content'    => $activityContent,
        'created_at' => current_time('mysql')
    ));

    return rest_ensure_response(array('id' => $taskId));
}

function doctor_khaste_update_task($request) {
    global $wpdb;
    $id = (int)$request->get_param('id');
    $body = $request->get_json_params();

    $table_tasks = $wpdb->prefix . 'doctor_khaste_tasks';
    $oldTask = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_tasks WHERE id = %d", $id));
    if (!$oldTask) {
        return new WP_Error('not_found', 'تسک یافت نشد.', array('status' => 404));
    }

    $update = array();
    if (isset($body['title'])) $update['title'] = trim($body['title']);
    if (isset($body['description'])) $update['description'] = trim($body['description']);
    if (isset($body['status'])) $update['status'] = trim($body['status']);
    if (isset($body['priority'])) $update['priority'] = trim($body['priority']);
    if (isset($body['assigned_to'])) $update['assigned_to'] = $body['assigned_to'] ? (int)$body['assigned_to'] : null;
    if (isset($body['due_date'])) {
        $update['due_date'] = $body['due_date'] ? trim($body['due_date']) : null;
        $update['notified_1day'] = 0;
        $update['notified_due'] = 0;
        $update['notified_overdue'] = 0;
    }
    $update['updated_at'] = current_time('mysql');

    $wpdb->update($table_tasks, $update, array('id' => $id));

    $me = wp_get_current_user();
    $me_name = $me->display_name ? $me->display_name : $me->user_login;

    // Log status changes to Workspace chat
    if (isset($body['status']) && $body['status'] !== $oldTask->status) {
        $statusLabels = array('pending' => 'در انتظار', 'in_progress' => 'در حال انجام', 'done' => 'انجام‌شده');
        $sLabel = isset($statusLabels[$body['status']]) ? $statusLabels[$body['status']] : $body['status'];
        $activityContent = "🔄 وضعیت تسک «{$oldTask->title}» توسط {$me_name} به <b>[{$sLabel}]</b> تغییر یافت.";

        $table_messages = $wpdb->prefix . 'doctor_khaste_messages';
        $wpdb->insert($table_messages, array(
            'admin_id'   => $me->ID,
            'content'    => $activityContent,
            'created_at' => current_time('mysql')
        ));
    }

    if (isset($body['status']) && $body['status'] === 'done') {
        doctor_khaste_notify_all_admins("✅ تسک «{$oldTask->title}» توسط {$me_name} تکمیل شد.", $me->ID);
    }

    return rest_ensure_response(array('ok' => true));
}

function doctor_khaste_delete_task($request) {
    global $wpdb;
    $id = (int)$request->get_param('id');
    $table_tasks = $wpdb->prefix . 'doctor_khaste_tasks';
    $wpdb->delete($table_tasks, array('id' => $id));
    return rest_ensure_response(array('ok' => true));
}

// 6. Notes Operations
function doctor_khaste_get_notes() {
    global $wpdb;
    $table_notes = $wpdb->prefix . 'doctor_khaste_notes';
    $results = $wpdb->get_results("SELECT * FROM $table_notes ORDER BY created_at DESC");

    $notes = array();
    foreach ($results as $n) {
        $creator = get_userdata($n->created_by);
        $creator_name = $creator ? ($creator->display_name ? $creator->display_name : $creator->user_login) : 'نامشخص';
        $creator_color = $creator ? get_user_meta($creator->ID, '_doctor_khaste_color', true) : '#f2a154';
        if (empty($creator_color)) $creator_color = '#f2a154';

        $notes[] = array(
            'id'            => (int)$n->id,
            'title'         => $n->title,
            'content'       => $n->content,
            'color'         => $n->color,
            'folder'        => $n->folder,
            'created_by'    => (int)$n->created_by,
            'created_at'    => $n->created_at,
            'updated_at'    => $n->updated_at,
            'creator_name'  => $creator_name,
            'creator_color' => $creator_color
        );
    }
    return rest_ensure_response(array('notes' => $notes));
}

function doctor_khaste_create_note($request) {
    global $wpdb;
    $body = $request->get_json_params();
    $title = isset($body['title']) ? trim($body['title']) : '';
    if (empty($title)) {
        return new WP_Error('bad_request', 'عنوان یادداشت الزامی است.', array('status' => 400));
    }

    $content = isset($body['content']) ? trim($body['content']) : '';
    $color = isset($body['color']) ? trim($body['color']) : '#4fd1c5';
    $folder = isset($body['folder']) ? trim($body['folder']) : 'عمومی';

    $table_notes = $wpdb->prefix . 'doctor_khaste_notes';
    $wpdb->insert($table_notes, array(
        'title'      => $title,
        'content'    => $content,
        'color'      => $color,
        'folder'     => $folder,
        'created_by' => get_current_user_id(),
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ));
    return rest_ensure_response(array('id' => $wpdb->insert_id));
}

function doctor_khaste_update_note($request) {
    global $wpdb;
    $id = (int)$request->get_param('id');
    $body = $request->get_json_params();

    $table_notes = $wpdb->prefix . 'doctor_khaste_notes';
    $update = array();
    if (isset($body['title'])) $update['title'] = trim($body['title']);
    if (isset($body['content'])) $update['content'] = trim($body['content']);
    if (isset($body['color'])) $update['color'] = trim($body['color']);
    if (isset($body['folder'])) $update['folder'] = trim($body['folder']);
    $update['updated_at'] = current_time('mysql');

    $wpdb->update($table_notes, $update, array('id' => $id));
    return rest_ensure_response(array('ok' => true));
}

function doctor_khaste_delete_note($request) {
    global $wpdb;
    $id = (int)$request->get_param('id');
    $table_notes = $wpdb->prefix . 'doctor_khaste_notes';
    $wpdb->delete($table_notes, array('id' => $id));
    return rest_ensure_response(array('ok' => true));
}

// 7. Base64 Upload File directly into local uploads with extension/MIME verification
function doctor_khaste_upload_file($request) {
    $body = $request->get_json_params();
    $filename = isset($body['filename']) ? trim($body['filename']) : '';
    $dataBase64 = isset($body['dataBase64']) ? trim($body['dataBase64']) : '';

    if (empty($filename) || empty($dataBase64)) {
        return new WP_Error('bad_request', 'فایل نامعتبر است.', array('status' => 400));
    }

    // Sanitize extension and check allowed ones (images only to prevent RCE)
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'svg', 'webp');
    if (!in_array($ext, $allowed_extensions)) {
        return new WP_Error('invalid_extension', 'فقط بارگذاری فرمت‌های تصویری مجاز است.', array('status' => 400));
    }

    if (strpos($dataBase64, ',') !== false) {
        $parts = explode(',', $dataBase64);
        $dataBase64 = $parts[1];
    }

    $decodedData = base64_decode($dataBase64);
    if ($decodedData === false) {
        return new WP_Error('bad_request', 'فایل نامعتبر است.', array('status' => 400));
    }

    $upload_dir = wp_upload_dir();
    $target_dir = $upload_dir['basedir'] . '/doctor-khaste';
    if (!file_exists($target_dir)) {
        wp_mkdir_p($target_dir);
    }

    $safe_name = time() . '-' . sanitize_file_name($filename);
    $target_file = $target_dir . '/' . $safe_name;

    if (file_put_contents($target_file, $decodedData) === false) {
        return new WP_Error('write_failed', 'خطا در ذخیره‌سازی فایل.', array('status' => 500));
    }

    $url = $upload_dir['baseurl'] . '/doctor-khaste/' . $safe_name;
    return rest_ensure_response(array('url' => $url));
}

// Include standalone frontend file
function doctor_khaste_include_frontend_html() {
    include plugin_dir_path(__FILE__) . 'frontend-dashboard.php';
}
