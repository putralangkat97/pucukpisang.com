<?php

namespace App\Enums;

enum Status: int
{
    case PENDING = 0;
    case EXTRACTING_DOCUMENT = 1;
    case GENERATING_DOCUMENT = 2;
    case DOWNLOAD_AUDIO = 3;
    case EXTRACTING_AUDIO = 4;
    case PROCESSING_TRANSCRIBE = 5;
    case PROCESSING_SUMMARY = 6;
    case PROCESSING_TRANSLATE = 7;
    case COMPLETE = 98;
    case ERRORED = 99;

    public function message(): string
    {
        return match ($this) {
            Status::PENDING => 'Pending processing',
            Status::EXTRACTING_DOCUMENT => 'Extracting document',
            Status::GENERATING_DOCUMENT => 'Generating document',
            Status::DOWNLOAD_AUDIO => 'Downloading audio',
            Status::EXTRACTING_AUDIO => 'Extracting audio',
            Status::PROCESSING_TRANSCRIBE => 'Transcribing',
            Status::PROCESSING_SUMMARY => 'Summarizing',
            Status::PROCESSING_TRANSLATE => 'Translating',
            Status::COMPLETE => 'All done!',
            Status::ERRORED => 'There was an error, check the console for more details',
        };
    }
}
