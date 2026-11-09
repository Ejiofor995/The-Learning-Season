<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// jamb_score and post_utme_score

if($_SERVER['REQUEST_METHOD']!=='POST'){
    http_response_code(200);
    exit(json_encode([
        'ok'=>false,
        'response'=>'Sorry Bro, i was expecting a POST request!'
    ]));
}

if($_SERVER['REQUEST_METHOD']==='POST'){

    if(isset($_POST['jamb_score']) && isset($_POST['post_utme_score']) && !empty($_POST['jamb_score']) && !empty($_POST['post_utme_score'])){
        http_response_code(200);
        echo json_encode([
            'ok'=>true,
            'response'=>"Congratulations on you Admission into UNN, your verification was successful, with JAMB score of {$_POST['jamb_score']} and a Post-UTME score of {$_POST['post_utme_score']}."
        ]);
        
    }else{
        http_response_code(200);
        exit(json_encode([
            'ok'=>false,
            'response'=>'Sorry Bro, i couldn\'t make use of the data you sent!'
        ]));
    }
}