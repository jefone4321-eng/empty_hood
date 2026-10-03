<?php

ini_set('display_errors', '0');
ini_set('log_errors', '1');


$errorLogPath = __DIR__ . '/../logs/php_errors.log';
$errorLogDir = dirname($errorLogPath);
if (!is_dir($errorLogDir)) {
    @mkdir($errorLogDir, 0755, true);
}
ini_set('error_log', $errorLogPath);

if (!function_exists('eh_render_friendly_error')) {
    function eh_render_friendly_error() {
        if (!headers_sent()) {
            http_response_code(500);
        }
      
      
        echo '<!DOCTYPE html><html><head><title>Something went wrong</title>';
        echo '<style>
            body { background:#0d0d0d; color:#fff; font-family: Poppins, sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; text-align:center; padding:20px; }
            .box { max-width:420px; }
            h1 { font-size:1.6rem; margin-bottom:12px; }
            p { color:#999; margin-bottom:24px; }
            a { color:#fff; background:#000; border:1px solid #333; padding:12px 24px; text-decoration:none; border-radius:4px; display:inline-block; }
        </style></head><body>';
        echo '<div class="box">';
        echo '<h1>Something went wrong</h1>';
        echo '<p>We hit an unexpected error. It has been logged and we will look into it.</p>';
        echo '<a href="/EmptyHood/php/index.php">Back to homepage</a>';
        echo '</div></body></html>';
        exit;
    }
}


set_exception_handler(function ($exception) {
    error_log('Uncaught exception: ' . $exception->getMessage()
        . ' in ' . $exception->getFile() . ':' . $exception->getLine());
    eh_render_friendly_error();
});


set_error_handler(function ($severity, $message, $file, $line) {
  
    if (!(error_reporting() & $severity)) {
        return false;
    }
    error_log("PHP error [$severity]: $message in $file:$line");

   
    if (in_array($severity, [E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR], true)) {
        eh_render_friendly_error();
    }
    return true;
});


register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        error_log("Fatal error: {$error['message']} in {$error['file']}:{$error['line']}");
        if (!headers_sent()) {
            eh_render_friendly_error();
        }
    }
});
?>