<?php

namespace App\Enums;

enum Status: int
{
    case PENDING = 0;
    case EXTRACTING_DOCUMENT = 1;
    case PROCESSING_TRANSLATE = 2;
    case PROCESSING_SUMMARY = 3;
    case GENERATING_DOCUMENT = 4;
    case COMPLETE = 98;
    case ERRORED = 99;

    public function message(): string
    {
        return match ($this) {
            Status::PENDING => 'Pending processing',
            Status::EXTRACTING_DOCUMENT => 'Extracting document',
            Status::PROCESSING_TRANSLATE => 'Translating',
            Status::PROCESSING_SUMMARY => 'Summarizing',
            Status::GENERATING_DOCUMENT => 'Generating document',
            Status::COMPLETE => 'All done!',
            Status::ERRORED => 'There was an error, check the console for more details',
        };
    }
}
