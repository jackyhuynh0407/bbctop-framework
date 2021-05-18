<?php
namespace Bbctop\Lib\Foundation;
use think\app as App;
class Application extends App
{

	/**
	 * 调试模式设置
	 * @access protected
	 * @return void
	 */
	protected function debugModeInit(): void
	{
	    // 应用调试模式
	    if (!$this->appDebug) {
	        $this->appDebug = $this->env->get('app_debug') ? true : false;
	        ini_set('display_errors', 'On');
	    }

	    if (!$this->runningInConsole()) {
	        //重新申请一块比较大的buffer
	        if (ob_get_level() > 0) {
	            $output = ob_get_clean();
	        }
	        ob_start();
	        if (!empty($output)) {
	            echo $output;
	        }
	    }
	}
}