<?php

namespace Tests\Feature;

use App\Controllers\DatasetUpload;
use App\Database\Seeds\MvpSeeder;
use App\Models\DatasetFileModel;
use App\Models\DatasetModel;
use App\Models\DatasetVersionModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\Files\FileCollection;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * @internal
 */
final class DatasetUploadTest extends CIUnitTestCase
{
    use ControllerTestTrait;
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $namespace = 'App';
    protected $seed = MvpSeeder::class;

    /** @var string[] */
    private array $temporaryFiles = [];

    /** @var string[] */
    private array $createdDirectories = [];

    protected function setUp(): void
    {
        parent::setUp();
        Services::resetSingle('validation');
        $this->setUpControllerTestTrait();

        $user = model(UserModel::class)->where('email', 'user@example.test')->first();
        $this->assertIsArray($user);
        session()->set([
            'user_id' => (int) $user['id'],
            'user_name' => (string) $user['name'],
            'user_email' => (string) $user['email'],
            'role' => 'user',
            'roles' => ['user'],
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        foreach ($this->createdDirectories as $path) {
            if (is_dir($path) && (scandir($path) ?: []) === ['.', '..']) {
                rmdir($path);
            }
        }

        parent::tearDown();
    }

    /**
     * @return array<string, array{string, string, string, string}>
     */
    public static function validUploadTrials(): array
    {
        return [
            'tabular trial' => ['Evaluation Tabular Dataset', 'Tabular', '.csv', 'survey,statistics'],
            'text trial' => ['Evaluation Text Dataset', 'Text', '.txt, .json', 'interview,language'],
            'image trial' => ['Evaluation Image Dataset', 'Image', '.jpg, .png', 'image,classification'],
        ];
    }

    #[DataProvider('validUploadTrials')]
    public function testValidDatasetUploadCreatesRepositoryRecords(
        string $title,
        string $dataType,
        string $contentFormats,
        string $tags
    ): void {
        $nextDatasetId = (int) (model(DatasetModel::class)->selectMax('id')->first()['id'] ?? 0) + 1;
        $targetDirectory = WRITEPATH . 'uploads/datasets/' . $nextDatasetId;
        if (! is_dir($targetDirectory)) {
            $this->createdDirectories[] = $targetDirectory;
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'asog-upload-');
        $this->assertIsString($temporaryFile);
        file_put_contents($temporaryFile, "PK\x03\x04evaluation");
        $this->temporaryFiles[] = $temporaryFile;

        $uploadedFile = new class(
            $temporaryFile,
            strtolower(str_replace(' ', '-', $title)) . '.zip',
            'application/zip',
            filesize($temporaryFile),
            UPLOAD_ERR_OK
        ) extends UploadedFile {
            public function move(string $targetPath, ?string $name = null, bool $overwrite = false)
            {
                $this->hasMoved = true;

                return true;
            }
        };

        $fileCollection = new class($uploadedFile) extends FileCollection {
            public function __construct(UploadedFile $uploadedFile)
            {
                $this->files = ['dataset_file' => $uploadedFile];
            }
        };

        $this->withUri('http://example.com/upload');
        $request = $this->request;
        $request->setMethod('post');
        $postData = [
            'title' => $title,
            'description' => 'A valid isolated upload used for repository evaluation.',
            'category' => 'Evaluation',
            'tags' => $tags,
            'data_type' => $dataType,
            'content_formats' => $contentFormats,
            'source_type' => 'Primary',
            'research_title' => 'Dataset Repository Evaluation',
            'project_head' => 'Evaluation Team',
            'members' => 'Evaluation Team',
            'source_link' => '',
            'access_type' => 'public',
            'anonymized' => '1',
            'form' => 'Evaluation trial',
        ];
        $request->setGlobal('post', $postData);
        $request->setGlobal('request', $postData);

        $filesProperty = new ReflectionProperty(IncomingRequest::class, 'files');
        $filesProperty->setValue($request, $fileCollection);
        Services::resetSingle('validation');

        $result = $this->withRequest($request)
            ->controller(DatasetUpload::class)
            ->execute('store');

        $dataset = model(DatasetModel::class)->where('title', $title)->first();
        $this->assertIsArray($dataset, json_encode(session()->getFlashdata('errors')) ?: 'Upload did not create a dataset record.');
        $datasetId = (int) $dataset['id'];

        $result->assertRedirectTo(site_url('datasets/' . $datasetId . '/edit'));
        $this->assertSame(DatasetModel::STATUS_PENDING_TECHNICAL, $dataset['status']);
        $this->assertSame($dataType, $dataset['data_type']);
        $this->assertSame(1, model(DatasetFileModel::class)->where('dataset_id', $datasetId)->countAllResults());
        $this->assertSame(1, model(DatasetVersionModel::class)->where('dataset_id', $datasetId)->countAllResults());
        $this->assertSame(1, db_connect()->table('audit_logs')
            ->where('action', 'dataset_upload')
            ->where('entity_id', $datasetId)
            ->countAllResults());
    }
}
