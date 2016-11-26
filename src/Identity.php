<?php
namespace Uuling\Idcard;

use InvalidArgumentException;

/**
 * Class Identity
 */
class Identity implements IdentityInterface
{
    // 性别显示方式：英文首字符
    const GENDER_EN = 'en';

    // 性别显示方式：中文
    const GENDER_CN = 'zh';

    /**
     * 中国大陆身份证号码
     *
     * @var string
     */
    protected $idNumber;

    /**
     * 中国大陆身份证号码长度
     *
     * @var int
     */
    protected $idLength;

    /**
     * 身份证号码是否验证通过
     *
     * @var bool
     */
    protected $isValidate = false;

    /**
     * 加权因子
     *
     * @var array
     */
    protected $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];

    /**
     * 校验码
     *
     * @var array
     */
    protected $verifyCode = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

    /**
     * Identity constructor.
     * @param null|static $id 身份证号码
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->setId($id);
        }
    }

    /**
     * 设置身份证号码
     *
     * @param string $id 身份证号码
     * @throws InvalidArgumentException
     * @return void
     */
    public function setId($id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Id Card must not be empty.');
        }

        $this->idNumber = strtoupper(trim($id));

        $this->idLength = strlen($this->idNumber);

        $this->isValidate = false;
    }

    /**
     * 验证身份证号码是否正确
     *
     * @return bool
     */
    public function isValidate()
    {
        if ($this->isValidate) {
            return true;
        }

        if ($this->checkFormat() && $this->checkBirth() && $this->checkLastCode()) {
            $this->isValidate = true;
            return true;
        }

        return false;

    }

    /**
     * 根据身份证号码获取其归属地
     *
     * @return array|bool
     */
    public function getArea()
    {
        if (!$this->isValidate || !$this->isValidate()) {
            return false;
        }
        return GBArea::getArea($this->idNumber);
    }

    /**
     * 根据身份证信息获取其生日信息
     *
     * @param string $delimiter 生日日期分割符
     * @return bool|string
     */
    public function getBirth($delimiter = '-')
    {
        if (!$this->isValidate || !$this->isValidate()) {
            return false;
        }

        $birth = [
            'year' => substr($this->idNumber, 6, 4),
            'month' => substr($this->idNumber, 10, 2),
            'day' => substr($this->idNumber, 12, 2),
        ];

        return implode('-', $birth);
    }

    /**
     * 根据身份证信息获取其性别
     *
     * @param string $lang 性别显示方式 en-英文， cn-中文
     * @return bool|string
     */
    public function getGender($lang = self::GENDER_EN)
    {
        if (!$this->isValidate || !$this->isValidate()) {
            return false;
        }

        // 倒数第2位
        $gender = substr($this->idNumber, 16, 1);

        if ($lang == self::GENDER_CN) {
            $gender = ($gender % 2 == 0) ? '女' : '男';
        } else {
            $gender = ($gender % 2 == 0) ? 'f' : 'm';
        }
        return $gender;
    }

    /**
     * 通过正则表达式检测身份证号码格式
     *
     * @return bool
     */
    protected function checkFormat()
    {
        $this->id15To18();

        if ($this->idLength == 15) {
            $pattern = '/^\d{6}(18|19|20)\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}$/';
        } else {
            $pattern = '/^\d{6}(18|19|20)\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/';
        }

        if (preg_match($pattern, $this->idNumber)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检测身份证生日是否正确
     *
     * @return bool
     */
    protected function checkBirth()
    {
        $year = substr($this->idNumber, 6, 4);
        $month = substr($this->idNumber, 10, 2);
        $day = substr($this->idNumber, 12, 2);

        if (checkdate($month, $day, $year)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 校验身份证号码最后一位校验码
     *
     * @return bool
     */
    protected function checkLastCode()
    {
        if ($this->idLength == 15) {
            return true;
        }

        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += substr($this->idNumber, $i, 1) * $this->factor[$i];
        }

        $mod = $sum % 11;

        if ($this->verifyCode[$mod] != substr($this->idNumber, -1)) {
            return false;
        }
        return true;
    }

    /**
     * 将 15 位身份证转化为 18 位身份证号码
     *
     * @return string
     */
    protected function id15To18()
    {
        if ($this->idLength == 15) {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($this->idNumber, 12, 3), ['996', '997', '998', '999']) !== false) {
                $this->idNumber = substr($this->idNumber, 0, 6) . '18' . substr($this->idNumber, 6, 9);
            } else {
                $this->idNumber = substr($this->idNumber, 0, 6) . '19' . substr($this->idNumber, 6, 9);
            }

            // 补全最后一位

        }
        return $this->idNumber;
    }
}