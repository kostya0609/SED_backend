<?php
namespace SED\Common\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
	protected function getDtoClass(): ?string
	{
		return null;
	}

	public function createDto()
	{
		if (is_null($this->getDtoClass())) {
			throw new \LogicException('Объект DTO не был установлен!');
		}

		$dto = (new \ReflectionClass($this->getDtoClass()))->newInstance();
		$ro = new \ReflectionObject($dto);
		$props = $ro->getProperties(\ReflectionProperty::IS_PUBLIC);

		foreach ($props as $prop) {
			$prop_name = $prop->getName();
			$default_value = $prop->getDeclaringClass()->getDefaultProperties()[$prop_name] ?? null;
			$input = $this->input($prop_name, $default_value);
			$dto->{$prop_name} = $input;
		}

		return $dto;
	}
}