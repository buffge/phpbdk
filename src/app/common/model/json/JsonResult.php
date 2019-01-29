<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/1/4
 * Time: 11:33
 */

namespace bdk\app\common\model\json;

use bdk\constant\JsonReturnCode;
use JsonSerializable;

class JsonResult implements JsonSerializable
{
    /**
     * @var int
     */
    private $code;
    /**
     * @var string
     */
    private $msg;
    /**
     * @var mixed
     */
    private $data;
    /**
     * @var int
     */
    private $count;
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $limit;

    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * @param int|null $code
     */
    public function setCode(?int $code): void
    {
        $this->code = $code;
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

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
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
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @param int|null $page
     */
    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function __construct($jsonObj = null)
    {
        if (is_array($jsonObj)) {
            $jsonObj = (object)$jsonObj;
        }
        if (empty($jsonObj)) {
            return;
        }
        $this->setCode($jsonObj->code ?? JsonReturnCode::SUCCESS);
        $this->setMsg($jsonObj->msg ?? null);
        $this->setCount($jsonObj->count ?? null);
        $this->setData($jsonObj->data ?? null);
        $this->setPage($jsonObj->page ?? null);
        $this->setLimit($jsonObj->limit ?? null);
    }

    public function jsonSerialize()
    {
        $res = [
            'code' => $this->getCode() ?? JsonReturnCode::SUCCESS,
        ];
        if (!is_null($this->getMsg())) {
            $res['msg'] = $this->getMsg();
        }
        if (!is_null($this->getCount())) {
            $res['count'] = $this->getCount();
        }
        if (!is_null($this->getData())) {
            $res['data'] = $this->getData();
        }
        if (!is_null($this->getPage())) {
            $res['page'] = $this->getPage();
        }
        if (!is_null($this->getLimit())) {
            $res['limit'] = $this->getLimit();
        }
        return $res;
    }

}