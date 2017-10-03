<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 03
 */

require_once __DIR__ . "/common.php";

const DELETE_REVISIONS = false;

function process() {
    
    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $log = LogManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();
    $recaptcha = ReCaptchaManager::get_instance();
    $email = EmailManager::get_instance();
    $settings = SettingsManager::get_instance();

    $http_article_title = $http_vars->get("article-title") !== null ? $http_vars->get("article-title") : $http_vars->get("t");
    $http_article_id = $http_vars->get("article-id") !== null ? $http_vars->get("article-id") : $http_vars->get("i");
    $http_article_new_title = strip_tags($http_vars->get("article-new-title"));
    $http_article_content = $http_vars->get("article-content");
    $http_article_tags = preg_replace("!\s+!", " ", strip_tags($http_vars->get("article-tags")));
    $http_article_delete = $http_vars->get("article-delete") !== null;
    $http_article_change_permission = $http_vars->get("article-permission") !== null;
    $http_article_permission = $http_article_change_permission ? abs(intval($http_vars->get("article-permission"))) : 0;
    $http_article_comment = strip_tags($http_vars->get("article-comment"));
    
    $read_by_id = !empty($http_article_id);
    
    if (empty($http_article_title) && empty($http_article_id)) {
        return array(
            "result" => false,
            "redirect" => "./?page-not-found"
        );
    }
    
    if (!$user->authorized()) {
        return array(
            "result" => false,
            "redirect" => "./?signin&redirect=./?write" . ($read_by_id ? "%26i=" . $http_article_id : "%26t=" . $http_article_title)
        );
    }

    if (!$db->connect()) {
        return array(
            "result" => false,
            "message" => STRINGS["ESDB0"],
            "redirect" => "./?out-of-service"
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
    
    if (intval($article_data["permission"]) > $user->get("permission")) {
        return array(
            "result" => false,
            "article" => $article_data,
            "message" => STRINGS["EPWR1"]
        );
    }
    
    // 글 삭제
    if ($http_article_delete) {
        if ($user->get("permission") < PERMISSION_DELETE_ARTICLE) {
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
        
        return array(
            "result" => true,
            "redirect" => "./?read&t=" . $article_data["title"]
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
    if ($user->get("permission") < PERMISSION_NO_FILTERING) {
        $http_article_content = $http_article_content;
    }
    
    $article_data["content"] = $http_article_content;
    $article_data["tags"] = $http_article_tags;

    $db->in(DB_ARTICLE_TABLE);
    
    $change_title = false;
    $change_permission = false;

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
        $change_title = true;
    }
    
    // 퍼미션 유효성 검사
    if ($http_article_change_permission) {
        if ($http_article_permission > $user->get("permission")) {
            return array(
                "result" => false,
                "article" => $article_data,
                "message" => STRINGS["EPWR7"]
            );
        }
        $article_data["permission"] = $http_article_permission;
        $change_permission = true;
    }
    
    $response = $db->in(DB_USER_TABLE)
                   ->update("total_contributions", "`total_contributions` + 1", true)
                   ->where("id", "=", $user->id)
                   ->go();

    $response = $db->in(DB_REVISION_TABLE)
                   ->insert("article_id", $article_data["id"])
                   ->insert("article_title", $article_data["title"])
                   ->insert("predecessor_id", $article_data["latest_revision_id"])
                   ->insert("revision", intval($article_data["revisions"]) + 1)
                   ->insert("user_name", $user->name)
                   ->insert("snapshot_content", $article_data["content"])
                   ->insert("snapshot_tags", $article_data["tags"])
                   ->insert("fluctuation", (strlen($article_data["content"]) - strlen($article_snapshot_data["content"])))
                   ->insert("comment", $http_article_comment)
                   ->go();

    $recent_revision_id = $db->last_insert_id();

    $db->in(DB_ARTICLE_TABLE);
    if ($change_permission) {
        $db->update("permission", $http_article_permission);
    }
    if ($change_title) {
        $db->update("title", $http_article_new_title);
    }
    $db->update("tags", $http_article_tags);
    $db->update("latest_revision_id", $recent_revision_id);
    $db->update("content", $http_article_content);
    $db->update("revisions", "`revisions` + 1", true);
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

    if (!$response) {
        return array(
            "result" => false,
            "article" => $article_data,
            "message" => STRINGS["EPWR10"]
        );
    }

    $response = $db->in(DB_LOG_TABLE)
                   ->insert("user_name", $user->name) 
                   ->insert("behavior", "write")
                   ->insert("data", $article_data["id"] . "/" . (intval($article_data["revisions"]) + 1))
                   ->go();
    
    return array(
        "result" => true,
        "redirect" => "./?read&t=" . $article_data["title"]
    );
}
