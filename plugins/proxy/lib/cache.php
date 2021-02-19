<?php

/**
 * @internal
 */
final class rex_proxy_cache
{
    /**
     * @param $url
     *
     * @return array|null
     */
    public function lookup($url)
    {
        $plugin = rex_plugin::get('experimental', 'proxy');
        $path = $plugin->getCachePath('proxy_cache');
        $file = $path . '/'. md5($url);

        $cached = rex_file::get($file);
        if ($cached) {
            [$content, $contentType, $storeTime] = unserialize($cached, false /* disallow classes */);

            if (self::isExpired($storeTime)) {
                // existing cache expired
                return null;
            }

            return [$content, $contentType];
        }
        return null;
    }

    /**
     * @return bool TRUE on success, FALSE on failure
     */
    public function store($url, $content, $contentType)
    {
        $plugin = rex_plugin::get('experimental', 'proxy');
        $path = $plugin->getCachePath('proxy_cache');
        $file = $path . '/'. md5($url);

        return rex_file::put($file, serialize([$content, $contentType, time()]));
    }

    public function clear()
    {
        $plugin = rex_plugin::get('experimental', 'proxy');
        return $plugin->clearCache();
    }

    private function isExpired($storeTime)
    {
        $plugin = rex_plugin::get('experimental', 'proxy');
        return (time() - $storeTime) > $plugin->getProperty('cache_expiration', 300);
    }
}
