<?php
namespace Uuling\Idcard;

/**
 * Interface IdentityInterface
 * @package Uuling\Idcard
 */
interface IdentityInterface
{
    /**
     * 设置身份证号码
     * @param string $id
     * @return mixed
     */
    public function setId($id);

    /**
     * 验证身份证号码是否正确
     *
     * @return bool
     */
    public function isValidate();

    /**
     * 根据身份证号码获取其归属地
     *
     * @return array|bool
     */
    public function getArea();

    /**
     * 根据身份证信息获取其生日信息
     *
     * @param string $delimiter 生日日期分割符
     * @return bool|string
     */
    public function getBirth($delimiter = '-');

    /**
     * 根据身份证信息获取其性别
     *
     * @param string $lang 性别显示方式 en-英文， cn-中文
     * @return bool|string
     */
    public function getGender($lang = 'en');
}