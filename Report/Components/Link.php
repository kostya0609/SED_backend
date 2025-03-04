<?php
namespace SED\Report\Components;

class Link
{
	public string $title;
	public string $url;

	public function __construct(string $title, string $url)
	{
		$this->title = $title;
		$this->url = $url;
	}
}