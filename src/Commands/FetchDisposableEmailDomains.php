<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Commands;

use AshAllenDesign\EmailUtilities\Lists\DisposableDomainList;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

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
            $this->components->error("The configuration 'email-utilities.disposable_email_list_path' is not set. Please set it to a valid file path.");

            return self::FAILURE;
        }

        /** @var Response $response */
        $response = Http::get(self::BLOCKLIST_URL);

        if (! $response->successful()) {
            $this->components->error('Failed to fetch the blocklist. Status code: ' . $response->status());
            return self::FAILURE;
        }

        $lines = $this->readLinesFromResponse(trim($response->body()));

        if (!$this->linesAreValid($lines)) {
            return self::FAILURE;
        }

        $domains = $this->readDomainsFromLines($lines);

        $existingList = $this->getExistingList();

        File::put(
            DisposableDomainList::getListPath(),
            json_encode($domains, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
        );

        $this->summary(existingList: $existingList, newList: $domains);

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
            $this->components->error('The blocklist contains fewer than 1000 lines. Aborting.');

            return false;
        }

        return true;
    }

    /**
     * @param string[] $existingList
     * @param string[] $newList
     */
    protected function summary(array $existingList, array $newList): void
    {
        $this->components->success('Blocklist successfully fetched and stored.');

        $this->components->twoColumnDetail('Stored at: ', DisposableDomainList::getListPath());

        $this->newLine();

        $this->components->twoColumnDetail('Previous domain count: ', (string) count($existingList));
        $this->components->twoColumnDetail('New domain count: ', (string) count($newList));

        $this->newLine();

        // Find the domains that are present in the new list but weren't in the existing list
        $addedDomains = array_diff($newList, $existingList);

        // Find the domains that are present in the existing list but aren't in the new list
        $removedDomains = array_diff($existingList, $newList);

        $this->components->twoColumnDetail('Added domains: ', (string) count($addedDomains));

        if (count($addedDomains)) {
            $this->components->bulletList($addedDomains, verbosity: OutputInterface::VERBOSITY_VERBOSE);
        }

        $this->components->twoColumnDetail('Removed domains: ', (string) count($removedDomains));

        if (count($removedDomains)) {
            $this->components->bulletList($removedDomains, verbosity: OutputInterface::VERBOSITY_VERBOSE);
        }
    }

    /**
     * Read the existing list before we overwrite it. We are doing this so we can build
     * a summary of what has changed in the list.
     *
     * @return list<string>
     */
    protected function getExistingList(): array
    {
        try {
            return DisposableDomainList::get();
        } catch (FileNotFoundException) {
            return [];
        }
    }
}
