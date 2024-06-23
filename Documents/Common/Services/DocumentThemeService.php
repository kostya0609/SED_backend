<?php
namespace SED\Documents\Common\Services;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\FindOrCreateThemeDto;
use SED\Documents\Common\Models\DocumentTheme;

class DocumentThemeService
{
	public function findOrCreateMany(Collection $themes): Collection
	{
		return $themes->map(fn(FindOrCreateThemeDto $dto) => $this->findOrCreate($dto));
	}

	public function findOrCreate(FindOrCreateThemeDto $dto): DocumentTheme
	{
		if (!empty($dto->id)) {
			return $this->getById($dto->id);
		} else if (!empty($dto->title)) {
			return $this->create($dto->title);
		} else {
			throw new \LogicException("Не удалось найти тему документа, т.к. неправильно переданы параметры темы!");
		}
	}

	public function create(string $title): DocumentTheme
	{
		$theme = new DocumentTheme();
		$theme->title = $title;

		$theme->save();

		return $theme;
	}

	public function getById(int $theme_id): DocumentTheme
	{
		$theme = DocumentTheme::find($theme_id);

		if (!$theme) {
			throw new \LogicException("Не удалось найти тему документа по id $theme_id");
		}

		return $theme;
	}

	public function getAll(): Collection
	{
		return DocumentTheme::all();
	}
}