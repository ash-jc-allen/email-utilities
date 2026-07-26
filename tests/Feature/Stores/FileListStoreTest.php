<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Stores;

use AshAllenDesign\EmailUtilities\Exceptions\ListStorageException;
use AshAllenDesign\EmailUtilities\Lists\ListType;
use AshAllenDesign\EmailUtilities\Stores\FileListStore;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;

class FileListStoreTest extends TestCase
{
    protected function tearDown(): void
    {
        File::delete(storage_path('app/file-list-store-test.json'));

        parent::tearDown();
    }

    #[Test]
    public function null_is_returned_if_no_path_is_configured(): void
    {
        config(['email-utilities.disposable_email_list_path' => null]);

        $this->assertNull((new FileListStore())->get(ListType::DisposableDomains));
    }

    #[Test]
    public function list_can_be_stored_and_retrieved(): void
    {
        config(['email-utilities.disposable_email_list_path' => storage_path('app/file-list-store-test.json')]);

        $store = new FileListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com', 'throwaway.com']);

        $this->assertSame(['disposable.com', 'throwaway.com'], $store->get(ListType::DisposableDomains));
    }

    #[Test]
    public function exception_is_thrown_if_the_configured_file_does_not_exist(): void
    {
        $this->expectException(FileNotFoundException::class);

        config(['email-utilities.disposable_email_list_path' => './invalid-path.json']);

        (new FileListStore())->get(ListType::DisposableDomains);
    }

    #[Test]
    public function exception_is_thrown_when_storing_without_a_configured_path(): void
    {
        $this->expectException(ListStorageException::class);
        $this->expectExceptionMessage("The configuration 'email-utilities.disposable_email_list_path' is not set. Please set it to a valid file path.");

        config(['email-utilities.disposable_email_list_path' => null]);

        (new FileListStore())->put(ListType::DisposableDomains, ['disposable.com']);
    }
}
