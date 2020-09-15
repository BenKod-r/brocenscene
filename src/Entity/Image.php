<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="poster")
     */
    private $pproduct;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="image", cascade={"persist"})
     */
    private $iproduct;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Content::class, mappedBy="poster")
     */
    private $posterIndex;

    public function __construct()
    {
        $this->pproduct = new ArrayCollection();
        $this->iproduct = new ArrayCollection();
        $this->creationDate = new DateTime();
        $this->posterIndex = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getPproduct(): Collection
    {
        return $this->pproduct;
    }

    public function addPproduct(Product $pproduct): self
    {
        if (!$this->pproduct->contains($pproduct)) {
            $this->pproduct[] = $pproduct;
            $pproduct->setPoster($this);
        }

        return $this;
    }

    public function removePproduct(Product $pproduct): self
    {
        if ($this->pproduct->contains($pproduct)) {
            $this->pproduct->removeElement($pproduct);
            // set the owning side to null (unless already changed)
            if ($pproduct->getPoster() === $this) {
                $pproduct->setPoster(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getIproduct(): Collection
    {
        return $this->iproduct;
    }

    public function addIproduct(Product $iproduct): self
    {
        if (!$this->iproduct->contains($iproduct)) {
            $this->iproduct[] = $iproduct;
            $iproduct->addImage($this);
        }

        return $this;
    }

    public function removeIproduct(Product $iproduct): self
    {
        if ($this->iproduct->contains($iproduct)) {
            $this->iproduct->removeElement($iproduct);
            $iproduct->removeImage($this);
        }

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Content[]
     */
    public function getPosterIndex(): Collection
    {
        return $this->posterIndex;
    }

    public function addPosterIndex(Content $posterIndex): self
    {
        if (!$this->posterIndex->contains($posterIndex)) {
            $this->posterIndex[] = $posterIndex;
            $posterIndex->setPoster($this);
        }

        return $this;
    }

    public function removePosterIndex(Content $posterIndex): self
    {
        if ($this->posterIndex->contains($posterIndex)) {
            $this->posterIndex->removeElement($posterIndex);
            // set the owning side to null (unless already changed)
            if ($posterIndex->getPoster() === $this) {
                $posterIndex->setPoster(null);
            }
        }

        return $this;
    }
}
