# php-vegas-renderer

Install using composer :
```
composer require pierreminiggio/mp4-youtube-video-downloader
```

```php

use PierreMiniggio\MP4YoutubeVideoDownloader\FrFrVersion170Build387\Template\WMV\WindowsMediaVideoV11\WMVVideoHd108030p8Mbitss;
use PierreMiniggio\MP4YoutubeVideoDownloader\MP4YoutubeVideoDownloader;

$renderer = new MP4YoutubeVideoDownloader('C:\\Program Files\\VEGAS\\VEGAS Pro 17.0\\vegas170.exe');
$renderer->render(
    'F:\\videos\\vlogs\\test\\projet.veg',
    new WMVVideoHd108030p8Mbitss(),
    'F:\\videos\\vlogs\\test\\projet.wmv'
);

```
