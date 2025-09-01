<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Media\Media;
use App\Domain\Media\MediaType;
use App\Domain\Shared\Url;
use App\Domain\Media\Service\MediaService;

class UploadMedia extends Command
{
    protected $signature = 'media:upload
        {type : image|video|audio|graph|file}
        {title : Title}
        {description : Description}
        {source_url : Valid URL}
        {--meta=* : key=value pairs (repeatable)}';

    protected $description = 'Simulate a media upload (create + validate + enrich + store)';

    public function handle(MediaService $service): int
    {
        try {
            $type = MediaType::from($this->argument('type'));
        } catch (\ValueError) {
            $this->error('Invalid type. Use one of: image, video, audio, graph, file');
            return self::INVALID;
        }

        $metadata = [];
        foreach ((array) $this->option('meta') as $pair) {
            if (str_contains($pair, '=')) {
                [$k, $v] = explode('=', $pair, 2);
                $metadata[$k] = $v;
            }
        }

        try {
            $media = Media::create(
                $type,
                (string) $this->argument('title'),
                (string) $this->argument('description'),
                Url::fromString((string) $this->argument('source_url')),
                null,
                $metadata
            );

            $stored = $service->store($media);
            $this->line(json_encode($stored, JSON_PRETTY_PRINT));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
