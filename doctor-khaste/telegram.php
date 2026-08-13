<?php
/**
 * Telegram integration for Doctor Khaste
 */

if (!defined('ABSPATH')) {
    exit;
}

// Helper to convert Gregorian to Jalali (Shamsi) in Persian
function doctor_khaste_gregorian_to_shamsi($gDateStr) {
    if (empty($gDateStr)) return '';
    try {
        $parts = explode('-', $gDateStr);
        if (count($parts) < 3) return $gDateStr;

        $gy = (int)$parts[0];
        $gm = (int)$parts[1];
        $gd = (int)$parts[2];

        list($jy, $jm, $jd) = doctor_khaste_gregorian_to_jalali($gy, $gm, $gd);
        return "$jy/$jm/$jd";
    } catch (Exception $e) {
        return $gDateStr;
    }
}

function doctor_khaste_gregorian_to_jalali($gy, $gm, $gd) {
    $g_d_m = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 335);
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $g_days_in = 365 * $gy + (int)(($gy2 + 3) / 4) - (int)(($gy2 + 99) / 100) + (int)(($gy2 + 399) / 400) - 80 + $gd + $g_d_m[$gm - 1];
    $jy = 979 + 33 * (int)($g_days_in / 12053);
    $g_days_in %= 12053;
    $jy += 8 * (int)($g_days_in / 2951);
    $g_days_in %= 2951;
    $jy += (int)($g_days_in / 365);
    $g_days_in %= 365;
    $jy_day = $g_days_in + 1;

    if ($jy_day < 187) {
        $jm = 1 + (int)(($jy_day - 1) / 31);
        $jd = 1 + (($jy_day - 1) % 31);
    } else {
        $jm = 7 + (int)(($jy_day - 187) / 30);
        $jd = 1 + (($jy_day - 187) % 30);
    }
    return array($jy, $jm, $jd);
}

// Send simple text message
function doctor_khaste_tg_send($chatId, $text) {
    $botToken = get_option('doctor_khaste_TELEGRAM_BOT_TOKEN');
    if (empty($chatId) || empty($botToken)) return;

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    wp_remote_post($url, array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => json_encode(array(
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML'
        )),
    ));
}

// Send message with standard custom Reply Keyboard
function doctor_khaste_tg_send_with_keyboard($chatId, $text, $replyMarkup) {
    $botToken = get_option('doctor_khaste_TELEGRAM_BOT_TOKEN');
    if (empty($chatId) || empty($botToken)) return;

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    wp_remote_post($url, array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => json_encode(array(
            'chat_id'      => $chatId,
            'text'         => $text,
            'parse_mode'   => 'HTML',
            'reply_markup' => $replyMarkup
        )),
    ));
}

// Send message with inline buttons
function doctor_khaste_tg_send_inline($chatId, $text, $replyMarkup) {
    $botToken = get_option('doctor_khaste_TELEGRAM_BOT_TOKEN');
    if (empty($chatId) || empty($botToken)) return;

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    wp_remote_post($url, array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => json_encode(array(
            'chat_id'      => $chatId,
            'text'         => $text,
            'parse_mode'   => 'HTML',
            'reply_markup' => $replyMarkup
        )),
    ));
}

// Answer callback query (popups)
function doctor_khaste_answer_callback_query($callbackQueryId, $text) {
    $botToken = get_option('doctor_khaste_TELEGRAM_BOT_TOKEN');
    if (empty($botToken)) return;

    $url = "https://api.telegram.org/bot{$botToken}/answerCallbackQuery";
    wp_remote_post($url, array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => json_encode(array(
            'callback_query_id' => $callbackQueryId,
            'text'              => $text,
            'show_alert'        => true
        )),
    ));
}

// Edit existing message text (for dynamic button updates)
function doctor_khaste_edit_message_text($chatId, $messageId, $text) {
    $botToken = get_option('doctor_khaste_TELEGRAM_BOT_TOKEN');
    if (empty($botToken)) return;

    $url = "https://api.telegram.org/bot{$botToken}/editMessageText";
    wp_remote_post($url, array(
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => json_encode(array(
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'text'       => $text,
            'parse_mode' => 'HTML'
        )),
    ));
}

