<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Article\Contract\ArticleRepositoryInterface;
use App\Domain\Article\Article;
use App\Domain\Article\MediaAttachment;
use App\Domain\Media\MediaType;
use App\Domain\Shared\MediaId;

class CreateArticle extends Command
{
    protected $signature = 'article:create
        {headline : Article headline}
        {--content= : Inline content (use --content-file for files)}
        {--content-file= : Path to a text/markdown file for content}
        {--image=* : Image UUID (repeatable)}
        {--attach=* : Attachment spec (uuid:type[:label]) repeatable}
        {--json : Output JSON payload}';

    protected $description = 'Create an article and store it using the file-backed repository';

    public function handle(ArticleRepositoryInterface $articles): int
    {
        $headline = trim((string) $this->argument('headline'));

        // 1) Content
        $content = (string) ($this->option('content') ?? '');
        $contentFile = $this->option('content-file');

        if ($contentFile) {
            if (!is_file($contentFile)) {
                $this->error("Content file not found: {$contentFile}");
                return self::INVALID;
            }
            $fileData = file_get_contents($contentFile);
            if ($fileData === false) {
                $this->error("Unable to read content file: {$contentFile}");
                return self::FAILURE;
            }
            $content = (string) $fileData;
        }

        // 2) Images (UUID list)
        $imageUuids = [];
        foreach ((array) $this->option('image') as $uuid) {
            $uuid = trim((string) $uuid);
            if ($uuid === '') { continue; }
            try {
                // Validate shape; store as string
                MediaId::fromString($uuid);
                $imageUuids[] = $uuid;
            } catch (\Throwable) {
                $this->warn("Skipping invalid image UUID: {$uuid}");
            }
        }

        // 3) Attachments: uuid:type[:label]
        $attachments = [];
        foreach ((array) $this->option('attach') as $spec) {
            $spec = trim((string) $spec);
            if ($spec === '') { continue; }

            // Parse "uuid:type[:label]"
            $parts = explode(':', $spec, 3);
            if (count($parts) < 2) {
                $this->warn("Skipping invalid attachment spec (need uuid:type[:label]): {$spec}");
                continue;
            }
            [$uuid, $typeRaw] = $parts;
            $label = $parts[2] ?? null;

            try {
                $mediaId = MediaId::fromString($uuid);
                $type    = MediaType::from($typeRaw);
            } catch (\Throwable) {
                $this->warn("Skipping invalid attachment: {$spec}");
                continue;
            }

            $attachments[] = new MediaAttachment($mediaId, $type, $label);
        }

        // 4) Create and persist
        try {
            $article = Article::create(
                headline: $headline,
                content: $content,
                imageUuidList: $imageUuids,
                mediaAttachments: $attachments
            );
            $articles->save($article);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        // 5) Output
        if ($this->option('json')) {
            $this->line(json_encode($article, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } else {
            $this->info('Article created: '.$article->id()->toString());
            $this->line('Headline: '.$article->headline());
            $this->line('Images: '.implode(', ', $article->imageUuidList()) ?: '(none)');
            if ($attachments) {
                $this->line('Attachments:');
                foreach ($attachments as $a) {
                    $this->line(sprintf(
                        '  - %s (%s%s)',
                        (string) $a->mediaId(),
                        $a->type()->value,
                        $a->label() ? '; label='.$a->label() : ''
                    ));
                }
            }
        }

        return self::SUCCESS;
    }
}
