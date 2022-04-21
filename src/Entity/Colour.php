<?php

namespace App\Entity;

use App\Repository\ColourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 */
#[ORM\Entity(repositoryClass: ColourRepository::class)]
class Colour
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private $id;

	#[ORM\Column(type: 'string', length: 255)]
	private $name;

	#[ORM\Column(type: 'boolean', nullable: true)]
	private $editable;

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return string|null
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName(string $name): self
	{
		$this->name = strtolower($name);

		return $this;
	}

	/**
	 * @return bool|null
	 */
	public function getEditable(): ?bool
	{
		return $this->editable;
	}

	/**
	 * @param bool|null $editable
	 * @return $this
	 */
	public function setEditable(?bool $editable): self
	{
		$this->editable = $editable;

		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'editable' => $this->getEditable(),
		];
	}
}
