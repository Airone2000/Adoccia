<?php

namespace App\Entity;

use App\Validator\Enum;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Feedback given by an authenticated User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\FeedbackRepository")
 * @ORM\EntityListeners("App\EntityListener\FeedbackListener")
 */
class Feedback
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string The type of feedback.
     *
     * @ORM\Column(type="string", length=20)
     *
     * @Enum(
     *     enumClass="App\Enum\FeedbackTypeEnum",
     *     message="The type should be either 'bug', 'feature', or 'else'."
     * )
     */
    private $type;

    /**
     * @var string What a user is reporting or suggesting.
     *
     * @ORM\Column(type="text", length=1000)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="1000")
     */
    private $description;

    /**
     * @var User The user associated to the feedback.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="feedback")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var DateTimeImmutable When the feedback has been given.
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
