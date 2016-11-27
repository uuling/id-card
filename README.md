# 中国（大陆）公民身份证类包

* 身份证号码验证，兼容18位和15位的新老身份证
* 基于 国标 `GB/T 2260-2007` (中华人民共和国行政区划代码 标准) 。

## 安装

`composer require uuling/id-card`

## 环境要求

* PHP >= 5.4.0

## 使用说明

```php
use Uuling\Idcard;

$identity = new Identity();
$identity->setId('******************');
或者
$identity = new Identity('******************');

// 验证身份证号码格式是否正确
$identity->isValidate();
// true 或者 false

// 获取生日，格式YYYY mm dd
$identity->getBirth('-');
// 2015-01-01
$identity->getBirth('/');
// 2015/01/01

// 获取性别
$identity->getGender();
// m
$identity->getGender(Identity::GENDER_CN);
// 男

// 获取区域
$identity->getArea();
// ['province' => '***', 'city' => '***', 'county' => '***']

```

### 选项

|***选项***|***描述***|
|-----------|----------|
|Identity::GENDER_EN|性别显示方式：英文首字符【默认】(m/f)|
|Identity::GENDER_CN|性别显示方式：中文(男/女)|

## License

MIT