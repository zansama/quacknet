<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment implements \JsonSerializable
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
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ducks", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quack", inversedBy="comments")
     */
    private $quack;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOk;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?Ducks
    {
        return $this->Author;
    }

    public function setAuthor(?Ducks $Author): self
    {
        $this->Author = $Author;

        return $this;
    }

    public function getQuack(): ?Quack
    {
        return $this->quack;
    }

    public function setQuack(?Quack $quack): self
    {
        $this->quack = $quack;

        return $this;
    }

    public function getIsOk(): ?bool
    {
        return $this->isOk;
    }

    public function setIsOk(bool $isOk): self
    {
        $this->isOk = $isOk;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
        $id = strval($this->id);
        return $id;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'author' => [$this->Author],
            'message' => $this->message,
            'created_at' => $this->createdAt
        ];
    }
}
