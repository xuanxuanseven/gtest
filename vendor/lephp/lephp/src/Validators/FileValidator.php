<?php
namespace Lephp\Validators;

use Lephp\Core\Input;
use Lephp\Core\InvalidParamException;

/**
 * Class FileValidator 文件校验器
 * @package Lephp\Validators
 *
 * [
 *    ['file1'],
 *    'file' => [
 *                  'type' => 'text/markdown' // mime types,
 *                  'maxSize' => '1000000',
 *              ]
 * ]
 */
class FileValidator extends Validator
{
    public $types;

    public $minSize;

    public $maxSize;

    public $maxFiles;

    public $message;

    public $uploadedRequired;

    public $tooBig;

    public $tooSmall;

    public $wrongType;

    public $tooMany;

    public $rules;

    public $fileArray;

    public $isFileArray = false;

    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = 'File Upload Failed.';
        }

        if ($this->uploadedRequired === null) {
            $this->uploadedRequired = 'Please upload a file';
        }

        if ($this->tooMany === null) {
            $this->tooMany = 'Too many files';
        }

        if ($this->wrongType === null) {
            $this->wrongType = 'Wrong file type';
        }

        if ($this->tooBig === null) {
            $this->tooBig = 'file size reach max limit';
        }

        if ($this->tooSmall === null) {
            $this->tooMany = 'file size too small';
        }

        if (!is_array($this->types)) {
            $this->types = preg_split('/[\s,]+/', strtolower($this->types), -1, PREG_SPLIT_NO_EMPTY);
        }


    }

    public function validateInputs()
    {
        if (is_array($this->attributes)) {
            foreach ($this->attributes as $attribute) {
                $inputData = Input::getFile($attribute);
                $this->_validate($inputData, $attribute);
            }
        } else {
            $inputData = Input::getFile($this->attributes);
            if ($this->isFileArray($inputData)) {
                $this->fileArray = $this->buildFileArray($inputData);
                $this->isFileArray = true;
            }
            $this->_validate($inputData, $this->attributes);
        }
    }

    /**
     * @param $inputData
     * @param $attribute
     * @return bool
     * @throws InvalidParamException
     */
    protected function _validate($inputData, $attribute)
    {
        if (is_array($this->rules)) {
            if (is_null($inputData) || UPLOAD_ERR_OK !== $inputData['error'])
                throw new InvalidParamException('input file is null or incomplete!');
            foreach ($this->rules as $key => $rule) {
                call_user_func([$this, 'validate' . ucfirst($key)], $inputData, $rule);
            }
        } else
            throw new InvalidParamException('validate rules should be an array!');

        return true;
    }

    /**
     * 校验文件数目
     * @param $inputData
     * @param $maxFiles
     * @return bool
     * @throws InvalidParamException
     */
    public function validateMaxFiles($inputData, $maxFiles)
    {
        if ($this->isFileArray) {
            //有多个文件
            if (count($this->fileArray) > $maxFiles) {
                throw new InvalidParamException($this->tooMany);
            }
        } else {
            // 只有一个文件
            return true;
        }
        return true;
    }

    /**
     * 校验文件大小
     * @param $inputData
     * @param $maxSize
     * @return bool
     * @throws InvalidParamException
     */
    public function validateMaxSize($inputData, $maxSize)
    {
        if ($this->isFileArray) {
            foreach ($this->fileArray as $item) {
                self::validateMaxSize($item, $maxSize);
            }
        } else {
            if (isset($inputData['size']) && ($inputData['size'] < $maxSize)) {
                return true;
            } else {
                throw new InvalidParamException($this->tooBig);
            }
        }
        return true;
    }

    /**
     * 验证最小文件大小
     * @param $inputData
     * @param $minSize
     * @return bool
     * @throws InvalidParamException
     */
    public function validateMinSize($inputData, $minSize)
    {
        if ($this->isFileArray) {
            foreach ($this->fileArray as $item) {
                self::validateMinSize($item, $minSize);
            }
        } else {
            if (isset($inputData['size']) && ($inputData['size'] > $minSize)) {
                return true;
            } else {
                throw new InvalidParamException($this->tooSmall);
            }
        }
        return true;
    }

    /**
     * 验证文件类型
     * @param $inputData
     * @param $type
     * @return bool
     * @throws InvalidParamException
     * @todo 支持校验多种类型
     */
    public function validateType($inputData, $type)
    {
        if ($this->isFileArray) {
            foreach ($this->fileArray as $item) {
                self::validateType($item, $type);
            }
        } else {
            if (isset($inputData['type']) && ($inputData['type'] === $type)) {
                return true;
            } else {
                throw new InvalidParamException($this->wrongType);
            }
        }
        return true;
    }

    public function buildFileArray($inputData)
    {
        $fileArray = [];
        foreach ($inputData as $key => $values) {
            foreach ($values as $index => $value) {
                $fileArray[$index][$key] = $value;
            }
        }
        return $fileArray;
    }

    /**
     * 判断参数是否是数组形式提交
     * @param $inputData
     * @return bool
     */
    public function isFileArray($inputData)
    {
        if (!is_array($inputData)) {
            return false;
        }

        if (empty($inputData)) {
            return false;
        }

        if (isset($inputData['name']) && is_array($inputData['name'])) {
            return true;
        }

        return false;
    }

    /**
     * Builds the RegExp from the $mask
     *
     * @param string $mask
     * @return string the regular expression
     * @see mimeTypes
     */
    private function buildMimeTypeRegexp($mask)
    {
        return '/^' . str_replace('\*', '.*', preg_quote($mask, '/')) . '$/';
    }
}