<?php
/**
 * Convert numbers to their textual representation
 *
 * @package     TimeApp\Textualizer
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Textualizer class
 *
 * @since       1.0.0
 */
class TimeApp_Textualizer {


	/**
	 * @var         array $numbers The textual representations
	 * @since       1.0.0
	 */
	private $numbers = array(
		0  => 'zero',
		1  => 'one',
		2  => 'two',
		3  => 'three',
		4  => 'four',
		5  => 'five',
		6  => 'six',
		7  => 'seven',
		8  => 'eight',
		9  => 'nine',
		10 => 'ten',
		11 => 'eleven',
		12 => 'twelve',
		13 => 'thirteen',
		14 => 'fourteen',
		15 => 'fifteen',
		16 => 'sixteen',
		17 => 'seventeen',
		18 => 'eighteen',
		19 => 'nineteen',
		20 => 'twenty',
		30 => 'thirty',
		40 => 'forty',
		50 => 'fifty',
		60 => 'sixty',
		70 => 'seventy',
		80 => 'eighty',
		90 => 'ninety'
	);


	/**
	 * Get things started?
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function __construct() {}

	/**
	 * Convert a given number sequence
	 *
	 * @access      private
	 * @since       1.0.0
	 * @param       int $number The number to convert
	 * @return      string $converted The converted number
	 */
	private function convert_set( $number ) {
		$num = ltrim( $number, '0' );

		switch ( strlen( $num ) ) {
			case 0:
				$converted = $this->numbers[0];
				break;
			case 1:
				$converted = $this->numbers[(int) $num];
				break;
			case 2:
				$tens = $this->numbers[(int) ( $num[0] . '0' )];
				$ones = '';

				if ( $num[1] != '0' ) {
					$ones = $this->numbers[(int) $num[1]];
				}

				$converted = $tens . ' ' . $ones;
				break;
			case 3:
				$hundreds = $this->numbers[(int) ( $num[0] )] . " hundred";
				$tens = '';
				$ones = '';

				if ( $num[1] != '0' ) {
					$tens = ' ' . $this->numbers[(int) ( $num[1] . '0' )];
				}

				if ( $num[2] != '0' ) {
					$ones = ' ' . $this->numbers[(int) $num[2]];
				}

				$converted = $hundreds . $tens . $ones;
				break;
		}

		return $converted;
	}


	/**
	 * Convert a given number to its textual format
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       int $number The number to convert
	 * @return      string $converted The converted number
	 */
	public function textualize( $number ) {
		if ( isset( $this->numbers[(int) $number] ) ) {
			$converted = $this->numbers[(int)$number];
		} else {
			$groups    = str_split( strrev( $number ), 3 );
			$converted = '';

			foreach ( $groups as $index => $group ) {
				$groups[ $index ] = $this->convert_set( strrev( $group ) );
			}

			switch ( count( $groups ) ) {
				case 5:
					if ( $groups[4] != 'zero' ) {
						$converted .= $groups[4] . ' trillion ';
					}
				case 4:
					if ( $groups[3] != 'zero' ) {
						$converted .= $groups[3] . ' billion ';
					}
				case 3:
					if ( $groups[2] != 'zero' ) {
						$converted .= $groups[2] . ' million ';
					}
				case 2:
					if ( $groups[1] != 'zero' ) {
						$converted .= $groups[1] . ' thousand ';
					}
				case 1:
					if ( $groups[0] != 'zero') {
						$converted .= $groups[0];
					}
			}
		}

		// Cleanup
		$converted = trim( str_replace( '  ', ' ', $converted ) );

		return $converted;
	}
}