// Notify all system administrators
function doctor_khaste_notify_all_admins($text, $excludeAdminId = null) {
    $admins = get_users(array('role' => 'administrator'));
    foreach ($admins as $admin) {
        if ($excludeAdminId && $admin->ID == $excludeAdminId) continue;
        $chatId = get_user_meta($admin->ID, '_doctor_khaste_telegram_chat_id', true);
        if (!empty($chatId)) {
            doctor_khaste_tg_send($chatId, $text);
        }
    }
}

// Notify all system administrators with inline keyboard buttons
function doctor_khaste_notify_all_admins_inline($text, $replyMarkup, $excludeAdminId = null) {
    $admins = get_users(array('role' => 'administrator'));
    foreach ($admins as $admin) {
        if ($excludeAdminId && $admin->ID == $excludeAdminId) continue;
        $chatId = get_user_meta($admin->ID, '_doctor_khaste_telegram_chat_id', true);
        if (!empty($chatId)) {
            doctor_khaste_tg_send_inline($chatId, $text, $replyMarkup);
        }
    }
}

// Main Telegram webhook handling callback
function doctor_khaste_process_telegram_update($update) {
    global $wpdb;

    $replyKeyboard = array(
        'keyboard' => array(
            array(array('text' => '📋 تسک‌های باز')),
            array(array('text' => '🗒️ یادداشت جدید'), array('text' => '💬 پیام جدید'))
        ),
        'resize_keyboard' => true,
        'persistent'      => true
    );

    // 1. Inline Buttons Actions (Callback Queries)
    if (isset($update['callback_query'])) {
        $cb = $update['callback_query'];
        $fromId = $cb['from']['id'];
        $data = $cb['data'];
        $callbackQueryId = $cb['id'];

        // Find admin by telegram_chat_id meta key
        $users = get_users(array(
            'meta_key'   => '_doctor_khaste_telegram_chat_id',
            'meta_value' => (string)$fromId,
            'number'     => 1
        ));
        $admin = !empty($users) ? $users[0] : null;

        if (!$admin) {
            doctor_khaste_answer_callback_query($callbackQueryId, "❌ حساب تلگرام شما هنوز در داشبورد ست نشده است. آیدی شما: {$fromId}");
            return;
        }

        $parts = explode(':', $data);
        $action = $parts[0];
        $taskId = (int)$parts[1];

        $table_tasks = $wpdb->prefix . 'doctor_khaste_tasks';
        $task = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_tasks WHERE id = %d", $taskId));
        if (!$task) {
            doctor_khaste_answer_callback_query($callbackQueryId, '❌ این تسک یافت نشد یا ممکن است حذف شده باشد.');
            return;
        }

        $admin_name = $admin->display_name ? $admin->display_name : $admin->user_login;

        if ($action === 'complete') {
            $wpdb->update($table_tasks, array('status' => 'done', 'updated_at' => current_time('mysql')), array('id' => $taskId));
            doctor_khaste_answer_callback_query($callbackQueryId, '✅ تسک تکمیل و ثبت شد.');
            doctor_khaste_edit_message_text($cb['message']['chat']['id'], $cb['message']['message_id'], "✅ <b>تسک تکمیل شد:</b>\n<s>{$task->title}</s>\n\nتکمیل‌کننده: <b>{$admin_name}</b>");
        } else if ($action === 'inprogress') {
            $wpdb->update($table_tasks, array('status' => 'in_progress', 'updated_at' => current_time('mysql')), array('id' => $taskId));
            doctor_khaste_answer_callback_query($callbackQueryId, '🔄 تسک به وضعیت در حال انجام تغییر کرد.');
            doctor_khaste_edit_message_text($cb['message']['chat']['id'], $cb['message']['message_id'], "🔄 <b>تسک در حال انجام است:</b>\n«{$task->title}»\n\nمسئول شروع: <b>{$admin_name}</b>");
        } else if ($action === 'assign') {
            $wpdb->update($table_tasks, array('assigned_to' => $admin->ID, 'updated_at' => current_time('mysql')), array('id' => $taskId));
            doctor_khaste_answer_callback_query($callbackQueryId, '🙋‍♂️ تسک به شما واگذار شد.');
            doctor_khaste_edit_message_text($cb['message']['chat']['id'], $cb['message']['message_id'], "🙋‍♂️ <b>تسک واگذار شد به:</b>\n«{$task->title}»\n\nمسئول جدید: <b>{$admin_name}</b>");
        }
        return;
    }

    // 2. Standard Text Commands
    if (isset($update['message']) && isset($update['message']['text'])) {
        $msg = $update['message'];
        $chatId = $msg['chat']['id'];
        $text = trim($msg['text']);

        // Find admin
        $users = get_users(array(
            'meta_key'   => '_doctor_khaste_telegram_chat_id',
            'meta_value' => (string)$chatId,
            'number'     => 1
        ));
        $admin = !empty($users) ? $users[0] : null;

        if ($admin) {
            $admin_name = $admin->display_name ? $admin->display_name : $admin->user_login;

            if (strpos($text, '/start') === 0 || $text === '/help' || $text === '/راهنما') {
                doctor_khaste_tg_send_with_keyboard(
                    $chatId,
                    "👋 سلام <b>{$admin_name}</b> عزیز!\n\nحساب تلگرام شما متصل است و دکمه‌های پایینی منوی سریع برای شما فعال شدند.\n\n📋 دستورات:\n/tasks — نمایش لیست تسک‌های باز",
                    $replyKeyboard
                );
                return;
            }

            if ($text === '/tasks' || $text === '/تسکها' || $text === '/تسک‌ها' || $text === '📋 تسک‌های باز') {
                $table_tasks = $wpdb->prefix . 'doctor_khaste_tasks';
                $results = $wpdb->get_results("SELECT * FROM $table_tasks WHERE status != 'done' ORDER BY (due_date IS NULL), due_date ASC LIMIT 15");

                if (empty($results)) {
                    doctor_khaste_tg_send_with_keyboard($chatId, '🎉 هیچ تسک بازی وجود ندارد!', $replyKeyboard);
                    return;
                }

                $icons = array('urgent' => '🔴', 'high' => '🟠', 'normal' => '🟡', 'low' => '⚪️');
                $out = "📋 <b>تسک‌های باز:</b>\n\n";
                foreach ($results as $t) {
                    $shamsiDate = $t->due_date ? doctor_khaste_gregorian_to_shamsi($t->due_date) : '';
                    $prefix = isset($icons[$t->priority]) ? $icons[$t->priority] : '⚪️';
                    $out .= "{$prefix} {$t->title}" . ($shamsiDate ? '  —  موعد: ' . $shamsiDate : '') . "\n";
                }
                doctor_khaste_tg_send_with_keyboard($chatId, $out, $replyKeyboard);
                return;
            }

            if ($text === '🗒️ یادداشت جدید') {
                doctor_khaste_tg_send_with_keyboard(
                    $chatId,
                    "🗒️ <b>یادداشت‌های تیمی (ابسیدین استایل):</b>\n\nبرای نوشتن یادداشت جدید یا بارگذاری تصاویر، لطفاً وارد بخش «یادداشت‌ها» در داشبورد وب شوید.",
                    $replyKeyboard
                );
                return;
            }

            if ($text === '💬 پیام جدید') {
                doctor_khaste_tg_send_with_keyboard(
                    $chatId,
                    "💬 <b>پیام تیمی در میز کار:</b>\n\nبرای گفتگوی لحظه‌ای و ثبت توافقات غیرقابل‌حذف با همکار خود، به تب «میز کار و گفتگو» در وب اپ مراجعه کنید.",
                    $replyKeyboard
                );
                return;
            }
        } else {
            doctor_khaste_tg_send(
                $chatId,
                "👋 سلام! به ربات «دکتر خسته» خوش آمدید.\n\n⚠️ حساب تلگرام شما هنوز به هیچ ادمینی متصل نیست.\n\n<b>آیدی عددی تلگرام شما:</b>\n<code>{$chatId}</code>\n\nلطفاً این آیدی عددی را کپی کرده و در پنل وب داشبورد (منوی ادمین‌ها -> ویرایش ادمین شما -> آیدی عددی تلگرام) وارد و ذخیره کنید تا ربات فوراً حساب شما را فعال کند!"
            );
            return;
        }
    }
}
