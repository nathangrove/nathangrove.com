<?php

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
// Check for curl or wget in the user agent
$isTerminalClient = stripos($userAgent, 'curl') !== false || stripos($userAgent, 'wget') !== false;

// Basic Router
$route = parse_url($requestUri, PHP_URL_PATH);

// Define allowed routes and their corresponding base filenames
$allowedRoutes = [
    '/' => 'index',
    '/about' => 'about',
    '/projects' => 'projects',
    '/contact' => 'contact',
];

// Check if the request is for an asset
$assetPattern = '/\.(css|js|jpg|jpeg|png|gif|svg|ico)$/i';
if (preg_match($assetPattern, $route, $matches)) {
    $assetPath = __DIR__ . '/pages' . $route; // Assets are inside pages/assets

    if (is_file($assetPath)) {
        $fileExtension = strtolower($matches[1]);
        $contentType = match ($fileExtension) {
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            default => 'application/octet-stream',
        };
        header('Content-Type: ' . $contentType);
        readfile($assetPath);
        exit;
    } else {
        http_response_code(404);
        echo "404 Asset Not Found";
        exit;
    }
}

// Handle defined routes
if (array_key_exists($route, $allowedRoutes)) {
    $baseFilename = $allowedRoutes[$route];

    $fileExtension = $isTerminalClient ? 'txt' : 'html';

    // Special handling for /projects route
    if ($route === '/projects') {
        $projectsJsonPath = __DIR__ . '/projects.json';
        $templatePath = __DIR__ . '/pages/project-template.' . $fileExtension;

        if (!is_file($templatePath)) {
            http_response_code(500);
            echo "Server configuration error: Missing projects template.";
            exit;
        }

        # if the projects is more than 24hrs old, get the latest from https://api.github.com/users/nathangrove/repos
        $projectsFileTime = filemtime($projectsJsonPath);
        $currentTime = time();
        if ($projectsFileTime === false || ($currentTime - $projectsFileTime) > 86400) { // 86400 seconds = 24 hours
            $ch = curl_init('https://api.github.com/users/nathangrove/repos');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'nathangrove.com');
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                http_response_code(500);
                echo "Error fetching projects from GitHub.";
                exit;
            }

            $projects = json_decode($response, true);
            if ($projects === null || !is_array($projects)) {
                http_response_code(500);
                echo "Error decoding GitHub projects data.";
                exit;
            }

            // sort by updated_at in descending order
            usort($projects, function ($a, $b) {
                return strtotime($b['updated_at']) - strtotime($a['updated_at']);
            });

            // Limit to the first 10 projects
            $projects = array_slice($projects, 0, 10);

            // Save the fetched projects to the JSON file
            file_put_contents($projectsJsonPath, json_encode($projects, JSON_PRETTY_PRINT));
        }

        $projectsJson = file_get_contents($projectsJsonPath);
        $projects = json_decode($projectsJson, true);
        $template = file_get_contents($templatePath);

        $allProjectOutput = '';
        foreach ($projects as $project) {
            $projectOutput = $template;
            $projectOutput = str_replace('{{name}}', $project['name'] ?? 'N/A', $projectOutput);
            $projectOutput = str_replace('{{description}}', $project['description'] ?? 'N/A', $projectOutput);
            $projectOutput = str_replace('{{url}}', $project['html_url'] ?? 'N/A', $projectOutput);
            $allProjectOutput .= $projectOutput;
        }

        $output = file_get_contents(__DIR__ . '/pages/projects.' . $fileExtension);
        $output = str_replace('{{projects}}', $allProjectOutput, $output);

        if ($isTerminalClient) header('Content-Type: text/plain; charset=utf-8');
        else header('Content-Type: text/html; charset=utf-8');
        echo $output;
        exit;

    } else {
        // Standard handling for other routes
        $filePath = __DIR__ . '/pages/' . $baseFilename . '.' . $fileExtension;

        if (is_file($filePath)) {
            $contentType = $isTerminalClient ? 'text/plain' : 'text/html';
            header('Content-Type: ' . $contentType . '; charset=utf-8');
            readfile($filePath);
            exit;
        } else {
            // File for the specific user agent not found
            http_response_code(404);
            // Use the correct variable name here
            $clientType = $isTerminalClient ? 'terminal client (curl/wget)' : 'browser';
            echo "404 Not Found (Content for your {$clientType} is missing)";
            exit;
        }
    }
} else {
    // Route not defined
    http_response_code(404);
    // Optionally serve a generic 404 page
    // Update to check for terminal client for 404 page
    $notFoundPage = __DIR__ . '/pages/404.' . ($isTerminalClient ? 'txt' : 'html');
    if (is_file($notFoundPage)) {
        $contentType = $isTerminalClient ? 'text/plain' : 'text/html';
        header('Content-Type: ' . $contentType . '; charset=utf-8'); // Added charset=utf-8
        readfile($notFoundPage);
    } else {
        echo "404 Route Not Found";
    }
    exit;
}

?>