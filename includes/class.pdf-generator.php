<?php
/**
 * PDF Generator
 *
 * @package     TimeApp\PDFGenerator
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Generation class
 *
 * @since       1.0.0
 */
class TimeApp_Generate_PDF {


    public $pdf, $file, $id, $cancelled;


    /**
     * Setup the generator
     *
     * @access      public
     * @since       1.0.0
     * @param       string $file The path to the output file
     * @param       int $post_id The play we are building this for
     * @param       bool $cancelled Whether to flag the play as cancelled
     * @return      void
     */
    public function __construct( $file, $post_id, $cancelled = false ) {
        // We need the FPDF libs!
        require_once TIMEAPP_DIR . 'includes/libraries/fpdf/fpdf.php';
        require_once TIMEAPP_DIR . 'includes/class.timeapp-fpdf.php';

        $this->pdf          = new TimeApp_FPDF( 'P', 'mm', 'letter' );
        $this->file         = $file;
        $this->id           = $post_id;
        $this->cancelled    = $cancelled;
    }


    /**
     * Generate the file
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function build() {
        $point          = .35;
        $textualizer    = new TimeApp_Textualizer();
        $play           = get_post( $this->id );
        $effective_date = date( 'F jS, Y', strtotime( $play->post_date ) );
        $artist         = get_post_meta( $this->id, '_timeapp_artist', true );
        $artist         = get_post( $artist );
        $purchaser      = get_post_meta( $this->id, '_timeapp_purchaser', true );
        $purchaser      = get_post( $purchaser );
        $address        = get_post_meta( $purchaser->ID, '_timeapp_address', true );
        $city           = get_post_meta( $purchaser->ID, '_timeapp_city', true );
        $state          = get_post_meta( $purchaser->ID, '_timeapp_state', true );
        $zip_code       = get_post_meta( $purchaser->ID, '_timeapp_zip', true );
        $full_address   = $address . ', ' . $city . ', ' . $state . ' ' . $zip_code;
        $full_address   = trim( $full_address );
        $phone          = get_post_meta( $purchaser->ID, '_timeapp_phone_number', true );
        $email          = get_post_meta( $purchaser->ID, '_timeapp_email', true );
        $website        = get_post_meta( $purchaser->ID, '_timeapp_venue_url', true );
        $start_date     = get_post_meta( $this->id, '_timeapp_start_date', true );
        $end_date       = get_post_meta( $this->id, '_timeapp_end_date', true );
        $compensation   = get_post_meta( $this->id, '_timeapp_guarantee', true );
        $bonus          = get_post_meta( $this->id, '_timeapp_bonus', true ) ? true : false;
        $bonus_details  = get_post_meta( $this->id, '_timeapp_bonus_details', true );
        $deposit1_date  = get_post_meta( $this->id, '_timeapp_deposit1_date', true );
        $deposit1_amt   = get_post_meta( $this->id, '_timeapp_deposit1_amt', true );
        $balance        = $compensation - (int) $deposit1_amt;
        $production_cost= get_post_meta( $this->id, '_timeapp_production_cost', true );
        $production     = get_post_meta( $this->id, '_timeapp_production', true ) ? 'Venue to provide production' : 'Artist to provide production';
        $notes          = get_post_meta( $this->id, '_timeapp_notes', true );
        $approved       = get_post_meta( $this->id, '_timeapp_approved', true ) ? true : false;
        $contract_terms = get_post_meta( $artist->ID, '_timeapp_contract_terms', true );
        $accommodations = get_post_meta( $this->id, '_timeapp_accommodations', true );
        $commission     = get_post_meta( $artist->ID, '_timeapp_commission', true );
        $contact_fname  = get_post_meta( $purchaser->ID, '_timeapp_first_name', true );
        $contact_lname  = get_post_meta( $purchaser->ID, '_timeapp_last_name', true );
        $set_reqs       = get_post_meta( $this->id, '_timeapp_set_reqs', true );
        $tax_id         = get_post_meta( $artist->ID, '_timeapp_tax_id', true );
        $signatory      = get_post_meta( $purchaser->ID, '_timeapp_signatory', true ) ? true : false;
        $contact_name   = '';
        $date           = '';
        $terms          = '';

        // Is a contact first name specified?
        if( $contact_fname && $contact_fname != '' ) {
            $contact_name .= $contact_fname;
        }

        // Is a contact last name specified?
        if( $contact_lname && $contact_lname != '' ) {
            if( $contact_name != '' ) {
                $contact_name .= ' ';
            }

            $contact_name .= $contact_lname;
        }

        if( $signatory ) {
            $purchaser_fname = get_post_meta( $purchaser->ID, '_timeapp_signatory_first_name', true );
            $purchaser_lname = get_post_meta( $purchaser->ID, '_timeapp_signatory_last_name', true );

            $contact_name = $purchaser_fname . ( $purchaser_lname ? ' ' . $purchaser_lname : '' );
        }
        
        $contact_title = ( $contact_name != $purchaser->post_title ? $contact_name . ', ' . $purchaser->post_title : $contact_name );

        // Setup the date
        if( $start_date && $start_date != '' ) {
            $date .= $start_date;
        }

        if( $end_date && $end_date != '' ) {
            if( $date != '' ) {
                $date .= ' - ';
            }

            $date .= $end_date;
        }

        // Setup the terms
        if( $approved && $contract_terms && $contract_terms != '' ) {
            $terms .= $contract_terms;
        }

        if( $notes && $notes != '' ) {
            if( $terms != '' ) {
                $terms .= "\n";
            }

            $terms .= $notes;
        }

        $terms .= ( $terms != '' ? "\n" : '' ) . sprintf( __( 'See attached "%s RIDER"', 'timeapp' ), strtoupper( $artist->post_title ) );

        $this->pdf->SetMargins( 14, 14 );
        $this->pdf->SetAutoPageBreak( true, 18 );
        $this->pdf->AddPage();

        $this->pdf->SetFont( 'Arial', 'B', 22 );
        $this->pdf->Cell( 0, 22 * $point, 'TIME MUSIC AGENCY, INC', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'BU', 12 );
        $this->pdf->Cell( 0, 12 * $point, 'PO Box 353, Long Lake MN 55356   Office: (952) 448-4202   www.timemusicagency.com', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 15 );
        $this->pdf->Cell( 0, 15 * $point, 'MUSICAL PERFORMANCE SERVICES AGREEMENT', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'BI', 11 );
        $this->pdf->MultiCell( 0, 11 * $point, 'ANY AND ALL RIDERS ATTACHED HERETO ARE MADE A PART HEREOF', 0, 'C' );

        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, '    This Musical Performance Services Agreement is being entered into on ' . $effective_date . ' by and between the purchaser and the Artist. Whereas the purchaser of Artist (herein called "Purchaser") is a business or organization desiring to hire entertainment, and whereas the Artist (herein called "Artist") are desiring to furnish the Purchaser their services. Therefore in consideration of the promises of the parties and for other good and valuable consideration as set forth below, the Purchaser agrees to hire the Artist and the Artist agrees to perform for the Purchaser upon the terms and conditions as set forth in this agreement. The agent (herein called "Agency") is Time Music Agency.' );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '1. Artist: ' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, $artist->post_title, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '2. Purchaser: ' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, $purchaser->post_title . " / " . $address . " / " . $city . ', ' . $state . ' ' . $zip_code, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '3. Date(s) of Engagement:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, $date . ( $set_reqs && $set_reqs != '' ? "\n" . $set_reqs : '' ), 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '4. Compensation:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, timeapp_format_price( $compensation ) . ' (' . timeapp_format_price( $compensation, true ) . ')' . ( $bonus && $bonus_details && $bonus_details != '' ? "\n" . 'or ' . $bonus_details . ', whichever is greater' : '' ) , 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '5. Payment:', 0, 1 );

        if( $deposit1_amt && $deposit1_amt != '' ) {
            $this->pdf->Cell( 15, 12 * $point, '5a.' );
            $this->pdf->SetFont( 'Times', '', 12 );
            $date = ( $deposit1_date ) ? 'by ' . date( 'F jS, Y', strtotime( $deposit1_date ) ) : 'with signed contract.';

            $this->pdf->MultiCell( 0, 12 * $point, 'A non-refundable earnest money deposit in the amount of ' . timeapp_format_price( $deposit1_amt ) . ' made payable to Time Music Agency is due ' . $date . ';', 0, 1 );
        }

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 15, 12 * $point, '5b.' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'The remaining balance of ' . timeapp_format_price( $balance ) . ' is due, owing and shall be made payable to ARTIST the day of the show.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, 'TIME IS OF THE ESSENCE ON ALL PAYMENTS DUE ARTIST.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '6. Production:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, $production, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '7. Additional Terms:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, $terms, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '8. Accommodations:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, ( $accommodations ? $accommodations : 'N/A' ), 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '12. Inclement Weather/Outdoor Performances:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'In the event of inclement weather, purchaser agrees to provide an alternative site PRIOR to any setup of the ARTIST or the ARTIST\'s production crew if applicable. If no alternative site is provided, ARTIST will have no liability to Purchaser if ARTIST determines, in its exclusive and sole right of discretion, that its performance is or will become hazardous, prevented or substantially impaired due to inclement weather. In such occurrence, Purchaser remains liable to ARTIST for the full contract price. Water in the air space or on the surface of the performance area can render that show cancellable at ARTIST\'s discretion.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '21. RE-BOOKING:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'In the event Purchaser, on its own effort and without involving Time Music Agency, Inc., books Artist into any establishment owned in whole or part, booked alone or with other parties, or controlled/represented by the Purchaser within twelve (12) months after the engagement date above, Time Music Agency, Inc shall be owed and paid by Purchaser a commission of ' . $commission . '% (' . $textualizer->textualize( $commission ) . ' percent) of the total contract price that Artist and Purchaser agreed to therein and Purchaser\'s failure to honor this clause is a material breach of this contract.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->AddPage();

        $this->pdf->MultiCell( 0, 12 * $point, '    The parties have read, understood, and agree to the conditions and terms of this contract and any riders attached to this agreement. Both parties have had adequate time to review this contract with their respective legal counsel or advisors.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 10, 12 * $point, 'Dated:' );

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Line( $x + 5, $y, $x + 55, $y );

        $this->pdf->SetX( 115 );

        $this->pdf->Cell( 10, 12 * $point, 'Dated:' );

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Line( $x + 5, $y, $x + 55, $y );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, 'For Purchaser' );

        $this->pdf->SetX( 115 );

        $this->pdf->Cell( 0, 12 * $point, 'For ARTIST - Tax ID#: ' . $tax_id, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Cell( 0, 12 * $point, 'X' );
        $this->pdf->Line( $x, $y, $x + 85, $y );

        $this->pdf->SetX( 115 );

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Line( $x, $y, $x + 85, $y );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, 'By:' );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'By:', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, $contact_name );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Chad Higgins', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, $purchaser->post_title );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Time Music Agency', 0, 1 );

        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 7, 12 * $point, 'Its' );
        
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Line( $x, $y, $x + 78, $y );

        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Its CEO', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, $purchaser->post_title );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'c/o Time Music Agency', 0, 1 );
    
        $this->pdf->Cell( 0, 12 * $point, $address );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'PO Box 353', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, $city . ', ' . $state . ( $zip_code && $zip_code != '' ? ' ' . $zip_code : '' ) );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Long Lake, MN 55356', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, 'Office: ' . ( $phone && $phone != '' ? $phone : '' ) );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Office: (952) 448-4202', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ( $website && $website != '' ? $website : 'Website:' ) );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'www.timemusicagency.com', 0, 1 );

        $this->pdf->AliasNbPages();
        $this->pdf->last_page = true;

        // Output the generated PDF
        $this->pdf->Output( $this->file, 'F' );
    }
}
