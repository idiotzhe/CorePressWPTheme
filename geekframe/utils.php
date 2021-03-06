<?php
/**
 * wordpress实用功能
 */


function file_load_js($path)
{
    echo '<script src="' . THEME_JS_PATH . '/' . $path . '?v=' . THEME_VERSIONNAME . '"></script>';
}

function file_load_img($path)
{
    echo "<img src=\"" . THEME_IMG_PATH . "/{$path}\">";
}
function file_get_img_url($path)
{
    return  THEME_IMG_PATH . "/".$path;
}
function file_load_face()
{

    $files = scandir(THEME_PATH . "/static/img/face");
    foreach ($files as $v) {
        /* if(is_file($v)){
             $fileItem[] = $v;
         }*/
        if (pathinfo($v, PATHINFO_EXTENSION) == 'gif') {
            $html = '<img class="img-pace" src="' . THEME_IMG_PATH . '/face/' . $v . '" width="30" facename="' . basename($v, ".gif") . '">' . $html;
        }

    }
    return $html;
}

function file_load_css($path)
{

    echo '<link rel="stylesheet" href="' . THEME_CSS_PATH . '/' . $path . '?v=' . THEME_VERSIONNAME . '">';
}

function file_load_lib($path, $type)
{
    if ($type == 'css') {
        echo '<link rel="stylesheet" href="' . THEME_LIB_PATH . '/' . $path . '?v=' . THEME_VERSIONNAME . '">';
    } elseif ($type == 'js') {
        echo '<script src="' . THEME_LIB_PATH . '/' . $path . '"></script>';
    }
}

function file_load_component($name)
{
    require_once(THEME_PATH . "/component/{$name}");
}

function islogin()
{
    return is_user_logged_in();
}

function loginAndBack($url = null)
{
    if ($url == null) {
        return wp_login_url('//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }else{
        return wp_login_url($url);

    }

}

function corepress_get_user_nickname($user_id = null)
{
    if ($user_id == null) {
        $currentUser = wp_get_current_user();
        $name = $currentUser->user_nicename;
    } else {
        $user = get_userdata($user_id);
        $name = $user->user_nicename;
    }
    return $name;
}

function corepress_get_avatar_url($email = null, $size = 60)
{

    if ($email == null) {
        $currentUser = wp_get_current_user();
        $avatarurl = get_avatar_url($currentUser->user_email, array('size' => $size));
    } else {
        $avatarurl = get_avatar_url($email, array('size' => $size));
    }

    return $avatarurl;

}

function isadmin($user_id = null)
{
    if ($user_id == null) {
        $currentUser = wp_get_current_user();
        $roles = $currentUser->roles;

    } else {
        $user = get_userdata($user_id);
        $roles = $user->roles;
    }

    if (!empty($roles) && in_array('administrator', $roles)) {
        return true;
    } else {
        return false;  // 非管理员
    }

}

function diffBetweenTwoDay($pastDay)
{
    $timeC = time() - strtotime($pastDay);
    $dateC = round((strtotime(date('Y-m-d')) - strtotime(date('Y-m-d', strtotime($pastDay)))) / 60 / 60 / 24);
    if ($timeC <= 3 * 60) {
        $dayC = '刚刚';
    } elseif ($timeC > 3 * 60 && $timeC <= 5 * 60) {
        $dayC = '3分钟前';
    } elseif ($timeC > 5 * 60 && $timeC <= 10 * 60) {
        $dayC = '5分钟前';
    } elseif ($timeC > 10 * 60 && $timeC <= 30 * 60) {
        $dayC = '10分钟前';
    } elseif ($timeC > 30 * 60 && $timeC <= 60 * 60) {
        $dayC = '30分钟前';
    } elseif ($timeC > 60 * 60 && $timeC <= 120 * 60) {
        $dayC = '1小时前';
    } elseif ($timeC > 120 * 60 && $dateC == 0) {
        $dayC = '今天';
    } elseif ($dateC == 1) {
        $dayC = '昨天';
    } else {
        $dayC = date('Y-m-d', strtotime($pastDay));
    }
    return $dayC;
}

if (!function_exists('utf8_excerpt')) :
    function utf8_excerpt($str, $len)
    {
        $str = strip_tags(str_replace(array("\n", "\r"), ' ', $str));
        if (function_exists('mb_substr')) {
            $excerpt = mb_substr($str, 0, $len, 'utf-8');
        } else {
            preg_match_all("/[x01-x7f]|[xc2-xdf][x80-xbf]|xe0[xa0-xbf][x80-xbf]|[xe1-xef][x80-xbf][x80-xbf]|xf0[x90-xbf][x80-xbf][x80-xbf]|[xf1-xf7][x80-xbf][x80-xbf][x80-xbf]/", $str, $ar);
            $excerpt = join('', array_slice($ar[0], 0, $len));
        }

        if (trim($str) != trim($excerpt)) {
            $excerpt .= '...';
        }
        return $excerpt;
    }
endif;

function format_date($time)
{
    global $options, $post;
    $p_id = isset($post->ID) ? $post->ID : 0;
    $q_id = get_queried_object_id();
    $single = $p_id == $q_id && is_single();
    if (isset($options['time_format']) && $options['time_format'] == '0') {
        return date(get_option('date_format') . ($single ? ' ' . get_option('time_format') : ''), $time);
    }
    $t = current_time('timestamp') - $time;
    $f = array(
        '86400' => '天',
        '3600' => '小时',
        '60' => '分钟',
        '1' => '秒'
    );
    if ($t == 0) {
        return '1秒前';
    } else if ($t >= 604800 || $t < 0) {
        return date(get_option('date_format') . ($single ? ' ' . get_option('time_format') : ''), $time);
    } else {
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
    }
}

function get_share_url($type, $title, $summary)
{
    global $set;
    if ($set['seo']['description'] != null) {
        $description = $set['seo']['description'];
    } else {
        $description = get_bloginfo('description');
    }
    $url = urlencode(get_bloginfo('url'));
    if ($type == 'qq') {
        return 'https://connect.qq.com/widget/shareqq/index.html?url=' . $url . '&title=' . urlencode($title) . '&source=' . urlencode(get_bloginfo('name')) . '&desc=' . urlencode($description) . '&pics=&summary=' . urlencode($summary);
    } else if ($type == 'weibo') {
        return 'https://service.weibo.com/share/share.php?url=' . $url . '&title=' . urlencode($summary) . '&pic=&appkey=&searchPic=true';
    } else if ($type = 'qzone') {
        return 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' . $url . '&title=' . urlencode($title) . '&pics=&summary=' . urlencode($summary);
    }
}
function corepress_replace_copyright($str){
    global $post;
    $author_name = get_the_author();
    $post_url = get_permalink();

    $str = str_replace('<#username#>', $author_name, $str);
    $str = str_replace('<#url#>', $post_url, $str);

    return $str;
}
