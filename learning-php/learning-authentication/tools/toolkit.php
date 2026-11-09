<?php

    function sanitize_post_data($data){
        $sanitized_data = htmlspecialchars(trim($_POST[$data] ?? ''));
        return $sanitized_data;
    }