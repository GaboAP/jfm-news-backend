<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Media\Contract\MediaMetadataServiceInterface;
use App\Domain\Media\Contract\MediaRepositoryInterface;
use App\Domain\Shared\MediaId;

class EnrichMedia extends Command
{
    protected $signature = 'media:enrich
        {uuid? : Media UUID}
        {--all : Enrich all stored media}';

    protected $description = 'Apply metadata enrichment to one or all media entries';

    public function handle(
        MediaRepositoryInterface $repo,
        MediaMetadataServiceInterface $metadata
    ): int {
        $uuid = $this->argument('uuid');
        $all  = (bool) $this->option('all');

        if (!$uuid && !$all) {
            $this->error('Provide a {uuid} or use --all');
            return self::INVALID;
        }

        $targets = [];
        if ($all) {
            // crude fetch-all via search with empty criteria (repo impl detail)
            $targets = $repo->search(new \App\Domain\Media\DTO\SearchCriteria());
        } else {
            try {
                $id = MediaId::fromString((string) $uuid);
            } catch (\Throwable) {
                $this->error('Invalid UUID');
                return self::INVALID;
            }
            $media = $repo->get($id);
            if (!$media) {
                $this->error('Media not found');
                return self::FAILURE;
            }
            $targets = [$media];
        }

        foreach ($targets as $media) {
            $enriched = $metadata->enrich($media);
            // persist enriched copy
            $repo->save($enriched);
            $this->line('Enriched: ' . $enriched->id()->toString());
        }

        return self::SUCCESS;
    }
}
