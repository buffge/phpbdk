<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/3/28
 * Time: 22:31
 */

namespace bdk\app\common\ws;

use bdk\constant\JsonReturnCode;
use JsonSerializable;

class SendFrame implements JsonSerializable
{
    /**
     * 事件
     */
    public const EVENT = [
        'pong'         => 0x0,
        'receiveMsg'   => 0x1,
        'closeInquiry' => 0x2,
    ];
    /**
     * @var int
     */
    private $code = JsonReturnCode::SUCCESS;
    /**
     * @var int
     */
    private $event;
    /**
     * @var array|null
     */
    private $data;
    /**
     * @var int|null
     */
    private $count;
    /**
     * @var string|null
     */
    private $msg;

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getEvent(): int
    {
        return $this->event;
    }

    /**
     * @param int $event
     */
    public function setEvent(int $event): void
    {
        $this->event = $event;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int|null $count
     */
    public function setCount(?int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return string|null
     */
    public function getMsg(): ?string
    {
        return $this->msg;
    }

    /**
     * @param string|null $msg
     */
    public function setMsg(?string $msg): void
    {
        $this->msg = $msg;
    }

    public function jsonSerialize(): array
    {
        $json = [
            'code'  => $this->getCode() ?? JsonReturnCode::SUCCESS,
            'event' => $this->getEvent(),
        ];
        if ( $this->getCount() !== null ) {
            $json['count'] = $this->getCount();
        }
        if ( $this->getData() !== null ) {
            $json['data'] = $this->getData();
        }
        if ( $this->getMsg() !== null ) {
            $json['msg'] = $this->getMsg();
        }
        return $json;
    }


}