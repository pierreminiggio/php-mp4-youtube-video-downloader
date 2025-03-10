<?php

namespace PierreMiniggio\MP4YoutubeVideoDownloader;

use Exception;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloader;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloaderFactory;
use Throwable;
use YouTube\Exception\VideoNotFoundException;
use YouTube\YouTubeDownloader;

class BestDownloadLinkFinder
{

    private YouTubeDownloader $yt;
    private ?Repository $yt1dApiGithubRepository;
    private GithubActionRunStarterAndArtifactDownloader $githubActionRunStarterAndArtifactDownloader;

    public function __construct(?Repository $yt1dApiGithubRepository = null)
    {
        $this->yt = new YouTubeDownloader();
        $this->yt1dApiGithubRepository = $yt1dApiGithubRepository;
        $this->githubActionRunStarterAndArtifactDownloader = (
            new GithubActionRunStarterAndArtifactDownloaderFactory()
        )->make();
    }

    /**
     * @throws Exception
     */
    public function find(string $youtubeLink): string
    {
        // Try YouTubeDownloader
        try {
            $links = $this->yt->getDownloadLinks($youtubeLink);
        } catch (VideoNotFoundException $e) {
            $links = []; // Sometimes it can't find the video for some reason
        } catch (Throwable $e) {
            throw $e;
        }

        $bestFormat = 0;
        $bestLink = null;

        foreach ($links as $link) {
            if (
                strpos($link['format'], 'mp4') !== false
                && strpos($link['format'], 'audio') !== false
            ) {
                $explodedFormat = explode(',', $link['format']);
                $format = (int) substr(trim($explodedFormat[2]), 0, -1);
                if ($format > $bestFormat) {
                    $bestFormat = $format;
                    $bestLink = $link['url'];
                }
            }
        }

        if ($bestLink !== null) {
            return $bestLink;
        }

        // Try yt1d
        if (! $this->yt1dApiGithubRepository) {
            throw new Exception('yt1d API repository not set');
        }

        $artifacts = $this->githubActionRunStarterAndArtifactDownloader->runActionAndGetArtifacts(
            token: $this->yt1dApiGithubRepository->token,
            owner: $this->yt1dApiGithubRepository->owner,
            repo: $this->yt1dApiGithubRepository->repo,
            workflowIdOrWorkflowFileName: 'get-link.yml',
            refreshTime: 60,
            inputs: ['link' => $youtubeLink]
        );

        if (! $artifacts) {
            throw new Exception('No artifact');
        }

        $artifact = $artifacts[0];

        if (! file_exists($artifact)) {
            throw new Exception('Artifact missing');
        }

        $downloadLink = trim(file_get_contents($artifact));
        unlink($artifact);

        if ($downloadLink) {
            return $downloadLink;
        }
        
        throw new Exception('Best link not found');
    }
}
