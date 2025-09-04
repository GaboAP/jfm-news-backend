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
        {--title= : substring match}
        {--with-meta : Includes metadata in output}
        {--json : Output full JSON records}';

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

        // Full JSON mode (includes metadata, etc, etc.)
        if ($this->option('json')) {
            $payload = array_map(fn($m) => $m->jsonSerialize(), $results);
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        }

        // Default human-readable mode
        $withMeta = (bool) $this->option('with-meta');

        foreach ($results as $m) {
            $this->line(sprintf(
                '[%s] %s | %s | %s',
                $m->id()->toString(),
                $m->type()->value,
                $m->title(),
                (string) $m->sourceUrl()
            ));

            if ($withMeta) {
                $meta = $m->metadata();
                if (!empty($meta)) {
                    $this->line('  meta: ' . json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
                } else {
                    $this->line('  meta: {}');
                }
            }
        }
        return self::SUCCESS;
    }
}
