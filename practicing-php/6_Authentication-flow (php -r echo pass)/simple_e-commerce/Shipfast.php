<?php

class Shipfast{


// 1)===========================================================
public function connectDb($host, $dbname, $dbusername, $dbpassword){
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try{
            $pdo = new PDO($dsn,$dbusername,$dbpassword,[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch(PDOException $err){
            error_log("Database connection failed: {$err->getMessage()}");
            http_response_code(500);
            exit('Database unavailable, Please try again later!');
        }

        return $pdo;
    }


// 2)===========================================================


    public function uploadFile($file, $allowedExts = [], $maxSizeMB = 5) {

         /**
         * @param array $file = $_FILES['your_input']
         * @param array $allowedExts = ['jpg','pdf','mp4']
         * @param int $maxSizeMB = 10
         * @return string|false  Path on success, false + error message
         */
        
        // 1. Error check
        if (!isset($file) || $file['error'] !== 0) {
            return ['ok' => false, 'msg' => 'No file or upload error'];
        }

        // 2. Size check
        $maxBytes = $maxSizeMB * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            return ['ok' => false, 'msg' => "Too big. Max is {$maxSizeMB}MB"];
        }

        // 3. Extension check
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedExts) && !in_array($ext, $allowedExts)) {
            return ['ok' => false, 'msg' => "Extension .$ext not allowed"];
        }

        // 4. Create folder
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);

        // 5. Move
        $newName = uniqid('file_', true) . '.' . $ext;
        $dest = 'uploads/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return ['ok' => true, 'path' => $dest];
        }

        return ['ok' => false, 'msg' => 'Failed to save'];

            // --- HOW TO USE ---

            // Profile picture: 2MB max
            #$result = uploadFile($_FILES['avatar'], ['jpg','jpeg','png','webp'], 2);

            // PDF document: 10MB max
            #$result = uploadFile($_FILES['document'], ['pdf','docx'], 10);

            // Video: 100MB max
            #$result = uploadFile($_FILES['video'], ['mp4','mov'], 100);

            // Check result

            // if ($result['ok']) {
            //     echo $result['path']; // save this to DB
            // } else {
            //     echo $result['msg'];
            // }
    }


// 3)===========================================================


// 

}

