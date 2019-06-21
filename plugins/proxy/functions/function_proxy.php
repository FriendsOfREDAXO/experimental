<?php

function rex_getProxyUrl($url)
{
    return rex_url::frontendController(['_rex_proxy' => $url]);
}

function rex_isProxyRequest()
{
    return (bool) rex_get('_rex_proxy');
}
