<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Article\Contract\ArticleRepositoryInterface;
use App\Domain\Article\Contract\MediaResolverInterface;
use App\Domain\Shared\ArticleId;

class ShowArticle extends Command
{
    protected $signature = 'article:show
        {article_uuid : The Article UUID}
        {--resolve : Resolve image_uuid_list and media_attachments into real media}
        {--json : Output as JSON (includes resolved media if --resolve)}';

    protected $description = 'Display an article and optionally its resolved media';

    public function handle(
        ArticleRepositoryInterface $articles,
        MediaResolverInterface $resolver
    ): int {
        try {
            $id = ArticleId::fromString((string)$this->argument('article_uuid'));
        } catch (\Throwable) {
            $this->error('Invalid article_uuid (must be a UUID).');
            return self::INVALID;
        }

        $article = $articles->get($id);
        if (!$article) {
            $this->error('Article not found.');
            return self::FAILURE;
        }

        $resolved = null;
        if ($this->option('resolve')) {
            $resolved = $resolver->resolveFor($article);
        }

        if ($this->option('json')) {
            $payload = $article->jsonSerialize();
            if ($resolved) {
                $payload['resolved_media'] = [
                    'images' => array_map(fn($m) => $m->jsonSerialize(), $resolved['images']),
                    'attachments' => array_map(fn($m) => $m->jsonSerialize(), $resolved['attachments']),
                ];
            }
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        }

        // Human-readable output
        //$this->line('ID: ' . $article->id()->toString());
        $this->line("\nCreated: " . $article->createdAt()->format(DATE_ATOM) . "\n");
        $this->info($article->headline());
        $this->newLine();
        $this->line($article->content());
        $this->newLine();

        $this->line('Images:');
        foreach ($article->imageUuidList() as $uuid) {
            $this->line('  - ' . $uuid);
        }
        $this->newLine();

        $this->line('Attachments:');
        foreach ($article->mediaAttachments() as $att) {
            $this->line(sprintf('  - %s (%s)', (string)$att->mediaId(), $att->type()->value));
        }

        if ($resolved) {
            $this->newLine();
            $this->comment('Resolved media:');
            foreach ($resolved['images'] as $m) {
                $this->line(sprintf('  [image] %s | %s', $m->sourceUrl()->toString(), $m->title()));
            }
            foreach ($resolved['attachments'] as $m) {
                $this->line(sprintf('  [attachment] %s | %s (%s)', $m->sourceUrl()->toString(), $m->title(), $m->type()->value));
            }
        }

        return self::SUCCESS;
    }
}
