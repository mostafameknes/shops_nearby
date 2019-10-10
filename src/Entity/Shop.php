<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Shop
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $Name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Address;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $Longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $Latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255)
     */
    private $picture;

    /**
    * @var file $file
    * @Assert\File(
    *      maxSize = "4M",
    *      mimeTypes = {"image/jpeg", "image/jpg", "image/png", "image/x-png", "image/gif"},
    *      mimeTypesMessage = "this image format is unknown",
    *      uploadIniSizeErrorMessage = "The downloaded file is too large",
    *      uploadFormSizeErrorMessage = "The downloaded file is larger than the one authorized by the HTML input field",
    *      uploadErrorMessage = "The downloaded file can not be transferred for some unknown reason",
    *      maxSizeMessage = "The file is too large"
    * )
    */
    private $file;

    public function __construct()
    {
        $this->enabled = true; // by default activate the shop
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->Address;
    }

    public function setAddress(string $Address): self
    {
        $this->Address = $Address;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->Longitude;
    }

    public function setLongitude(?float $Longitude): self
    {
        $this->Longitude = $Longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->Latitude;
    }

    public function setLatitude(?float $Latitude): self
    {
        $this->Latitude = $Latitude;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /*  Upload de fichier picture */

    public function getFile()
    {
        return $this->file;
    }
    
    public function setFile($file)
    {
        $this->file = $file;
    
        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $chemin=str_replace(" ","-", $this->Name);
            $this->picture=$chemin.'.'.$this->file->getClientOriginalExtension();
        }
    }
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }
        if (file_exists($this->getUploadRootDir() . '/'. $this->picture)) {
                unlink($this->getUploadRootDir() . '/'. $this->picture);
            }
        $this->file->move($this->getUploadRootDir(), $this->picture);

        unset($this->file);
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->getAbsolutePath())) {
            // delete the file
            unlink($this->getAbsolutePath());
            $this->picture=null;
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->picture ? null : $this->getUploadRootDir().'/'.$this->picture;
    }

    public function getWebPath()
    {
        return null === $this->picture ? null : $this->getUploadDir().'/'.$this->picture;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../public/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'img/shops';
    }
}