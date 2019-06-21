Das Proxy Plugin ermöglicht das ausliefern von externen Inhalten über einen im plugin integrierten HTTP Proxy.

Dies ist unter anderem in folgenden Szenarien interessant:
- Es sollen Inhalte eines externen Services eingebunden werden, ohne dass diese Kenntnis der IP Adresse des Endusers bekommt (GDPR/DSGVO).
- Inhalte eines langsamen externen Servers sollen über den eigenen Server ausgeliefert werden um der Performance zu beschleunigen

API:

Um eine Url durch den das Proxy Plugin ausliefern zu lassen steht die Funktion `rex_getProxyUrl()` zur verfügung.
Dieser muss als Parameter die zu vermittelnde URL übergeben werden. 

Beispiel:

```
<a href="https://github.com/redaxo/redaxo">
  <img src="<?php echo rex_getProxyUrl('https://gh-card.dev/repos/redaxo/redaxo.svg') ?>">
</a>
```

Url-Whitelist:

Damit Urls über den Proxy ausgeliefert werden können müssen entsprechende Url-Muster in der package.yml dieses Plugins hinterlegt werden.
Die müsste können dabei nur auf die Domain, oder auch auf einzelne Teilpfade eingeschränkt werden.

Passend zum o.g. Beispiel müsste zum Beispiel folgendes Muster hinterlegt werden:
 
```
allowed_urls:
    - '*//gh-card.dev/*'
```

Caching:

Der Proxy cached die Inhalte für eine feste Dauer. Diese kann in der package.yml unter dem Schlüssel `cache_expiration` konfiguriert werden.
Der Wert ist dabei in Sekunden zu hinterlegen.