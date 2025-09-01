<?php

namespace App\Domain\Media;

enum MediaType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case GRAPH = 'graph';
    case FILE  = 'file';
}
