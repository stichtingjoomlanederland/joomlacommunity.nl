<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * SQL string builder for Komento.
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class KomentoSql
{
	static $instance	= null;

	public $db 			= null;

	/**
	 * Stores the data internally in index format.
	 * @var array
	 */
	private $data 		= array();

	/**
	 * Stores the a unparsed Sql
	 * @var string
	 */
	private $string 	= '';

	/**
	 * Set the mode of the sql
	 * @var string
	 */
	private $mode		= 'select';

	/**
	 * Set the last action of the sql
	 * @var string
	 */
	private $action		= 'where';

	public static function factory()
	{
		return new self();
	}

	public function __construct()
	{
		$this->db = Komento::getDBO();
	}

	/**
	 * START OF USER BUILD FUNCTIONS
	 */

	public function clear()
	{
		// Reset all the data
		$this->data = array();
		$this->mode = 'select';
		$this->action = 'where';
		$this->string = '';
	}

	public function delete( $table )
	{
		// Set the mode
		$this->mode = 'delete';

		$this->data['table'] = array( $table, '' );

		return $this;
	}

	public function select( $table, $alias = '' )
	{
		// Set the mode
		$this->mode = 'select';

		$this->data['table'] = array( $table, $alias );

		return $this;
	}

	public function column( $column, $alias = '', $function = '', $psuedo = false )
	{
		if( !isset( $this->data['columns'] ) )
		{
			$this->data['columns'] = array();
		}

		$this->data['columns'][] = array( $column, $alias, $function, $psuedo );

		return $this;
	}

	public function update( $table )
	{
		$this->mode = 'update';

		$this->data['table'] = $table;

		return $this;
	}

	public function set( $column, $value )
	{
		if( !isset( $this->data['set'] ) )
		{
			$this->data['set'] = array();
		}

		$this->data['set'][] = array( $column, $value );

		return $this;
	}

	public function insert( $table, $columns = array() )
	{
		$this->mode = 'insert';

		$this->data['insert'] = array( $table, $columns );

		return $this;
	}

	public function values()
	{
		if( !isset( $this->data['values'] ) )
		{
			$this->data['values'] = array();
		}

		$this->data['values'][] = func_get_args();

		return $this;
	}

	public function join( $table, $alias, $type = 'LEFT' )
	{
		if( !isset( $this->data['join'] ) )
		{
			 $this->data['join'] = array();
		}

		$this->data['join'][] = array( $table, $alias, $type );

		return $this;
	}

	public function leftjoin( $tableName, $alias = '' )
	{
		return $this->join( $tableName, $alias, 'LEFT' );
	}

	public function rightjoin( $tableName, $alias = '' )
	{
		return $this->join( $tableName, $alias, 'RIGHT' );
	}

	public function innerjoin( $tableName, $alias = '' )
	{
		return $this->join( $tableName, $alias, 'INNER' );
	}

	public function on( $columnA, $columnB = '', $type = '=', $condition = 'AND' )
	{
		// Mark the last action
		$this->action = 'on';

		if( !isset( $this->data['on'] ) )
		{
			$this->data['on'] = array();
		}

		$joinindex = count( $this->data['join'] ) - 1;

		if( !isset( $this->data['on'][$joinindex] ) )
		{
			$this->data['on'][$joinindex] = array();
		}

		$this->data['on'][$joinindex][] = array( $columnA, $columnB, $type, $condition );

		return $this;
	}

	public function where( $column, $value = '', $type = '=', $condition = 'AND' )
	{
		// Mark the last action
		$this->action = 'where';

		if( !isset( $this->data['where'] ) )
		{
			$this->data['where'] = array();
		}

		$this->data['where'][] = array( $column, $value, $type, $condition );

		return $this;
	}

	public function group()
	{
		if( !isset( $this->data['group'] ) )
		{
			$this->data['group'] = array();
		}

		$columns = func_get_args();

		foreach( $columns as $column )
		{
			$this->data['group'][] = $column;
		}

		return $this;
	}

	public function having( $column, $value = '', $type = '=', $condition = 'AND' )
	{
		// Mark the last action
		$this->action = 'having';

		if( !isset( $this->data['having'] ) )
		{
			$this->data['having'] = array();
		}

		$this->data['having'][] = array( $column, $value, $type, $condition );

		return $this;
	}

	public function order( $column, $direction = 'ASC', $function = '' )
	{
		if( !isset( $this->data['order'] ) )
		{
			$this->data['order'] = array();
		}

		$this->data['order'][] = array( $column, $direction, $function );

		return $this;
	}

	public function limit( $start, $limit = null )
	{
		// If $limit is null, means that callee is passing in $start as the limit
		if( is_null( $limit ) )
		{
			$limit = $start;
			$start = 0;
		}

		$this->data['limit'] = array( $start, $limit );

		return $this;
	}

	public function raw( $string )
	{
		$this->string = $string;

		return $this;
	}

	/**
	 * END OF USER BUILD FUNCTIONS
	 */

	/**
	 * START OF STRING RETURN FUNCTIONS
	 */

	public function debug()
	{
		$prefix 	= Komento::_("JFactory::getConfig", "dbprefix");

		$query 		= $this->buildSql();

		return str_ireplace( '#__' , $prefix , $query );
	}

	public function getSql()
	{
		$query 	= $this->buildSql();

		return $query;
	}

	public function getTotalSql()
	{
		// Check for raw string
		if( !empty( $this->string ) )
		{
			$string = $this->parse( $this->string );

			$pattern = '/select.+from/i';
			$string = preg_replace( $pattern, 'SELECT COUNT(1) FROM', $string );

			return $string;
		}

		$query = $this->buildTotal();

		$directives = array( 'join', 'where', 'group', 'having' );

		$query .= $this->buildDirectives( $directives );

		return $query;
	}

	public function __toString()
	{
		$query 	= $this->buildSql();

		return $query;
	}

	/**
	 * END OF STRING RETURN FUNCTIONS
	 */

	/**
	 * START OF PARSING FUNCTIONS
	 */

	public function nameQuote( $string )
	{
		return $this->db->nameQuote( $string );
	}

	public function quote( $string )
	{
		return $this->db->quote( $string );
	}

	public function parse( $string )
	{
		return $this->parseNameQuote( $this->parseQuote( $string ) );
	}

	private function parseNameQuote( $string )
	{
		$pattern	= '/`(.*?)`/i';
		$string		= preg_replace( $pattern, $this->nameQuote( '$1' ), $string );

		return $string;
	}

	private function parseQuote( $string )
	{
		$pattern	= '/\'(.*?)\'/i';
		$string		= preg_replace( $pattern, $this->quote( '$1' ), $string );

		return $string;
	}

	private function parseTableName( $string )
	{
		if( substr( $string, 0, 3 ) !== '#__' )
		{
			$string = '#__' . $string;
		}

		return $string;
	}

	private function parseColumnName( $string )
	{
		$line = '';

		if( $this->hasColumnFunction( $string ) )
		{
			$line = $this->parse( $string );
		}
		else
		{
			$tmp = explode( '.', $string );

			foreach( $tmp as &$t )
			{
				if( $t !== '*' )
				{
					$t = $this->nameQuote( $t );
				}
			}

			$line = implode( '.', $tmp );
		}

		return $line;
	}

	private function hasColumnFunction( $string )
	{
		static $functions = array( 'count', 'distinct', 'unique', 'sum', 'lower' );

		foreach( $functions as $function )
		{
			if( stripos( $string, $function ) !== false )
			{
				return true;
			}
		}

		return false;
	}

	private function parseFunction( $function, $column )
	{
		// There could be multiple functions
		$funcs = array_reverse( explode( ' ', $function ) );

		$line = $column;

		foreach( $funcs as $func )
		{
			$line = strtoupper( $func ) . '(' . $line . ')';
		}

		return $line;
	}

	/**
	 * END OF PARSING FUNCTIONS
	 */

	/**
	 * START OF INTERNAL BUILDING FUNCTIONS
	 */

	private function buildSql()
	{
		// Check for raw string
		if( !empty( $this->string ) )
		{
			$string = $this->parse( $this->string );

			return $string;
		}

		// Build base sql from the mode
		$base = 'build' . ucfirst( $this->mode );

		$query = $this->$base();

		/* directives
		join
		where
		group
		having
		order
		limit
		*/

		// Build the remaining pieces of the sql
		$directives = array(
			'select'	=> array( 'join', 'where', 'group', 'having', 'order', 'limit' ),
			'delete'	=> array( 'where', 'limit' ),
			'update'	=> array( 'where', 'limit' ),
			'insert'	=> array( 'values' )
		);

		$query .= $this->buildDirectives( $directives[$this->mode] );

		return $query;
	}

	private function buildDirectives( $directives )
	{
		$query = '';

		foreach( $directives as $directive )
		{
			$command = 'build' . ucfirst( $directive );

			$query .= $this->$command();
		}

		return $query;
	}

	private function buildDelete()
	{
		$table = $this->data['table'];

		$query	= 'DELETE FROM ' . $this->nameQuote( $table[0] );

		return $query;
	}

	private function buildSelect()
	{
		$columns = array();

		// If no columns, then set the default column as *
		if( !isset( $this->data['columns'] ) )
		{
			$this->data['columns'] = array( array( '*', '', '', false ) );
		}

		foreach( $this->data['columns'] as $column )
		{
			list( $columnName, $alias, $function, $psuedo ) = $column;

			$name = $this->parseColumnName( $column[0] );

			// If it is a psuedo column, then quote the value instead
			if( $psuedo )
			{
				$columnName = $this->quote( $columnName );
			}
			else
			{
				$columnName = $this->parseColumnName( $column[0] );
			}

			// Set line as the column name
			$line = $columnName;

			// If this column is a function, set the line to use function instead
			if( !empty( $function ) )
			{
				$line = $this->parseFunction( $function, $line );
			}

			// Set column alias
			if( !empty( $alias ) )
			{
				$line .= ' AS ' . $this->nameQuote( $alias );
			}

			$columns[] = $line;
		}

		// Build the columns string
		$columns = implode( ',', $columns );

		// Main select query string
		$query = 'SELECT ' . $columns . ' FROM ' . $this->nameQuote( $this->data['table'][0] );

		// Set table alias
		if( !empty( $this->data['table'][1] ) )
		{
			$query	.= ' AS ' . $this->nameQuote( $this->data['table'][1] );
		}

		return $query;
	}

	private function buildUpdate()
	{
		// $this->data['set'][] = array( $column, $value );

		$sets = array();

		$set = $this->data['set'];

		$iLength = count( $set );
		for( $i = 0; $i < $iLength; $i++ )
		{
			list( $column, $value ) = $set[$i];

			// The value could be just not a value
			if( stripos( $value, '`' ) !== false )
			{
				$value = $this->parse( $value );
			}
			else
			{
				$value = $this->quote( $value );
			}

			$sets[] = $this->parseColumnName( $column ) . ' = ' . $value;
		}

		$query = 'UPDATE ' . $this->nameQuote( $this->data['table'] );

		$query .= count( $sets ) > 0 ? ' SET ' . implode( ', ', $sets ) : '';

		return $query;
	}

	private function buildInsert()
	{

	}

	private function buildValues()
	{

	}

	private function buildTotal()
	{
		// Main total query string
		$query = 'SELECT COUNT(1) FROM ' . $this->nameQuote( $this->data['table'][0] );

		// Set table alias
		if( !empty( $this->data['table'][1] ) )
		{
			$query	.= ' AS ' . $this->nameQuote( $this->data['table'][1] );
		}

		return $query;
	}

	private function buildWhere()
	{
		// $this->data['where'][] = array( $column, $value, $type, $condition );

		if( !isset( $this->data['where'] ) )
		{
			return '';
		}

		$where = $this->data['where'];

		$query = array();

		$joiner = false;

		$length = count( $where );
		for( $i = 0; $i < $length; $i++ )
		{
			list( $column, $value, $type, $condition ) = $where[$i];

			$line = '';

			// For second condition and above, we need the condition joiner
			if( $joiner )
			{
				$line = strtoupper( $condition ) . ' ';
			}
			else
			{
				$joiner = true;
			}

			if( $column === '(' || ( isset( $where[$i + 1 ] ) && $where[$i + 1][0] === ')' ) )
			{
				$joiner = false;
			}

			if( $column === '(' || $column === ')' )
			{
				$line .= $column;
			}
			else
			{
				$line .= $this->buildCondition( $column, $value, $type );
			}

			$query[] = $line;
		}

		$query	= count( $query ) > 0 ? ' WHERE ' . implode( ' ', $query ) : '';

		return $query;
	}

	private function buildCondition( $column, $value, $type = '=' )
	{
		$query = $this->parseColumnName( $column ) . ' ';

		// type could be IN or NOT IN, value could be an array of values
		if( stripos( $type, 'in' ) !== false )
		{
			if( !is_array( $value ) )
			{
				$value = array( $value );
			}

			foreach( $value as &$v )
			{
				$v = $this->quote( $v );
			}

			$query .= strtoupper( $type ) . ' (' . implode( ', ', $value ) . ')';
		}
		else
		{
			if( empty( $type ) )
			{
				$type = '=';
			}

			$query .= strtoupper( $type ) . ' ' . $this->quote( $value );
		}

		return $query;
	}

	private function buildJoin()
	{
		// $this->data['join'][] = array( $table, $alias, $type );

		if( !isset( $this->data['join'] ) )
		{
			return '';
		}

		$query = array();

		$join = $this->data['join'];

		$iLength = count( $join );
		for( $i = 0; $i < $iLength; $i++ )
		{
			list( $table, $alias, $type ) = $join[$i];

			$line = ' ' . strtoupper( $type ) . ' JOIN ' . $this->nameQuote( $table ) . ' AS ' . $this->nameQuote( $alias );

			if( isset( $this->data['on'] ) && $this->data['on'][$i] )
			{
				// $this->data['on'][$joinindex][] = array( $columnA, $columnB, $type, $condition );

				$line .= ' ON ';

				$on = $this->data['on'][$i];

				$joiner = false;

				$jLength = count( $on );
				for( $j = 0; $j < $jLength; $j++ )
				{
					list( $columnA, $columnB, $onType, $onCondition ) = $on[$j];

					// For second condition and above, we need the condition joiner
					if( $joiner )
					{
						$line .= ' ' . $onCondition . ' ';
					}
					else
					{
						$joiner = true;
					}

					if( $columnA === '(' || ( isset( $on[$j + 1 ] ) && $on[$j + 1][0] === ')' ) )
					{
						$joiner = false;
					}

					if( $columnA === '(' || $columnA === ')' )
					{
						$line .= $columnA;
					}
					else
					{
						if( empty( $onType ) )
						{
							$onType = '=';
						}

						$line .= $this->parseColumnName( $columnA ) . ' ' . $onType . ' ';

						// columnB could be a value rather than a column name
						// test for '.' to see if it is a column name
						if( strpos( $columnB, '.' ) !== false )
						{
							$line .= $this->parseColumnName( $columnB );
						}
						else
						{
							$line .= $this->quote( $columnB );
						}
					}
				}
			}

			$query[] = $line;
		}

		$query = implode( ' ', $query );

		return $query;
	}

	private function buildGroup()
	{
		if( !isset( $this->data['group'] ) )
		{
			return '';
		}

		$group = $this->data['group'];

		$query = array();

		foreach( $group as $column )
		{
			$query[] = $this->parseColumnName( $column );
		}

		$query = count( $query ) > 0 ? ' GROUP BY ' . implode( ', ', $query ) : '';

		return $query;
	}

	private function buildHaving()
	{
		if( !isset( $this->data['having'] ) )
		{
			return '';
		}

		// $this->data['having'][] = array( $column, $value, $type, $condition );

		$having = $this->data['having'];

		$query = array();

		$joiner = false;

		$iLength = count( $having );
		for( $i = 0; $i < $iLength; $i++ )
		{
			list( $column, $value, $type, $condition ) = $having[$i];

			$line = '';

			// For second condition and above, prepend the where condition
			if( $joiner )
			{
				$line = strtoupper( $condition ) . ' ';
			}
			else
			{
				$joiner = true;
			}

			if( $column === '(' || ( isset( $having[$i + 1 ] ) && $having[$i + 1][0] === ')' ) )
			{
				$joiner = false;
			}

			if( $column === '(' || $column === ')' )
			{
				$line .= $column;
			}
			else
			{
				$line .= $this->buildCondition( $column, $value, $type );
			}

			$query[] = $line;
		}

		$query = count( $query ) > 0 ? ' HAVING ' . implode( ' ', $query ) : '';

		return $query;
	}

	private function buildOrder()
	{
		if( !isset( $this->data['order'] ) )
		{
			return '';
		}

		// $this->data['order'][] = array( $column, $direction );

		$order = $this->data['order'];

		$line = '';

		$iLength = count( $order );
		for( $i = 0; $i < $iLength; $i++ )
		{
			list( $column, $direction, $function ) = $order[$i];

			$line = $this->parseColumnName( $column );

			if( !empty( $function ) )
			{
				$line = $this->parseFunction( $function, $line );
			}

			$query[] = $line . ' ' . $direction;
		}

		$query	= count( $query ) > 0 ? ' ORDER BY ' . implode( ',', $query ) : '';

		return $query;
	}

	private function buildLimit()
	{
		if( !isset( $this->data['limit'] ) )
		{
			return '';
		}

		// $this->data['limit'] = array( $start, $limit );

		list( $start, $limit ) = $this->data['limit'];

		$query = ' LIMIT ' . $start . ', ' . $limit;

		return $query;
	}

	/**
	 * END OF INTERNAL BUILDING FUNCTIONS
	 */

	public function __call( $method, $args )
	{
		if( !is_callable( array( $this->db, $method ) ) )
		{
			return false;
		}

		$query = '';

		if( isset($args[0]) )
		{
			$query = $args[0];
		}
		else
		{
			$query = $this->getSql();
		}

		$this->db->setQuery( $query );

		return $this->db->$method();
	}
}
