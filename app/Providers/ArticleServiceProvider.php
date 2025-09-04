<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Article\Contract\ArticleRepositoryInterface;
use App\Infrastructure\Article\Repository\FileArticleRepository;
use App\Domain\Article\Contract\MediaResolverInterface;
use App\Domain\Article\Service\MediaResolverService;

final class ArticleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ArticleRepositoryInterface::class, FileArticleRepository::class);

        $this->app->singleton(MediaResolverInterface::class, MediaResolverService::class);
    }
}
