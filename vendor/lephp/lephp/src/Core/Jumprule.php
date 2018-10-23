<?php
/**
 * 生成打洞规则工具类
 * Created by PhpStorm.
 * User: wanglishuang
 * Date: 2016/9/28
 * Time: 10:56
 */

namespace Lephp\Core;
class Jumprule
{
	private static $jump_rule_list = null;
	/**
	 * 大屏购物app
	 * @param string $product
	 * @param string $jumpurl type=1&item_id=123&category_id=234&product_type=345&have_mmsid=456
	 * @return array
	 */
	public static function tvshopping($product, $jumpInfo) {
		if (empty(parse_url($jumpInfo, PHP_URL_HOST))) {
			return false;
		}
		$filterXss = str_replace(array('\'','"',';','<','>','(',')','{','}','[',']'), '', $jumpInfo);
		if ($filterXss != $jumpInfo) {
			return false;
		}
		
		if (empty(self::$jump_rule_list)) {
			self::$jump_rule_list = Config::get('appjump', $product);
		}
		//大屏购物app打洞规则.这是个模板,具体数值后面填
		$tvShoppingJumpRule = json_decode(self::$jump_rule_list['tvshopping'], true);
		
		parse_str($jumpInfo, $parseInfo);
		$tvShoppingJumpRule['params']['type'] = $parseInfo['type'];
		
		$tvShoppingJumpRule['params']['value']['item_id'] = 
				!empty($parseInfo['item_id']) ? $parseInfo['item_id'] : '';
		
		$tvShoppingJumpRule['params']['value']['category_id'] =
				!empty($parseInfo['category_id']) ? $parseInfo['category_id'] : '';
		
		$tvShoppingJumpRule['params']['value']['product_type'] =
				!empty($parseInfo['product_type']) ? $parseInfo['product_type'] : '';
		
		$tvShoppingJumpRule['params']['value']['have_mmsid'] =
				!empty($parseInfo['have_mmsid']) ? $parseInfo['have_mmsid'] : '';
		
		return $tvShoppingJumpRule;
	}
	
	/**
	 * 大屏跳转H5
	 * @param string $product
	 * @param string $jumpurl http://le.com
	 * @return array
	 */
	public static function tvh5($product, $jumpInfo) {
		if (empty(parse_url($jumpInfo, PHP_URL_HOST))) {
			return false;
		}
		$filterXss = str_replace(array('\'','"',';','<','>','(',')','{','}','[',']'), '', $jumpInfo);
		if ($filterXss != $jumpInfo) {
			return false;
		}
		
		if (empty(self::$jump_rule_list)) {
			self::$jump_rule_list = Config::get('appjump', $product);
		}
		
		$tvH5JumpRule = json_decode(self::$jump_rule_list['innerh5'], true);
		$tvH5JumpRule['jump']['value']['value']['url'] = $jumpInfo;
	
		return $tvH5JumpRule;
	}
	
	/**
	 * 根据urm返回值生成大屏用户中心app数据
	 * @param array $urmData
	 * @return array
	 */
	public static function anlayseUrmResultForTv($urmData) {
		
	}
}