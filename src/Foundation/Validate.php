<?php
/**
* 验证器
* @copyright (C)2021北京超越无限信息技术有限公司
* @license https://www.bbctop.com/
* @author 武志伟 <wzw0425@qq.com>
*/
declare (strict_types = 1);
namespace Bbctop\Lib\Foundation;
use think\Validate as BaseValidate;
use think\exception\ValidateException;
/**
 * 数据验证类
 * @package think
 */
class Validate extends BaseValidate
{



    /**
     * 数据自动验证
     * @access public
     * @param array $data  数据
     * @param array $rules 验证规则
     * @return bool
     */
    public function check(array $data, array $rules = []): bool
    {
        $this->error = [];

        if ($this->currentScene) {
            $this->getScene($this->currentScene);
        }

        if (empty($rules)) {
            // 读取验证规则
            $rules = $this->rule;
        }

        foreach ($this->append as $key => $rule) {
            if (!isset($rules[$key])) {
                $rules[$key] = $rule;
                unset($this->append[$key]);
            }
        }

        foreach ($rules as $key => $rule) {
            // field => 'rule1|rule2...' field => ['rule1','rule2',...]
            if (strpos($key, '|')) {
                // 字段|描述 用于指定属性名称
                [$key, $title] = explode('|', $key);
            } else {
                $title = $this->field[$key] ?? $key;
            }
            /**
            * BBCTOP重写
            * @author 武志伟 <wzw0425@qq.com>
            */
            if(substr($title, 0, 6) == ':lang='){
                $title=lang(str_replace(':lang=','',$title));
            }
            
            // 场景检测
            if (!empty($this->only) && !in_array($key, $this->only)) {
                continue;
            }

            // 获取数据 支持二维数组
            $value = $this->getDataValue($data, $key);

            // 字段验证
            if ($rule instanceof Closure) {
                $result = call_user_func_array($rule, [$value, $data]);
            } elseif ($rule instanceof ValidateRule) {
                //  验证因子
                $result = $this->checkItem($key, $value, $rule->getRule(), $data, $rule->getTitle() ?: $title, $rule->getMsg());
            } else {
                $result = $this->checkItem($key, $value, $rule, $data, $title);
            }

            if (true !== $result) {
                // 没有返回true 则表示验证失败
                if (!empty($this->batch)) {
                    // 批量验证
                    $this->error[$key] = $result;
                } elseif ($this->failException) {
                    throw new ValidateException($result);
                } else {
                    $this->error = $result;
                    return false;
                }
            }
        }

        if (!empty($this->error)) {
            if ($this->failException) {
                throw new ValidateException($this->error);
            }
            return false;
        }

        return true;
    }
}
