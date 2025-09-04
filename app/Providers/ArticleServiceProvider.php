<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Article\Contract\ArticleRepositoryInterface;
use App\Infrastructure\Article\Repository\FileArticleRepository;
use App\Domain\Article\Contract\MediaResolverInterface;
use App\Domain\Article\Service\MediaResolverService;
use App\Infrastructure\Article\Repository\InMemoryArticleRepository;

final class ArticleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
                $repo = config('article.repository', 'memory');
        $this->app->singleton(
            ArticleRepositoryInterface::class,
            $repo === 'file' ? FileArticleRepository::class : InMemoryArticleRepository::class
        );

        $this->app->singleton(MediaResolverInterface::class, MediaResolverService::class);
    }
}
