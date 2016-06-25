<?php
    require_once "../confs/const.php";
    require_once "../models/common.php";
    session_start();
    $method = get_request_method();
    if($method === 'POST'){
        $sql_kind = get_post_data('sql_kind');
        if($sql_kind === "logout"){
            logout();
        }
        header ("location:timeline.php");
        exit;
    }
