<?php

namespace AshAllenDesign\EmailUtilities\Commands;

use AshAllenDesign\EmailUtilities\Lists\DisposableDomainList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'email-utilities:fetch-disposable-domains')]
class FetchDisposableEmailDomains extends Command
{
    public const string BLOCKLIST_URL =
        'https://raw.githubusercontent.com/disposable-email-domains/disposable-email-domains/master/disposable_email_blocklist.conf';

    protected $signature = 'email-utilities:fetch-disposable-domains';

    protected $description = 'Fetch and store the blocklist of disposable email domains.';

    public function handle(): int
    {
        // Ensure config is set so we don't overwrite the vendor list
        if (blank(config('email-utilities.disposable_email_list_path'))) {
            $this->error("The configuration 'email-utilities.disposable_email_list_path' is not set. Please set it to a valid file path.");
            return self::FAILURE;
        }

        // Absolute file path (string), e.g. storage/app/disposable-domains.txt
        $listPath = DisposableDomainList::getListPath();

        $response = Http::get(self::BLOCKLIST_URL);

        if (! $response->successful()) {
            $this->error('Failed to fetch the blocklist. Status code: ' . $response->status());
            return self::FAILURE;
        }

        $body = trim($response->body());

        // Count lines before writing the file
        // File::lines() works only with real files, so we do this:
        $lines = preg_split('/\R/', $body);
        $lineCount = count(array_filter($lines));
        if ($lineCount < 1000) {
            $this->error('The blocklist contains fewer than 1000 lines. Aborting.');
            return self::FAILURE;
        }

        // Store the fetched contents
        File::put($listPath, $body);

        $fileSize = File::size($listPath);

        Log::info("Disposable domain blocklist updated: $listPath", [
            'domain_count' => $lineCount,
            'file_size'    => $fileSize,
            'file_size_h'  => Number::fileSize($fileSize),
        ]);
        $this->info('Blocklist successfully fetched and stored. Domain count: '.$lineCount);

        return self::SUCCESS;
    }
}
