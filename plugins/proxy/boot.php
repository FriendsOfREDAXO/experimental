<?php

require_once __DIR__ .'/functions/function_proxy.php';

rex_extension::register('PACKAGES_INCLUDED', static function () {
    $proxyUrl = rex_get('_rex_proxy');
    if ($proxyUrl) {
        if (rex_request::requestMethod() !== 'get') {
            throw new Exception('Unsupported request-method '. rex_request::requestMethod());
        }

        $onWhitelist = false;
        $plugin = rex_plugin::get('experimental', 'proxy');
        $urlWhitelist = $plugin->getProperty('allowed_urls');
        foreach ($urlWhitelist as $urlPattern) {
            if (fnmatch($urlPattern, $proxyUrl)) {
                $onWhitelist = true;
                break;
            }
        }

        if (!$onWhitelist) {
            throw new Exception('Url '. $proxyUrl . ' is not whitelisted');
        }

        $proxyCache = new rex_proxy_cache();
        $cached = $proxyCache->lookup($proxyUrl);
        if ($cached) {
            [$content, $contentType] = $cached;

            rex_response::setStatus(rex_response::HTTP_OK);
            rex_response::sendContentType($contentType);
            rex_response::sendContent($content);
            exit();
        }

        $socket = rex_socket::factoryUrl($proxyUrl);
        $response = $socket->doGet();
        if ($response->isOk()) {
            $contentType = $response->getHeader('Content-Type');
            $content = $response->getBody();

            // XXX add streaming
            rex_response::setStatus(rex_response::HTTP_OK);
            rex_response::sendContentType($contentType);
            rex_response::sendContent($content);

            $proxyCache->store($proxyUrl, $content, $contentType);
        } else {
            rex_response::setStatus($response->getStatusCode());
            rex_response::setContent($response->getStatusMessage());
        }
        exit();
    }
}, rex_extension::EARLY);
