<?php
/**
 * ═══════════════════════════════════════════════════════════════════
 *  SHIPFAST — a tiny PHP toolkit that does the boring stuff
 *            so you can ship your app faster.
 * ═══════════════════════════════════════════════════════════════════
 *
 *  WHAT'S INSIDE (5 categories, 11 public methods)
 *  ─────────────────────────────────────────────────────────────────
 *    1) SETUP ......... connectDb
 *    2) SECURITY ...... csrf, handlePassword
 *    3) VALIDATION .... validate_data, validate
 *    4) AUTH .......... login, logout, isAuthenticated, currentUser
 *    5) UPLOADS ....... uploadFile, uploadField
 *
 *  THE ONE RULE TO REMEMBER
 *  ─────────────────────────────────────────────────────────────────
 *   Every method that can fail returns the SAME shape:
 *
 *       [ 'ok' => true|false, 'response' => <anything> ]
 *
 *   - ok       → true = it worked, false = it failed
 *   - response → on success: the good stuff (data, path, token, HTML…)
 *                on failure: a short message you can show the user
 *
 *   Some methods add extra keys (like `errors` for validation),
 *   but `ok` and `response` are ALWAYS there.
 *
 *   So you always do:
 *
 *       $r = $s->something();
 *       if (!$r['ok']) {
 *           echo $r['response'];    // error message
 *       } else {
 *           // use $r['response'] — the real payload
 *       }
 *
 *  SETUP (in 30 seconds)
 *  ─────────────────────────────────────────────────────────────────
 *   Step 1 — Save this file as `Shipfast.php` in your project.
 *
 *   Step 2 — In your bootstrap file (e.g. index.php, config.php):
 *
 *       require 'Shipfast.php';
 *       $s = new Shipfast();
 *       $s->connectDb('localhost', 'myapp', 'root', '');
 *
 *   Step 3 — Use it!
 *
 *       // Handle a login form:
 *       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 *
 *           if (!$s->csrf('verify')['ok']) exit('CSRF failed');
 *
 *           $v = $s->validate_data([
 *               'email'    => 'required|email',
 *               'password' => 'required|string',
 *           ]);
 *
 *           if (!$v['ok']) {
 *               $errors = $v['errors'];          // show on form
 *           } else {
 *               $r = $s->login('users',
 *                   $v['response']['email'],
 *                   $v['response']['password']);
 *
 *               if (!$r['ok']) $errors['general'] = $r['response'];
 *               else { header('Location: /dashboard.php'); exit; }
 *           }
 *       }
 *
 *       // On any protected page:
 *       if (!$s->isAuthenticated()) { header('Location: /login.php'); exit; }
 *       $me = $s->currentUser();      // ['id'=>1, 'username'=>'jane', …]
 *
 *  A NOTE ON ORDERING
 *  ─────────────────────────────────────────────────────────────────
 *   Protected helper methods come first (you'll rarely need them).
 *   Public methods come after, numbered and grouped by purpose so
 *   you can scroll straight to the section you need.
 */
class Shipfast
{
    // ═══════════════════════════════════════════════════════════════
    //  PROPERTIES
    // ═══════════════════════════════════════════════════════════════

    /** @var PDO|null  The connection created by connectDb(). */
    public ?PDO $pdo = null;

    /* Validation state — reset automatically per validate_data() call.
       You never read or write these directly. */
    protected array $_valSource     = [];
    protected array $_valClean      = [];
    protected array $_valErrors     = [];
    protected array $_valMessages   = [];
    protected array $_valAttributes = [];


    // ═══════════════════════════════════════════════════════════════
    //  PROTECTED HELPERS  — internal plumbing, not for daily use
    // ═══════════════════════════════════════════════════════════════

    /** Start the PHP session with safe cookie defaults, if not already running. */
    protected function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        if (session_status() !== PHP_SESSION_NONE)   return;
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    /** Fail loudly if the developer forgot to call connectDb(). */
    protected function requirePdo(): void
    {
        if ($this->pdo === null) {
            throw new RuntimeException('No database connection. Call connectDb() first.');
        }
    }

