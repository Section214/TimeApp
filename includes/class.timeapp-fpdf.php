<?php
/**
 * TimeApp FPDF class
 *
 * @package     TimeApp\FDPF
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Generation class
 *
 * @since       1.0.0
 */
class TimeApp_FPDF extends FPDF {
    /**
     * Setup the header
     *
     * @access      public
     * @since       1.3.0
     * @return      void
     */
    public function Header() {
        if( ! isset( $this->last_page ) ) {
            $contract_sent = get_post_meta( $this->post_id, '_timeapp_contract_sent', true );

            if( $contract_sent ) {
                $this->SetFont( 'Arial', '', 14 );
                $this->SetTextColor( '94', '187', '23' );
                $this->Cell( 0, 12, 'Revised: ' . current_time( 'm/d/Y' ), 0, 2, 'C' );
            }
        }
    }


    /**
     * Setup the footer
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function Footer() {
        $this->SetY( -17 );
        $this->SetFont( 'Arial', '', 8 );

        $this->Cell( 0, 10, 'Page ' . $this->PageNo() . ' of {nb}' );

        if( ! isset( $this->last_page ) ) {
            $this->Cell( -15, 10, 'Initial:', 0, 0, 'R' );

            $x = $this->GetX();
            $y = $this->GetY() + 6;

            $this->Line( $x, $y, $x + 15, $y );
        }
    }
}
