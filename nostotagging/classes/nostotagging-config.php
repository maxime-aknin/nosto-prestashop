<?php

/**
 * Helper class for managing config values.
 */
class NostoTaggingConfig
{
	const ACCOUNT_NAME = 'NOSTOTAGGING_ACCOUNT_NAME';
	const USE_DEFAULT_NOSTO_ELEMENTS = 'NOSTOTAGGING_DEFAULT_ELEMENTS';
	const ADMIN_URL = 'NOSTOTAGGING_ADMIN_URL';

	/**
	 * @param string $name
	 * @param int|null $lang_id
	 * @param int|null $id_shop_group
	 * @param int|null $id_shop
	 * @param bool $lang_fallback
	 * @return bool
	 */
	public static function read($name, $lang_id = null, $id_shop_group = null, $id_shop = null, $lang_fallback = true)
	{
		$value = Configuration::get($name, $lang_id, $id_shop_group, $id_shop);
		if ($value === false || $value === null && $lang_fallback && $lang_id > 0)
			return Configuration::get($name);
		else
			return $value;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param bool $global
	 * @param int $language_id
	 * @return bool
	 */
	public static function write($name, $value, $global = false, $language_id = 0)
	{
		$callback = array(
			'Configuration',
			$global ? 'updateGlobalValue' : 'updateValue'
		);
		// Store this value for given language only if specified.
		if (!is_array($value) && !empty($language_id))
			$value = array($language_id => $value);
		return call_user_func($callback, (string)$name, $value);
	}

	/**
	 * @param string $name
	 * @param int $lang_id
	 * @param bool $lang_fallback
	 * @return bool
	 */
	public static function exists($name, $lang_id = 0, $lang_fallback = true)
	{
		$value = self::read($name, $lang_id, $lang_fallback);
		return ($value !== false && $value !== null);
	}

	/**
	 * Removes all "NOSTOTAGGING_" config entries.
	 */
	public static function purge()
	{
		$config_table = _DB_PREFIX_.bqSQL(Configuration::$definition['table']);
		$config_lang_table = $config_table.'_lang';

		Db::getInstance()->execute('
			DELETE FROM `'.$config_lang_table.'`
			LEFT JOIN `'.$config_table.'`
			ON `'.$config_lang_table.'`.`id_configuration` = `'.$config_table.'`.`id_configuration`
			WHERE `'.$config_table.'`.`name` LIKE "NOSTOTAGGING_%"'
		);
		Db::getInstance()->execute('
			DELETE FROM `'.$config_table.'`
			WHERE `'.$config_table.'`.`name` LIKE "NOSTOTAGGING_%"'
		);

		Configuration::$_cache[Configuration::$definition['table']] = null;

		return true;
	}

	/**
	 * Removes all "NOSTOTAGGING_" config entries for the current context and given language.
	 *
	 * @param int|null $language_id the ID of the language object to remove the config entries for.
	 * @return bool
	 */
	public static function deleteAllFromContext($language_id = null)
	{
		$id_shop = (int)Shop::getContextShopID(true);
		$id_shop_group = (int)Shop::getContextShopGroupID(true);

		if ($id_shop)
			$context_restriction = ' AND `id_shop` = '.$id_shop;
		elseif ($id_shop_group)
			$context_restriction = ' AND `id_shop_group` = '.$id_shop_group.' AND (`id_shop` IS NULL OR `id_shop` = 0)';
		else
			$context_restriction = ' AND (`id_shop_group` IS NULL OR `id_shop_group` = 0) AND (`id_shop` IS NULL OR `id_shop` = 0)';

		$config_table = _DB_PREFIX_.bqSQL(Configuration::$definition['table']);
		$config_lang_table = $config_table.'_lang';

		if (!empty($language_id))
			Db::getInstance()->execute('
				DELETE `'.$config_lang_table.'` FROM `'.$config_lang_table.'`
				INNER JOIN `'.$config_table.'`
				ON `'.$config_lang_table.'`.`id_configuration` = `'.$config_table.'`.`id_configuration`
				WHERE `'.$config_table.'`.`name` LIKE "NOSTOTAGGING_%"
				AND `id_lang` = '.(int)$language_id
				.$context_restriction
			);
		// We do not actually delete the main config entries, just set them to NULL, as there might me other language
		// specific entries tied to them. The main entries are not used anyways if there are languages defined.
		Db::getInstance()->execute('
			UPDATE `'.$config_table.'`
			SET `value` = NULL
			WHERE `name` LIKE "NOSTOTAGGING_%"'
			.$context_restriction
		);

		Configuration::$_cache[Configuration::$definition['table']] = null;

		return true;
	}
}
