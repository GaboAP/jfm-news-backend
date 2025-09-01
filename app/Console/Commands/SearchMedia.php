<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Media\DTO\SearchCriteria;
use App\Domain\Media\MediaType;
use App\Domain\Media\Service\MediaService;

class SearchMedia extends Command
{
protected $signature = 'media:search
        {--type= : image|video|audio|graph|file}
        {--title= : substring match}';

    protected $description = 'Search media by type and/or title';

    public function handle(MediaService $service): int
    {
        $typeOpt = $this->option('type');
        $type = null;

        if ($typeOpt !== null && $typeOpt !== '') {
            try {
                $type = MediaType::from($typeOpt);
            } catch (\ValueError) {
                $this->error('Invalid --type. Use one of: image, video, audio, graph, file');
                return self::INVALID;
            }
        }

        $criteria = new SearchCriteria(
            type: $type,
            titleContains: $this->option('title') ?: null
        );

        $results = $service->search($criteria);
        if (!$results) {
            $this->info('No results.');
            return self::SUCCESS;
        }

        foreach ($results as $m) {
            $this->line(sprintf(
                '[%s] %s | %s | %s',
                $m->id()->toString(),
                $m->type()->value,
                $m->title(),
                (string) $m->sourceUrl()
            ));
        }
        return self::SUCCESS;
    }
}
