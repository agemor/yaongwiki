<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 03
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/module.db.php";
require_once __DIR__ . "/module.form.php";
require_once __DIR__ . "/module.user.php";
require_once __DIR__ . "/module.purifier.php";
require_once __DIR__ . "/module.redirect.php";

const DELETE_REVISIONS = false;

function process() {
    
    global $db;
    global $post;
    global $get;
    global $user;
    global $redirect;
    global $purifier;

    $http_article_title = $post->retrieve("article-title") !== null ? $post->retrieve("article-title") : $get->retrieve("t");
    $http_article_id = $post->retrieve("article-id") !== null ? $post->retrieve("article-id") : $get->retrieve("i");
    $http_article_new_title = strip_tags($post->retrieve("article-new-title"));
    $http_article_content = $post->retrieve("article-content");
    $http_article_tags = preg_replace("!\s+!", " ", strip_tags($post->retrieve("article-tags")));
    $http_article_delete = $post->retrieve("article-delete") !== null;
    $http_article_change_permission = $post->retrieve("article-permission") !== null;
    $http_article_permission = $http_article_change_permission ? abs(intval($post->retrieve("article-permission"))) : 0;
    $http_article_comment = strip_tags($post->retrieve("article-comment"));
    
    $read_by_id = !empty($http_article_id);
    
    if (empty($http_article_title) && empty($http_article_id)) {
        $redirect->set("./");
        return array(
            "redirect" => true
        );
    }
    
    if (!$user->signined()) {
        $redirect->set("./?signin&redirect=./?write" . ($read_by_id ? "%26i=" . $http_article_id : "%26t=" . $http_article_title));
        return array(
            "redirect" => true
        );
    }
    
    $db->in(DB_ARTICLE_TABLE)->select("*");

    if ($read_by_id) {
        $db->where("id", "=", $http_article_id);
    } else {
        $db->where("title", "=", $http_article_title);
    }

    $article_data = $db->go_and_get();
    $article_snapshot_data = $article_data;
    
    if (!$article_data) {
        return array(
            "result" =>false,
            "message" => STRINGS["EPWR0"]
        );
    }
    
    if (intval($article_data["permission"]) > $user->permission) {
        return array(
            "result" => false,
            "article" => $article_data,
            "message" => STRINGS["EPWR1"]
        );
    }
    
    // 글 삭제
    if ($http_article_delete) {
        if ($user->permission < PERMISSION_DELETE_ARTICLE) {
            return array(
                "result" => false,
                "article" => $article_data,
                "message" => STRINGS["EPWR2"]
            );
        }

        $response = $db->in(DB_ARTICLE_TABLE)
                       ->delete()
                       ->where("id", "=", $article_data["id"])
                       ->go();

        if (!$response) {
            return array(
                "result" =>false,
                "message" => STRINGS["EPWR3"]
            );
        }
        
        if (DELETE_REVISIONS) {
            $response = $db->in(DB_REVISION_TABLE)
                           ->delete()
                           ->where("article_id", "=", $article_data["id"])
                           ->go();
        }
        
        $redirect->set("./?read&t=" . $article_data["title"]);
        return array(
            "redirect" => true
        );
    }
    
    // 편집 모드
    if (empty($http_article_content)) {
        return array(
            "result" => true,
            "article" => $article_data,
        );
    }

    // 글 내용 필터링
    if ($user->permission < PERMISSION_NO_FILTERING) {
        $http_article_content = $purifier->purify($http_article_content);
    }
    
    $article_data["content"] = $http_article_content;
    $article_data["tags"] = $http_article_tags;

    $db->in(DB_ARTICLE_TABLE);
    
    // 제목 유효성 검사
    if (!empty($http_article_new_title) && strcmp($http_article_new_title, $article_data["title"]) != 0) {
        if (strlen(preg_replace("/\s+/", "", $http_article_new_title)) < 2) {
            return array(
                "result" => false,
                "article" => $article_data,
                "message" => STRINGS["EPWR4"]
            );
        }
        
        if (is_numeric($http_article_new_title)) {
            return array(
                "result" => false,
                "article" => $article_data,
                "message" => STRINGS["EPWR5"]
            );
        }

        $response = $db->in(DB_ARTICLE_TABLE)
                       ->select("*")
                       ->where("title", "=", $http_article_new_title)
                       ->go_and_get();
                     
        if ($response) {
            return array(
                "result" => false,
                "article" => $article_data,
                "message" => STRINGS["EPWR6"]
            );
        }
        
        $article_data["title"] = $http_article_new_title;
        $db->in(DB_ARTICLE_TABLE);
        $db->update("title", $http_article_new_title);
    }
    
    // 퍼미션 유효성 검사
    if ($http_article_change_permission) {
        if ($http_article_permission > $user->permission) {
            return array(
                "result" => false,
                "article" => $article_data,
                "message" => STRINGS["EPWR7"]
            );
        }
        
        $article_data["permission"] = $http_article_permission;
        $db->update("permission", $http_article_permission);
    }
    
    $db->update("tags", $http_article_tags);
    $db->update("content", $http_article_content);
    $db->where("id", "=", $article_data["id"]);
    $response = $db->go();
    
    if (!$response) {
        return array(
            "result" => false,
            "article" => $article_data,
            "message" => STRINGS["EPWR8"]
        );
    }

    $recent_revision_data = $db->in(DB_REVISION_TABLE)
                               ->select("revision")
                               ->where("article_id", "=", $article_data["id"])
                               ->order_by("`timestamp` DESC")
                               ->limit("1")
                               ->go_and_get();
    
    if (!$recent_revision_data) {
        return array(
            "result" => false,
            "article" => $article_data,
            "message" => STRINGS["EPWR9"]
        );
    }

    $response = $db->in(DB_REVISION_TABLE)
                   ->insert("article_id", $article_data["id"])
                   ->insert("article_title", $article_data["title"])
                   ->insert("revision", intval($recent_revision_data["revision"]) + 1)
                   ->insert("user_name", $user->name)
                   ->insert("snapshot_content", $article_data["content"])
                   ->insert("snapshot_tags", $article_data["tags"])
                   ->insert("fluctuation", (strlen($article_data["content"]) - strlen($article_snapshot_data["content"])))
                   ->insert("comment", $http_article_comment)
                   ->go();

    if (!$response) {
        return array(
            "result" => false,
            "article" => $article_data,
            "message" => STRINGS["EPWR10"]
        );
    }

    $response = $db->in(DB_LOG_TABLE)
                   ->insert("behavior", "write")
                   ->insert("data", $article_data["id"] . "/" . (intval($recent_revision_data["revision"]) + 1))
                   ->go();
    
    $redirect->set("./?read&t=" . $article_data["title"]);
    
    return array(
        "redirect" => true
    );
}
