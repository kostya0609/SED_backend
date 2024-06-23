<?php
namespace SED\Common\Services;

use App\Modules\File\Facades\FileFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DocumentFileService
{
	private int $document_id;
	private array $document_files_by_type;
	private Collection $input_files;

	public function __construct(int $document_id, Collection $input_files)
	{
		$this->document_id = $document_id;
		$this->input_files = $input_files;
	}

	public function setType(string $type, int $type_id, $model, Collection $files)
	{
		$this->document_files_by_type[$type] = [
			'files' => $files,
			'model' => $model,
			'type_id' => $type_id,
		];

		return $this;
	}

	public function uploads()
	{
		if ($this->input_files->keys()->isEmpty()) {
			throw new \LogicException("Не было передано ни одного типа файлов!");
		}

		$this->input_files->keys()->each(function ($type) {
			$this->uploadFilesByType($type);
		});
	}

	protected function getInputFilesByType(string $type)
	{
		return $this->input_files->get($type);
	}

	protected function getFilesByType(string $type): Collection
	{
		if (!$this->document_files_by_type[$type]) {
			throw new \Exception("Not found file type $type");
		}

		return $this->document_files_by_type[$type]['files'];
	}

	protected function getDocumentFileByType(string $type)
	{
		if (!$this->document_files_by_type[$type]) {
			throw new \Exception("Not found file type $type");
		}

		return $this->document_files_by_type[$type];
	}

	protected function uploadFilesByType(string $type): void
	{
		$document_files = $this->getFilesByType($type);
		$input_files = collect($this->input_files[$type]);

		$uploaded_files = $this->getUploadedFiles($input_files);
		$saved_file_ids = $this->getSavedFileIds($input_files);
		$deleted_files = $this->getDeletedFiles($document_files, $saved_file_ids);

		$this->delete($deleted_files, $type);
		$this->upload($uploaded_files, $type);
	}

	protected function getDocument(int $document_id): Model
	{
		throw new \Exception('Method not implemented!');
	}

	protected function getUploadedFiles(Collection $input_files): Collection
	{
		return $input_files
			->filter(fn($file) => !empty ($file['raw']))
			->map(fn($file) => $file['raw'])
			->values();
	}

	protected function getSavedFileIds(Collection $input_files): Collection
	{
		return $input_files
			->filter(fn($file) => !empty ($file['id']))
			->map(fn($file) => $file['id'])
			->values();
	}

	protected function getDeletedFiles(Collection $document_files, Collection $saved_file_ids): Collection
	{
		return $document_files->filter(function ($document_file) use ($saved_file_ids): bool {
			return !$saved_file_ids->contains($document_file->file_id);
		})->pluck('file_id');
	}

	protected function delete(Collection $deleted_files, string $type)
	{
		$document_file = $this->getDocumentFileByType($type);

		$deleted_files->each(function ($file_id) {
			FileFacade::delete($file_id);
		});

		$document_file['model']->whereIn('file_id', $deleted_files)->delete();
	}

	protected function upload(Collection $uploaded_files, string $type)
	{
		$uploaded_files = $uploaded_files = FileFacade::createMany($uploaded_files)->map(fn($file) => $file->id);
		$document_file = $this->getDocumentFileByType($type);

		$files = $uploaded_files->map(function ($file) use ($document_file) {
			return [
				'type_id' => $document_file['type_id'],
				'file_id' => $file,
			];
		});

		$document_file['model']->createMany($files);
	}
}