    /** True if the name is a safe SQL identifier (letters, digits, underscores). */
    protected function isValidIdentifier(string $name): bool
    {
        return (bool)preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name);
    }


    /* ─── Validation internals ─────────────────────────────────────
       These implement the validation engine. Called only by
       validate_data(). All are protected so subclasses can tune
       behaviour if they need to.                                   */

    protected function expandRules(array $rules): array
    {
        $out = [];
        foreach ($rules as $key => $rule) {
            $parsed = $this->parseRules($rule);
            if (!str_contains($key, '*')) {
                $out[$key] = $parsed;
                continue;
            }
            foreach ($this->expandKey($key) as $concrete) {
                $out[$concrete] = $parsed;
            }
        }
        return $out;
    }

    protected function parseRules($rule): array
    {
        if (is_string($rule)) $rule = explode('|', $rule);
        if (!is_array($rule)) throw new InvalidArgumentException('Rule must be string or array');
        $parsed = [];
        foreach ($rule as $r) if (is_string($r) && $r !== '') $parsed[] = $this->parseSingle($r);
        return $parsed;
    }

    protected function parseSingle(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$name, $rest] = explode(':', $rule, 2);
            $name   = strtolower(trim($name));
            $params = $name === 'regex' ? [$rest] : array_map('trim', explode(',', $rest));
            return [$name, $params];
        }
        return [strtolower(trim($rule)), []];
    }

    protected function expandKey(string $pattern): array
    {
        return $this->walkKey(explode('.', $pattern), $this->_valSource, '');
    }

    protected function walkKey(array $parts, $node, string $prefix): array
    {
        if (empty($parts)) return [$prefix];
        $head = array_shift($parts);
        if ($head === '*') {
            if (!is_array($node)) return [];
            $out = [];
            foreach ($node as $k => $v) {
                $p = $prefix === '' ? (string)$k : "{$prefix}.{$k}";
                $out = array_merge($out, $this->walkKey($parts, $v, $p));
            }
            return $out;
        }
        $p    = $prefix === '' ? $head : "{$prefix}.{$head}";
        $next = is_array($node) && array_key_exists($head, $node) ? $node[$head] : null;
        return $this->walkKey($parts, $next, $p);
    }

    protected function getValue(string $attribute, ?array $from = null)
    {
        $cur = $from ?? $this->_valSource;
        foreach (explode('.', $attribute) as $p) {
            if (!is_array($cur) || !array_key_exists($p, $cur)) return null;
            $cur = $cur[$p];
        }
        return $cur;
    }

    protected function hasKey(string $attribute, ?array $from = null): bool
    {
        $cur = $from ?? $this->_valSource;
        foreach (explode('.', $attribute) as $p) {
            if (!is_array($cur) || !array_key_exists($p, $cur)) return false;
            $cur = $cur[$p];
        }
        return true;
    }

    protected function setPath(array &$target, string $path, $value): void
    {
        $cur = &$target;
        foreach (explode('.', $path) as $p) {
            if (!isset($cur[$p]) || !is_array($cur[$p])) $cur[$p] = [];
            $cur = &$cur[$p];
        }
        $cur = $value;
    }

    protected function validateAttribute(string $attribute, array $rules): void
    {
        $names     = array_column($rules, 0);
        $nullable  = in_array('nullable',  $names, true);
        $sometimes = in_array('sometimes', $names, true);
        $bail      = in_array('bail',      $names, true);
        $present   = $this->hasKey($attribute);
        $value     = $this->getValue($attribute);

        /* Rules that must run even when a field is missing.
           Everything else is skipped if the field wasn't sent. */
        $implicit = [
            'required', 'required_if', 'required_unless', 'required_with',
            'required_with_all', 'required_without', 'required_without_all',
            'present', 'filled',
        ];

        if ($sometimes && !$present) return;
        if ($nullable && $value === null) {
            $this->setPath($this->_valClean, $attribute, null);
            return;
        }

        $hadError = false;
        $coerced  = $value;

        foreach ($rules as [$name, $params]) {
            if (in_array($name, ['nullable', 'sometimes', 'bail'], true)) continue;

            // Skip non-implicit rules when the field is absent.
            // 'bio' => 'string|max:500' and no 'bio' in POST → nothing to check.
            if (!$present && !in_array($name, $implicit, true)) continue;

            $err = $this->applyRule($name, $params, $value, $attribute, $present);
            if ($err === null) {
                $cast = $this->castValue($name, $value);
                if ($cast !== false) $coerced = $cast;
                continue;
            }
            $hadError = true;
            $this->_valErrors[$attribute][] = $this->formatMessage($attribute, $name, $params, $err);
            if ($bail) return;
        }

        // Only add to clean output if the field was actually provided,
        // or a rule replaced its value (e.g. a cast).
        if (!$hadError && ($present || $coerced !== $value)) {
            $this->setPath($this->_valClean, $attribute, $coerced);
        }
    }

    protected function applyRule(string $name, array $params, $value, string $attribute, bool $present): ?string
    {
        switch ($name) {
            case 'required': if (!$this->filled($value, $present)) return 'The :attribute field is required.'; return null;
            case 'present':  if (!$present) return 'The :attribute field must be present.'; return null;
            case 'filled':   if (!$present || !$this->filled($value, true)) return 'The :attribute field must have a value.'; return null;
            case 'string':   if (!is_string($value) && !is_numeric($value)) return 'The :attribute field must be a string.'; return null;
            case 'integer':  if (!is_int($value) && !(is_string($value) && preg_match('/^[+-]?\d+$/', $value))) return 'The :attribute field must be an integer.'; return null;
            case 'numeric':  if (!is_numeric($value)) return 'The :attribute field must be a number.'; return null;
            case 'boolean':  if (!in_array($value, [true, false, 1, 0, '1', '0'], true)) return 'The :attribute field must be true or false.'; return null;
            case 'array':    if (!is_array($value)) return 'The :attribute field must be an array.'; return null;
            case 'json':
                if (!is_string($value)) return 'The :attribute field must be a valid JSON string.';
                json_decode($value);
                return json_last_error() === JSON_ERROR_NONE ? null : 'The :attribute field must be a valid JSON string.';
            case 'email': if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) return 'The :attribute field must be a valid email address.'; return null;
            case 'url':   if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_URL))   return 'The :attribute field must be a valid URL.'; return null;
            case 'ip':    if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_IP))    return 'The :attribute field must be a valid IP address.'; return null;
            case 'ipv4':  if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) return 'The :attribute field must be a valid IPv4 address.'; return null;
            case 'ipv6':  if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) return 'The :attribute field must be a valid IPv6 address.'; return null;
            case 'uuid':  if (!is_string($value) || !preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value)) return 'The :attribute field must be a valid UUID.'; return null;
            case 'alpha':      if (!is_string($value) || !preg_match('/^\pL+$/u', $value))         return 'The :attribute field must only contain letters.'; return null;
            case 'alpha_num':  if (!is_string($value) || !preg_match('/^[\pL\pN]+$/u', $value))    return 'The :attribute field must only contain letters and numbers.'; return null;
            case 'alpha_dash': if (!is_string($value) || !preg_match('/^[\pL\pN_-]+$/u', $value))  return 'The :attribute field must only contain letters, numbers, dashes, and underscores.'; return null;
            case 'regex':
                $pattern = $params[0] ?? '';
                return (!is_string($value) || !@preg_match($pattern, $value)) ? 'The :attribute field format is invalid.' : null;
            case 'date':        return $this->isDate($value) ? null : 'The :attribute field must be a valid date.';
            case 'date_format':
                $dt = DateTime::createFromFormat($params[0] ?? '', (string)$value);
                return $dt ? null : 'The :attribute field must match the format :format.';
            case 'min':
                $m = (int)$params[0];
                if (is_array($value)   && count($value) < $m)      return 'The :attribute field must have at least :min items.';
                if (is_numeric($value) && (float)$value < $m)      return 'The :attribute field must be at least :min.';
                if (is_string($value)  && mb_strlen($value) < $m)  return 'The :attribute field must be at least :min characters.';
                return null;
            case 'max':
                $m = (int)$params[0];
                if (is_array($value)   && count($value) > $m)      return 'The :attribute field must not have more than :max items.';
                if (is_numeric($value) && (float)$value > $m)      return 'The :attribute field must not be greater than :max.';
                if (is_string($value)  && mb_strlen($value) > $m)  return 'The :attribute field must not be greater than :max characters.';
                return null;
            case 'size':
                $m = (int)$params[0];
                if (is_array($value)   && count($value) !== $m)             return 'The :attribute field must contain :size items.';
                if (is_numeric($value) && (float)$value !== (float)$m)      return 'The :attribute field must be :size.';
                if (is_string($value)  && mb_strlen($value) !== $m)         return 'The :attribute field must be :size characters.';
                return null;
            case 'between':
                $lo = (int)$params[0]; $hi = (int)($params[1] ?? $lo);
                $n  = is_array($value) ? count($value) : (is_numeric($value) ? (float)$value : mb_strlen((string)$value));
                return ($n < $lo || $n > $hi) ? 'The :attribute field must be between :min and :max.' : null;
            case 'gt': case 'gte': case 'lt': case 'lte':
                $other = $this->getValue($params[0] ?? '');
                if (!is_numeric($value) || !is_numeric($other)) return 'The :attribute field must be a number.';
                $a = (float)$value; $b = (float)$other;
                $ok  = match ($name) { 'gt' => $a > $b, 'gte' => $a >= $b, 'lt' => $a < $b, 'lte' => $a <= $b };
                $map = ['gt'=>'greater than','gte'=>'greater than or equal to','lt'=>'less than','lte'=>'less than or equal to'];
                return $ok ? null : "The :attribute field must be {$map[$name]} :other.";
            case 'in':
                return in_array(is_scalar($value) ? (string)$value : '', array_map('strval', $params), true) ? null : 'The selected :attribute is invalid.';
            case 'not_in':
                return in_array(is_scalar($value) ? (string)$value : '', array_map('strval', $params), true) ? 'The selected :attribute is invalid.' : null;
            case 'same':
                return (string)$value === (string)$this->getValue($params[0] ?? '') ? null : 'The :attribute field must match :other.';
            case 'different':
                return (string)$value !== (string)$this->getValue($params[0] ?? '') ? null : 'The :attribute field and :other must be different.';
            case 'confirmed':
                return (string)$value === (string)$this->getValue($attribute . '_confirmation') ? null : 'The :attribute field confirmation does not match.';
            case 'starts_with':
                foreach ($params as $n) if (is_string($value) && str_starts_with($value, $n)) return null;
                return 'The :attribute field must start with one of the following: :values.';
            case 'ends_with':
                foreach ($params as $n) if (is_string($value) && str_ends_with($value, $n)) return null;
                return 'The :attribute field must end with one of the following: :values.';
            case 'required_if':
                $other = $params[0] ?? '';
                $vals  = array_map('strval', array_slice($params, 1));
                if (in_array((string)$this->getValue($other), $vals, true) && !$this->filled($value, $present))
                    return 'The :attribute field is required when :other is :value.';
                return null;
            case 'required_unless':
                $other = $params[0] ?? '';
                $vals  = array_map('strval', array_slice($params, 1));
                if (!in_array((string)$this->getValue($other), $vals, true) && !$this->filled($value, $present))
                    return 'The :attribute field is required unless :other is in :values.';
                return null;
            case 'required_with':
                foreach ($params as $p) if ($this->filled($this->getValue($p), true)) {
                    return $this->filled($value, $present) ? null : 'The :attribute field is required when :values is present.';
                }
                return null;
            case 'required_with_all':
                $all = true;
                foreach ($params as $p) if (!$this->filled($this->getValue($p), true)) { $all = false; break; }
                return ($all && !$this->filled($value, $present)) ? 'The :attribute field is required when :values are present.' : null;
            case 'required_without':
                foreach ($params as $p) if (!$this->filled($this->getValue($p), true)) {
                    return $this->filled($value, $present) ? null : 'The :attribute field is required when :values is not present.';
                }
                return null;
            case 'required_without_all':
                $none = true;
                foreach ($params as $p) if ($this->filled($this->getValue($p), true)) { $none = false; break; }
                return ($none && !$this->filled($value, $present)) ? 'The :attribute field is required when none of :values are present.' : null;
            default:
                throw new InvalidArgumentException("Unknown validation rule: {$name}");
        }
    }

    protected function filled($value, bool $present): bool
    {
        if (!$present || $value === null) return false;
        if (is_string($value) && trim($value) === '') return false;
        if (is_array($value) && count($value) === 0) return false;
        return true;
    }

    protected function isDate($value): bool
    {
        return is_string($value) && strtotime($value) !== false;
    }

    protected function castValue(string $rule, $value)
    {
        return match ($rule) {
            'integer' => (int)$value,
            'numeric' => (float)$value,
            'boolean' => in_array($value, [true, 1, '1'], true),
            'string'  => (string)$value,
            'array'   => (array)$value,
            'json'    => json_decode((string)$value, true),
            default   => false,
        };
    }

    protected function formatMessage(string $attribute, string $rule, array $params, string $template): string
    {
        $use  = $this->_valMessages["{$attribute}.{$rule}"] ?? $this->_valMessages[$rule] ?? $template;
        $name = fn($k) => $this->_valAttributes[$k] ?? str_replace(['-', '_'], ' ', $k);

        $interp = [
            ':attribute' => $name($attribute),
            ':min'       => $params[0] ?? '',
            ':max'       => $params[1] ?? $params[0] ?? '',
            ':size'      => $params[0] ?? '',
            ':format'    => $params[0] ?? '',
        ];

        switch ($rule) {
            case 'required_if':
                $interp[':other']  = $name($params[0] ?? '');
                $interp[':value']  = implode(', ', array_slice($params, 1));
                $interp[':values'] = implode(', ', array_slice($params, 1));
                break;
            case 'required_unless':
                $interp[':other']  = $name($params[0] ?? '');
                $interp[':values'] = implode(', ', array_slice($params, 1));
                break;
            case 'required_with': case 'required_with_all':
            case 'required_without': case 'required_without_all':
                $interp[':values'] = implode(', ', array_map($name, $params));
                $interp[':other']  = $name($params[0] ?? '');
                break;
            case 'same': case 'different':
            case 'gt': case 'gte': case 'lt': case 'lte':
                $interp[':other'] = $name($params[0] ?? '');
                break;
            default:
                $val = $this->getValue($attribute);
                $interp[':value'] = is_scalar($val) ? (string)$val : '';
                if (in_array($rule, ['starts_with', 'ends_with'], true)) {
                    $interp[':values'] = implode(', ', $params);
                }
        }
        return strtr($use, $interp);
    }

    /** First error message string from the current _valErrors bag. */
    protected function firstValidationError(): string
    {
        foreach ($this->_valErrors as $msgs) {
            if (is_array($msgs) && isset($msgs[0]) && is_string($msgs[0])) {
                return $msgs[0];
            }
        }
        return 'Validation failed';
    }


    // ═══════════════════════════════════════════════════════════════
    //  PUBLIC METHODS
    // ═══════════════════════════════════════════════════════════════


    // 1)═══════════════════════════════════════════════════════════
    //    SETUP
    // ═══════════════════════════════════════════════════════════════

    /**
     * 1.1) connectDb — Connect to MySQL and remember the connection.
     *
     * WHAT IT DOES
     *   Creates a PDO connection with safe defaults, stores it in
     *   $this->pdo (so login() can use it without you passing it in),
     *   and returns it so you can run your own queries.
     *   If connection fails, it logs the real error and shows a
     *   friendly 500 page — no DB passwords ever leak to the browser.
     *
     * RETURNS  PDO
     *
     * USAGE
     *     $pdo = $s->connectDb('localhost', 'myapp', 'root', '');
     *     // Both work from here on:
     *     $s->login('users', $email, $password);        // uses $s->pdo
     *     $pdo->query('SELECT * FROM posts')->fetchAll();
     */
    public function connectDb(string $host, string $dbname, string $user, string $pass): PDO
    {
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: {$e->getMessage()}");
            http_response_code(500);
            exit('Database unavailable, please try again later!');
        }
        $this->pdo = $pdo;
        return $pdo;
    }


    // 2)═══════════════════════════════════════════════════════════
    //    SECURITY
    // ═══════════════════════════════════════════════════════════════

    /**
     * 2.1) csrf — Protect forms against cross-site request forgery.
     *
     * WHAT IT DOES
     *   Keeps a random token in $_SESSION and checks it on every POST.
     *   Four actions: set/token, field, verify, rotate.
     *
     * RETURNS  {ok, response}
     *   - ok       : bool — did the operation succeed?
     *   - response : mixed —
     *       'set' | 'token' | 'rotate' → the token string
     *       'field'                    → ready-to-echo <input type="hidden">
     *       'verify'                   → 'OK' on success, error message on failure
     *
     * USAGE
     *   In your form template:
     *       <form method="post">
     *           <?= $s->csrf('field')['response'] ?>
     *           <input name="email" …>
     *       </form>
     *
     *   In your POST handler:
     *       if (!$s->csrf('verify')['ok']) {
     *           exit($s->csrf('verify')['response']);  // "Invalid CSRF token"
     *       }
     *
     *   After login (to kill any planted token):
     *       $s->csrf('rotate');
     */
    public function csrf(string $action = 'set', $input = null): array
    {
        $this->startSession();

        if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        switch ($action) {
            case 'set':
            case 'token':
                return ['ok' => true, 'response' => $_SESSION['csrf']];

            case 'field':
                $t = htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8');
                return [
                    'ok'       => true,
                    'response' => '<input type="hidden" name="csrf" value="' . $t . '">',
                ];

            case 'verify':
                $token = $input ?? ($_POST['csrf'] ?? null);
                if (!is_string($token) || $token === '') {
                    return ['ok' => false, 'response' => 'Missing CSRF token'];
                }
                return hash_equals($_SESSION['csrf'], $token)
                    ? ['ok' => true,  'response' => 'OK']
                    : ['ok' => false, 'response' => 'Invalid CSRF token'];

            case 'rotate':
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                return ['ok' => true, 'response' => $_SESSION['csrf']];
        }
        return ['ok' => false, 'response' => "Invalid action: {$action}"];
    }

    /**
     * 2.2) handlePassword — Hash a password (with optional match check).
     *
     * WHAT IT DOES
     *   Confirms the two passwords aren't empty, checks they match,
     *   and returns a bcrypt hash ready to save.
     *
     * RETURNS  {ok, response}
     *   - response : the hash on success, or an error message on failure
     *
     * USAGE
     *     $p = $s->handlePassword($_POST['password'], $_POST['password_confirm']);
     *     if (!$p['ok']) {
     *         $errors['password'] = $p['response'];   // "Both passwords don't add up!"
     *     } else {
     *         $hash = $p['response'];                 // save to DB
     *     }
     */
    public function handlePassword(string $pass, string $confirm): array
    {
        if ($pass === '' || $confirm === '') {
            return ['ok' => false, 'response' => "Password can't be empty!"];
        }
        if ($pass !== $confirm) {
            return ['ok' => false, 'response' => "Both passwords don't add up!"];
        }
        return ['ok' => true, 'response' => password_hash($pass, PASSWORD_DEFAULT)];
    }


    // 3)═══════════════════════════════════════════════════════════
    //    VALIDATION
    // ═══════════════════════════════════════════════════════════════

    /**
     * 3.1) validate_data — Validate form or query data.
     *
     * WHAT IT DOES
     *   Takes an associative array of rules and returns typed values
     *   on success or per-field error messages on failure.
     *   Source defaults to $_POST + $_GET (POST wins on collision).
     *
     * RETURNS  {ok, response, errors?}
     *   - response : array of typed values on success, FIRST ERROR STRING on failure
     *   - errors   : only on failure — ['email'=>['…'], 'age'=>['…']]
     *
     * USAGE
     *     $v = $s->validate_data([
     *         'username' => 'required|alpha_dash|min:3|max:32',
     *         'email'    => 'required|email',
     *         'age'      => 'required|integer|between:18,120',
     *         'bio'      => 'string|max:500',   // optional — skipped if not sent
     *     ]);
     *
     *     if (!$v['ok']) {
     *         $errors = $v['errors'];        // show on form
     *         // $v['response'] is the first error string, e.g.
     *         // "The email field must be a valid email address."
     *     } else {
     *         $data = $v['response'];        // ['username'=>'jane', 'age'=>27, …]
     *         // $data['age'] is an int, not "27"
     *     }
     *
     * See the docblock at the top of the file for the full rule list.
     */
    public function validate_data(
        array $rules,
        ?array $source = null,
        array $messages = [],
        array $attributes = []
    ): array {
        $this->_valSource     = $source ?? $_POST + $_GET;
        $this->_valClean      = [];
        $this->_valErrors     = [];
        $this->_valMessages   = $messages;
        $this->_valAttributes = $attributes;

        foreach ($this->expandRules($rules) as $attr => $parsed) {
            $this->validateAttribute($attr, $parsed);
        }

        if ($this->_valErrors) {
            $result = [
                'ok'       => false,
                'response' => $this->firstValidationError(),   // string
                'errors'   => $this->_valErrors,
            ];
        } else {
            $result = [
                'ok'       => true,
                'response' => $this->_valClean,
            ];
        }

        $this->_valSource = $this->_valClean = $this->_valErrors = [];
        return $result;
    }

    /**
     * 3.2) validate — Same engine as validate_data, but throws on failure.
     *
     * WHEN TO USE
     *   Use validate_data() for forms (you want $errors[] to re-render).
     *   Use validate() for webhooks, CLI scripts, or places where an
     *   invalid payload should abort the whole request.
     *
     * RETURNS  array (the typed values)
     * THROWS   RuntimeException on validation failure
     *
     * USAGE
     *     try {
     *         $data = $s->validate($payload, [
     *             'event'  => 'required|string',
     *             'amount' => 'required|numeric|min:1',
     *         ]);
     *         // $data has typed values
     *     } catch (RuntimeException $e) {
     *         http_response_code(400);
     *         exit($e->getMessage());
     *     }
     */
    public function validate(array $data, array $rules, array $messages = [], array $attributes = []): array
    {
        $r = $this->validate_data($rules, $data, $messages, $attributes);
        if (!$r['ok']) {
            throw new RuntimeException($r['response']);
        }
        return $r['response'];
    }


    // 4)═══════════════════════════════════════════════════════════
    //    AUTH  (login, logout, isAuthenticated, currentUser)
    // ═══════════════════════════════════════════════════════════════

    /**
     * 4.1) login — Check credentials and remember the user in session.
     *
     * WHAT IT DOES
     *   Looks up the user by email, verifies the password (timing-safe
     *   even when the user doesn't exist), transparently rehashes if
     *   needed, regenerates the session ID, and stores the columns
     *   you asked for in $_SESSION['user'] with isAuthenticated = true.
     *
     * RETURNS  {ok, response}
     *   - response : the stored user array on success, error message on failure
     *
     * USAGE
     *     // Everything (all columns except password):
     *     $r = $s->login('users', $email, $password);
     *
     *     // Lean — store only what the layout needs:
     *     $r = $s->login('users', $email, $password, ['id', 'username', 'role']);
     *
     *     // Custom column names:
     *     $r = $s->login('customers', $email, $password,
     *         ['id', 'name'],
     *         emailColumn: 'mail',
     *         passwordColumn: 'pass_hash',
     *         sessionKey: 'customer');
     *
     *     if (!$r['ok']) {
     *         $errors['general'] = $r['response'];   // "Invalid credentials"
     *     } else {
     *         $user = $r['response'];                // ['id'=>1, 'username'=>'jane', …]
     *         header('Location: /dashboard.php'); exit;
     *     }
     */
    public function login(
        string $table,
        string $email,
        string $password,
        array  $sessionFields  = ['*'],
        string $emailColumn    = 'email',
        string $passwordColumn = 'password',
        string $sessionKey     = 'user'
    ): array {
        $this->requirePdo();
        $this->startSession();

        foreach ([$table, $emailColumn, $passwordColumn] as $id) {
            if (!$this->isValidIdentifier($id)) {
                return ['ok' => false, 'response' => "Invalid identifier: {$id}"];
            }
        }
        foreach ($sessionFields as $f) {
            if ($f !== '*' && !$this->isValidIdentifier($f)) {
                return ['ok' => false, 'response' => "Invalid field: {$f}"];
            }
        }

        $fetch = ($sessionFields === ['*'] || $sessionFields === [])
            ? ['*']
            : array_values(array_unique(array_merge($sessionFields, [$passwordColumn])));

        $sql = 'SELECT ' . implode(', ', $fetch) . " FROM {$table} WHERE {$emailColumn} = ? LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $row = $stmt->fetch();
        } catch (PDOException $e) {
            error_log("login: {$e->getMessage()}");
            return ['ok' => false, 'response' => 'Login failed'];
        }

        // Timing-safe: always run password_verify, even when the user doesn't exist.
        $dummy = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // bcrypt("password")
        $hash  = (is_array($row) ? ($row[$passwordColumn] ?? null) : null) ?? $dummy;

        if (!$row || !password_verify($password, $hash)) {
            return ['ok' => false, 'response' => 'Invalid credentials'];
        }

        // Silently upgrade the hash if PASSWORD_DEFAULT changed.
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            try {
                $this->pdo
                    ->prepare("UPDATE {$table} SET {$passwordColumn} = ? WHERE {$emailColumn} = ?")
                    ->execute([password_hash($password, PASSWORD_DEFAULT), $email]);
            } catch (PDOException $e) {
                error_log("login rehash: {$e->getMessage()}");
            }
        }

        $user = $row;
        if ($fetch !== ['*']) {
            $user = array_intersect_key($row, array_flip(
                array_filter($sessionFields, fn($f) => $f !== '*' && $f !== $passwordColumn)
            ));
        }
        unset($user[$passwordColumn]);

        session_regenerate_id(true);
        $_SESSION['isAuthenticated'] = true;
        $_SESSION[$sessionKey]       = $user;

        return ['ok' => true, 'response' => $user];
    }

    /**
     * 4.2) logout — Wipe the session and clear the auth flag.
     *
     * WHAT IT DOES
     *   Clears all session data, regenerates the session ID (so the
     *   old one is useless if someone stole it), and sets
     *   isAuthenticated = false.
     *
     * RETURNS  {ok, response}
     *
     * USAGE
     *     // Always on POST — GET logout can be CSRF'd
     *     if ($_SERVER['REQUEST_METHOD'] === 'POST' && $s->csrf('verify')['ok']) {
     *         $s->logout();
     *     }
     *     header('Location: /login.php'); exit;
     */
    public function logout(): array
    {
        $this->startSession();
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['isAuthenticated'] = false;
        return ['ok' => true, 'response' => 'OK'];
    }

    /**
     * 4.3) isAuthenticated — Is there a logged-in user right now?
     *
     * RETURNS  bool
     *
     * USAGE
     *     if (!$s->isAuthenticated()) {
     *         header('Location: /login.php'); exit;
     *     }
     */
    public function isAuthenticated(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE
            && ($_SESSION['isAuthenticated'] ?? false) === true;
    }

    /**
     * 4.4) currentUser — Get the logged-in user's data.
     *
     * RETURNS  array|null  — null if nobody is logged in
     *
     * USAGE
     *     $me = $s->currentUser();       // ['id'=>1, 'username'=>'jane', 'role'=>'admin']
     *     if ($me === null) {
     *         // not logged in
     *     } else {
     *         echo 'Hello, ' . htmlspecialchars($me['username']);
     *     }
     */
    public function currentUser(string $sessionKey = 'user'): ?array
    {
        return $this->isAuthenticated() ? ($_SESSION[$sessionKey] ?? null) : null;
    }


    // 5)═══════════════════════════════════════════════════════════
    //    FILE UPLOADS
    // ═══════════════════════════════════════════════════════════════

    /**
     * 5.1) uploadFile — Validate and save an uploaded file.
     *
     * WHAT IT DOES
     *   Checks upload error, size, and extension. Creates uploads/ if
     *   missing. Moves the file with a safe, unique name.
     *
     * RETURNS  {ok, response}
     *   - response : the saved file path on success, error message on failure
     *
     * USAGE
     *     $field = array_key_first($_FILES);
     *     $u = $s->uploadFile($_FILES[$field], ['jpg','png','webp'], 2);  // 2MB max
     *
     *     if (!$u['ok']) {
     *         $errors['avatar'] = $u['response'];    // "Too big. Max is 2MB"
     *     } else {
     *         $path = $u['response'];                // "uploads/file_…jpg"
     *         // save $path to DB
     *     }
     */
    public function uploadFile(array $file, array $allowedExts = [], int $maxSizeMB = 5): array
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'response' => 'No file or upload error'];
        }
        $maxBytes = $maxSizeMB * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            return ['ok' => false, 'response' => "Too big. Max is {$maxSizeMB}MB"];
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedExts) && !in_array($ext, $allowedExts, true)) {
            return ['ok' => false, 'response' => "Extension .{$ext} not allowed"];
        }
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);

        $newName = uniqid('file_', true) . '.' . $ext;
        $dest    = 'uploads/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return ['ok' => true, 'response' => $dest];
        }
        return ['ok' => false, 'response' => 'Failed to save'];
    }

    /**
     * 5.2) uploadField — Render a full <input type="file"> with live preview.
     *
     * WHAT IT DOES
     *   Emits the entire file input (right accept attribute, class,
     *   style, onchange handler, auto-generated random name) plus the
     *   JS that shows the uploaded image/video/audio/document inline.
     *   The JS runs once per page no matter how many fields you render.
     *
     * RETURNS  string (the HTML)
     *
     * USAGE
     *   Image (uses <img> as the preview target):
     *       <img id="avatarPreview" style="display:none" alt="">
     *       <?= $s->uploadField('avatarPreview', 'image', 'form-control') ?>
     *
     *   Video / Audio (use <video> / <audio> as preview targets):
     *       <video id="clipPreview" style="display:none" controls muted></video>
     *       <?= $s->uploadField('clipPreview', 'video', 'form-control') ?>
     *
     *   Document (use a <div> — the JS appends an iframe/info card inside):
     *       <div id="docPreview" style="display:none"></div>
     *       <?= $s->uploadField('docPreview', 'document', 'form-control') ?>
     *
     *   Extra HTML attributes via the 5th argument:
     *       <?= $s->uploadField('avatarPreview', 'image', 'form-control', '',
     *           ['id' => 'avatarInput', 'required' => true, 'name' => 'avatar']) ?>
     */
    public function uploadField(
        string $previewId,
        string $type  = 'image',
        string $class = '',
        string $style = '',
        array  $attrs = []
    ): string {
        static $types = [
            'image'    => 'image/*',
            'video'    => 'video/*',
            'audio'    => 'audio/*',
            'document' => '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,.odt,.csv',
        ];
        static $scriptPrinted = false;

        if (!isset($types[$type])) {
            throw new InvalidArgumentException("Unknown upload type: {$type}");
        }
        $accept = $types[$type];

        $uuid = (static function (): string {
            $d = random_bytes(16);
            $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
            $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
        })();

        $e    = static fn($v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        $name = $attrs['name'] ?? 'file_' . $uuid;
        unset($attrs['name']);

        if (isset($attrs['class'])) {
            $class = trim($attrs['class'] . ($class !== '' ? ' ' . $class : ''));
            unset($attrs['class']);
        }
        if (isset($attrs['style'])) {
            $style = rtrim($attrs['style'], ';') . ($style !== '' ? '; ' . $style : '');
            unset($attrs['style']);
        }

        $onchange = 'previewUpload(this, ' . $e(json_encode($previewId)) . ', ' . $e(json_encode($type)) . ')';

        $script = '';
        if (!$scriptPrinted) {
            $scriptPrinted = true;
            $script = <<<'HTML'
<script>
function previewUpload(input, previewId, type) {
  const preview = document.getElementById(previewId);
  if (!input || !preview) return;
  if (preview.dataset.blobUrl) { URL.revokeObjectURL(preview.dataset.blobUrl); delete preview.dataset.blobUrl; }
  preview.querySelectorAll('.upload-preview-item').forEach(el => el.remove());
  const file = input.files && input.files[0];
  if (!file) { preview.style.display = 'none'; return; }
  preview.style.display = '';
  const kind = type || (file.type ? file.type.split('/')[0] : 'file');
  const makeUrl = () => { const u = URL.createObjectURL(file); preview.dataset.blobUrl = u; return u; };
  const mount = (el) => { el.classList.add('upload-preview-item'); preview.appendChild(el); return el; };
  const isTag = (t) => preview.tagName === t;
  switch (kind) {
    case 'image':
      if (isTag('IMG')) { preview.src = makeUrl(); preview.alt = file.name; }
      else { const i = new Image(); i.src = makeUrl(); i.alt = file.name; i.style.maxWidth = '100%'; mount(i); }
      break;
    case 'video': { const v = isTag('VIDEO') ? preview : mount(document.createElement('video')); v.src = makeUrl(); v.controls = true; v.muted = true; v.style.maxWidth = '100%'; break; }
    case 'audio': { const a = isTag('AUDIO') ? preview : mount(document.createElement('audio')); a.src = makeUrl(); a.controls = true; break; }
    default:
      if (file.type === 'application/pdf') { const f = mount(document.createElement('iframe')); f.src = makeUrl(); Object.assign(f.style, { width: '100%', height: '400px', border: '0' }); }
      const info = mount(document.createElement('div')); info.textContent = file.name + ' \u2014 ' + previewBytes(file.size);
  }
}
function previewBytes(b) { if (!b) return '0 B'; const k = 1024, u = ['B','KB','MB','GB','TB'], i = Math.min(Math.floor(Math.log(b) / Math.log(k)), u.length - 1); return (b / Math.pow(k, i)).toFixed(1) + ' ' + u[i]; }
</script>
HTML;
        }

        $parts = [
            'type="file"',
            'name="' . $e($name) . '"',
            'accept="' . $e($accept) . '"',
            'onchange="' . $onchange . '"',
        ];
        if ($class !== '') $parts[] = 'class="' . $e($class) . '"';
        if ($style !== '') $parts[] = 'style="' . $e($style) . '"';
        foreach ($attrs as $k => $v) {
            if ($v === true)                      $parts[] = $e($k);
            elseif ($v === false || $v === null)  continue;
            else                                  $parts[] = $e($k) . '="' . $e($v) . '"';
        }
        return $script . '<input ' . implode(' ', $parts) . '>';
    }
}