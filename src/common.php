<?php
/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

const DB_HOST = '{DB_HOST}';
const DB_USER = '{DB_USER}';
const DB_NAME = '{DB_NAME}';
const DB_PASSWORD = '{DB_PASSWORD}';

const FILE_MAXIMUM_SIZE = 10485760; // 10MB
const FILE_DIRECTORY = "uploads";
const FILE_AVALIABLE_EXTENSIONS = array("jpg", "jpeg", "png", "gif", "svg", "tiff", "bmp");

const PERMISSION_NO_FILTERING = 3;
const PERMISSION_CHANGE_TITLE = 1;
const PERMISSION_DELETE_ARTICLE = 1;

const TITLE_AFFIX = ' - 연세위키';

const HREF_MAIN = './';
const HREF_SIGNIN = './signin';
const HREF_SEARCH = './keywords';
const HREF_SIGNUP = './signup';
const HREF_SIGNOUT = './signout';
const HREF_RESET = './reset';
const HREF_WRITE = './edit';
const HREF_READ = './pages';
const HREF_DASHBOARD = './dashboard';
const HREF_PROFILE = './users';
const HREF_CREATE = './create';
const HREF_REVISIONS = './revisions';
const HREF_REVISION = './revision';
const HREF_404 = './404';
const HREF_SUGGEST = './suggest';
const HREF_RECENT = './recent';

const LOG_DELETE_ACCOUNT = 'delete-account';
const LOG_STUDENT_AUTH = 'auth';
const LOG_CHANGE_EMAIL = 'change-email';
const LOG_CHANGE_PASSWORD = 'change-password';
const LOG_WRITE = 'write';
const LOG_CREATE = 'create';
const LOG_UPDATE_USER_INFO = 'update-user-info';
const LOG_RESET = 'reset';
const LOG_SIGNIN = 'signin';
const LOG_SIGNUP = 'signup';

function permissionInfo($permission) {
    switch ($permission) {
        case 0:
            return array(
                'description' => '독자',
                'color' => 'info'
            );
        case 1:
            return array(
                'description' => '편집자',
                'color' => 'warning'
            );
        case 2:
            return array(
                'description' => '중재자',
                'color' => 'success'
            );
        case 3:
            return array(
                'description' => '관리자',
                'color' => 'danger'
            );
        default:
            return array(
                'description' => '개발자',
                'color' => 'primary'
            );
            
    }
}

function navigateTo($link = HREF_MAIN) {
    header('Location: ' . $link);
    exit();
}

function passwordHash($password) {
    return hash('sha512', $password . 'yw');
}

?>