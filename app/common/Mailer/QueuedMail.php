<?php
declare(strict_types=1);

namespace App\Common\Mailer;

use App\Common\Database\AbstractAppModel;
use App\Common\Database\Primary\MailsQueue;
use App\Common\Exception\AppException;
use Comely\DataTypes\Buffer\Binary;

/**
 * Class QueuedMail
 * @package App\Common\Mailer
 */
class QueuedMail extends AbstractAppModel
{
    public const TABLE = MailsQueue::NAME;
    public const SERIALIZABLE = false;

    /** @var int */
    public int $id;
    /** @var string */
    public string $lang;
    /** @var string */
    public string $status;
    /** @var string|null */
    public ?string $lastError = null;
    /** @var int */
    public int $attempts;
    /** @var string */
    public string $email;
    /** @var string */
    public string $subject;
    /** @var string|null */
    public ?string $preHeader = null;
    /** @var string */
    public string $compiled;
    /** @var string|null */
    public ?string $template = null;
    /** @var int */
    public int $deleteOnSent = 0;
    /** @var int */
    public int $timeStamp;
    /** @var int|null */
    public ?int $lastAttempt = null;

    /** @var bool|null */
    public ?bool $_checksumVerified = null;

    /**
     * @throws AppException
     */
    public function beforeQuery()
    {
        if (is_string($this->lastError)) {
            if (strlen($this->lastError) > 255) {
                $this->lastError = substr($this->lastError, 0, 255);
            }
        }

        if (is_string($this->preHeader)) {
            if (strlen($this->preHeader) > MailsQueue::MAX_PRE_HEADER) {
                throw new AppException(
                    sprintf('Message pre-header exceeds limit of %d bytes', MailsQueue::MAX_PRE_HEADER)
                );
            }
        }

        if (strlen($this->compiled) > MailsQueue::MAX_COMPILED_BODY) {
            throw new AppException(
                sprintf('Message complied body exceeds limit of %d bytes', MailsQueue::MAX_COMPILED_BODY)
            );
        }

        if (is_string($this->template) && !in_array($this->template, ["template1", "template2", "template3"])) {
            throw new AppException('Invalid template selection');
        }
    }

    /**
     * @return Binary
     * @throws \App\Common\Exception\AppConfigException
     */
    public function checksum(): Binary
    {
        $raw = sprintf(
            '%d:%s:%s:%s:%s:%s:%s:%d',
            $this->id,
            strtolower($this->lang),
            trim(strtolower($this->email)),
            trim(strtolower(md5(strtolower($this->subject)))),
            $this->preHeader ? trim(strtolower(md5(strtolower($this->preHeader)))) : "",
            trim(strtolower(md5(strtolower($this->compiled)))),
            trim(strtolower($this->template)),
            $this->timeStamp
        );

        return $this->app->ciphers()->secondary()->pbkdf2("sha1", $raw, 0x1a);
    }

    /**
     * @throws AppException
     * @throws \App\Common\Exception\AppConfigException
     */
    public function validate(): void
    {
        $this->_checksumVerified = false;
        if ($this->private("checksum") !== $this->checksum()->raw()) {
            throw new AppException('Message checksum failed');
        }

        $this->_checksumVerified = true;
    }
}
