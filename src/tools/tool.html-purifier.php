<?php
require_once 'libs/htmlpurifier/HTMLPurifier.auto.php';

function getHtmlPurifier($html) {
    
    $config = HTMLPurifier_Config::createDefault();
    
    $config->set('Attr.EnableID', false);
    $config->set('HTML.MaxImgLength', null);
    $config->set('CSS.MaxImgLength', null);
    $config->set('HTML.FlashAllowFullScreen', true);
    $config->set('HTML.SafeEmbed', true);
    $config->set('HTML.SafeIframe', true);
    $config->set('HTML.SafeObject', true);
    $config->set('Output.FlashCompat', true);
    
    $config->set('URI.SafeIframeRegexp', '#^(?:https?:)?//(?:'.implode('|', array(
        'www\\.youtube(?:-nocookie)?\\.com/',
        'maps\\.google\\.com/',
        'player\\.vimeo\\.com/video/',
        'www\\.microsoft\\.com/showcase/video\\.aspx',
        '(?:serviceapi\\.nmv|player\\.music)\\.naver\\.com/',
        '(?:api\\.v|flvs|tvpot|videofarm)\\.daum\\.net/',
        'v\\.nate\\.com/',
        'www\\.vimeo\\.com/',
        'play\\.mgoon\\.com/',
        'channel\\.pandora\\.tv/',
        'www\\.tagstory\\.com/',
        'play\\.pullbbang\\.com/',
        'tv\\.seoul\\.go\\.kr/',
        'ucc\\.tlatlago\\.com/',
        'vodmall\\.imbc\\.com/',
        'www\\.musicshake\\.com/',
        'www\\.afreeca\\.com/player/Player\\.swf',
        'static\\.plaync\\.co\\.kr/',
        'video\\.interest\\.me/',
        'player\\.mnet\\.com/',
        'sbsplayer\\.sbs\\.co\\.kr/',
        'img\\.lifestyler\\.co\\.kr/',
        'c\\.brightcove\\.com/',
        'www\\.slideshare\\.net/',
    )).')#');

    $purifier = new HTMLPurifier($config);
    
    return $purifier->purify($html);
}
?>