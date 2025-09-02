<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Media\Contract\MediaRepositoryInterface;
use App\Domain\Media\Contract\MediaValidatorInterface;
use App\Domain\Media\Contract\MediaMetadataServiceInterface;
use App\Infrastructure\Media\Repository\InMemoryMediaRepository;
use App\Infrastructure\Media\Validation\AllowedTypesValidator;
use App\Infrastructure\Media\Metadata\BasicMetadataService;
use App\Infrastructure\Media\Repository\FileMediaRepository;

class MediaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //For in memory processing (Forgets data after every command.)
        /* $this->app->singleton(
            MediaRepositoryInterface::class,
            InMemoryMediaRepository::class
        ); */
        $this->app->singleton(
            MediaRepositoryInterface::class,
            FileMediaRepository::class
        );
        $this->app->singleton(
            MediaValidatorInterface::class,
            AllowedTypesValidator::class
        );

        $this->app->singleton(
            MediaMetadataServiceInterface::class,
            BasicMetadataService::class
        );
    }
}
