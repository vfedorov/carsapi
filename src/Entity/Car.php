<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 */
#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private $id;

	#[ORM\Column(type: 'string', length: 255)]
	private $make;

	#[ORM\Column(type: 'string', length: 255)]
	private $model;

	#[ORM\Column(type: 'datetime')]
	private $buildAt;

	#[ORM\ManyToOne(targetEntity: Colour::class)]
	private $colour;

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
	public function getMake(): ?string
	{
		return $this->make;
	}

	/**
	 * @param string $make
	 * @return $this
	 */
	public function setMake(string $make): self
	{
		$this->make = $make;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getModel(): ?string
	{
		return $this->model;
	}

	/**
	 * @param string $model
	 * @return $this
	 */
	public function setModel(string $model): self
	{
		$this->model = $model;

		return $this;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getBuildAt(): ?\DateTime
	{
		return $this->buildAt;
	}

	/**
	 * @param \DateTime $buildAt
	 * @return $this
	 */
	public function setBuildAt(\DateTime $buildAt): self
	{
		$this->buildAt = $buildAt;

		return $this;
	}

	/**
	 * @return Colour|null
	 */
	public function getColour(): ?Colour
	{
		return $this->colour;
	}

	/**
	 * @param Colour|null $colour
	 * @return $this
	 */
	public function setColour(?Colour $colour): self
	{
		$this->colour = $colour;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getColourName(): ?string
	{
		$colourName = null;
		try {
			$colourName = $this->getColour()?->getName();
		} catch (\Exception $e) {
		}
		return $colourName;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'id' => $this->getId(),
			'make' => $this->getMake(),
			'model' => $this->getModel(),
			'build_date' => $this->getBuildAt()->format('Y-m-d'),
			'colour' => $this->getColourName(),
		];
	}

	/**
	 * @return array
	 */
	public function toArrayWithoutId()
	{
		return [
			'make' => $this->getMake(),
			'model' => $this->getModel(),
			'build_date' => $this->getBuildAt() ? $this->getBuildAt()->format('Y-m-d') : null,
			'colour' => $this->getColourName(),
		];
	}
}
