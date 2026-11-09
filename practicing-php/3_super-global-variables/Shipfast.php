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


    public function uploadField(
            string $previewId,
            string $type    = 'image',
            string $class   = '',
            string $style   = '',
            array  $attrs   = []
        ): string {
            /* ── static state (persists for the whole request) ─────── */
            static $types = [
                'image'    => 'image/*',
                'video'    => 'video/*',
                'audio'    => 'audio/*',
                'document' => '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,.odt,.csv',
            ];
            static $scriptPrinted = false;

            /* ── validate type ─────────────────────────────────────── */
            if (!isset($types[$type])) {
                throw new InvalidArgumentException("Unknown upload type: {$type}");
            }
            $accept = $types[$type];

            /* ── helper: UUID v4 ───────────────────────────────────── */
            $uuid = (static function (): string {
                $d = random_bytes(16);
                $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
                $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
                return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
            })();

            /* ── helper: escape ────────────────────────────────────── */
            $e = static fn($v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

            /* ── name (UUID by default, overridable) ───────────────── */
            $name = $attrs['name'] ?? 'file_' . $uuid;
            unset($attrs['name']);

            /* ── merge class / style from $attrs ───────────────────── */
            if (isset($attrs['class'])) {
                $class = trim($attrs['class'] . ($class !== '' ? ' ' . $class : ''));
                unset($attrs['class']);
            }
            if (isset($attrs['style'])) {
                $style = rtrim($attrs['style'], ';') . ($style !== '' ? '; ' . $style : '');
                unset($attrs['style']);
            }

            /* ── build onchange expression ─────────────────────────── */
            $pid      = $e(json_encode($previewId));
            $t        = $e(json_encode($type));
            $onchange = "previewUpload(this, {$pid}, {$t})";

            /* ── inline preview JS, emitted exactly once ───────────── */
            $script = '';
            if (!$scriptPrinted) {
                $scriptPrinted = true;
                $script = <<<'HTML'
        <script>
        function previewUpload(input, previewId, type) {
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        if (preview.dataset.blobUrl) {
            URL.revokeObjectURL(preview.dataset.blobUrl);
            delete preview.dataset.blobUrl;
        }
        preview.querySelectorAll('.upload-preview-item').forEach(el => el.remove());

        const file = input.files && input.files[0];
        if (!file) { preview.style.display = 'none'; return; }
        preview.style.display = '';

        const kind = type || (file.type ? file.type.split('/')[0] : 'file');
        const makeUrl = () => {
            const url = URL.createObjectURL(file);
            preview.dataset.blobUrl = url;
            return url;
        };
        const mount = (el) => {
            el.classList.add('upload-preview-item');
            preview.appendChild(el);
            return el;
        };
        const isTag = (tag) => preview.tagName === tag;

        switch (kind) {
            case 'image': {
            if (isTag('IMG')) { preview.src = makeUrl(); preview.alt = file.name; }
            else { const i = new Image(); i.src = makeUrl(); i.alt = file.name; i.style.maxWidth = '100%'; mount(i); }
            break;
            }
            case 'video': {
            const v = isTag('VIDEO') ? preview : mount(document.createElement('video'));
            v.src = makeUrl(); v.controls = true; v.muted = true; v.style.maxWidth = '100%';
            break;
            }
            case 'audio': {
            const a = isTag('AUDIO') ? preview : mount(document.createElement('audio'));
            a.src = makeUrl(); a.controls = true;
            break;
            }
            case 'document':
            default: {
            if (file.type === 'application/pdf') {
                const f = mount(document.createElement('iframe'));
                f.src = makeUrl();
                Object.assign(f.style, { width: '100%', height: '400px', border: '0' });
            }
            const info = mount(document.createElement('div'));
            info.textContent = file.name + ' \u2014 ' + previewBytes(file.size);
            break;
            }
        }
        }
        function previewBytes(b) {
        if (!b) return '0 B';
        const k = 1024, u = ['B','KB','MB','GB','TB'];
        const i = Math.min(Math.floor(Math.log(b) / Math.log(k)), u.length - 1);
        return (b / Math.pow(k, i)).toFixed(1) + ' ' + u[i];
        }
        </script>
        HTML;
            }

            /* ── assemble <input> ──────────────────────────────────── */
            $parts = [
                'type="file"',
                'name="' . $e($name) . '"',
                'accept="' . $e($accept) . '"',
                'onchange="' . $onchange . '"',   // already escaped by $e(json_encode(...))
            ];
            if ($class !== '') $parts[] = 'class="' . $e($class) . '"';
            if ($style !== '') $parts[] = 'style="' . $e($style) . '"';

            foreach ($attrs as $k => $v) {
                if ($v === true) {
                    $parts[] = $e($k);
                } elseif ($v === false || $v === null) {
                    continue;
                } else {
                    $parts[] = $e($k) . '="' . $e($v) . '"';
                }
            }

            return $script . '<input ' . implode(' ', $parts) . '>';
    }

}

