<?php
namespace SED\Report\Interfaces;

interface DocumentFactory
{
	/**
	 * @return \Illuminate\Support\Collection<Document>
	 */
	public function getAll();
}