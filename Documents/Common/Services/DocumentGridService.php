<?php
namespace SED\Documents\Common\Services;

/**
 * Сервис для формирования грида
 */
class DocumentGridService
{
	/**
	 * Преобразование списка акций для вывода в grid
	 */
	public function toGrid(\Illuminate\Support\Collection $promos): \Illuminate\Support\Collection
	{
		return $promos->map(function ($document) {
			return [
				'id' => [
					[
						'value' => $document->id,
					]
				],
				'document_id' => [
					[
						'value' => $document->document_id,
					]
				],
				'number' => [
					[
						'value' => $document->number,
					],
				],
				'type' => [
					[
						'value' => $document->type->title,
						'params' => [
							'type_id' => $document->type->id,
						],
					],
				],
				'theme' => [
					[
						'value' => $document->theme,
					],
				],
				'executor' => [
					[
						'value' => $this->createLink($document->initiator->full_name, $document->initiator->link),
					],
				],
				'status_title' => [
					[
						'value' => $document->status_title,
					],
				],
				'created_at' => [
					[
						'value' => $document->created_at->format('d-m-Y'),
					]
				],
				'updated_at' => [
					[
						'value' => $document->updated_at->format('d-m-Y'),
					]
				],
			];
		});
	}

	protected function getDownloadUrl(int $file_id): string
	{
		return (env('APP_ENV') === 'dev' ? 'http://localhost/' : 'https://bitrix.bsi.local/api/') . "promo/v1/downloads/$file_id";
	}

	protected function createLink($title, $url): array
	{
		return [
			'params' => [
				'href' => $url,
				'download' => 'download',
				'class' => 'el-link el-link--primary grid-link',
			],
			'tag' => 'a',
			'value' => $title,
		];
	}

	/**
	 * @param mixed $file
	 * @return array|string
	 */
	protected function createLinkFile($file)
	{
		return $file ? $this->createLink($file->name, $this->getDownloadUrl($file->id)) : '';
	}

	protected function createBooleanValue(bool $value): string
	{
		return $value ? 'Да' : 'Нет';
	}

	/**
	 * @param string|array $title
	 */
	protected function createTag($title): array
	{
		return [
			'tag' => 'div',
			'value' => [
				'tag' => 'span',
				'value' => $title,
				'params' => [
					'class' => 'el-tag el-tag--light',
				],
			],
		];
	}
}