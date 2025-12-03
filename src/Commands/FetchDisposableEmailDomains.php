<?php

declare(strict_types=1);

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
        if ($this->attemptingToWriteToVendorList()) {
            $this->error("The configuration 'email-utilities.disposable_email_list_path' is not set. Please set it to a valid file path.");

            return self::FAILURE;
        }

        // Absolute file path (string), e.g. storage/app/disposable-domains.json
        $listPath = DisposableDomainList::getListPath();

        $response = Http::get(self::BLOCKLIST_URL);

        if (! $response->successful()) {
            $this->error('Failed to fetch the blocklist. Status code: ' . $response->status());
            return self::FAILURE;
        }

        $body = trim($response->body());

        // Count lines before writing the file
        $lines = preg_split('/\R/', $body); // removes trailing CR/LF
        $lineCount = count(array_filter($lines));
        if ($lineCount < 1000) {
            $this->error('The blocklist contains fewer than 1000 lines. Aborting.');
            return self::FAILURE;
        }

        // Sanitize and store the fetched contents
        $isValidDomain = fn (string $domain): bool =>
            filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
        $domains = collect($lines)
            ->map(fn (string $line) => trim($line))
            ->filter()               // removes blank lines
            ->filter($isValidDomain) // keeps only valid domains (including idn ascii 'xn--' format)
            ->values()
            ->all();

        File::put(
            $listPath,
            json_encode($domains, JSON_PRETTY_PRINT)
        );

        Log::info("Disposable domain blocklist updated: $listPath", [
            'domain_count' => $lineCount,
            'file_size'    => $fileSize = File::size($listPath),
            'file_size_h'  => Number::fileSize($fileSize),
        ]);

        $this->info('Blocklist successfully fetched and stored. Domain count: '.$lineCount);

        return self::SUCCESS;
    }

    protected function attemptingToWriteToVendorList(): bool
    {
        return DisposableDomainList::getListPath() === DisposableDomainList::defaultListPath();
    }
}
