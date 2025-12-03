<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Commands;

use AshAllenDesign\EmailUtilities\Lists\DisposableDomainList;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'email-utilities:fetch-disposable-domains')]
class FetchDisposableEmailDomains extends Command
{
    public const string BLOCKLIST_URL = 'https://raw.githubusercontent.com/disposable-email-domains/disposable-email-domains/master/disposable_email_blocklist.conf';

    protected $signature = 'email-utilities:fetch-disposable-domains';

    protected $description = 'Fetch and store the blocklist of disposable email domains.';

    /**
     * @throws ConnectionException
     * @throws \JsonException
     */
    public function handle(): int
    {
        if ($this->attemptingToWriteToVendorList()) {
            $this->error("The configuration 'email-utilities.disposable_email_list_path' is not set. Please set it to a valid file path.");

            return self::FAILURE;
        }

        /** @var Response $response */
        $response = Http::get(self::BLOCKLIST_URL);

        if (! $response->successful()) {
            $this->error('Failed to fetch the blocklist. Status code: ' . $response->status());
            return self::FAILURE;
        }

        $lines = $this->readLinesFromResponse(trim($response->body()));

        if (!$this->linesAreValid($lines)) {
            return self::FAILURE;
        }

        $domains = $this->readDomainsFromLines($lines);

        File::put(
            DisposableDomainList::getListPath(),
            json_encode($domains, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
        );

        $this->info('Blocklist successfully fetched and stored. Domain count: '.count($domains));

        return self::SUCCESS;
    }

    protected function attemptingToWriteToVendorList(): bool
    {
        return DisposableDomainList::getListPath() === DisposableDomainList::defaultListPath();
    }

    /**
     * Read the domains from the given lines and then return them for storing.
     * We'll trim each line, filter out any empty lines, and validate the
     * domains.
     *
     * @param list<string> $lines
     * @return string[]
     */
    protected function readDomainsFromLines(array $lines): array
    {
        return collect($lines)
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->filter(function (string $domain): bool {
                return filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
            })
            ->values()
            ->all();
    }

    /**
     * Split the response body into an array of lines. If we fail to split the
     * lines, we'll just return an empty array and let the "linesAreValid"
     * method handle the error.
     *
     * @return list<string>
     */
    protected function readLinesFromResponse(string $responseBody): array
    {
        return preg_split('/\R/', $responseBody) ?: [];
    }

    /**
     * @param string[] $lines
     * @return bool
     */
    protected function linesAreValid(array $lines): bool
    {
        $lineCount = count(array_filter($lines));

        if ($lineCount < 1000) {
            $this->error('The blocklist contains fewer than 1000 lines. Aborting.');

            return false;
        }

        return true;
    }
}
