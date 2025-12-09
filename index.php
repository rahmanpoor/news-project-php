<?php

use Auth\Auth;
use Parsidev\Jalali\jDate;

session_start();

//configuration
define('BASE_PATH', __DIR__);
define('CURRENT_DOMAIN', current_domain() . '/news-project/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'project');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DISPLAY_ERROR', true);

//mail config 
define('MAIL_HOST', 'smtp.gmail.com');
define('SMTP_AUTH', true);
define('MAIL_USERNAME', 'onlinephp.attendance@gmail.com');
define('MAIL_PASSWORD', '');
define('MAIL_PORT', 587);
define('SENDER_MAIL', 'onlinephp.attendance@gmail.com');
define('SENDER_NAME', 'وب خبری');

//database
require_once 'database/Database.php';
require_once 'database/CreateDB.php';

// $db = new Database\Database(); 

// $db = new CreateDB(); 
// $db->run(); 

//admin 
require_once 'activities/Admin/Admin.php';
require_once 'activities/Admin/Category.php';
require_once("activities/Admin/Dashboard.php");
require_once 'activities/Admin/Post.php';
require_once 'activities/Admin/Banner.php';
require_once 'activities/Admin/User.php';
require_once 'activities/Admin/Comment.php';
require_once 'activities/Admin/Menu.php';
require_once("activities/Admin/WebSetting.php");

//auth 
require_once 'activities/Auth/Auth.php';

//Home 
require_once("activities/Home.php");

//helpers 
spl_autoload_register(function ($className) {
    $path = BASE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
    $className = str_replace('\\',  DIRECTORY_SEPARATOR,  $className);
    include $path . $className . '.php';
});

function jalaliDate($date)
{
    return jDate::forge($date)->format('%A, %d %B %y');
}

// uri('admin/category', 'Admin\Category', 'index'); 
function uri($reservedUrl, $class, $method, $requestMethod = "GET")
{
    // current url array 
    $currentUrl = explode('?', currentUrl())[0];
    $currentUrl = str_replace(CURRENT_DOMAIN, '', $currentUrl);
    $currentUrl = trim($currentUrl, '/');
    $currentUrlArray = explode('/', $currentUrl);
    $currentUrlArray = array_filter($currentUrlArray);
    // reserved url array 
    $reservedUrl = trim($reservedUrl, '/');
    $reservedUrlArray = explode('/', $reservedUrl);
    $reservedUrlArray = array_filter($reservedUrlArray);
    // admin/category/create 
    // admin/category/create 
    if (
        sizeof($currentUrlArray) != sizeof($reservedUrlArray) ||
        methodField() != $requestMethod
    ) {
        return false;
    }
    // admin/category/edit/2 
    // admin/category/edit/{id} 
    $parameters = [];
    for ($key = 0; $key < sizeof($currentUrlArray); $key++) {
        if (
            $reservedUrlArray[$key][0] == '{' &&
            $reservedUrlArray[$key][strlen($reservedUrlArray[$key]) - 1] ==
            "}"
        ) {
            array_push($parameters, $currentUrlArray[$key]);
        } elseif ($currentUrlArray[$key] !== $reservedUrlArray[$key]) {
            // admin/category/delete/2 
            // admin/category/edit/{id} 
            return false;
        }
    }
    if (methodField() == 'POST') {
        $request = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        $parameters = array_merge([$request], $parameters);
    }
    $object = new $class;
    call_user_func_array(array($object, $method), $parameters);
    // Category 
    // $category = new Category; 
    // $category->index(); 
    exit;
}
function asset($src)
{
    $domain = trim(CURRENT_DOMAIN, '/ ');
    $src = $domain . '/' . trim($src, '/ ');
    return $src;
}

function url($url)
{
    $domain = trim(CURRENT_DOMAIN, '/ ');
    $url = $domain . '/' . trim($url, '/ ');
    return $url;
}

function protocol()
{
    return stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?
        'https://' : 'http://';
}

function current_domain()
{
    return protocol() . $_SERVER['HTTP_HOST'];
}
// echo current_domain(); 

function currentUrl()
{
    return current_domain() . $_SERVER['REQUEST_URI'];
}
// echo currentUrl(); 

function methodField()
{
    return $_SERVER['REQUEST_METHOD'];
}
// echo methodField();

function dd($vars)
{
    echo '<pre>';
    var_dump($vars);
    exit;
}
// dd('hi)

function displayError($displayError)
{
    if ($displayError) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }
}

displayError(DISPLAY_ERROR);
global $flashMessage;

if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
