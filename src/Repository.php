<?php

namespace PierreMiniggio\MP4YoutubeVideoDownloader;

class Repository
{
    public function __construct(
        public string $token,
        public string $owner,
        public string $repo
    )
    {
    }
}
